SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属模块',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户组Id',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `menu_auth` text COLLATE utf8mb4_unicode_ci COMMENT '菜单权限',
  `log_auth` text COLLATE utf8mb4_unicode_ci COMMENT '记录权限',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=为删除 1=已删除',
  `pid` int(11) NOT NULL DEFAULT '0',
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `module` (`module`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限规则';

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
BEGIN;
INSERT INTO `auth_rule` VALUES (3, 'api', 2, '系统管理员', '[[\"system\",\"1\",\"9\",\"98\"],[\"system\",\"1\",\"9\",\"10\"],[\"system\",\"1\",\"9\",\"14\"],[\"system\",\"1\",\"9\",\"11\"],[\"system\",\"1\",\"9\",\"12\"],[\"system\",\"1\",\"9\",\"13\"],[\"system\",\"93\",\"2\",\"3\"],[\"system\",\"93\",\"2\",\"99\"],[\"system\",\"93\",\"2\",\"4\"],[\"system\",\"93\",\"2\",\"8\"],[\"system\",\"93\",\"2\",\"5\"],[\"system\",\"93\",\"2\",\"6\"],[\"system\",\"93\",\"2\",\"7\"],[\"system\",\"93\",\"15\",\"16\"],[\"system\",\"93\",\"15\",\"17\"],[\"system\",\"93\",\"15\",\"18\"],[\"system\",\"93\",\"15\",\"19\"],[\"system\",\"93\",\"15\",\"20\"],[\"system\",\"93\",\"15\",\"21\"],[\"system\",\"93\",\"78\",\"79\"],[\"system\",\"93\",\"78\",\"80\"],[\"system\",\"93\",\"78\",\"81\"],[\"system\",\"93\",\"78\",\"82\"],[\"system\",\"32\",\"97\"],[\"system\",\"32\",\"33\"],[\"system\",\"32\",\"96\"],[\"system\",\"88\",\"92\"]]', '[467,500,501,507,508,509,460,497,506]', 4, 1, 0, 0, 0, '2020-08-13 12:20:25', '2020-08-13 12:20:25');
INSERT INTO `auth_rule` VALUES (4, 'api', 4, '游客', '[[\"system\",\"1\",\"9\",\"10\"],[\"system\",\"1\",\"9\",\"14\"],[\"system\",\"1\",\"9\",\"11\"],[\"system\",\"1\",\"9\",\"12\"],[\"system\",\"1\",\"9\",\"13\"],[\"system\",\"93\",\"2\",\"3\"],[\"system\",\"93\",\"2\",\"4\"],[\"system\",\"93\",\"2\",\"8\"],[\"system\",\"93\",\"2\",\"5\"],[\"system\",\"93\",\"2\",\"6\"],[\"system\",\"93\",\"2\",\"7\"],[\"system\",\"93\",\"78\",\"79\"],[\"system\",\"93\",\"78\",\"80\"],[\"system\",\"93\",\"78\",\"81\"],[\"system\",\"93\",\"78\",\"82\"],[\"system\",\"93\",\"15\",\"16\"],[\"system\",\"93\",\"15\",\"17\"],[\"system\",\"93\",\"15\",\"18\"],[\"system\",\"93\",\"15\",\"19\"],[\"system\",\"93\",\"15\",\"20\"],[\"system\",\"93\",\"15\",\"21\"],[\"system\",\"32\",\"97\"],[\"system\",\"32\",\"33\"],[\"system\",\"32\",\"96\"],[\"system\",\"88\",\"92\"]]', '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,167,169,170,171,172,774,168,369,370,371,372,373,374,375,376,377,378,379,380,629,453,454,455,456,457,458,459]', 4, 1, 0, 0, 0, '2020-08-05 11:41:40', '2020-08-05 11:41:40');
INSERT INTO `auth_rule` VALUES (5, 'admin', 1, '超级管理员', '[[\"system\",\"1\",\"9\",\"10\"],[\"system\",\"1\",\"9\",\"14\"],[\"system\",\"1\",\"9\",\"11\"],[\"system\",\"1\",\"9\",\"12\"],[\"system\",\"1\",\"9\",\"13\"],[\"system\",\"93\",\"2\",\"3\"],[\"system\",\"93\",\"2\",\"4\"],[\"system\",\"93\",\"2\",\"8\"],[\"system\",\"93\",\"2\",\"5\"],[\"system\",\"93\",\"2\",\"6\"],[\"system\",\"93\",\"2\",\"7\"],[\"system\",\"93\",\"78\",\"79\"],[\"system\",\"93\",\"78\",\"80\"],[\"system\",\"93\",\"78\",\"81\"],[\"system\",\"93\",\"78\",\"82\"],[\"system\",\"93\",\"15\",\"16\"],[\"system\",\"93\",\"15\",\"17\"],[\"system\",\"93\",\"15\",\"18\"],[\"system\",\"93\",\"15\",\"19\"],[\"system\",\"93\",\"15\",\"20\"],[\"system\",\"93\",\"15\",\"21\"],[\"system\",\"32\",\"97\"],[\"system\",\"32\",\"33\"],[\"system\",\"32\",\"96\"],[\"system\",\"88\",\"92\"]]', '[]', 4, 1, 0, 0, 0, '2020-08-05 11:41:41', '2020-08-05 11:41:41');
INSERT INTO `auth_rule` VALUES (28, 'home', 2, '普通管理员', '[[\"system\",\"1\",\"9\",\"10\"],[\"system\",\"1\",\"9\",\"14\"],[\"system\",\"1\",\"9\",\"11\"],[\"system\",\"1\",\"9\",\"12\"],[\"system\",\"1\",\"9\",\"13\"],[\"system\",\"93\",\"2\",\"3\"],[\"system\",\"93\",\"2\",\"4\"],[\"system\",\"93\",\"2\",\"8\"],[\"system\",\"93\",\"2\",\"5\"],[\"system\",\"93\",\"2\",\"6\"],[\"system\",\"93\",\"2\",\"7\"],[\"system\",\"93\",\"78\",\"79\"],[\"system\",\"93\",\"78\",\"80\"],[\"system\",\"93\",\"78\",\"81\"],[\"system\",\"93\",\"78\",\"82\"],[\"system\",\"93\",\"15\",\"16\"],[\"system\",\"93\",\"15\",\"17\"],[\"system\",\"93\",\"15\",\"18\"],[\"system\",\"93\",\"15\",\"19\"],[\"system\",\"93\",\"15\",\"20\"],[\"system\",\"93\",\"15\",\"21\"],[\"system\",\"32\",\"97\"],[\"system\",\"32\",\"33\"],[\"system\",\"32\",\"96\"],[\"system\",\"88\",\"92\"]]', '[]', 2, 1, 0, 0, 0, '2020-08-05 11:41:42', '2020-08-05 11:41:42');
COMMIT;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '命名空间, 字母',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名, 字母',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '可读配置名',
  `remark` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '配置规则描述',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '具体配置值 key:value',
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '权限',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_need_form` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用表单：0，否；1，是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`name`,`namespace`),
  KEY `namespace` (`namespace`),
  KEY `update_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='通用配置';

-- ----------------------------
-- Records of config
-- ----------------------------
BEGIN;
INSERT INTO `config` VALUES (1, 'system', 'namespace', '可用空间', '系统模块', NULL, '{\"system\":\"\\u7cfb\\u7edf\",\"common\":\"\\u901a\\u7528\"}', NULL, '2019-10-18 16:47:52', '2020-08-15 22:58:17', 0);
INSERT INTO `config` VALUES (2, 'system', 'website_config', '站点配置', '', '{\"open_export|\\u5f00\\u542f\\u5bfc\\u51fa\":{\"type\":\"switch\"},\"navbar_notice|\\u5168\\u5c40\\u63d0\\u9192\":\"\",\"system_module|\\u7cfb\\u7edf\\u6a21\\u5757\":{\"type\":\"sub-form\",\"children\":{\"icon\":{\"type\":\"icon-select\"},\"name\":\"\",\"label\":\"\",\"indexUrl\":\"\"},\"repeat\":true,\"props\":{\"sort\":true}},\"open_screen_lock|\\u95f2\\u7f6e\\u9501\\u5c4f\":{\"type\":\"switch\"},\"screen_autho_lock_time|\\u95f2\\u7f6e\\u9501\\u5c4f\\u65f6\\u957f\":{\"type\":\"number\",\"info\":\"\\u5355\\u4f4d\\u79d2\"}}', '{\"open_export\":0,\"navbar_notice\":\"\\u6b22\\u8fce\\u4f7f\\u7528hyperf-admin\\u540e\\u53f0\\u5feb\\u901f\\u5f00\\u53d1\\u63d2\\u4ef6\",\"system_module\":[{\"icon\":\"el-icon-setting\",\"name\":\"system\",\"label\":\"\\u7cfb\\u7edf\",\"indexUrl\":\"\\/system\\/#\\/dashboard\"},{\"icon\":\"eye-open\",\"name\":\"default\",\"label\":\"\\u9996\\u98751\",\"indexUrl\":\"\\/default\\/#\\/dashboard\"}],\"open_screen_lock\":0,\"screen_autho_lock_time\":36}', NULL, '2020-03-17 08:29:10', '2020-08-15 20:16:30', 1);
INSERT INTO `config` VALUES (3, 'system', 'permissions', '公共权限', '', '{\"open_api|\\u516c\\u5171\\u8d44\\u6e90\":{\"rule\":\"array\",\"type\":\"table-transfer\",\"props\":{\"tableHeader\":[{\"title\":\"\\u8def\\u7531\\u5730\\u5740\",\"field\":\"controller\"},{\"title\":\"\\u65b9\\u6cd5\",\"field\":\"action\"},{\"title\":\"\\u8bf7\\u6c42\\u65b9\\u5f0f\",\"field\":\"http_method\"}],\"remoteApi\":\"\\/menu\\/getOpenApis?field=open_api\"}},\"user_open_api|\\u7528\\u6237\\u5f00\\u653e\\u8d44\\u6e90\":{\"rule\":\"array\",\"type\":\"table-transfer\",\"props\":{\"tableHeader\":[{\"title\":\"\\u63a7\\u5236\\u5668\",\"field\":\"controller\"},{\"title\":\"\\u65b9\\u6cd5\",\"field\":\"action\"},{\"title\":\"\\u8bf7\\u6c42\\u65b9\\u5f0f\",\"field\":\"http_method\"}],\"remoteApi\":\"\\/menu\\/getOpenApis?field=user_open_api\"}}}', '{\"open_api\":[\"POST::\\/api\\/user\\/login\",\"GET::\\/api\\/system\\/config\",\"GET::\\/api\\/user\\/menu\",\"GET::\\/swagger\\/index\",\"GET::\\/swagger\",\"GET::\\/swagger\\/api\"],\"user_open_api\":[]}', NULL, '2020-03-29 15:47:19', '2020-08-05 15:44:42', 1);
INSERT INTO `config` VALUES (4, 'agent', 'agent', '运营商配置', '', '{\"open_export|\\u5f00\\u542f\\u5bfc\\u51fa\":{\"type\":\"switch\"},\"navbar_notice|\\u5168\\u5c40\\u63d0\\u9192\":\"\",\"open_screen_lock|\\u95f2\\u7f6e\\u9501\\u5c4f\":{\"type\":\"switch\"},\"screen_autho_lock_time|\\u95f2\\u7f6e\\u9501\\u5c4f\\u65f6\\u957f\":{\"type\":\"number\",\"info\":\"\\u5355\\u4f4d\\u79d2\"}}', '\"\"', NULL, '2020-08-02 15:06:02', '2020-08-02 15:09:10', 1);
INSERT INTO `config` VALUES (6, 'common', 'system_config', '系统设置', '', '{\"title|\\u7ad9\\u70b9\\u540d\\u79f0\":{\"type\":\"input\"},\"storage|\\u4e0a\\u4f20\\u50a8\\u5b58\\u81f3\":{\"type\":\"select\",\"value\":[\"local\"],\"options\":[{\"value\":\"local\",\"label\":\"\\u672c\\u5730\\u786c\\u76d8\"},{\"value\":\"oss\",\"label\":\"\\u963f\\u91cc\\u4e91OSS\"},{\"value\":\"qiniu\",\"label\":\"\\u4e03\\u725b\\u4e91\"},{\"value\":\"cos\",\"label\":\"\\u817e\\u8baf\\u4e91\\u50a8\\u5b58\"}],\"compute\":[{\"when\":[\"in\",[\"local\",\"qiniu\",\"cos\"]],\"set\":{\"oss_key_id\":{\"type\":\"hidden\"},\"oss_key_secret\":{\"type\":\"hidden\"}}},{\"when\":[\"=\",\"oss\"],\"set\":{\"oss_key_id\":{\"rule\":\"required\"},\"oss_key_secret\":{\"rule\":\"required\"}}}]},\"oss_key_id|KeyID\":{\"type\":\"input\"},\"oss_key_secret|Secret\":{\"type\":\"input\"},\"allow_ext|\\u5141\\u8bb8\\u4e0a\\u4f20\\u540e\\u7f00\":{\"type\":\"select\",\"value\":[\"png\",\"jpg\",\"gif\",\"jpeg\"],\"options\":[{\"value\":\"png\",\"label\":\"png\"},{\"value\":\"jpg\",\"label\":\"jpg\"},{\"value\":\"jpeg\",\"label\":\"jpeg\"},{\"value\":\"gif\",\"label\":\"gif\"}],\"props\":{\"multiple\":true}}}', '{\"title\":\"\\u5feb\\u4e50\\u91d1\\u5e93\",\"storage\":\"local\",\"oss_key_id\":\"\",\"oss_key_secret\":\"\",\"allow_ext\":[\"png\",\"jpg\",\"gif\",\"jpeg\"]}', NULL, '2020-08-09 20:59:43', '2020-08-09 21:52:51', 1);
COMMIT;

-- ----------------------------
-- Table structure for front_routes
-- ----------------------------
DROP TABLE IF EXISTS `front_routes`;
CREATE TABLE `front_routes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'label名称',
  `module` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模块',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '路径',
  `view` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '非脚手架渲染是且path路径为正则时, vue文件路径',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'icon',
  `open_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '打开方式 0 当前页面 2 新标签页 3URL',
  `is_scaffold` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否脚手架渲染, 1是, 0否',
  `is_menu` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否菜单 0 否 1 是',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0 禁用 1 启用',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大越在前面',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '权限标识',
  `http_method` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '请求方式; 0, Any; 1, GET; 2, POST; 3, PUT; 4, DELETE;',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '菜单类型 0 目录  1 菜单 2 其他',
  `page_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '页面类型： 0 列表  1 表单',
  `scaffold_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '脚手架预置权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='前端路由(菜单)';

-- ----------------------------
-- Records of front_routes
-- ----------------------------
BEGIN;
INSERT INTO `front_routes` VALUES (1, 0, '系统管理', 'system', '#', '', 'el-icon-s-tools', 0, 1, 1, 1, 100, '2020-03-27 10:53:43', '2020-08-01 20:33:27', '', 0, 0, 0, '\'\'');
INSERT INTO `front_routes` VALUES (2, 93, '菜单配置', 'system', '/menu/list', '', 'nested', 0, 1, 1, 1, 99, '2020-03-28 22:16:42', '2020-08-03 16:46:36', NULL, 1, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (3, 2, '新建', 'system', '/menu/form', '', '', 0, 1, 0, 1, 99, '2020-03-28 22:16:42', '2020-08-05 15:20:29', '[\"POST::\\/api\\/menu\\/create\"]', 1, 1, 1, '[]');
INSERT INTO `front_routes` VALUES (4, 2, '编辑', 'system', '/menu/:id', '', '', 0, 1, 0, 1, 98, '2020-03-28 22:16:43', '2020-08-05 15:21:16', '[\"PUT::\\/api\\/menu\\/{id:\\\\d+}\",\"PUT::\\/api\\/menu\\/rowchange\\/{id:\\\\d+}\",\"GET::\\/api\\/menu\\/{id:\\\\d+}\"]', 1, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (5, 2, '删除', 'system', '#', '', '', 0, 0, 0, 1, 96, '2020-03-28 22:16:43', '2020-08-05 15:21:45', '[\"DELETE::\\/api\\/menu\\/{id:\\\\d+}\",\"DELETE::\\/api\\/menu\\/batch_del\"]', 2, 2, 0, '[]');
INSERT INTO `front_routes` VALUES (6, 2, '导入', 'system', '', '', '', 0, 0, 0, 1, 95, '2020-03-28 22:16:44', '2020-03-28 22:16:44', '/menu/import', 2, 2, 0, '');
INSERT INTO `front_routes` VALUES (7, 2, '导出', 'system', '', '', '', 0, 0, 0, 1, 94, '2020-03-28 22:16:44', '2020-03-28 22:16:44', '/menu/export', 2, 2, 0, '');
INSERT INTO `front_routes` VALUES (8, 2, '行编辑', 'system', '#', '', '', 0, 0, 0, 1, 97, '2020-03-28 22:19:18', '2020-08-05 15:22:48', '[\"PUT::\\/api\\/menu\\/rowchange\\/{id:\\\\d+}\",\"GET::\\/api\\/menu\\/{id:\\\\d+}\"]', 2, 2, 0, '[]');
INSERT INTO `front_routes` VALUES (9, 1, '通用配置', 'system', '/cconf/list', '', 'el-icon-s-tools', 0, 1, 1, 1, 98, '2020-06-10 06:45:13', '2020-06-10 07:14:10', '', 0, 1, 0, '{\"list\":10,\"create\":11,\"edit\":12,\"delete\":13}');
INSERT INTO `front_routes` VALUES (10, 9, '列表', 'system', '', '', '', 0, 0, 0, 1, 99, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'GET::/cconf/info,GET::/cconf/list,GET::/cconf/list.json,GET::/cconf/childs/{id:\\d+},GET::/cconf/act,GET::/cconf/notice', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (11, 9, '新建', 'system', '/cconf/form', '', '', 0, 1, 0, 1, 98, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'GET::/cconf/form,GET::/cconf/form.json,POST::/cconf/form', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (12, 9, '编辑', 'system', '/cconf/:id', '', '', 0, 1, 0, 1, 97, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'GET::/cconf/{id:\\d+},GET::/cconf/{id:\\d+}.json,POST::/cconf/{id:\\d+},GET::/cconf/newversion/{id:\\d+}/{last_ver_id:\\d+}', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (13, 9, '删除', 'system', '#', '', '', 0, 0, 0, 1, 96, '2020-06-10 06:45:13', '2020-08-05 13:14:03', '[\"GET::\\/api\\/system\\/routes\",\"GET::\\/api\\/system\\/config\"]', 0, 2, 0, '[]');
INSERT INTO `front_routes` VALUES (14, 9, '表单预览', 'system', '/cconf/cconf_(.*)', '', '', 0, 1, 0, 1, 99, '2020-06-10 07:10:29', '2020-06-10 07:13:35', 'POST::/cconf/form,GET::/cconf/{id:\\d+}', 0, 1, 0, '\"\"');
INSERT INTO `front_routes` VALUES (15, 93, '用户管理', 'system', '/user/list', '', 'user', 0, 1, 1, 1, 99, '2020-06-10 08:03:01', '2020-08-03 17:12:23', NULL, 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (16, 15, '列表', 'system', '#', '', '', 0, 0, 0, 1, 99, '2020-06-10 08:03:01', '2020-08-05 15:24:15', '[\"GET::\\/api\\/user\\/list\",\"GET::\\/api\\/user\\/form\",\"GET::\\/api\\/user\\/info\"]', 0, 2, 0, '[]');
INSERT INTO `front_routes` VALUES (17, 15, '新建', 'system', '/user/form', '', '', 0, 1, 0, 1, 98, '2020-06-10 08:03:01', '2020-08-05 15:24:43', '[\"GET::\\/api\\/user\\/form\",\"POST::\\/api\\/user\\/form\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (18, 15, '编辑', 'system', '/user/:id', '', '', 0, 1, 0, 1, 97, '2020-06-10 08:03:01', '2020-08-05 15:25:02', '[\"POST::\\/api\\/user\\/form\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (19, 15, '行编辑', 'system', '', '', '', 0, 0, 0, 1, 96, '2020-06-10 08:03:01', '2020-06-10 16:03:38', 'POST::/user/rowchange/{id:\\d+}', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (20, 15, '删除', 'system', '', '', '', 0, 0, 0, 1, 95, '2020-06-10 08:03:01', '2020-06-10 16:03:39', 'POST::/user/delete', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (21, 15, '导出', 'system', '', '', '', 0, 0, 0, 1, 94, '2020-06-10 08:03:01', '2020-06-10 16:03:41', 'POST::/user/export', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (32, 0, '开发工具', 'system', '#', '', 'oms-icon-tool', 0, 1, 1, 1, 99, '2020-06-10 09:50:21', '2020-08-03 18:57:49', '', 0, 0, 0, '\"\"');
INSERT INTO `front_routes` VALUES (33, 32, '代码生成', 'system', '/devtools/maker', '/devtools/maker', 'el-icon-star-off', 0, 0, 1, 1, 97, '2020-06-10 09:52:23', '2020-08-03 18:57:45', '[\"GET::\\/api\\/dev\\/controllermaker\",\"GET::\\/api\\/dev\\/make\",\"GET::\\/api\\/dev\\/dbAct\",\"GET::\\/api\\/dev\\/tableAct\",\"GET::\\/api\\/dev\\/transType\",\"GET::\\/api\\/dev\\/tableSchema\",\"POST::\\/api\\/dev\\/controllermaker\",\"POST::\\/api\\/dev\\/make\",\"POST::\\/api\\/dev\\/dbAct\",\"POST::\\/api\\/dev\\/tableAct\",\"POST::\\/api\\/dev\\/tableSchema\",\"POST::\\/api\\/dev\\/transType\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (78, 93, '角色管理', 'system', '/auth_rule/list', '', 'eye-open', 0, 1, 1, 1, 99, '2020-06-16 03:05:50', '2020-08-03 17:13:21', NULL, 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (79, 78, '列表', 'system', '#', '', '', 0, 0, 0, 1, 99, '2020-06-16 03:05:50', '2020-08-05 15:26:23', '[\"GET::\\/api\\/auth_rule\\/info\",\"GET::\\/api\\/auth_rule\\/list\"]', 0, 2, 0, '[]');
INSERT INTO `front_routes` VALUES (80, 78, '新建', 'system', '/auth_rule/form', '', '', 0, 1, 0, 1, 98, '2020-06-16 03:05:50', '2020-08-03 14:18:33', 'GET::/role/form,GET::/role/form.json,POST::/role/form', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (81, 78, '编辑', 'system', '/auth_rule/:id', '', '', 0, 1, 0, 1, 97, '2020-06-16 03:05:50', '2020-08-03 14:18:41', 'GET::/role/{id:\\d+},GET::/role/{id:\\d+}.json,POST::/role/{id:\\d+},GET::/role/newversion/{id:\\d+}/{last_ver_id:\\d+}', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (82, 78, '删除', 'system', '', '', '', 0, 0, 0, 1, 96, '2020-06-16 03:05:50', '2020-06-16 11:14:05', 'POST::/role/delete', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (88, 0, '接口文档', 'system', '#', '', 'eye-open', 0, 0, 1, 1, 98, '2020-08-03 11:22:27', '2020-08-03 17:13:39', '[]', 0, 0, 0, '[]');
INSERT INTO `front_routes` VALUES (92, 88, '接口文档', 'system', 'http://127.0.0.1:9501/swagger/index', '', 'form', 3, 1, 1, 1, 99, '2020-08-03 13:35:16', '2020-08-03 21:56:31', '[\"GET::\\/swagger\\/index\",\"GET::\\/swagger\",\"GET::\\/swagger\\/api\"]', 0, 0, 0, '[]');
INSERT INTO `front_routes` VALUES (93, 0, '权限管理', 'system', '#', '', 'tree', 0, 1, 1, 1, 99, '2020-08-03 16:40:44', '2020-08-03 16:40:44', '[]', 0, 0, 0, '[]');
INSERT INTO `front_routes` VALUES (96, 32, '验证器生成', 'system', '/devtools/validate', '/devtools/maker', 'el-icon-s-help', 0, 0, 1, 1, 95, '2020-08-03 17:17:21', '2020-08-03 18:57:55', '[\"GET::\\/api\\/dev\\/make\",\"GET::\\/api\\/dev\\/controllermaker\",\"GET::\\/api\\/dev\\/dbAct\",\"GET::\\/api\\/dev\\/tableAct\",\"GET::\\/api\\/dev\\/tableSchema\",\"GET::\\/api\\/dev\\/transType\",\"POST::\\/api\\/dev\\/controllermaker\",\"POST::\\/api\\/dev\\/make\",\"POST::\\/api\\/dev\\/dbAct\",\"POST::\\/api\\/dev\\/tableAct\",\"POST::\\/api\\/dev\\/transType\",\"POST::\\/api\\/dev\\/tableSchema\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (97, 32, '控制器生成', 'system', '/devtools/controller', '/devtools/maker', 'el-icon-circle-plus', 0, 0, 1, 1, 99, '2020-08-03 18:00:00', '2020-08-03 18:01:04', '[\"GET::\\/api\\/dev\\/maker\",\"GET::\\/api\\/dev\\/tableAct\",\"GET::\\/api\\/dev\\/validate\",\"GET::\\/api\\/dev\\/make\",\"GET::\\/api\\/dev\\/dbAct\",\"GET::\\/api\\/dev\\/tableSchema\",\"GET::\\/api\\/dev\\/transType\",\"POST::\\/api\\/dev\\/maker\",\"POST::\\/api\\/dev\\/dbAct\",\"POST::\\/api\\/dev\\/make\",\"POST::\\/api\\/dev\\/validate\",\"POST::\\/api\\/dev\\/tableAct\",\"POST::\\/api\\/dev\\/tableSchema\",\"POST::\\/api\\/dev\\/transType\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (98, 9, '后台用户登陆相关', 'system', '#', '', '', 0, 1, 0, 1, 99, '2020-08-05 14:06:09', '2020-08-05 14:06:09', '[\"GET::\\/api\\/user\\/menu\",\"GET::\\/api\\/system\\/config\",\"GET::\\/api\\/system\\/routes\"]', 0, 2, 0, '[]');
INSERT INTO `front_routes` VALUES (99, 2, '列表', 'system', '#', '', '', 0, 1, 0, 1, 99, '2020-08-05 15:23:36', '2020-08-05 15:23:36', '[\"GET::\\/api\\/menu\\/list\",\"GET::\\/api\\/menu\\/info\",\"GET::\\/api\\/menu\\/form\",\"GET::\\/api\\/menu\\/childs\"]', 1, 2, 0, '[]');
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `realname` varchar(50) NOT NULL DEFAULT '',
  `password` char(40) NOT NULL,
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `login_time` timestamp NULL DEFAULT NULL,
  `login_ip` varchar(50) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is admin',
  `is_default_pass` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否初始密码1:是,0:否',
  `qq` varchar(20) NOT NULL DEFAULT '' COMMENT '用户qq',
  `roles` varchar(50) NOT NULL DEFAULT '10',
  `sign` varchar(255) NOT NULL DEFAULT '' COMMENT '签名',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `avatar_small` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='后台用户';

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'admin', '管理员', 'db32a1c7951afbd106d19c488dbcdb2e9889ea7c', '13422222222', '4213509@qq.com', 1, '2020-08-15 22:57:17', '127.0.0.1', 1, 1, '', '1', '哈哈', 'http://qupinapptest.oss-cn-beijing.aliyuncs.com/1/202002/d81d3c9dc7c3df7590d333f783a97995.jpeg', '', '2017-12-12 10:49:11', '2020-08-15 22:57:17');
INSERT INTO `user` VALUES (3, 'admin1', '管理员2', 'db32a1c7951afbd106d19c488dbcdb2e9889ea7c', '13422222222', '4213509@qq.com', 1, '2020-08-05 15:26:45', '127.0.0.1', 1, 1, '', '10', 'tongge', 'http://qupinapptest.oss-cn-beijing.aliyuncs.com/1/202002/d81d3c9dc7c3df7590d333f783a97995.jpeg', '', '2020-08-05 11:40:06', '2020-08-05 15:26:45');
COMMIT;

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `module` char(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_role_id` (`user_id`,`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户角色';

-- ----------------------------
-- Records of user_role
-- ----------------------------
BEGIN;
INSERT INTO `user_role` VALUES (18, 3, 5, '2020-08-05 11:40:06', '2020-08-13 12:31:05', 'admin');
INSERT INTO `user_role` VALUES (19, 3, 4, '2020-08-05 11:40:06', '2020-08-13 12:31:09', 'admin');
INSERT INTO `user_role` VALUES (20, 3, 3, '2020-08-05 11:40:06', '2020-08-13 12:31:10', 'admin');
INSERT INTO `user_role` VALUES (21, 3, 28, '2020-08-05 11:40:06', '2020-08-13 12:31:12', 'admin');
INSERT INTO `user_role` VALUES (33, 1, 3, '2020-08-15 22:57:07', '2020-08-15 22:57:07', 'admin');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
