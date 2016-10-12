-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 01 月 09 日 01:25
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `rise_tel`
--

-- --------------------------------------------------------

--
-- 表的结构 `data_base`
--

CREATE TABLE IF NOT EXISTS `data_base` (
  `no` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `tel` int(11) NOT NULL COMMENT '电话号码',
  `s_name` varchar(20) NOT NULL COMMENT '学生姓名',
  `sex` int(1) NOT NULL DEFAULT '1' COMMENT '学生性别1男0女',
  `age` varchar(10) NOT NULL COMMENT '年龄',
  `important` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否重点1重点0非重点',
  `dt_name` varchar(20) NOT NULL COMMENT '地推人',
  `dt_date` varchar(20) NOT NULL COMMENT '地推时间',
  `fp_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分配状态0未分配1已分配',
  `fp_name` varchar(20) NOT NULL COMMENT '分配至',
  `qudao` varchar(40) NOT NULL COMMENT '渠道',
  `bz1` varchar(20) NOT NULL COMMENT '备注',
  `bz2` varchar(20) NOT NULL COMMENT '备注',
  `bz3` varchar(20) NOT NULL COMMENT '备注',
  `bz4` varchar(20) NOT NULL COMMENT '备注',
  PRIMARY KEY (`no`),
  UNIQUE KEY `tel` (`tel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学生基础信息表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
