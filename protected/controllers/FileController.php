<?php

class FileController extends Controller
{

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'users' => array(
                    '@'
                )
            ),
            array(
                'deny',
                'users' => array(
                    '*'
                )
            )
        );
    }

    public function actions()
    {
        return array(
            'tmpUpload' => array(
                'model' => File::model(),
                'attribute' => 'image',
                'class' => 'ext.file-upload-action.EFileUploadAction',
                // 'name' => 'File[image]',
                'createDirectory' => true,
                'createDirectoryMode' => 0777,
                'createDirectoryRecursive' => true,
                'filenameRule' => 'date("d.m.Y-H.i.s")."_".md5(date("m-d-H-i-s")).".".$file->extensionName',
                'path' => realpath(Yii::app()->basePath . '/..') . '/uploads/tmp/',
                
                'onBeforeUpload' => function ($event)
                {
                    if(CUploadedFile::getInstanceByName($event->sender->name) === null) {
                        $result = CJSON::encode(array('error' => true, 'message' => 'Select image file'));
                        
                        echo stripcslashes($result);
                        exit();
                    }
                },
                
                'onAfterUpload' => function ($event)
                {
                    if ($event->sender->hasErrors()) {
                        //$result = CJSON::encode($event->sender->getErrors());
                        $result = CJSON::encode(array('error' => true, 'message' => $event->sender->getErrors()));
                    } else {
                        $file = array(
                            'filelink' => Yii::app()->baseUrl . '/uploads/tmp/' . $event->sender->filename
                        // 'filename' => $event->sender->filename
                                                );
                        
                        $result = CJSON::encode($file);
                    }
                    echo stripcslashes($result);
                    exit();
                }
            )
        );
    }

    public function actionRandom()
    {
        $model = new File('');
        $model->setIsNewRecord(false);
        $modelN = File::model();
        var_dump($model, $modelN);
        
        $result = CJSON::encode(array(
            'filelink' => 'ASDASFAG'
        ));
        
        echo stripcslashes($result);
        exit();
    }
}