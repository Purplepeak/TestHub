-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 18 2015 г., 23:08
-- Версия сервера: 5.6.15-log
-- Версия PHP: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test_me`
--

-- --------------------------------------------------------

--
-- Структура таблицы `account_interaction`
--

CREATE TABLE IF NOT EXISTS `account_interaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `key` varchar(128) NOT NULL,
  `email` varchar(250) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scenario` enum('confirm','restore') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_account_interaction` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `answer_options`
--

CREATE TABLE IF NOT EXISTS `answer_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `option_number` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_options_question_idx` (`question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=852 ;

-- --------------------------------------------------------

--
-- Структура таблицы `correct_answers`
--

CREATE TABLE IF NOT EXISTS `correct_answers` (
  `question_id` int(11) NOT NULL,
  `c_answer` int(11) NOT NULL,
  PRIMARY KEY (`question_id`,`c_answer`),
  KEY `FK_correct_question_idx` (`question_id`),
  KEY `FK_correct_answer_idx` (`c_answer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=264 ;

-- --------------------------------------------------------

--
-- Структура таблицы `group_test`
--

CREATE TABLE IF NOT EXISTS `group_test` (
  `test_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`test_id`,`group_id`),
  KEY `FK_test_group_idx` (`test_id`),
  KEY `FK_group_test_idx` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `type` enum('select_one','select_many','numeric','string') NOT NULL,
  `difficulty` int(11) NOT NULL,
  `answer_id` int(11) DEFAULT NULL,
  `answer_text` varchar(50) DEFAULT NULL,
  `answer_number` decimal(15,4) DEFAULT NULL,
  `precision_percent` decimal(6,5) DEFAULT NULL,
  `picture` varchar(200) DEFAULT NULL,
  `test_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_question_test_idx` (`test_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

-- --------------------------------------------------------

--
-- Структура таблицы `result_lookup`
--

CREATE TABLE IF NOT EXISTS `result_lookup` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `type` varchar(45) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `social_accounts`
--

CREATE TABLE IF NOT EXISTS `social_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` enum('facebook','vk','mail') NOT NULL,
  `social_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `info` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_social_user_idx` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `student_answer`
--

CREATE TABLE IF NOT EXISTS `student_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `answer_id` int(11) DEFAULT NULL,
  `answer_text` varchar(200) DEFAULT NULL,
  `answer_number` decimal(15,4) DEFAULT NULL,
  `exec_time` int(11) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `test_result` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_answer_question_idx` (`question_id`),
  KEY `FK_answer_test_result_idx` (`test_result`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Структура таблицы `student_test`
--

CREATE TABLE IF NOT EXISTS `student_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attempts` int(11) DEFAULT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `test_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_test_idx` (`test_id`),
  KEY `FK_student_idx` (`student_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11543 ;

-- --------------------------------------------------------

--
-- Структура таблицы `s_many_answers`
--

CREATE TABLE IF NOT EXISTS `s_many_answers` (
  `answer_id` int(11) NOT NULL,
  `s_answer` int(11) NOT NULL,
  PRIMARY KEY (`answer_id`,`s_answer`),
  KEY `FK_student_question_idx` (`answer_id`),
  KEY `FK_student_answer_idx` (`s_answer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `teacher_group`
--

CREATE TABLE IF NOT EXISTS `teacher_group` (
  `teacher_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`teacher_id`,`group_id`),
  KEY `FK_group_teacher_idx` (`group_id`),
  KEY `FK_teacher_group_idx` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `foreword` text NOT NULL,
  `minimum_score` int(11) NOT NULL,
  `time_limit` int(11) NOT NULL,
  `attempts` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deadline` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_teacher_test_idx` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Структура таблицы `test_images`
--

CREATE TABLE IF NOT EXISTS `test_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `type` enum('question','test') NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_question_image_idx` (`question_id`),
  KEY `FK_test_image_idx` (`test_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=187 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `time_registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `gender` enum('male','female','undefined') NOT NULL,
  `avatar` varchar(200) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `type` enum('student','teacher','admin') NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `avatarX` int(11) DEFAULT NULL,
  `avatarY` int(11) DEFAULT NULL,
  `avatarWidth` int(11) DEFAULT NULL,
  `avatarHeight` int(11) DEFAULT NULL,
  `cropped_avatar` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_student_group_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2024 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `account_interaction`
--
ALTER TABLE `account_interaction`
  ADD CONSTRAINT `FK_account_interaction` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `answer_options`
--
ALTER TABLE `answer_options`
  ADD CONSTRAINT `FK_options_question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `correct_answers`
--
ALTER TABLE `correct_answers`
  ADD CONSTRAINT `FK_correct_answer` FOREIGN KEY (`c_answer`) REFERENCES `answer_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_correct_question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `group_test`
--
ALTER TABLE `group_test`
  ADD CONSTRAINT `FK_group_test` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_test_group` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_question_test` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD CONSTRAINT `FK_social_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `student_answer`
--
ALTER TABLE `student_answer`
  ADD CONSTRAINT `FK_answer_question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_answer_test_result` FOREIGN KEY (`test_result`) REFERENCES `student_test` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `student_test`
--
ALTER TABLE `student_test`
  ADD CONSTRAINT `FK_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_test` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `s_many_answers`
--
ALTER TABLE `s_many_answers`
  ADD CONSTRAINT `FK_student_answer` FOREIGN KEY (`s_answer`) REFERENCES `answer_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_student_question` FOREIGN KEY (`answer_id`) REFERENCES `student_answer` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `teacher_group`
--
ALTER TABLE `teacher_group`
  ADD CONSTRAINT `FK_group_teacher` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_teacher_group` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `FK_teacher_test` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `test_images`
--
ALTER TABLE `test_images`
  ADD CONSTRAINT `FK_question_image` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_test_image` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_student_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
