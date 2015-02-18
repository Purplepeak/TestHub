<?php

/**
 * This is the model class for table "test_images".
 *
 * The followings are the available columns in table 'test_images':
 * @property integer $id
 * @property string $link
 * @property string $type
 * @property integer $question_id
 * @property integer $test_id
 *
 * The followings are the available model relations:
 * @property Test $test
 * @property Question $question
 */
class TestImages extends CActiveRecord
{

    public $imageFile;

    public $_type;

    protected $imageDir;

    protected $idAttribute;

    protected $type;
    
    // максимальны размер загружаемого изображения в мегабайтах
    private $imageMaxSize = 2;

    const TYPE_FOREWORD = 'test';

    const TYPE_QUESTION = 'question';

    public function tableName()
    {
        return 'test_images';
    }

    protected function instantiate($attributes)
    {
        switch ($attributes['type']) {
            case 'question':
                $class = 'QuestionImage';
                break;
            case 'test':
                $class = 'TestForewordImage';
                break;
            default:
                throw new CException('Nonexistent image type ' . $attributes['type']);
        }
        $model = new $class(null);
        return $model;
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'link',
                'required',
                'on' => 'saveRecord'
            ),
            array(
                'question_id, test_id',
                'numerical',
                'integerOnly' => true,
                'on' => 'saveRecord'
            ),
            array(
                'imageFile',
                'file',
                'safe' => true,
                'types' => 'jpg, gif, png',
                'allowEmpty' => true,
                'maxSize' => $this->imageMaxSize * 1024 * 1024,
                'tooLarge' => "Размер картинки не должен превышать ". $this->imageMaxSize ."МБ.",
                'wrongType' => 'Допустимые расширения изображения: jpg, gif, png.'
            ),
            array(
                'link',
                'length',
                'max' => 200,
                'on' => 'saveRecord'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, link, type, question_id, test_id',
                'safe',
                'on' => 'search'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'link' => 'Link',
            'type' => 'Type',
            'question_id' => 'Question',
            'test_id' => 'Test'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     *         based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria();
        
        $criteria->compare('id', $this->id);
        $criteria->compare('link', $this->link, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('test_id', $this->test_id);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    /**
     * Метод обрабатывает переданную в него строку на наличие изображений.
     * $textAttribute - строка, содержимое которой должно подвергнуться обработке;
     * $modelImagesRelation - связь модели с таблицей test_images;
     */
    public function saveTestImages($model, $attribute, $modelImagesRelation)
    {
        /**
         * $tmpImageRegexr - регулярное выражение для поиска в тексте ссылок на временный файл изображения.
         * $existImageRegexr - для поиска ссылок на постоянные изображения (необходимо для редактирования
         * уже существующего текста).
         */
        $regexr = "{src=[\"\'](%s(%s[\/\.\w]*))[\"\']}ui";
        $tmpImageRegexr = sprintf($regexr, addcslashes(Yii::app()->baseUrl, '/'), addcslashes(Yii::app()->params['tmpDir'], '/'));
        $existImageRegexr = sprintf($regexr, addcslashes(Yii::app()->baseUrl, '/'), addcslashes(Yii::app()->params['testImages'], '/'));
        $resultImageDir = $this->imageDir . "/{$model->id}";
        
        if (preg_match_all($tmpImageRegexr, $model->{$attribute}, $tmpImages) === false || preg_match_all($existImageRegexr, $model->{$attribute}, $existImages) === false) {
            throw new RegExrException("An error ooccurred while performing a regular expression match for string: {$model->{$attribute}}");
        }
        
        if (!$tmpImages[0] && !$existImages[0] && mb_strpos($model->{$attribute}, '<img') !== false) {
            throw new RegExrException("Regular expressions don't match with any image 'src' attribute in string: {$model->{$attribute}}");
        }
        
        /**
         * Если изображение находится в базе, но ссылки на него нет в отредактированном тексте - удаляем.
         */
        if ($modelImagesRelation) {
            foreach ($modelImagesRelation as $existImage) {
                if (mb_strpos($model->{$attribute}, $existImage->link) === false) {
                    $file = Yii::app()->file->set(Yii::getPathOfAlias('webroot') . $existImage->link, true);
                    $file->delete();
                    $existImage->delete();
                }
            }
            
            $imageDir = Yii::app()->file->set($resultImageDir, true);
            
            if ($imageDir->isEmpty) {
                $imageDir->delete();
            }
        }
        
        /**
         * $links[1]: содержание свойства src изображения.
         * $links[2]: относительные пути этих же ссылок.
         * Ниже мы сделаем из временных файлов постоянные и в соответствии с этим
         * изменим исходную строку.
         */
        if ($tmpImages[0]) {
            $testImageArray = array();
            $newImageDir = Yii::app()->file->set($resultImageDir, true);
            
            if (!$newImageDir->exists) {
                $newImageDir->createDir(0777, $newImageDir->realpath);
            }
            
            foreach ($tmpImages[2] as $key => $tmpImage) {
                $imageFile = Yii::app()->file->set(Yii::getPathOfAlias('webroot') . $tmpImage, true);
                
                $resultImage = $imageFile->move($resultImageDir . '/' . $imageFile->basename);
                
                $realImagePath = $resultImage->getRealPath();
                $rootPathLen = mb_strlen(Yii::getPathOfAlias('webroot'));
                $realImagePathLen = mb_strlen($resultImage->getRealPath());
                
                $relativeImagePath = str_replace('\\', '/', mb_substr($realImagePath, $rootPathLen, $realImagePathLen));
                
                $imgRegexr = addcslashes($tmpImages[1][$key], '/');
                $model->{$attribute} = preg_replace("/{$imgRegexr}/", Yii::app()->baseUrl . $relativeImagePath, $model->{$attribute});
                
                $testImageArray[] = array(
                    'attributeId' => $model->id,
                    'link' => $relativeImagePath
                );
            }
            
            $connection = Yii::app()->db;
            
            $insertValues = '';
            
            foreach ($testImageArray as $key => $testImage) {
                if ($insertValues !== '') {
                    $insertValues .= ',';
                }
                
                $insertValues .= "(:link{$key}, :type{$key}, :attId{$key})";
            }
            
            $sql = "INSERT INTO test_images(link, type, {$this->idAttribute}) VALUES" . $insertValues;
            
            $command = $connection->createCommand($sql);
            
            if ($this->_type === 'test') {
                $type = 'test';
            } elseif ($this->_type === 'question') {
                $type = 'question';
            }
            
            foreach ($testImageArray as $key => $testImage) {
                $command->bindParam(":link{$key}", $testImage['link'], PDO::PARAM_STR);
                $command->bindParam(":type{$key}", $type, PDO::PARAM_STR);
                $command->bindParam(":attId{$key}", $testImage['attributeId'], PDO::PARAM_STR);
            }
            
            $command->execute();
            
            $model->updateByPk($model->id, array(
                $attribute => $model->{$attribute}
            ));
        }
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
