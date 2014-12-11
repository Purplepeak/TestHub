<?php

class SHelper extends CApplicationComponent
{

    public static function deleteFolder($dir, $completeDelete = false)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($subDir = $dir . '/' . $object)) {
                        self::deleteFolder($subDir);
                        rmdir($subDir);
                    } else {
                        unlink($subDir);
                    }
                }
            }
            
            if ($completeDelete) {
                rmdir($dir);
            }
        }
    }
    
    public static function purifyHtml($html)
    {
        $purifier = new CHtmlPurifier();
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.DefinitionID', 'enduser-customize.html tutorial');
        $config->set('HTML.DefinitionRev', 1);
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('HTML.Doctype','HTML 4.01 Strict');
        //$config->set('HTML.AllowedElements','del');
        $config->set('Cache.SerializerPath',Yii::app()->getRuntimePath());
        $config->set('Cache.DefinitionImpl', null); // TODO: remove this later!
    
    
        $purifier->options = $config;
        $cleanHtml = $purifier->purify($html);
    
        return $cleanHtml;
    }

    public static function getSafeImageName($name, $prefix, $id)
    {
        $nameReg = '{(.*)(\\..+)}ui';
        if (preg_match($nameReg, $name, $nameArray)) {
            $fileName = $nameArray[1];
            $fileExt = $nameArray[2];
        }
        
        $safeName = uniqid($prefix . '_' . $id . '_') . $fileExt;
        
        return $safeName;
    }
    
    public function beachers($groupsPerTeacher, $groupStart, $groupEnd, $singleGroup = null)
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
    
    public function gggg($studentsPerGroup)
    {
        $groups = Group::model()->findAll();
    
        $io = array();
        
        foreach ($groups as $group) {
            $numberOfStudents = $studentsPerGroup - rand(0, 4);
            $studentDataArray = $this->getRandomUserData($numberOfStudents, 25);
            
            $uu = array();
    
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
    
                array_push($uu, $attributes);
            }
            
            array_push($io, $uu);
        }
        
        return $io;
    }
    
    public function ttt($studentsPerGroup)
    {
        $studentsPerGroup = $studentsPerGroup - rand(0, 4);
        
        $amountOfFemaleStudents = round(($studentsPerGroup / 100) * 25);
        $amountOfMaleStudents = $studentsPerGroup - $amountOfFemaleStudents;
        
        
        return array($amountOfFemaleStudents, $amountOfMaleStudents); 
    }

    public function getRandomUserData($amount, $femalePercent)
    {
        $amountOfFemaleUsers = round(($amount / 100) * $femalePercent);
        $amountOfMaleUsers = $amount - $amountOfFemaleUsers;
    
        if($amountOfFemaleUsers == 0) {
            $usersArray = array('male' => $amountOfMaleUsers);
        } else {
            $usersArray = array('male' => $amountOfMaleUsers, 'female' => $amountOfFemaleUsers);
        }
    
        $totalUsersArray = array();
    
        foreach($usersArray as $gender => $amountOfUsers) {
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
        
        if(count($totalUsersArray) == 1) {
            $result = $totalUsersArray[0];
        } else {
            $result = array_merge($totalUsersArray[0], $totalUsersArray[1]);
        }
        
        shuffle($result);
        
        return $result;
    }

    private function getExcerptArray($array, $amount)
    {
        $excerptKeys = array_rand($array, $amount);
        
        $excerptArray = array();
        
        if($amount == '1') {
            array_push($excerptArray, $array[$excerptKeys]);
        } else {
            foreach ($excerptKeys as $value) {
                array_push($excerptArray, $array[$value]);
            }
        }
        
        return $excerptArray;
    }

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

    private function getNamesArray($namesString)
    {
        $nameReg = '/[а-я]+/iu';
        
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