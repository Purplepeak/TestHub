<?php
return array (
  'startTestUser' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'viewTest' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'createTest' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'updateTest' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'deleteTest' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'viewTeacherTests' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'adminTest' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'beginTest' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'viewQuestion' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'createAnswerOption' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'validateQuestionForm' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'createQuestion' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'updateQuestion' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'deleteQuestion' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'adminQuestion' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'studentTests' => 
  array (
    'type' => 0,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'updateOwnTest' => 
  array (
    'type' => 1,
    'description' => '',
    'bizRule' => 'return Yii::app()->user->id==$params["test"]->teacher_id;',
    'data' => NULL,
    'children' => 
    array (
      0 => 'updateTest',
    ),
  ),
  'deleteOwnTest' => 
  array (
    'type' => 1,
    'description' => '',
    'bizRule' => 'return Yii::app()->user->id==$params["test"]->teacher_id;',
    'data' => NULL,
    'children' => 
    array (
      0 => 'deleteTest',
    ),
  ),
  'beginTestForMe' => 
  array (
    'type' => 1,
    'description' => '',
    'bizRule' => 'return $params["beginAccess"]==true;',
    'data' => NULL,
    'children' => 
    array (
      0 => 'beginTest',
    ),
  ),
  'updateOwnQuestion' => 
  array (
    'type' => 1,
    'description' => '',
    'bizRule' => 'return Yii::app()->user->id==$params["question"]->test->teacher_id;',
    'data' => NULL,
    'children' => 
    array (
      0 => 'updateQuestion',
    ),
  ),
  'deleteOwnQuestion' => 
  array (
    'type' => 1,
    'description' => '',
    'bizRule' => 'return Yii::app()->user->id==$params["question"]->test->teacher_id;',
    'data' => NULL,
    'children' => 
    array (
      0 => 'deleteQuestion',
    ),
  ),
  'guest' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'viewTest',
    ),
  ),
  'student' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'guest',
      1 => 'startTestUser',
      2 => 'studentTests',
      3 => 'beginTestForMe',
    ),
  ),
  'teacher' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'guest',
      1 => 'createTest',
      2 => 'updateOwnTest',
      3 => 'deleteOwnTest',
      4 => 'viewTeacherTests',
      5 => 'viewQuestion',
      6 => 'createAnswerOption',
      7 => 'validateQuestionForm',
      8 => 'createQuestion',
      9 => 'updateOwnQuestion',
      10 => 'deleteOwnQuestion',
    ),
  ),
  'admin' => 
  array (
    'type' => 2,
    'description' => '',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'viewTest',
      1 => 'createTest',
      2 => 'updateTest',
      3 => 'deleteTest',
      4 => 'viewTeacherTests',
      5 => 'adminTest',
      6 => 'viewQuestion',
      7 => 'createAnswerOption',
      8 => 'validateQuestionForm',
      9 => 'createQuestion',
      10 => 'updateQuestion',
      11 => 'deleteQuestion',
      12 => 'adminQuestion',
    ),
  ),
);
