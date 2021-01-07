SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `laravel_vue_admin`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin_log`
--

CREATE TABLE `admin_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL COMMENT '管理员ID',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `ip` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '操作内容',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `avatar` char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `name`, `avatar`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '超级管理员', '', '$2y$10$4KWpL6/..AMiyoPwVfrMGudPH1Ud4BENlG/koHNypMEqqUQZyKkse', '2019-09-09 10:09:51', '2020-03-23 11:05:44');

-- --------------------------------------------------------

--
-- 表的结构 `albums`
--

CREATE TABLE `albums` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '相册名称',
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '封面图',
  `weigh` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '权重',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `album_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '相册ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '附件名称',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '上传人ID',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片存储路径',
  `mime_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'mime-type',
  `size` int(11) NOT NULL DEFAULT '0' COMMENT '图片字节大小',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `configs`
--

CREATE TABLE `configs` (
  `id` int(10) UNSIGNED NOT NULL,
  `group` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '组名称',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名称',
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置字段类型:string,text,editor,switch,',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '配置值内容',
  `rule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '扩展数据，一般类型为switch、radio的时候选择值',
  `tips` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '提示说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `configs`
--

INSERT INTO `configs` (`id`, `group`, `title`, `name`, `type`, `value`, `rule`, `extend`, `tips`, `created_at`, `updated_at`) VALUES
(1, 'website', '站点名称', 'name', 'string', 'LaravelVueAdmin', 'required', '', '', '2020-03-16 05:36:41', '2020-06-16 00:45:17'),
(2, 'website', 'logo', 'logo', 'image', '', 'required', '', '', '2020-03-16 06:56:22', '2020-06-16 00:44:10');

-- --------------------------------------------------------

--
-- 表的结构 `dictionary`
--

CREATE TABLE `dictionary` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字典名称',
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字典标识名',
  `describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字典描述',
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '字段内容，键值对',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `dictionary`
--

INSERT INTO `dictionary` (`id`, `title`, `name`, `describe`, `value`, `created_at`, `updated_at`) VALUES
(1, '系统配置组', 'config_group', '', '{\"website\":\"\\u7ad9\\u70b9\\u8bbe\\u7f6e\"}', '2020-03-16 03:29:07', '2020-03-16 03:29:07');

-- --------------------------------------------------------

--
-- 表的结构 `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'Jmhc\\Admin\\Models\\Auth\\AdminUser', 1);

-- --------------------------------------------------------

--
-- 表的结构 `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限标题',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'icon',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则',
  `component_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '组件路径',
  `view_route_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '视图路由名称',
  `view_route_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '视图路由路径',
  `redirect_path` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '默认跳转路径',
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_menu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是菜单',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏:0=否,1=是',
  `weigh` int(10) UNSIGNED NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `permissions`
--

INSERT INTO `permissions` (`id`, `title`, `icon`, `pid`, `name`, `component_path`, `view_route_name`, `view_route_path`, `redirect_path`, `guard_name`, `is_menu`, `is_hidden`, `weigh`, `created_at`, `updated_at`) VALUES
(1, '控制面板', 'dashboard', 0, 'dashboard', '', '', '/', '/dashboard', 'admin', 1, 0, 1000, '2019-09-29 00:57:13', '2020-03-18 09:44:02'),
(16, '系统管理', 'system', 0, 'system', '', '', '/system', '', 'admin', 1, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(18, '字典设置', 'dictionary', 16, 'dictionary', '/system/dictionary', 'Dictionary', 'dictionary', '', 'admin', 1, 0, 18, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(20, '权限管理', 'peoples', 0, 'auth', '', '', '/auth', '', 'admin', 1, 0, 80, '2020-03-19 10:58:25', '2020-03-19 10:58:25'),
(23, '角色管理', 'user', 20, 'roles', '/auth/roles', 'Roles', 'roles', '', 'admin', 1, 0, 23, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(24, '菜单管理', 'tree', 20, 'permissions', '/auth/permissions', 'Permissions', 'permissions', '', 'admin', 1, 0, 24, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(29, '系统配置', 'system', 16, 'configs', '/system/configs', 'Configs', 'configs', '', 'admin', 1, 0, 29, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(30, '相册管理', 'album', 16, 'albums', '/system/albums', 'Albums', 'albums', '', 'admin', 1, 0, 30, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(31, '查看', 'list', 30, 'albums.show', '', '', '', '', 'admin', 0, 0, 31, '2020-03-19 11:02:46', '2020-03-24 02:49:42'),
(32, '附件管理', 'file-white', 16, 'attachments', '/system/attachments', 'Attachments', 'attachments', '', 'admin', 1, 0, 32, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(34, '控制面板', 'dashboard', 1, 'dashboard.index', '/dashboard/index', 'Dashboard', 'dashboard', '', 'admin', 1, 0, 34, '2019-09-29 00:57:13', '2020-03-18 09:48:20'),
(36, '操作日志', 'list', 16, 'admin-log', '/system/admin-log', 'AdminLog', 'admin-log', '', 'admin', 1, 1, 100, '2020-03-20 09:20:57', '2020-06-16 01:06:13'),
(37, '列表', 'list', 36, 'admin-log.index', '', '', '', '', 'admin', 0, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(38, '查看', 'list', 36, 'admin-log.show', '', '', '', '', 'admin', 0, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(39, '更新', 'list', 36, 'admin-log.update', '', '', '', '', 'admin', 0, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(40, '添加', 'list', 36, 'admin-log.store', '', '', '', '', 'admin', 0, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(41, '删除', 'list', 36, 'admin-log.destroy', '', '', '', '', 'admin', 0, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(42, '批量更新', 'list', 36, 'admin-log.multi', '', '', '', '', 'admin', 0, 0, 100, '2020-03-20 09:20:57', '2020-03-20 09:20:57'),
(43, '批量删除', 'list', 36, 'admin-log.multiDestroy', '', '', '', '', 'admin', 0, 0, 43, '2020-03-19 09:18:23', '2020-03-19 09:18:23'),
(45, '列表', 'list', 18, 'dictionary.index', '', '', '', '', 'admin', 0, 0, 45, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(46, '查看', 'list', 18, 'dictionary.show', '', '', '', '', 'admin', 0, 0, 46, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(47, '更新', 'list', 18, 'dictionary.update', '', '', '', '', 'admin', 0, 0, 47, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(48, '添加', 'list', 18, 'dictionary.store', '', '', '', '', 'admin', 0, 0, 48, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(49, '删除', 'list', 18, 'dictionary.destroy', '', '', '', '', 'admin', 0, 0, 49, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(50, '批量更新', 'list', 18, 'dictionary.multi', '', '', '', '', 'admin', 0, 0, 50, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(51, '批量删除', 'list', 18, 'dictionary.multiDestroy', '', '', '', '', 'admin', 0, 0, 51, '2020-03-19 09:43:56', '2020-03-19 09:43:56'),
(52, '用户管理', 'user1', 20, 'admin-users', '/auth/admin-users', 'AdminUsers', 'admin-users', '', 'admin', 1, 0, 52, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(55, '更新', 'list', 52, 'admin-users.update', '', '', '', '', 'admin', 0, 0, 55, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(56, '添加', 'list', 52, 'admin-users.store', '', '', '', '', 'admin', 0, 0, 56, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(57, '删除', 'list', 52, 'admin-users.destroy', '', '', '', '', 'admin', 0, 0, 57, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(58, '批量更新', 'list', 52, 'admin-users.multi', '', '', '', '', 'admin', 0, 0, 58, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(59, '批量删除', 'list', 52, 'admin-users.multiDestroy', '', '', '', '', 'admin', 0, 0, 59, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(60, '列表', 'list', 23, 'roles.index', '', '', '', '', 'admin', 0, 0, 60, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(61, '查看', 'list', 23, 'roles.show', '', '', '', '', 'admin', 0, 0, 61, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(62, '更新', 'list', 23, 'roles.update', '', '', '', '', 'admin', 0, 0, 62, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(63, '添加', 'list', 23, 'roles.store', '', '', '', '', 'admin', 0, 0, 63, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(64, '删除', 'list', 23, 'roles.destroy', '', '', '', '', 'admin', 0, 0, 64, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(65, '批量更新', 'list', 23, 'roles.multi', '', '', '', '', 'admin', 0, 0, 65, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(66, '批量删除', 'list', 23, 'roles.multiDestroy', '', '', '', '', 'admin', 0, 0, 66, '2020-03-19 10:49:18', '2020-03-19 10:49:18'),
(67, '列表', 'list', 24, 'permissions.index', '', '', '', '', 'admin', 0, 0, 67, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(68, '查看', 'list', 24, 'permissions.show', '', '', '', '', 'admin', 0, 0, 68, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(69, '更新', 'list', 24, 'permissions.update', '', '', '', '', 'admin', 0, 0, 69, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(70, '添加', 'list', 24, 'permissions.store', '', '', '', '', 'admin', 0, 0, 70, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(71, '删除', 'list', 24, 'permissions.destroy', '', '', '', '', 'admin', 0, 0, 71, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(72, '批量更新', 'list', 24, 'permissions.multi', '', '', '', '', 'admin', 0, 0, 72, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(73, '批量删除', 'list', 24, 'permissions.multiDestroy', '', '', '', '', 'admin', 0, 0, 73, '2020-03-19 10:53:43', '2020-03-19 10:53:43'),
(74, '列表', 'list', 29, 'configs.index', '', '', '', '', 'admin', 0, 0, 74, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(75, '查看', 'list', 29, 'configs.show', '', '', '', '', 'admin', 0, 0, 75, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(76, '更新', 'list', 29, 'configs.update', '', '', '', '', 'admin', 0, 0, 76, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(77, '添加', 'list', 29, 'configs.store', '', '', '', '', 'admin', 0, 0, 77, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(78, '删除', 'list', 29, 'configs.destroy', '', '', '', '', 'admin', 0, 0, 78, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(79, '批量更新', 'list', 29, 'configs.multi', '', '', '', '', 'admin', 0, 0, 79, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(80, '批量删除', 'list', 29, 'configs.multiDestroy', '', '', '', '', 'admin', 0, 0, 80, '2020-03-19 11:02:29', '2020-03-19 11:02:29'),
(81, '列表', 'list', 30, 'albums.index', '', '', '', '', 'admin', 0, 0, 81, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(82, '更新', 'list', 30, 'albums.update', '', '', '', '', 'admin', 0, 0, 82, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(83, '添加', 'list', 30, 'albums.store', '', '', '', '', 'admin', 0, 0, 83, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(84, '删除', 'list', 30, 'albums.destroy', '', '', '', '', 'admin', 0, 0, 84, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(85, '批量更新', 'list', 30, 'albums.multi', '', '', '', '', 'admin', 0, 0, 85, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(86, '批量删除', 'list', 30, 'albums.multiDestroy', '', '', '', '', 'admin', 0, 0, 86, '2020-03-19 11:02:46', '2020-03-19 11:02:46'),
(87, '列表', 'list', 32, 'attachments.index', '', '', '', '', 'admin', 0, 0, 87, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(88, '查看', 'list', 32, 'attachments.show', '', '', '', '', 'admin', 0, 0, 88, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(89, '更新', 'list', 32, 'attachments.update', '', '', '', '', 'admin', 0, 0, 89, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(90, '添加', 'list', 32, 'attachments.store', '', '', '', '', 'admin', 0, 0, 90, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(91, '删除', 'list', 32, 'attachments.destroy', '', '', '', '', 'admin', 0, 0, 91, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(92, '批量更新', 'list', 32, 'attachments.multi', '', '', '', '', 'admin', 0, 0, 92, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(93, '批量删除', 'list', 32, 'attachments.multiDestroy', '', '', '', '', 'admin', 0, 0, 93, '2020-03-19 11:08:37', '2020-03-19 11:08:37'),
(94, '列表', 'list', 52, 'admin-users.index', '', '', '', '', 'admin', 0, 0, 55, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(95, '查看', '', 52, 'admin-users.show', '', '', '', '', 'admin', 0, 0, 55, '2020-03-19 09:51:34', '2020-03-19 09:51:34'),
(96, '分配权限', '', 23, 'roles.assign-permission', '', '', '', '', 'admin', 0, 0, 100, '2020-03-23 01:05:59', '2020-03-23 01:16:39'),
(97, '分配角色', '', 52, 'admin-users.assign-role', '', '', '', '', 'admin', 0, 0, 100, '2020-03-23 01:08:27', '2020-03-23 01:16:56'),
(98, '获取配置', '', 29, 'config-group', '', '', '', '', 'admin', 0, 0, 100, '2020-03-23 01:09:30', '2020-03-23 01:09:30'),
(99, '更新个人信息', '', 52, 'admin-users.update-self', '', '', '', '', 'admin', 0, 0, 100, '2020-03-23 11:10:06', '2020-03-23 11:10:06'),
(100, '相册图片列表', '', 16, 'album-detail', '/system/album-detail', 'AlbumDetail', 'album-detail', '', 'admin', 1, 1, 100, '2020-03-24 02:51:00', '2020-03-24 02:58:59');

-- --------------------------------------------------------

--
-- 表的结构 `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `roles`
--

INSERT INTO `roles` (`id`, `parent_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 0, 'admin', 'admin', '2021-01-07 02:43:09', '2021-01-07 02:43:09');

-- --------------------------------------------------------

--
-- 表的结构 `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `admin_log`
--
ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jm_admin_users_username_unique` (`username`);

--
-- 表的索引 `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `group` (`group`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`guard_name`,`name`) USING BTREE;

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
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin_log`
--
ALTER TABLE `admin_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `configs`
--
ALTER TABLE `configs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `dictionary`
--
ALTER TABLE `dictionary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- 使用表AUTO_INCREMENT `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 限制导出的表
--

--
-- 限制表 `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- 限制表 `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- 限制表 `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
