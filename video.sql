-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2021-12-10 14:02:46
-- 服务器版本： 10.4.20-MariaDB
-- PHP 版本： 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `video`
--

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

CREATE TABLE `categories` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `title` char(10) NOT NULL COMMENT '标题'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `categories`
--

INSERT INTO `categories` (`id`, `title`) VALUES
(1, '搞笑'),
(2, '明星'),
(3, '美食'),
(4, '时尚'),
(5, '美妆'),
(6, '街坊'),
(7, '旅游'),
(8, '娱乐'),
(9, '生活'),
(10, '资讯'),
(11, '亲子'),
(12, '知识'),
(13, '游戏');

-- --------------------------------------------------------

--
-- 表的结构 `configs`
--

CREATE TABLE `configs` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `free_space` tinyint(4) NOT NULL COMMENT '免费试看间隔（单位天）',
  `stop_wx_send_file` tinyint(1) DEFAULT 0 COMMENT '是否限制微信发送文件功能',
  `free_union` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否合并免费时间',
  `free_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '每次试看数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `configs`
--

INSERT INTO `configs` (`id`, `free_space`, `stop_wx_send_file`, `free_union`, `free_count`) VALUES
(1, 3, 0, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `plays`
--

CREATE TABLE `plays` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '视频标题',
  `file_name` smallint(5) UNSIGNED NOT NULL COMMENT '文件编号',
  `status` tinyint(3) UNSIGNED DEFAULT 0 COMMENT '0:正常,1:停止',
  `created_at` datetime NOT NULL COMMENT '添加时间',
  `updated_at` datetime NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `play_category`
--

CREATE TABLE `play_category` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `play_id` smallint(5) UNSIGNED NOT NULL COMMENT '视频id',
  `category_id` tinyint(3) UNSIGNED NOT NULL COMMENT '视频类型id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `user_id` char(40) NOT NULL COMMENT 'wxid',
  `money` decimal(10,2) DEFAULT 0.00 COMMENT '余额',
  `unit_id` tinyint(3) UNSIGNED NOT NULL,
  `union_id` smallint(5) UNSIGNED DEFAULT 0 COMMENT '联合id',
  `status` tinyint(3) UNSIGNED DEFAULT 0 COMMENT '0:未激活,1:激活,2:黑名单',
  `free_at` datetime DEFAULT NULL COMMENT '试看时间',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_binds`
--

CREATE TABLE `user_binds` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `first_id` char(40) NOT NULL COMMENT '绑定者user_id',
  `first_unit` tinyint(3) UNSIGNED NOT NULL COMMENT '绑定者类型',
  `first_table_id` smallint(5) UNSIGNED NOT NULL COMMENT '表主键id',
  `secondary_id` char(40) NOT NULL COMMENT '被绑定者user_id',
  `secondary_unit` tinyint(3) UNSIGNED NOT NULL COMMENT '被绑定者类型',
  `secondary_table_id` smallint(5) UNSIGNED NOT NULL COMMENT '表主键id',
  `key_code` char(36) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0:未验证,1:已验证',
  `union_id` smallint(5) UNSIGNED NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_pay_records`
--

CREATE TABLE `user_pay_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `user_id` smallint(5) UNSIGNED NOT NULL COMMENT 'user表主键id',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_play_records`
--

CREATE TABLE `user_play_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `play_id` smallint(5) UNSIGNED NOT NULL COMMENT '文件id',
  `user_id` smallint(5) UNSIGNED NOT NULL COMMENT 'user表主键id',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_unions`
--

CREATE TABLE `user_unions` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `wx_id` smallint(5) UNSIGNED DEFAULT NULL,
  `qq_id` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_unit`
--

CREATE TABLE `user_unit` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转储表的索引
--

--
-- 表的索引 `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `plays`
--
ALTER TABLE `plays`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `play_category`
--
ALTER TABLE `play_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_binds`
--
ALTER TABLE `user_binds`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_pay_records`
--
ALTER TABLE `user_pay_records`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_play_records`
--
ALTER TABLE `user_play_records`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_unions`
--
ALTER TABLE `user_unions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_unit`
--
ALTER TABLE `user_unit`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `configs`
--
ALTER TABLE `configs`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `plays`
--
ALTER TABLE `plays`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `play_category`
--
ALTER TABLE `play_category`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_binds`
--
ALTER TABLE `user_binds`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_pay_records`
--
ALTER TABLE `user_pay_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_play_records`
--
ALTER TABLE `user_play_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_unions`
--
ALTER TABLE `user_unions`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user_unit`
--
ALTER TABLE `user_unit`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
