-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--

-- --------------------------------------------------------

--
-- 表的结构 `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `name` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `avatar` char(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `name`, `avatar`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '超级管理员', '/assets/img/avatar.jpg', '$2y$10$V0NnfaQeMBiJceAGkmPFVOWOk.qJputHCNkJ6kJioiVdyjx.oGLYy', '2019-09-09 10:09:51', '2019-09-12 05:56:26');

-- --------------------------------------------------------

--
-- 表的结构 `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `album_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相册ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '附件名称',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '上传人ID',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片存储路径',
  `mime_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'mime-type',
  `size` int(11) NOT NULL DEFAULT 0 COMMENT '图片字节大小',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `configs`
--

CREATE TABLE `configs` (
  `id` int(10) UNSIGNED NOT NULL,
  `group` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '组名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名称',
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置字段类型:string,text,editor,switch,',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置值内容',
  `rule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '扩展数据，一般类型为switch、radio的时候选择值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `dictionary`
--

CREATE TABLE `dictionary` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字典名称',
  `name` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字典标识名',
  `describe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字典描述',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '字段内容，键值对',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\Auth\\AdminUser', 1);

-- --------------------------------------------------------

--
-- 表的结构 `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限标题',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'icon',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级id',
  `module_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则',
  `component_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '组件路径',
  `view_route_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '视图路由名称',
  `view_route_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '视图路由路径',
  `redirect_path` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '默认跳转路径',
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_menu` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是菜单',
  `weigh` int(10) UNSIGNED NOT NULL DEFAULT 100,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `permissions`
--

INSERT INTO `permissions` (`id`, `title`, `icon`, `pid`, `module_id`, `name`, `component_path`, `view_route_name`, `view_route_path`, `redirect_path`, `guard_name`, `is_menu`, `weigh`, `created_at`, `updated_at`) VALUES
(1, '控制台', 'el-icon-s-home', 0, 1, 'home', '', '', '/index', '/index/index', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(16, '系统设置', 'el-icon-s-tools', 0, 3, 'system-settings', '', '', '/system', '/system/mall', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(18, '数据字典', 'el-icon-notebook-1', 16, 3, 'dictionary', '/system/set/dictionary', 'dictionary', 'dictionary', '', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(20, '权限管理', 'el-icon-key', 0, 4, 'auth', '', '', '/auth', '/auth/module', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(21, '模块管理', 'el-icon-s-grid', 20, 4, 'modules', '/auth/module', 'module', 'module', '', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(22, '用户管理', 'el-icon-s-custom', 20, 4, 'admin_users', '/auth/user', 'user', 'user', '', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(23, '角色管理', 'el-icon-user', 20, 4, 'roles', '/auth/role', 'role', 'role', '', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(24, '权限管理', 'el-icon-thumb', 20, 4, 'permissions', '/auth/auth', 'auth', 'auth', '', 'admin', 1, 100, '2019-09-29 00:57:13', '2019-09-29 00:57:13'),
(26, '会员列表', 'el-icon-s-operation', 25, 5, 'members.index', '/member/memberlist', 'memberlist', 'memberlist', '', 'admin', 1, 100, '2019-09-29 00:57:14', '2019-09-29 00:57:14');

-- --------------------------------------------------------

--
-- 表的结构 `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `roles`
--

INSERT INTO `roles` (`id`, `parent_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 0, 'admin', 'admin', '2019-09-10 00:00:00', '2019-09-10 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jm_admin_users_username_unique` (`username`);

--
-- 表的索引 `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`);

--
-- 表的索引 `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- 表的索引 `dictionary`
--
ALTER TABLE `dictionary`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- 表的索引 `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- 表的索引 `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `jm_role_has_permissions_role_id_foreign` (`role_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `configs`
--
ALTER TABLE `configs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `dictionary`
--
ALTER TABLE `dictionary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- 使用表AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 限制导出的表
--

--
-- 限制表 `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `jm_model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- 限制表 `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `jm_model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- 限制表 `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `jm_role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jm_role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
