<?php

/**
 * Команда "fill" позволяет сгенерировать и наполнить базу данными.
 */

mb_internal_encoding('utf-8');

class FillCommand extends CConsoleCommand
{

    /**
     * Команда добавляет преподавателей к имеющимся группам.
     * Синтаксис: yiic fill teachers --groupsPerTeacher=4 --groupStart=1001 --groupEnd=1020.
     * Группам со 1001 до 1020 будут назначены преподаватели. Один преподаватель
     * берет на себя четыре группы. Число групп относительно groupsPerTeacher
     * должно быть целым.
     */
    
    public function actionTeachers($groupsPerTeacher, $groupStart, $groupEnd, $singleGroup = null)
    {
        if (! isset($singleGroup) && $groupsPerTeacher > 1) {
            $numberOfGroups = ($groupEnd - $groupStart) + 1;
            
            $numbersOfTeachers = floor($numberOfGroups / $groupsPerTeacher);
            
            $teacherDataArray = $this->getRandomUserData($numbersOfTeachers, 25);
            
            $firstGroup = $groupStart;
            
            foreach ($teacherDataArray as $teacher) {
                $lastGroup = $firstGroup + ($groupsPerTeacher - 1);
                
                $attributes = array(
                    'name' => $teacher['name'],
                    'surname' => $teacher['surname'],
                    'email' => $teacher['email'],
                    'gender' => $teacher['gender'],
                    'active' => 1,
                    'groups' => "{$firstGroup}-{$lastGroup}",
                    'type' => 'teacher',
                    'password' => '$2a$13$hcBaAd16nNnSfyQnvquezeNrMU4Hop./4sOvDyGPW9/BOx0AFZ5F.'
                );
                
                $teacherModel = new Teacher();
                
                $teacherModel->attributes = $attributes;
                
                $teacherModel->isGroupExist();
                
                $teacherModel->save(false);
                
                $firstGroup = $lastGroup + 1;
            }
        }
    }
    
    /**
     * Команда наполняет группы студентами.
     * Синтаксис: yiic fill students --studentsPerGroup=25.
     * studentsPerGroup максимально количество студентов на группу.
     */

    public function actionStudents($studentsPerGroup)
    {
        $groups = Group::model()->findAll();
        
        foreach ($groups as $group) {
            $numberOfStudents = $studentsPerGroup - rand(0, 4);
            $studentDataArray = $this->getRandomUserData($numberOfStudents, 25);
            
            foreach ($studentDataArray as $student) {
                $attributes = array(
                    'name' => $student['name'],
                    'surname' => $student['surname'],
                    'email' => $student['email'],
                    'gender' => $student['gender'],
                    'active' => 1,
                    'group' => "{$group->number}",
                    'type' => 'student',
                    'password' => '$2a$13$hcBaAd16nNnSfyQnvquezeNrMU4Hop./4sOvDyGPW9/BOx0AFZ5F.'
                );
                
                $studentModel = new Student();
                
                $studentModel->attributes = $attributes;
                
                $studentModel->save(false);
            }
        }
    }
    
    /**
     * Команда добавляет группы в базу.
     * Синтаксис: yiic fill groups --begin=1001 --end=1020.
     * Группы со 1001 до 1020 будут добавлены в базу.
     */

    public function actionGroups($begin, $end)
    {
        if ($end < $begin && $end !== '0') {
            return false;
            Yii::log('Fill command error $end > $begin', CLogger::LEVEL_ERROR, 'application.command.CConsoleCommand');
        }
        
        $groups = array();
        
        if ($end !== '0') {
            $numberOfGroups = $end - $begin;
            for ($i = 0; $i <= $numberOfGroups; $i ++) {
                array_push($groups, $begin + $i);
            }
        } else {
            array_push($groups, $begin);
        }
        
        foreach ($groups as $groupNumber) {
            $groupModel = new Group();
            $groupModel->attributes = array(
                'number' => $groupNumber
            );
            $groupModel->save(false);
        }
    }
    
    /**
     * Метод возвратит массив сгенерированных пользователей.
     * $amount - количество пользователей.
     * $femalePercent - процент пользователей женского пола среди
     * общего голичества пользователей.
     */

    private function getRandomUserData($amount, $femalePercent)
    {
        $amountOfFemaleUsers = round(($amount / 100) * $femalePercent);
        $amountOfMaleUsers = $amount - $amountOfFemaleUsers;
        
        if ($amountOfFemaleUsers == 0) {
            $usersArray = array(
                'male' => $amountOfMaleUsers
            );
        } else {
            $usersArray = array(
                'male' => $amountOfMaleUsers,
                'female' => $amountOfFemaleUsers
            );
        }
        
        $totalUsersArray = array();
        
        foreach ($usersArray as $gender => $amountOfUsers) {
            switch ($gender) {
                case 'female':
                    $nameScenario = 'femaleName';
                    $surnameScenario = 'femaleSurname';
                    break;
                case 'male':
                    $nameScenario = 'maleName';
                    $surnameScenario = 'maleSurname';
                    break;
                default:
                    Yii::log('SHelper getRandomUserData() incorrect gender: ' . $gender, CLogger::LEVEL_ERROR, 'application.components.shelper');
                    break;
            }
            
            $names = $this->getDataString($nameScenario);
            $surnames = $this->getDataString($surnameScenario);
            
            $namesArray = $this->getNamesArray($names);
            $surnamesArray = $this->getNamesArray($surnames);
            
            $excerptNamesArray = $this->getExcerptArray($namesArray, $amountOfUsers);
            $excerptSurnamesArray = $this->getExcerptArray($surnamesArray, $amountOfUsers);
            
            $nameSurnamePare = array_combine($excerptNamesArray, $excerptSurnamesArray);
            
            $userData = array();
            
            foreach ($excerptNamesArray as $key => $name) {
                
                array_push($userData, array(
                    'name' => $name,
                    'surname' => $excerptSurnamesArray[$key],
                    'email' => $this->getUserEmail(array(
                        $name,
                        $excerptSurnamesArray[$key]
                    )),
                    'gender' => $gender
                ));
            }
            
            array_push($totalUsersArray, $userData);
        }
        
        if (count($totalUsersArray) == 1) {
            $result = $totalUsersArray[0];
        } else {
            $result = array_merge($totalUsersArray[0], $totalUsersArray[1]);
        }
        
        shuffle($result);
        
        return $result;
    }
    
    /**
     * Метод делает выборку массивов из глобального массива сгенерированных пользователей
     */

    private function getExcerptArray($array, $amount)
    {
        $excerptKeys = array_rand($array, $amount);
        
        $excerptArray = array();
        
        if ($amount == '1') {
            array_push($excerptArray, $array[$excerptKeys]);
        } else {
            foreach ($excerptKeys as $value) {
                array_push($excerptArray, $array[$value]);
            }
        }
        
        return $excerptArray;
    }
    
    /**
     * Метод генерирует e-mail на основе имени и фамилии пользователя.
     * При этом задействуется useTranslit($string), т.к имена хранятся в 
     * кирилических символах.
     */

    private function getUserEmail(array $namePair)
    {
        $emailLogin = '';
        
        foreach ($namePair as $value) {
            $emailLogin .= $value;
        }
        
        $hostArray = array(
            '@th.ru',
            '@pil1ow.com',
            '@randommail.net',
            '@fakeggmail.org'
        );
        
        $emailLogin = $this->useTranslit($emailLogin);
        
        $randomHost = array_rand($hostArray);
        
        return $emailLogin . $hostArray[$randomHost];
    }
    
    /**
     * Метод возвращает из текстовых файлов строку с именами / фамилиями.
     */

    private function getDataString($scenario)
    {
        switch ($scenario) {
            case 'femaleName':
                $file = '/femaleNames.txt';
                break;
            case 'femaleSurname':
                $file = '/femaleSurnames.txt';
                break;
            case 'maleName':
                $file = '/maleNames.txt';
                break;
            case 'maleSurname':
                $file = '/maleSurnames.txt';
                break;
            default:
                Yii::log('SHelper getDataString() incorrect scenario: ' . $scenario, CLogger::LEVEL_ERROR, 'application.components.shelper');
                break;
        }
        
        return file_get_contents(dirname(__FILE__) . $file);
    }
    
    /**
     * Метод обрабатывает полученную getDataString($scenario) строку и возвращает 
     * массив имен / фамилий.
     */

    private function getNamesArray($namesString)
    {
        $nameReg = '/[а-яёА-ЯЁ]+/u';
        
        if (! preg_match_all($nameReg, $namesString, $namesArray)) {
            Yii::log('preg_match error occurred in getNamesArray() method SHelper', CLogger::LEVEL_ERROR, 'application.components.shelper');
        }
        
        return $namesArray[0];
    }

    private function useTranslit($string)
    {
        $translit = array(
            'а' => 'a',
            'А' => 'A',
            'б' => 'b',
            'Б' => 'B',
            'в' => 'v',
            'В' => 'V',
            'г' => 'g',
            'Г' => 'G',
            'д' => 'd',
            'Д' => 'D',
            'е' => 'e',
            'Е' => 'E',
            'ё' => 'yo',
            'Ё' => 'Jo',
            'ж' => 'zh',
            'Ж' => 'Zh',
            'з' => 'z',
            'З' => 'Z',
            'и' => 'i',
            'И' => 'I',
            'й' => 'j',
            'Й' => 'J',
            'к' => 'k',
            'К' => 'K',
            'л' => 'l',
            'Л' => 'L',
            'м' => 'm',
            'М' => 'M',
            'н' => 'n',
            'Н' => 'N',
            'о' => 'o',
            'О' => 'O',
            'п' => 'p',
            'П' => 'P',
            'р' => 'r',
            'Р' => 'R',
            'с' => 's',
            'С' => 'S',
            'т' => 't',
            'Т' => 'T',
            'у' => 'u',
            'У' => 'U',
            'ф' => 'f',
            'Ф' => 'F',
            'х' => 'h',
            'Х' => 'H',
            'ц' => 'c',
            'Ц' => 'C',
            'ч' => 'ch',
            'Ч' => 'Ch',
            'ш' => 'sh',
            'Ш' => 'Sh',
            'щ' => 'shh',
            'Щ' => 'Shh',
            'ъ' => '',
            'Ъ' => '',
            'ы' => 'y',
            'Ы' => 'Y',
            'ь' => '',
            'Ь' => '',
            'э' => 'je',
            'Э' => 'Je',
            'ю' => 'yu',
            'Ю' => 'Ju',
            'я' => 'ya',
            'Я' => 'Ja'
        );
        
        return strtr($string, $translit);
    }
}