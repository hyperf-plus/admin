/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80016
 Source Host           : 127.0.0.1:3306
 Source Schema         : admin

 Target Server Type    : MySQL
 Target Server Version : 80016
 File Encoding         : 65001

 Date: 04/08/2020 14:09:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述',
  `system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '系统保留',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=为删除 1=已删除',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT '租户ID',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户组';

-- ----------------------------
-- Records of auth_group
-- ----------------------------
BEGIN;
INSERT INTO `auth_group` VALUES (1, '超级管理员', '系统保留，拥有最高权限', 1, 39, 1, 0, 1583320116, NULL, 0, 1);
INSERT INTO `auth_group` VALUES (2, '普通管理员测试啊', '系统保留，拥有较高权限', 1, 49, 1, 0, 1580289289, NULL, 0, 1);
INSERT INTO `auth_group` VALUES (3, '普通顾客', '系统保留，前台普通顾客', 1, 48, 1, 0, 1582606464, NULL, 0, 1);
INSERT INTO `auth_group` VALUES (4, '游客', '系统保留，无需授权即可访问', 1, 50, 1, 0, 1580724285, NULL, 0, 1);
INSERT INTO `auth_group` VALUES (7, '后勤', '这是一段描述', 0, 18, 1, 0, 1578832629, 1573984388, 0, 1);
INSERT INTO `auth_group` VALUES (9, '客服', '这是一段描述', 0, 50, 1, 0, 1576897983, NULL, 0, 1);
INSERT INTO `auth_group` VALUES (10, '财务', '这是一段描述', 0, 100, 1, 0, 1577438235, NULL, 0, 1);
INSERT INTO `auth_group` VALUES (12, '无权限测试', '无权限测试', 0, 99, 1, 0, 1578726389, NULL, 1573984150, 1);
COMMIT;

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属模块',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组Id',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则名称',
  `menu_auth` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '菜单权限',
  `log_auth` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '记录权限',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=为删除 1=已删除',
  `pid` int(11) NOT NULL DEFAULT '0',
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '菜单权限原始值',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `module` (`module`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限规则';

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
BEGIN;
INSERT INTO `auth_rule` VALUES (3, 'api', 2, '普通管理员11', '[[\"system\",1,78,79],[\"system\",1,78,80],[\"system\",1,78,81],[\"system\",1,78,82],[\"system\",1,9,10],[\"system\",1,9,14],[\"system\",1,9,11],[\"system\",1,9,12],[\"system\",1,9,13],[\"system\",88,92],[\"system\",93,2,3],[\"system\",93,2,4],[\"system\",93,2,8],[\"system\",93,2,5],[\"system\",93,2,6],[\"system\",93,2,7],[\"system\",93,15,16],[\"system\",93,15,17],[\"system\",93,15,18],[\"system\",93,15,19],[\"system\",93,15,20],[\"system\",93,15,21],[\"system\",32,33]]', '[467,500,501,507,508,509,460,497,506]', 3, 1, 0, 1, 0, '2020-08-03 22:54:29', '2020-08-03 22:54:29', '[[\"system\",1,2,3],[\"system\",1,2,63],[\"system\",1,2,57],[\"system\",1,2,58],[\"system\",1,2,64],[\"system\",1,2,4],[\"system\",1,2,8],[\"system\",1,2,59],[\"system\",1,2,65],[\"system\",1,2,60],[\"system\",1,2,66],[\"system\",1,2,5],[\"system\",1,2,61],[\"system\",1,2,67],[\"system\",1,2,6],[\"system\",1,2,62],[\"system\",1,2,7],[\"system\",1,2,68],[\"system\",1,78,79],[\"system\",1,78,80],[\"system\",1,78,81],[\"system\",1,78,82],[\"system\",1,15,16],[\"system\",1,15,17],[\"system\",1,15,18],[\"system\",1,15,19],[\"system\",1,15,20],[\"system\",1,15,21],[\"system\",1,9,10],[\"system\",1,9,14],[\"system\",1,9,11],[\"system\",1,9,12],[\"system\",1,9,13],[\"system\",88,92],[\"system\",32,33]]');
INSERT INTO `auth_rule` VALUES (4, 'api', 4, '游客', '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,615,617,618,23,24,25,26,27,28,29,30,614,616,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,619,87,88,89,90,91,92,93,94,95,96,622,113,114,115,116,117,118,119,120,121,122,130,131,132,133,134,135,136,137,138,150,151,152,153,154,155,156,157,158,159,160,623,167,169,170,171,172,774,168,173,174,175,176,177,178,179,180,777,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,624,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,232,233,234,235,236,237,238,239,240,241,242,626,243,244,245,246,247,248,249,250,251,252,253,254,255,281,282,283,284,734,285,286,287,288,289,290,291,292,293,294,295,296,627,311,312,313,314,315,316,317,318,319,628,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,369,370,371,372,373,374,375,376,377,378,379,380,629,385,386,387,388,389,390,391,392,393,394,395,723,724,725,726,727,396,397,398,399,400,401,402,403,404,630,414,415,416,417,418,419,907,420,421,631,422,423,424,425,426,427,428,429,430,431,432,433,434,435,574,641,648,664,665,436,437,438,439,440,441,442,443,632,444,445,446,447,448,449,450,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,840,470,471,472,473,474,491,492,493,494,495,496,633,634,635,636,637,638,639,640,657,658,659,660,661,662,663]', '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,167,169,170,171,172,774,168,369,370,371,372,373,374,375,376,377,378,379,380,629,453,454,455,456,457,458,459]', 3, 1, 0, 1, 0, '2020-08-03 21:32:12', '2020-08-03 21:32:12', '');
INSERT INTO `auth_rule` VALUES (5, 'admin', 1, '超级管理员', '[513,534,535,536,679,516,515,866,865,867,868,869,870,857,858,859,855,860,861,856,862,864,854,517,812,813,828,829,830,831,832,833,834,835,836,846,847,848,849,837,841,842,843,844,845,838,839,814,820,821,822,823,651,815,816,824,825,826,827,817,818,852,850,851,819,941,949,950,951,524,523,587,543,544,545,546,525,526,588,547,548,549,550,551,552,528,589,529,530,531,532,533,527,590,553,554,555,556,557,558,539,540,686,687,688,689,690,566,680,681,682,683,684,685,542,559,560,591,567,568,569,570,561,592,578,579,580,583,584,585,586,562,593,594,595,596,598,599,571,572,667,668,669,670,671,672,674,673,675,676,677,678,573,642,643,644,645,646,647,735,521,740,712,713,733,732,741,742,743,744,745,746,747,710,748,751,752,753,754,755,756,650,709,757,764,765,766,767,768,758,769,770,771,772,773,760,778,779,780,781,785,782,786,787,788,789,790,783,784,759,804,805,711,802,806,807,808,809,810,811,541,960,520]', '[]', 3, 1, 0, 1, 0, '2020-08-03 21:32:14', '2020-08-03 21:32:14', '');
INSERT INTO `auth_rule` VALUES (27, 'home', 1, '超级管理员', '[]', '[]', 3, 0, 0, 1, 0, '2020-08-03 21:32:15', '2020-08-03 21:32:15', '');
INSERT INTO `auth_rule` VALUES (28, 'home', 2, '普通管理员', '[]', '[]', 2, 0, 0, 1, 0, NULL, NULL, '');
COMMIT;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '命名空间, 字母',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '配置名, 字母',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '可读配置名',
  `remark` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '备注',
  `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '配置规则描述',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '具体配置值 key:value',
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '权限',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_need_form` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用表单：0，否；1，是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`name`,`namespace`),
  KEY `namespace` (`namespace`),
  KEY `update_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='通用配置';

-- ----------------------------
-- Records of config
-- ----------------------------
BEGIN;
INSERT INTO `config` VALUES (1, 'system', 'namespace', '可用空间', '系统模块', NULL, '{\"system\":\"\\u7cfb\\u7edf\",\"common\":\"\\u901a\\u7528\",\"agent\":\"\\u8fd0\\u8425\\u5546\",\"service\":\"\\u670d\\u52a1\\u5546\",\"supplier\":\"\\u4f9b\\u8d27\\u65b9\",\"merchant\":\"\\u5546\\u6237\",\"seller\":\"\\u9500\\u552e\\u65b9\"}', NULL, '2019-10-18 16:47:52', '2020-08-02 21:42:42', 0);
INSERT INTO `config` VALUES (2, 'system', 'website_config', '站点配置', '', '{\"open_export|\\u5f00\\u542f\\u5bfc\\u51fa\":{\"type\":\"switch\"},\"navbar_notice|\\u5168\\u5c40\\u63d0\\u9192\":\"\",\"system_module|\\u7cfb\\u7edf\\u6a21\\u5757\":{\"type\":\"sub-form\",\"children\":{\"icon\":{\"type\":\"icon-select\"},\"name\":\"\",\"label\":\"\",\"indexUrl\":\"\"},\"repeat\":true,\"props\":{\"sort\":true}},\"open_screen_lock|\\u95f2\\u7f6e\\u9501\\u5c4f\":{\"type\":\"switch\"},\"screen_autho_lock_time|\\u95f2\\u7f6e\\u9501\\u5c4f\\u65f6\\u957f\":{\"type\":\"number\",\"info\":\"\\u5355\\u4f4d\\u79d2\"}}', '{\"open_export\":0,\"navbar_notice\":\"\\u6b22\\u8fce\\u4f7f\\u7528\",\"system_module\":[{\"icon\":\"el-icon-setting\",\"name\":\"system\",\"label\":\"\\u7cfb\\u7edf\",\"indexUrl\":\"\\/system\\/#\\/dashboard\"},{\"icon\":\"eye-open\",\"name\":\"default\",\"label\":\"\\u9996\\u98751\",\"indexUrl\":\"\\/default\\/#\\/dashboard\"}],\"open_screen_lock\":0,\"screen_autho_lock_time\":36}', NULL, '2020-03-17 08:29:10', '2020-08-02 14:23:31', 1);
INSERT INTO `config` VALUES (3, 'system', 'permissions', '公共权限', '', '{\"open_api|\\u516c\\u5171\\u8d44\\u6e90\":{\"rule\":\"array\",\"type\":\"table-transfer\",\"props\":{\"tableHeader\":[{\"title\":\"\\u63a7\\u5236\\u5668\",\"field\":\"controller\"},{\"title\":\"\\u65b9\\u6cd5\",\"field\":\"action\"},{\"title\":\"\\u8bf7\\u6c42\\u65b9\\u5f0f\",\"field\":\"http_method\"}],\"remoteApi\":\"\\/menu\\/getOpenApis?field=open_api\"}},\"user_open_api|\\u7528\\u6237\\u5f00\\u653e\\u8d44\\u6e90\":{\"rule\":\"array\",\"type\":\"table-transfer\",\"props\":{\"tableHeader\":[{\"title\":\"\\u63a7\\u5236\\u5668\",\"field\":\"controller\"},{\"title\":\"\\u65b9\\u6cd5\",\"field\":\"action\"},{\"title\":\"\\u8bf7\\u6c42\\u65b9\\u5f0f\",\"field\":\"http_method\"}],\"remoteApi\":\"\\/menu\\/getOpenApis?field=user_open_api\"}}}', '{\"open_api\":[\"POST::\\/user\\/login\",\"GET::\\/tools\\/app\\/configs\",\"POST::\\/upload\\/image\"],\"user_open_api\":[\"GET::\\/user\\/menu\",\"GET::\\/search\\/user\",\"GET::\\/search\\/mall\",\"GET::\\/search\\/goods\",\"GET::\\/search\\/category\",\"GET::\\/search\\/activity\",\"GET::\\/search\\/special\",\"GET::\\/search\\/couponbrand\",\"GET::\\/search\\/saleattr\",\"GET::\\/search\\/commonspec\",\"GET::\\/search\\/allact\",\"GET::\\/search\\/channel\",\"GET::\\/search\\/weblink\",\"GET::\\/search\\/topicgroup\",\"GET::\\/search\\/coupon\",\"GET::\\/search\\/buyer\",\"GET::\\/search\\/coupon\\/onsale\",\"GET::\\/system\\/config\",\"GET::\\/system\\/state\",\"GET::\\/user\\/exports\",\"GET::\\/user\\/roles\",\"GET::\\/menu\\/getOpenApis\",\"GET::\\/coupongetgroup\\/searchgroup\",\"GET::\\/lucky\\/prize\\/search\",\"POST::\\/upload\\/image\",\"GET::\\/upload\\/ossprivateurl\",\"GET::\\/search\\/aftersale\\/reason\",\"GET::\\/search\\/aftersale\\/reason\\/outer\",\"GET::\\/buyer_group\\/act\",\"GET::\\/search\\/brands\"]}', NULL, '2020-03-29 15:47:19', '2020-06-08 16:02:43', 1);
INSERT INTO `config` VALUES (4, 'agent', 'agent', '运营商配置', '', '{\"open_export|\\u5f00\\u542f\\u5bfc\\u51fa\":{\"type\":\"switch\"},\"navbar_notice|\\u5168\\u5c40\\u63d0\\u9192\":\"\",\"open_screen_lock|\\u95f2\\u7f6e\\u9501\\u5c4f\":{\"type\":\"switch\"},\"screen_autho_lock_time|\\u95f2\\u7f6e\\u9501\\u5c4f\\u65f6\\u957f\":{\"type\":\"number\",\"info\":\"\\u5355\\u4f4d\\u79d2\"}}', '\"\"', NULL, '2020-08-02 15:06:02', '2020-08-02 15:09:10', 1);
COMMIT;

-- ----------------------------
-- Table structure for front_routes
-- ----------------------------
DROP TABLE IF EXISTS `front_routes`;
CREATE TABLE `front_routes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'label名称',
  `module` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '模块',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '路径',
  `view` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '非脚手架渲染是且path路径为正则时, vue文件路径',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'icon',
  `open_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '打开方式 0 当前页面 2 新标签页 3URL',
  `is_scaffold` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否脚手架渲染, 1是, 0否',
  `is_menu` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否菜单 0 否 1 是',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0 禁用 1 启用',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大越在前面',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '权限标识',
  `http_method` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '请求方式; 0, Any; 1, GET; 2, POST; 3, PUT; 4, DELETE;',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '菜单类型 0 目录  1 菜单 2 其他',
  `page_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '页面类型： 0 列表  1 表单',
  `scaffold_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '脚手架预置权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='前端路由(菜单)';

-- ----------------------------
-- Records of front_routes
-- ----------------------------
BEGIN;
INSERT INTO `front_routes` VALUES (1, 0, '系统管理', 'system', '#', '', 'el-icon-s-tools', 0, 1, 1, 1, 100, '2020-03-27 10:53:43', '2020-08-01 20:33:27', '', 0, 0, 0, '\'\'');
INSERT INTO `front_routes` VALUES (2, 93, '菜单配置', 'system', '/menu/list', '', 'nested', 0, 1, 1, 1, 99, '2020-03-28 22:16:42', '2020-08-03 16:46:36', NULL, 1, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (3, 2, '新建', 'system', '/menu/form', '', '', 0, 1, 0, 1, 99, '2020-03-28 22:16:42', '2020-03-29 20:03:46', '/menu/form', 1, 1, 1, '');
INSERT INTO `front_routes` VALUES (4, 2, '编辑', 'system', '/menu/:id', '', '', 0, 1, 0, 1, 98, '2020-03-28 22:16:43', '2020-03-28 22:16:43', '/menu/{id:\\d+}', 1, 1, 0, '');
INSERT INTO `front_routes` VALUES (5, 2, '删除', 'system', '', '', '', 0, 0, 0, 1, 96, '2020-03-28 22:16:43', '2020-03-28 22:16:43', '/menu/delete', 2, 2, 0, '');
INSERT INTO `front_routes` VALUES (6, 2, '导入', 'system', '', '', '', 0, 0, 0, 1, 95, '2020-03-28 22:16:44', '2020-03-28 22:16:44', '/menu/import', 2, 2, 0, '');
INSERT INTO `front_routes` VALUES (7, 2, '导出', 'system', '', '', '', 0, 0, 0, 1, 94, '2020-03-28 22:16:44', '2020-03-28 22:16:44', '/menu/export', 2, 2, 0, '');
INSERT INTO `front_routes` VALUES (8, 2, '行编辑', 'system', '', '', '', 0, 0, 0, 1, 97, '2020-03-28 22:19:18', '2020-03-28 22:19:18', '/menu/rowchange/{id:\\d+}', 2, 2, 0, '');
INSERT INTO `front_routes` VALUES (9, 1, '通用配置', 'system', '/cconf/list', '', 'el-icon-s-tools', 0, 1, 1, 1, 98, '2020-06-10 06:45:13', '2020-06-10 07:14:10', '', 0, 1, 0, '{\"list\":10,\"create\":11,\"edit\":12,\"delete\":13}');
INSERT INTO `front_routes` VALUES (10, 9, '列表', 'system', '', '', '', 0, 0, 0, 1, 99, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'GET::/cconf/info,GET::/cconf/list,GET::/cconf/list.json,GET::/cconf/childs/{id:\\d+},GET::/cconf/act,GET::/cconf/notice', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (11, 9, '新建', 'system', '/cconf/form', '', '', 0, 1, 0, 1, 98, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'GET::/cconf/form,GET::/cconf/form.json,POST::/cconf/form', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (12, 9, '编辑', 'system', '/cconf/:id', '', '', 0, 1, 0, 1, 97, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'GET::/cconf/{id:\\d+},GET::/cconf/{id:\\d+}.json,POST::/cconf/{id:\\d+},GET::/cconf/newversion/{id:\\d+}/{last_ver_id:\\d+}', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (13, 9, '删除', 'system', '', '', '', 0, 0, 0, 1, 96, '2020-06-10 06:45:13', '2020-06-10 06:45:13', 'POST::/cconf/delete', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (14, 9, '表单预览', 'system', '/cconf/cconf_(.*)', '', '', 0, 1, 0, 1, 99, '2020-06-10 07:10:29', '2020-06-10 07:13:35', 'POST::/cconf/form,GET::/cconf/{id:\\d+}', 0, 1, 0, '\"\"');
INSERT INTO `front_routes` VALUES (15, 93, '用户管理', 'system', '/user/list', '', 'user', 0, 1, 1, 1, 99, '2020-06-10 08:03:01', '2020-08-03 17:12:23', NULL, 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (16, 15, '列表', 'system', '', '', '', 0, 0, 0, 1, 99, '2020-06-10 08:03:01', '2020-06-10 16:03:34', 'GET::/user/info,GET::/user/list,GET::/user/list.json,GET::/user/childs/{id:\\d+},GET::/user/act,GET::/user/notice', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (17, 15, '新建', 'system', '/user/form', '', '', 0, 1, 0, 1, 98, '2020-06-10 08:03:01', '2020-06-10 16:03:35', 'GET::/user/form,GET::/user/form.json,POST::/user/form', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (18, 15, '编辑', 'system', '/user/:id', '', '', 0, 1, 0, 1, 97, '2020-06-10 08:03:01', '2020-06-10 16:03:36', 'GET::/user/{id:\\d+},GET::/user/{id:\\d+}.json,POST::/user/{id:\\d+},GET::/user/newversion/{id:\\d+}/{last_ver_id:\\d+}', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (19, 15, '行编辑', 'system', '', '', '', 0, 0, 0, 1, 96, '2020-06-10 08:03:01', '2020-06-10 16:03:38', 'POST::/user/rowchange/{id:\\d+}', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (20, 15, '删除', 'system', '', '', '', 0, 0, 0, 1, 95, '2020-06-10 08:03:01', '2020-06-10 16:03:39', 'POST::/user/delete', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (21, 15, '导出', 'system', '', '', '', 0, 0, 0, 1, 94, '2020-06-10 08:03:01', '2020-06-10 16:03:41', 'POST::/user/export', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (32, 0, '开发工具', 'system', '#', '', 'oms-icon-tool', 0, 1, 1, 1, 99, '2020-06-10 09:50:21', '2020-08-03 18:57:49', '', 0, 0, 0, '\"\"');
INSERT INTO `front_routes` VALUES (33, 32, '代码生成', 'system', '/devtools/maker', '/devtools/maker', 'el-icon-star-off', 0, 0, 1, 1, 97, '2020-06-10 09:52:23', '2020-08-03 18:57:45', '[\"GET::\\/api\\/dev\\/controllermaker\",\"GET::\\/api\\/dev\\/make\",\"GET::\\/api\\/dev\\/dbAct\",\"GET::\\/api\\/dev\\/tableAct\",\"GET::\\/api\\/dev\\/transType\",\"GET::\\/api\\/dev\\/tableSchema\",\"POST::\\/api\\/dev\\/controllermaker\",\"POST::\\/api\\/dev\\/make\",\"POST::\\/api\\/dev\\/dbAct\",\"POST::\\/api\\/dev\\/tableAct\",\"POST::\\/api\\/dev\\/tableSchema\",\"POST::\\/api\\/dev\\/transType\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (78, 93, '角色管理', 'system', '/auth_rule/list', '', 'eye-open', 0, 1, 1, 1, 99, '2020-06-16 03:05:50', '2020-08-03 17:13:21', NULL, 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (79, 78, '列表', 'system', '', '', '', 0, 0, 0, 1, 99, '2020-06-16 03:05:50', '2020-06-16 11:13:59', 'GET::/role/info,GET::/role/list,GET::/role/list.json,GET::/role/childs/{id:\\d+},GET::/role/act,GET::/role/notice', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (80, 78, '新建', 'system', '/auth_rule/form', '', '', 0, 1, 0, 1, 98, '2020-06-16 03:05:50', '2020-08-03 14:18:33', 'GET::/role/form,GET::/role/form.json,POST::/role/form', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (81, 78, '编辑', 'system', '/auth_rule/:id', '', '', 0, 1, 0, 1, 97, '2020-06-16 03:05:50', '2020-08-03 14:18:41', 'GET::/role/{id:\\d+},GET::/role/{id:\\d+}.json,POST::/role/{id:\\d+},GET::/role/newversion/{id:\\d+}/{last_ver_id:\\d+}', 0, 1, 0, '');
INSERT INTO `front_routes` VALUES (82, 78, '删除', 'system', '', '', '', 0, 0, 0, 1, 96, '2020-06-16 03:05:50', '2020-06-16 11:14:05', 'POST::/role/delete', 0, 2, 0, '');
INSERT INTO `front_routes` VALUES (88, 0, '接口文档', 'system', '#', '', 'eye-open', 0, 0, 1, 1, 98, '2020-08-03 11:22:27', '2020-08-03 17:13:39', '[]', 0, 0, 0, '[]');
INSERT INTO `front_routes` VALUES (92, 88, '接口文档', 'system', 'http://127.0.0.1:9501/swagger/index', '', 'form', 3, 1, 1, 1, 99, '2020-08-03 13:35:16', '2020-08-03 21:56:31', '[\"GET::\\/swagger\\/index\",\"GET::\\/swagger\",\"GET::\\/swagger\\/api\"]', 0, 0, 0, '[]');
INSERT INTO `front_routes` VALUES (93, 0, '权限管理', 'system', '#', '', 'tree', 0, 1, 1, 1, 99, '2020-08-03 16:40:44', '2020-08-03 16:40:44', '[]', 0, 0, 0, '[]');
INSERT INTO `front_routes` VALUES (96, 32, '验证器生成', 'system', '/devtools/validate', '/devtools/maker', 'el-icon-s-help', 0, 0, 1, 1, 95, '2020-08-03 17:17:21', '2020-08-03 18:57:55', '[\"GET::\\/api\\/dev\\/make\",\"GET::\\/api\\/dev\\/controllermaker\",\"GET::\\/api\\/dev\\/dbAct\",\"GET::\\/api\\/dev\\/tableAct\",\"GET::\\/api\\/dev\\/tableSchema\",\"GET::\\/api\\/dev\\/transType\",\"POST::\\/api\\/dev\\/controllermaker\",\"POST::\\/api\\/dev\\/make\",\"POST::\\/api\\/dev\\/dbAct\",\"POST::\\/api\\/dev\\/tableAct\",\"POST::\\/api\\/dev\\/transType\",\"POST::\\/api\\/dev\\/tableSchema\"]', 0, 1, 0, '[]');
INSERT INTO `front_routes` VALUES (97, 32, '控制器生成', 'system', '/devtools/controller', '/devtools/maker', 'el-icon-circle-plus', 0, 0, 1, 1, 99, '2020-08-03 18:00:00', '2020-08-03 18:01:04', '[\"GET::\\/api\\/dev\\/maker\",\"GET::\\/api\\/dev\\/tableAct\",\"GET::\\/api\\/dev\\/validate\",\"GET::\\/api\\/dev\\/make\",\"GET::\\/api\\/dev\\/dbAct\",\"GET::\\/api\\/dev\\/tableSchema\",\"GET::\\/api\\/dev\\/transType\",\"POST::\\/api\\/dev\\/maker\",\"POST::\\/api\\/dev\\/dbAct\",\"POST::\\/api\\/dev\\/make\",\"POST::\\/api\\/dev\\/validate\",\"POST::\\/api\\/dev\\/tableAct\",\"POST::\\/api\\/dev\\/tableSchema\",\"POST::\\/api\\/dev\\/transType\"]', 0, 1, 0, '[]');
COMMIT;

-- ----------------------------
-- Table structure for role_menus
-- ----------------------------
DROP TABLE IF EXISTS `role_menus`;
CREATE TABLE `role_menus` (
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `router_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '路由ID',
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `role_router_id` (`role_id`,`router_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='角色菜单';

-- ----------------------------
-- Records of role_menus
-- ----------------------------
BEGIN;
INSERT INTO `role_menus` VALUES (1, 82, '2020-06-16 03:20:34', '2020-06-16 03:20:34');
COMMIT;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `permissions` text NOT NULL COMMENT '角色拥有的权限',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0 禁用 1 启用',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序，数字越大越在前面',
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='角色表';

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO `roles` VALUES (1, 0, '超管', '\"[[\\\"system\\\",1,2,3],[\\\"system\\\",1,2,63],[\\\"system\\\",1,2,57],[\\\"system\\\",1,2,4],[\\\"system\\\",1,2,64],[\\\"system\\\",1,2,58],[\\\"system\\\",1,2,65],[\\\"system\\\",1,2,59],[\\\"system\\\",1,2,8],[\\\"system\\\",1,2,60],[\\\"system\\\",1,2,66],[\\\"system\\\",1,2,5],[\\\"system\\\",1,2,61],[\\\"system\\\",1,2,6],[\\\"system\\\",1,2,67],[\\\"system\\\",1,2,7],[\\\"system\\\",1,2,62],[\\\"system\\\",1,2,68],[\\\"system\\\",1,78,79],[\\\"system\\\",1,78,80],[\\\"system\\\",1,78,81],[\\\"system\\\",1,78,82],[\\\"system\\\",1,15,16],[\\\"system\\\",1,15,17],[\\\"system\\\",1,15,18],[\\\"system\\\",1,15,19],[\\\"system\\\",1,15,20],[\\\"system\\\",1,15,21],[\\\"system\\\",1,9,10],[\\\"system\\\",1,9,14],[\\\"system\\\",1,9,11],[\\\"system\\\",1,9,12],[\\\"system\\\",1,9,13],[\\\"system\\\",34,49,50],[\\\"system\\\",34,49,51],[\\\"system\\\",34,49,52],[\\\"system\\\",34,49,53],[\\\"system\\\",34,49,54],[\\\"system\\\",34,49,55],[\\\"system\\\",34,49,56],[\\\"system\\\",34,42,43],[\\\"system\\\",34,42,44],[\\\"system\\\",34,42,45],[\\\"system\\\",34,42,46],[\\\"system\\\",34,42,47],[\\\"system\\\",34,42,48],[\\\"system\\\",34,35,41],[\\\"system\\\",34,35,36],[\\\"system\\\",34,35,37],[\\\"system\\\",34,35,38],[\\\"system\\\",34,35,39],[\\\"system\\\",34,35,40],[\\\"system\\\",32,33],[\\\"system\\\",22,23,24],[\\\"system\\\",22,25,26],[\\\"system\\\",22,25,27],[\\\"system\\\",22,25,28],[\\\"system\\\",22,25,29],[\\\"system\\\",22,25,30],[\\\"system\\\",22,25,31]]\"', 1, 0, '2020-06-16 03:20:34', '2020-06-16 03:20:34');
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
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='后台用户';

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'daodao', '刀刀', '1ab3ea2f16515d5ac4c1b2194f5610e45ad3e0d6', '', '', 1, '2020-05-26 21:02:32', NULL, 1, 1, '', '1', '', 'http://qupinapptest.oss-cn-beijing.aliyuncs.com/1/202002/d81d3c9dc7c3df7590d333f783a97995.jpeg', '', '2017-12-12 10:49:11', '2020-06-15 13:46:24');
COMMIT;

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_role_id` (`user_id`,`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户角色';

-- ----------------------------
-- Records of user_role
-- ----------------------------
BEGIN;
INSERT INTO `user_role` VALUES (1, 1, 1, '2020-06-16 03:20:34', '2020-06-16 03:20:34');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
