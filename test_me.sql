-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Сен 23 2014 г., 15:02
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `student_test`
--

CREATE TABLE IF NOT EXISTS `student_test` (
  `student_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `result` int(11) NOT NULL,
  PRIMARY KEY (`student_id`,`test_id`),
  KEY `FK_test_idx` (`test_id`),
  KEY `FK_student_idx` (`student_id`)
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
  `rules` text NOT NULL,
  `minimum_score` int(11) NOT NULL,
  `time_limit` int(11) NOT NULL,
  `attempts` int(11) NOT NULL,
  `difficulty` varchar(20) NOT NULL,
  `create_time` int(11) NOT NULL,
  `deadline` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_teacher_test_idx` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2010 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `account_interaction`
--
ALTER TABLE `account_interaction`
  ADD CONSTRAINT `FK_account_interaction` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD CONSTRAINT `FK_social_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `student_test`
--
ALTER TABLE `student_test`
  ADD CONSTRAINT `FK_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_test` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `FK_teacher_test` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_student_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
