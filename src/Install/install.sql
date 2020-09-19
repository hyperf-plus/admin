/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : laravel

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 19/09/2020 17:03:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `is_menu` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
BEGIN;
INSERT INTO `admin_menu` VALUES (1, 0, 1, 1, '首页', 'el-icon-monitor', '/auth/main', NULL, NULL, '2020-09-19 16:35:36');
INSERT INTO `admin_menu` VALUES (2, 0, 1, 10, '系统', 'el-icon-setting', 'system', NULL, NULL, '2020-09-19 16:54:51');
INSERT INTO `admin_menu` VALUES (3, 2, 1, 3, '管理员', 'fa-ban', '/admin/users/list', '[1]', NULL, '2020-09-19 16:01:49');
INSERT INTO `admin_menu` VALUES (4, 2, 1, 4, '角色', 'fa-ban', '/admin/roles/list', NULL, NULL, '2020-09-19 16:01:42');
INSERT INTO `admin_menu` VALUES (5, 2, 1, 5, '权限', 'fa-ban', '/admin/permissions/list', NULL, NULL, '2020-09-19 15:59:33');
INSERT INTO `admin_menu` VALUES (6, 2, 1, 6, '菜单', 'fa-bars', '/admin/menu/list', NULL, NULL, '2020-09-19 16:55:40');
INSERT INTO `admin_menu` VALUES (7, 2, 1, 7, '操作日志', 'fa-ban', '/admin/logs/list', NULL, NULL, '2020-09-19 16:34:12');
COMMIT;

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `runtime` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `admin_operation_log_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3079 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------
BEGIN;
INSERT INTO `admin_operation_log` VALUES (3013, 1, '/admin', 'GET', '127.0.0.1', '[]', '8毫秒', '2020-09-11 12:40:34', '2020-09-11 12:40:34');
INSERT INTO `admin_operation_log` VALUES (3014, 1, '/admin/menu/list', 'GET', '127.0.0.1', '[]', '12.5毫秒', '2020-09-11 12:40:34', '2020-09-11 12:40:34');
INSERT INTO `admin_operation_log` VALUES (3015, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:40:34', '2020-09-11 12:40:34');
INSERT INTO `admin_operation_log` VALUES (3016, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '14.3毫秒', '2020-09-11 12:40:34', '2020-09-11 12:40:34');
INSERT INTO `admin_operation_log` VALUES (3017, 1, '/admin/menu/14', 'GET', '127.0.0.1', '{\"get_data\":\"true\"}', '5毫秒', '2020-09-11 12:40:36', '2020-09-11 12:40:36');
INSERT INTO `admin_operation_log` VALUES (3018, 1, '/admin/menu/route', 'GET', '127.0.0.1', '{\"page\":\"1\",\"query\":\"\\/t\",\"depend\":\"{}\"}', '0.4毫秒', '2020-09-11 12:40:44', '2020-09-11 12:40:44');
INSERT INTO `admin_operation_log` VALUES (3019, 1, '/admin/menu/route', 'GET', '127.0.0.1', '{\"page\":\"1\",\"query\":\"\\/top\",\"depend\":\"{}\"}', '0.3毫秒', '2020-09-11 12:40:45', '2020-09-11 12:40:45');
INSERT INTO `admin_operation_log` VALUES (3020, 1, '/admin/menu/14', 'PUT', '127.0.0.1', '{\"parent_id\":0,\"title\":\"数据交互\",\"icon\":\"el-icon-ice-cream-round\",\"uri\":\"\\/top\",\"order\":1,\"is_menu\":true,\"roles\":[],\"permission\":[]}', '19.6毫秒', '2020-09-11 12:40:50', '2020-09-11 12:40:50');
INSERT INTO `admin_operation_log` VALUES (3021, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '11.5毫秒', '2020-09-11 12:40:50', '2020-09-11 12:40:50');
INSERT INTO `admin_operation_log` VALUES (3022, 1, '/admin', 'GET', '127.0.0.1', '[]', '4.1毫秒', '2020-09-11 12:40:51', '2020-09-11 12:40:51');
INSERT INTO `admin_operation_log` VALUES (3023, 1, '/admin/menu/list', 'GET', '127.0.0.1', '[]', '4.7毫秒', '2020-09-11 12:40:51', '2020-09-11 12:40:51');
INSERT INTO `admin_operation_log` VALUES (3024, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:40:51', '2020-09-11 12:40:51');
INSERT INTO `admin_operation_log` VALUES (3025, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '12毫秒', '2020-09-11 12:40:51', '2020-09-11 12:40:51');
INSERT INTO `admin_operation_log` VALUES (3026, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:40:59', '2020-09-11 12:40:59');
INSERT INTO `admin_operation_log` VALUES (3027, 1, '/admin', 'GET', '127.0.0.1', '[]', '3.9毫秒', '2020-09-11 12:41:01', '2020-09-11 12:41:01');
INSERT INTO `admin_operation_log` VALUES (3028, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:41:01', '2020-09-11 12:41:01');
INSERT INTO `admin_operation_log` VALUES (3029, 1, '/admin', 'GET', '127.0.0.1', '[]', '3.1毫秒', '2020-09-11 12:41:04', '2020-09-11 12:41:04');
INSERT INTO `admin_operation_log` VALUES (3030, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:41:04', '2020-09-11 12:41:04');
INSERT INTO `admin_operation_log` VALUES (3031, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:41:36', '2020-09-11 12:41:36');
INSERT INTO `admin_operation_log` VALUES (3032, 1, '/top/list', 'GET', '127.0.0.1', '[]', '1.8毫秒', '2020-09-11 12:41:39', '2020-09-11 12:41:39');
INSERT INTO `admin_operation_log` VALUES (3033, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:41:39', '2020-09-11 12:41:39');
INSERT INTO `admin_operation_log` VALUES (3034, 1, '/top/list', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:41:39', '2020-09-11 12:41:39');
INSERT INTO `admin_operation_log` VALUES (3035, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:41:39', '2020-09-11 12:41:39');
INSERT INTO `admin_operation_log` VALUES (3036, 1, '/admin', 'GET', '127.0.0.1', '[]', '11.1毫秒', '2020-09-11 12:41:47', '2020-09-11 12:41:47');
INSERT INTO `admin_operation_log` VALUES (3037, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:41:48', '2020-09-11 12:41:48');
INSERT INTO `admin_operation_log` VALUES (3038, 1, '/admin/menu/list', 'GET', '127.0.0.1', '[]', '26.6毫秒', '2020-09-11 12:41:52', '2020-09-11 12:41:52');
INSERT INTO `admin_operation_log` VALUES (3039, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:41:52', '2020-09-11 12:41:52');
INSERT INTO `admin_operation_log` VALUES (3040, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '24.5毫秒', '2020-09-11 12:41:52', '2020-09-11 12:41:52');
INSERT INTO `admin_operation_log` VALUES (3041, 1, '/admin/menu/14', 'GET', '127.0.0.1', '{\"get_data\":\"true\"}', '5.5毫秒', '2020-09-11 12:41:53', '2020-09-11 12:41:53');
INSERT INTO `admin_operation_log` VALUES (3042, 1, '/admin/menu/route', 'GET', '127.0.0.1', '{\"page\":\"1\",\"query\":\"\\/\",\"depend\":\"{}\"}', '0.5毫秒', '2020-09-11 12:41:56', '2020-09-11 12:41:56');
INSERT INTO `admin_operation_log` VALUES (3043, 1, '/admin/menu/route', 'GET', '127.0.0.1', '{\"page\":\"1\",\"query\":\"\\/top\",\"depend\":\"{}\"}', '0.3毫秒', '2020-09-11 12:41:57', '2020-09-11 12:41:57');
INSERT INTO `admin_operation_log` VALUES (3044, 1, '/admin', 'GET', '127.0.0.1', '[]', '7.9毫秒', '2020-09-11 12:42:23', '2020-09-11 12:42:23');
INSERT INTO `admin_operation_log` VALUES (3045, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.3毫秒', '2020-09-11 12:42:24', '2020-09-11 12:42:24');
INSERT INTO `admin_operation_log` VALUES (3046, 1, '/admin/menu/list', 'GET', '127.0.0.1', '[]', '19.2毫秒', '2020-09-11 12:42:24', '2020-09-11 12:42:24');
INSERT INTO `admin_operation_log` VALUES (3047, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '14.1毫秒', '2020-09-11 12:42:24', '2020-09-11 12:42:24');
INSERT INTO `admin_operation_log` VALUES (3048, 1, '/admin/menu/14', 'GET', '127.0.0.1', '{\"get_data\":\"true\"}', '4.2毫秒', '2020-09-11 12:42:27', '2020-09-11 12:42:27');
INSERT INTO `admin_operation_log` VALUES (3049, 1, '/admin/menu/route', 'GET', '127.0.0.1', '{\"page\":\"1\",\"query\":\"top\",\"depend\":\"{}\"}', '0.5毫秒', '2020-09-11 12:42:29', '2020-09-11 12:42:29');
INSERT INTO `admin_operation_log` VALUES (3050, 1, '/admin/menu/route', 'GET', '127.0.0.1', '{\"page\":\"1\",\"query\":\"top\\/\",\"depend\":\"{}\"}', '0.3毫秒', '2020-09-11 12:42:30', '2020-09-11 12:42:30');
INSERT INTO `admin_operation_log` VALUES (3051, 1, '/admin/menu/14', 'PUT', '127.0.0.1', '{\"parent_id\":0,\"title\":\"数据交互\",\"icon\":\"el-icon-ice-cream-round\",\"uri\":\"\\/top\\/list\",\"order\":1,\"is_menu\":true,\"roles\":[],\"permission\":[]}', '12.3毫秒', '2020-09-11 12:42:33', '2020-09-11 12:42:33');
INSERT INTO `admin_operation_log` VALUES (3052, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '13.3毫秒', '2020-09-11 12:42:33', '2020-09-11 12:42:33');
INSERT INTO `admin_operation_log` VALUES (3053, 1, '/admin', 'GET', '127.0.0.1', '[]', '4.4毫秒', '2020-09-11 12:42:34', '2020-09-11 12:42:34');
INSERT INTO `admin_operation_log` VALUES (3054, 1, '/admin/menu/list', 'GET', '127.0.0.1', '[]', '4.3毫秒', '2020-09-11 12:42:34', '2020-09-11 12:42:34');
INSERT INTO `admin_operation_log` VALUES (3055, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:42:34', '2020-09-11 12:42:34');
INSERT INTO `admin_operation_log` VALUES (3056, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '11.9毫秒', '2020-09-11 12:42:34', '2020-09-11 12:42:34');
INSERT INTO `admin_operation_log` VALUES (3057, 1, '/admin', 'GET', '127.0.0.1', '[]', '10.5毫秒', '2020-09-11 12:42:40', '2020-09-11 12:42:40');
INSERT INTO `admin_operation_log` VALUES (3058, 1, '/admin/menu/list', 'GET', '127.0.0.1', '[]', '10.9毫秒', '2020-09-11 12:42:41', '2020-09-11 12:42:41');
INSERT INTO `admin_operation_log` VALUES (3059, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:42:41', '2020-09-11 12:42:41');
INSERT INTO `admin_operation_log` VALUES (3060, 1, '/admin/menu/list', 'GET', '127.0.0.1', '{\"get_data\":\"true\",\"page\":\"1\",\"per_page\":\"15\",\"sort_prop\":\"order\",\"sort_order\":\"asc\",\"sort_field\":\"order\"}', '13.4毫秒', '2020-09-11 12:42:41', '2020-09-11 12:42:41');
INSERT INTO `admin_operation_log` VALUES (3061, 1, '/top/list', 'GET', '127.0.0.1', '[]', '0.6毫秒', '2020-09-11 12:42:42', '2020-09-11 12:42:42');
INSERT INTO `admin_operation_log` VALUES (3062, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:42:42', '2020-09-11 12:42:42');
INSERT INTO `admin_operation_log` VALUES (3063, 1, '/admin', 'GET', '127.0.0.1', '[]', '4.1毫秒', '2020-09-11 12:42:48', '2020-09-11 12:42:48');
INSERT INTO `admin_operation_log` VALUES (3064, 1, '/top/list', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:42:48', '2020-09-11 12:42:48');
INSERT INTO `admin_operation_log` VALUES (3065, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 12:42:48', '2020-09-11 12:42:48');
INSERT INTO `admin_operation_log` VALUES (3066, 1, '/admin', 'GET', '127.0.0.1', '[]', '15毫秒', '2020-09-11 12:43:14', '2020-09-11 12:43:14');
INSERT INTO `admin_operation_log` VALUES (3068, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:43:15', '2020-09-11 12:43:15');
INSERT INTO `admin_operation_log` VALUES (3069, 1, '/admin', 'GET', '127.0.0.1', '[]', '8.5毫秒', '2020-09-11 12:43:57', '2020-09-11 12:43:57');
INSERT INTO `admin_operation_log` VALUES (3070, 1, '/top/list', 'GET', '127.0.0.1', '[]', '2毫秒', '2020-09-11 12:43:57', '2020-09-11 12:43:57');
INSERT INTO `admin_operation_log` VALUES (3071, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:43:57', '2020-09-11 12:43:57');
INSERT INTO `admin_operation_log` VALUES (3072, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 12:50:01', '2020-09-11 12:50:01');
INSERT INTO `admin_operation_log` VALUES (3073, 1, '/admin', 'GET', '127.0.0.1', '[]', '7.9毫秒', '2020-09-11 13:15:11', '2020-09-11 13:15:11');
INSERT INTO `admin_operation_log` VALUES (3074, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 13:15:11', '2020-09-11 13:15:11');
INSERT INTO `admin_operation_log` VALUES (3075, 1, '/admin', 'GET', '127.0.0.1', '[]', '3.9毫秒', '2020-09-11 13:15:12', '2020-09-11 13:15:12');
INSERT INTO `admin_operation_log` VALUES (3076, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0毫秒', '2020-09-11 13:15:12', '2020-09-11 13:15:12');
INSERT INTO `admin_operation_log` VALUES (3077, 1, '/admin', 'GET', '127.0.0.1', '[]', '9.6毫秒', '2020-09-11 13:15:52', '2020-09-11 13:15:52');
INSERT INTO `admin_operation_log` VALUES (3078, 1, '/favicon.ico', 'GET', '127.0.0.1', '[]', '0.1毫秒', '2020-09-11 13:15:52', '2020-09-11 13:15:52');
COMMIT;

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `path` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
BEGIN;
INSERT INTO `admin_permissions` VALUES (1, '所有权限', '0', '[\"*\"]', NULL, '2020-09-07 16:32:51', 1, 0, 0);
INSERT INTO `admin_permissions` VALUES (2, '首页', '0', '[\"GET::\\/admin\\/home\\/main\",\"ANY::\\/index\\/*\",\"ANY::\\/demo\\/*\",\"ANY::\\/form\\/*\",\"ANY::\\/admin\\/*\"]', NULL, '2020-09-09 10:48:26', 1, 0, 0);
INSERT INTO `admin_permissions` VALUES (3, '登录/退出', '1', '[\"GET::\\/admin\\/logs\\/*\",\"GET::\\/admin\\/logs\\/create\"]', NULL, '2020-09-07 16:43:09', 1, 0, 0);
INSERT INTO `admin_permissions` VALUES (5, '系统设置', '1', '[\"GET::\\/swagger\\/*\",\"GET::\\/swagger\",\"GET::\\/index\\/list\",\"GET::\\/index\\/*\"]', NULL, '2020-09-07 16:42:55', 1, 0, 0);
INSERT INTO `admin_permissions` VALUES (6, '菜单管理', '0', '[\"GET::\\/admin\\/menu\\/*\"]', '2020-09-08 15:49:27', '2020-09-08 18:49:09', 1, 0, 0);
INSERT INTO `admin_permissions` VALUES (8, '添加菜单', '', '[\"GET::\\/admin\\/menu\\/create\",\"POST::\\/admin\\/menu\"]', '2020-09-08 18:49:31', '2020-09-19 16:32:59', 1, 6, 0);
INSERT INTO `admin_permissions` VALUES (9, '删除菜单', '', '[\"DELETE::\\/admin\\/menu\\/{id}\"]', '2020-09-08 18:50:27', '2020-09-19 16:33:05', 1, 6, 0);
INSERT INTO `admin_permissions` VALUES (10, '修改菜单', '', '[\"GET::\\/admin\\/menu\\/{id:\\\\d+}\",\"PUT::\\/admin\\/menu\\/{id:\\\\d+}\"]', '2020-09-08 18:50:59', '2020-09-19 16:33:10', 1, 6, 0);
INSERT INTO `admin_permissions` VALUES (11, '管理员', '', '[\"ANY::\\/admin\\/users\\/*\"]', '2020-09-08 18:53:23', '2020-09-09 10:49:59', 1, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------
BEGIN;
INSERT INTO `admin_role_menu` VALUES (1, 2, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 8, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 4, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (2, 3, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role_permissions
-- ----------------------------
BEGIN;
INSERT INTO `admin_role_permissions` VALUES (2, 1, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 2, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 3, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 5, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 6, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 8, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 9, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 10, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (1, 11, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role_users
-- ----------------------------
BEGIN;
INSERT INTO `admin_role_users` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_role_users` VALUES (1, 4, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_roles_name_unique` (`name`),
  UNIQUE KEY `admin_roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
BEGIN;
INSERT INTO `admin_roles` VALUES (1, '超级管理员', 'administrator', '2020-09-03 16:19:02', '2020-09-03 16:19:02');
COMMIT;

-- ----------------------------
-- Table structure for admin_user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_permissions`;
CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_user_permissions
-- ----------------------------
BEGIN;
INSERT INTO `admin_user_permissions` VALUES (1, 2, NULL, NULL);
INSERT INTO `admin_user_permissions` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_user_permissions` VALUES (4, 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
BEGIN;
INSERT INTO `admin_users` VALUES (1, 'admin', '$2y$10$Sw7R0JYnA0DV/7fpo1HMfOR4gU6QiexaY3oFKMJIx6fTJbjl5esXO', '管理员', NULL, 'Uh5tXnhDocmTgAApkpyIMMEXsUQCxKUUh9LeULMHYW8alxpv3OtrUDptkfVx', '2020-09-03 16:19:02', '2020-09-09 20:06:30');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
