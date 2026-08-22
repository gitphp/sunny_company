/*
 Navicat Premium Dump SQL

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80046 (8.0.46)
 Source Host           : localhost:3306
 Source Schema         : sunny_company

 Target Server Type    : MySQL
 Target Server Version : 80046 (8.0.46)
 File Encoding         : 65001

 Date: 22/08/2026 15:40:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ad_material
-- ----------------------------
DROP TABLE IF EXISTS `ad_material`;
CREATE TABLE `ad_material`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `position_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的广告位ID',
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '广告标题/内部备注',
  `image_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '广告图片URL',
  `link_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '点击跳转链接',
  `target` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_blank' COMMENT '打开方式',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT '排序权重，值越小越靠前',
  `start_time` datetime NULL DEFAULT NULL COMMENT '生效开始时间',
  `end_time` datetime NULL DEFAULT NULL COMMENT '生效结束时间',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0-下线，1-上线',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_position_sort`(`position_id` ASC, `sort_order` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ad_material
-- ----------------------------
INSERT INTO `ad_material` VALUES (349410722486292480, 349410721982976000, '阳光管理系统上线', '/storage/ads/2026/08/22/349452948864110592.jpg', '/admin', '_self', 10, NULL, NULL, 1, '2026-08-22 04:33:27', '2026-08-22 07:21:17');
INSERT INTO `ad_material` VALUES (349453054296330240, 349410721982976000, '轮播图2', '/storage/ads/2026/08/22/349453032066519040.jpg', '/admin', '_blank', 0, NULL, NULL, 1, '2026-08-22 07:21:40', '2026-08-22 07:21:51');

-- ----------------------------
-- Table structure for ad_position
-- ----------------------------
DROP TABLE IF EXISTS `ad_position`;
CREATE TABLE `ad_position`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `pos_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '广告位名称',
  `pos_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '广告位唯一标识',
  `pos_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '广告位描述/备注',
  `ad_width` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '建议广告宽度（像素）',
  `ad_height` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '建议广告高度（像素）',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-正常',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_pos_code`(`pos_code` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ad_position
-- ----------------------------
INSERT INTO `ad_position` VALUES (349410721982976000, '首页顶部轮播图', 'home_top_banner', '官网首页顶部轮播', 1920, 600, 1, '2026-08-22 04:33:27', '2026-08-22 04:33:27');
INSERT INTO `ad_position` VALUES (349410722234634240, '首页侧边广告', 'home_side_ad', '官网首页侧栏', 300, 250, 1, '2026-08-22 04:33:27', '2026-08-22 04:33:27');

-- ----------------------------
-- Table structure for ai_provider
-- ----------------------------
DROP TABLE IF EXISTS `ai_provider`;
CREATE TABLE `ai_provider`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `provider_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '显示名称',
  `driver` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'openai' COMMENT '协议驱动，openai=兼容接口',
  `base_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '接口地址',
  `api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '接口密钥',
  `model` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模型名称',
  `temperature` decimal(3, 2) NOT NULL DEFAULT 0.70 COMMENT '温度',
  `max_tokens` int UNSIGNED NOT NULL DEFAULT 2048 COMMENT '最大输出长度',
  `system_prompt` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统提示词',
  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '0=禁用，1=启用',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ai_provider
-- ----------------------------
INSERT INTO `ai_provider` VALUES (349414922800730112, 'DeepSeek', 'openai', 'https://api.deepseek.com', '', 'deepseek-chat', 0.70, 2048, '你是名杨科技管理系统的智能助手，回答简洁、准确。', 1, 1, 30, '2026-08-22 04:50:09', '2026-08-22 04:50:09');
INSERT INTO `ai_provider` VALUES (349414923039805440, 'OpenAI', 'openai', 'https://api.openai.com/v1', '', 'gpt-4o-mini', 0.70, 2048, '', 0, 1, 20, '2026-08-22 04:50:09', '2026-08-22 04:50:09');
INSERT INTO `ai_provider` VALUES (349414923278880768, '通义千问', 'openai', 'https://dashscope.aliyuncs.com/compatible-mode/v1', '', 'qwen-plus', 0.70, 2048, '', 0, 1, 10, '2026-08-22 04:50:09', '2026-08-22 04:50:09');

-- ----------------------------
-- Table structure for article_category
-- ----------------------------
DROP TABLE IF EXISTS `article_category`;
CREATE TABLE `article_category`  (
  `id` bigint UNSIGNED NOT NULL,
  `cat_type` tinyint NOT NULL DEFAULT 0 COMMENT '分类类型 0=文章分类 1=导航分类',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID，0表示顶级',
  `cat_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `cat_url` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'URL别名',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类描述',
  `cat_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE,
  INDEX `idx_cat_type`(`cat_type` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of article_category
-- ----------------------------
INSERT INTO `article_category` VALUES (349402375494176768, 0, 0, '公司新闻', 'company-news', '', 90, 1, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `article_category` VALUES (349402375754223616, 0, 0, '产品动态', 'product', '', 80, 1, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `article_category` VALUES (349402376010076160, 0, 0, '行业资讯', 'industry', '', 70, 1, '2026-08-22 04:00:18', '2026-08-22 04:00:18', NULL);
INSERT INTO `article_category` VALUES (349402376274317312, 1, 0, '关于我们', 'about', '', 60, 1, '2026-08-22 04:00:18', '2026-08-22 04:00:18', NULL);

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章标题',
  `subtitle` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '副标题/摘要',
  `art_cover` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '封面图URL',
  `art_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '文章正文内容',
  `content_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '内容类型：1=富文本，2=Markdown，3=纯文本',
  `summary` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章摘要',
  `category_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类ID',
  `tag_ids` json NULL COMMENT '标签ID列表',
  `author_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '作者用户ID',
  `author_name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '作者姓名',
  `source` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文章来源',
  `source_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '原文链接',
  `art_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1草稿 2待审核 3审核通过 4已发布 5已下线 6审核驳回 7回收站',
  `is_top` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否置顶',
  `is_original` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否原创',
  `is_commentable` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许评论',
  `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `extra_fields` json NULL COMMENT '扩展字段',
  `view_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
  `like_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '点赞量',
  `collect_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏量',
  `share_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享量',
  `comment_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论量',
  `published_at` datetime NULL DEFAULT NULL COMMENT '发布时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '软删除时间',
  `reviewer_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核人ID',
  `reviewed_at` datetime NULL DEFAULT NULL COMMENT '审核时间',
  `reject_reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '驳回原因',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_author_id`(`author_id` ASC) USING BTREE,
  INDEX `idx_category_id`(`category_id` ASC) USING BTREE,
  INDEX `idx_status`(`art_status` ASC) USING BTREE,
  INDEX `idx_is_top`(`is_top` ASC) USING BTREE,
  INDEX `idx_published_at`(`published_at` ASC) USING BTREE,
  INDEX `idx_created_at`(`created_at` ASC) USING BTREE,
  INDEX `idx_deleted_at`(`deleted_at` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of articles
-- ----------------------------
INSERT INTO `articles` VALUES (349402424781443072, '阳光科技正式上线阳光管理系统', '统一后台，提升协同效率', '', '<p>阳光管理系统已正式上线，覆盖用户、角色、部门、岗位与官网内容管理。</p>', 1, '阳光管理系统正式上线，支持组织架构与官网内容一体化管理。', 349402375494176768, '[]', 934035802554576899, '管理员', '原创', '', 4, 1, 1, 1, '阳光科技正式上线阳光管理系统', '阳光科技,管理系统', '阳光管理系统正式上线。', NULL, 0, 0, 0, 0, 0, '2026-08-22 04:00:29', '2026-08-22 04:00:29', '2026-08-22 04:00:29', NULL, 0, NULL, NULL);
INSERT INTO `articles` VALUES (349456122916245504, '名杨科技管理系统正式上线', '统一后台，提升协同效率', '', '<p>名杨科技管理系统已正式上线，覆盖用户、角色、部门、岗位与官网内容管理，帮助团队把日常协作收拢到同一套流程里。</p>', 1, '名杨科技管理系统正式上线，支持组织架构与官网内容一体化管理。', 349402375494176768, '[]', 934035802554576899, '管理员', '原创', '', 4, 1, 1, 1, '名杨科技管理系统正式上线', '名杨科技', '名杨科技管理系统正式上线，支持组织架构与官网内容一体化管理。', NULL, 0, 0, 0, 0, 0, '2026-08-22 07:33:52', '2026-08-22 07:33:52', '2026-08-22 07:33:52', NULL, 0, NULL, NULL);
INSERT INTO `articles` VALUES (349456123591528448, '家居行业向绿色材料与智能制造加速迈进', '材料、工艺与体验正在被重新定义', '', '<p>行业正从规模扩张转向品质与可持续。企业更关注材料来源、结构寿命，以及产品进入空间后的真实使用体验。</p><p>名杨科技持续跟进这一趋势，把工艺沉淀落实到每一件产品上。</p>', 1, '绿色材料、柔性生产和更完整的交付体验，正在成为家居制造的新主线。', 349402376010076160, '[]', 934035802554576899, '管理员', '原创', '', 4, 0, 1, 1, '家居行业向绿色材料与智能制造加速迈进', '名杨科技', '绿色材料、柔性生产和更完整的交付体验，正在成为家居制造的新主线。', NULL, 0, 0, 0, 0, 0, '2026-08-22 07:33:52', '2026-08-22 07:33:52', '2026-08-22 07:33:52', NULL, 0, NULL, NULL);

-- ----------------------------
-- Table structure for auth_menus
-- ----------------------------
DROP TABLE IF EXISTS `auth_menus`;
CREATE TABLE `auth_menus`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级菜单ID, 0表示顶级菜单',
  `menu_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称，如：用户管理',
  `menu_icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单图标，如：el-icon-user',
  `menu_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端路由路径，如：/user/list',
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端组件路径，如：user/Index',
  `permission_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关联的权限标识，用于按钮级控制',
  `menu_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重，值越大越靠前',
  `menu_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0=禁用, 1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE,
  INDEX `idx_permission_code`(`permission_code` ASC) USING BTREE,
  INDEX `idx_status`(`menu_status` ASC) USING BTREE,
  INDEX `idx_deleted_at`(`deleted_at` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_menus
-- ----------------------------
INSERT INTO `auth_menus` VALUES (349393048200941568, 0, '首页', 'HomeFilled', '/index', 'dashboard/Index', '', 990, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393048444211200, 0, 'AI对话', 'ChatDotRound', '/ai', 'ai/Index', 'ai:chat', 980, 1, '2026-08-22 03:23:14', '2026-08-22 04:50:06', NULL);
INSERT INTO `auth_menus` VALUES (349393048695869440, 0, '系统管理', 'Setting', '/system', '', '', 900, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393048951721984, 349393048695869440, '用户管理', 'User', '/system/user', 'system/user/Index', 'system:user:list', 90, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393049182408704, 349393048951721984, '新增', '', '', '', 'system:user:add', 50, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393049438261248, 349393048951721984, '修改', '', '', '', 'system:user:edit', 40, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393049710891008, 349393048951721984, '删除', '', '', '', 'system:user:remove', 30, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393049962549248, 349393048951721984, '导入', '', '', '', 'system:user:import', 20, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393050197430272, 349393048951721984, '导出', '', '', '', 'system:user:export', 10, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393050449088512, 349393048951721984, '重置密码', '', '', '', 'system:user:resetPwd', 5, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393050675580928, 349393048695869440, '角色管理', 'UserFilled', '/system/role', 'system/role/Index', 'system:role:list', 80, 1, '2026-08-22 03:23:14', '2026-08-22 03:31:12', NULL);
INSERT INTO `auth_menus` VALUES (349393050935627776, 349393048695869440, '菜单管理', 'Menu', '/system/menu', 'system/menu/Index', 'system:menu:list', 70, 1, '2026-08-22 03:23:14', '2026-08-22 03:31:13', NULL);
INSERT INTO `auth_menus` VALUES (349393051178897408, 349393048695869440, '部门管理', 'OfficeBuilding', '/system/dept', 'system/dept/Index', 'system:dept:list', 60, 1, '2026-08-22 03:23:14', '2026-08-22 03:31:13', NULL);
INSERT INTO `auth_menus` VALUES (349393051417972736, 349393048695869440, '岗位管理', 'Postcard', '/system/post', 'system/post/Index', 'system:post:list', 50, 1, '2026-08-22 03:23:14', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349393051648659456, 349393048695869440, '字典管理', 'Collection', '/system/dict', 'placeholder/Index', '', 40, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393051900317696, 349393048695869440, '参数设置', 'EditPen', '/system/config', 'placeholder/Index', '', 30, 1, '2026-08-22 03:23:14', '2026-08-22 03:23:14', NULL);
INSERT INTO `auth_menus` VALUES (349393052156170240, 349393048695869440, '通知公告', 'Bell', '/system/notice', 'placeholder/Index', '', 20, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393052403634176, 349393048695869440, '日志管理', 'Document', '/system/log', '', '', 10, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393052663681024, 349393052403634176, '操作日志', '', '/system/log/operlog', 'system/log/operlog/Index', 'system:operlog:list', 20, 1, '2026-08-22 03:23:15', '2026-08-22 04:10:00', NULL);
INSERT INTO `auth_menus` VALUES (349393052944699392, 349393052403634176, '登录日志', '', '/system/log/logininfor', 'placeholder/Index', '', 10, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393053259272192, 0, '系统监控', 'Monitor', '/monitor', '', '', 800, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393053548679168, 349393053259272192, '在线用户', '', '/monitor/online', 'placeholder/Index', '', 40, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393053800337408, 349393053259272192, '定时任务', '', '/monitor/job', 'placeholder/Index', '', 30, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393054031024128, 349393053259272192, '数据监控', '', '/monitor/druid', 'placeholder/Index', '', 20, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393054286876672, 349393053259272192, '服务监控', '', '/monitor/server', 'placeholder/Index', '', 10, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393054521757696, 0, '系统工具', 'Tools', '/tool', '', '', 700, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393054748250112, 349393054521757696, '表单构建', '', '/tool/build', 'placeholder/Index', '', 30, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393054970548224, 349393054521757696, '代码生成', '', '/tool/gen', 'placeholder/Index', '', 20, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393055192846336, 349393054521757696, '系统接口', '', '/tool/swagger', 'placeholder/Index', '', 10, 1, '2026-08-22 03:23:15', '2026-08-22 03:23:15', NULL);
INSERT INTO `auth_menus` VALUES (349393055427727360, 0, '阳光官网', 'Link', '/site', '', '', 100, 1, '2026-08-22 03:23:15', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349395056127512576, 349393050675580928, '新增', '', '', '', 'system:role:add', 50, 1, '2026-08-22 03:31:12', '2026-08-22 03:31:12', NULL);
INSERT INTO `auth_menus` VALUES (349395056555331584, 349393050675580928, '修改', '', '', '', 'system:role:edit', 40, 1, '2026-08-22 03:31:12', '2026-08-22 03:31:12', NULL);
INSERT INTO `auth_menus` VALUES (349395057016705024, 349393050675580928, '删除', '', '', '', 'system:role:remove', 30, 1, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `auth_menus` VALUES (349395057880731648, 349393051178897408, '新增', '', '', '', 'system:dept:add', 50, 1, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `auth_menus` VALUES (349395058304356352, 349393051178897408, '修改', '', '', '', 'system:dept:edit', 40, 1, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `auth_menus` VALUES (349395058715398144, 349393051178897408, '删除', '', '', '', 'system:dept:remove', 30, 1, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `auth_menus` VALUES (349402368175116288, 349393051417972736, '新增', '', '', '', 'system:post:add', 50, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402368598740992, 349393051417972736, '修改', '', '', '', 'system:post:edit', 40, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402369064308736, 349393051417972736, '删除', '', '', '', 'system:post:remove', 30, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402370196770816, 349393055427727360, '文章分类', 'FolderOpened', '/site/category', 'site/category/Index', 'cms:category:list', 20, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402370448429056, 349402370196770816, '新增', '', '', '', 'cms:category:add', 50, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402370926579712, 349402370196770816, '修改', '', '', '', 'cms:category:edit', 40, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402371362787328, 349402370196770816, '删除', '', '', '', 'cms:category:remove', 30, 1, '2026-08-22 04:00:16', '2026-08-22 04:00:16', NULL);
INSERT INTO `auth_menus` VALUES (349402372025487360, 349393055427727360, '文章管理', 'Document', '/site/article', 'site/article/Index', 'cms:article:list', 10, 1, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `auth_menus` VALUES (349402372264562688, 349402372025487360, '新增', '', '', '', 'cms:article:add', 50, 1, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `auth_menus` VALUES (349402372700770304, 349402372025487360, '修改', '', '', '', 'cms:article:edit', 40, 1, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `auth_menus` VALUES (349402373149560832, 349402372025487360, '删除', '', '', '', 'cms:article:remove', 30, 1, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `auth_menus` VALUES (349404818785308672, 349393052663681024, '删除', '', '', '', 'system:operlog:remove', 30, 1, '2026-08-22 04:10:00', '2026-08-22 04:10:00', NULL);
INSERT INTO `auth_menus` VALUES (349404819603197952, 349393055427727360, '网站配置', 'Setting', '/site/config', 'site/config/Index', 'cms:config:list', 40, 1, '2026-08-22 04:10:00', '2026-08-22 04:10:00', NULL);
INSERT INTO `auth_menus` VALUES (349404819854856192, 349404819603197952, '修改', '', '', '', 'cms:config:edit', 40, 1, '2026-08-22 04:10:00', '2026-08-22 04:10:00', NULL);
INSERT INTO `auth_menus` VALUES (349404822144946176, 349393055427727360, '友情链接', 'Connection', '/site/link', 'site/link/Index', 'cms:link:list', 8, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349404822384021504, 349404822144946176, '新增', '', '', '', 'cms:link:add', 50, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349404822816034816, 349404822144946176, '修改', '', '', '', 'cms:link:edit', 40, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349404823235465216, 349404822144946176, '删除', '', '', '', 'cms:link:remove', 30, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349404823877193728, 349393055427727360, '留言反馈', 'ChatLineSquare', '/site/feedback', 'site/feedback/Index', 'cms:feedback:list', 5, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349404824124657664, 349404823877193728, '回复', '', '', '', 'cms:feedback:reply', 40, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349404824565059584, 349404823877193728, '删除', '', '', '', 'cms:feedback:remove', 30, 1, '2026-08-22 04:10:01', '2026-08-22 04:10:01', NULL);
INSERT INTO `auth_menus` VALUES (349410713317543936, 349393055427727360, '广告位', 'PictureFilled', '/site/ad-position', 'site/ad/position/Index', 'cms:ad-position:list', 18, 1, '2026-08-22 04:33:25', '2026-08-22 04:33:25', NULL);
INSERT INTO `auth_menus` VALUES (349410713598562304, 349410713317543936, '新增', '', '', '', 'cms:ad-position:add', 50, 1, '2026-08-22 04:33:25', '2026-08-22 04:33:25', NULL);
INSERT INTO `auth_menus` VALUES (349410714072518656, 349410713317543936, '修改', '', '', '', 'cms:ad-position:edit', 40, 1, '2026-08-22 04:33:26', '2026-08-22 04:33:26', NULL);
INSERT INTO `auth_menus` VALUES (349410714533892096, 349410713317543936, '删除', '', '', '', 'cms:ad-position:remove', 30, 1, '2026-08-22 04:33:26', '2026-08-22 04:33:26', NULL);
INSERT INTO `auth_menus` VALUES (349410715204980736, 349393055427727360, '广告素材', 'Picture', '/site/ad-material', 'site/ad/material/Index', 'cms:ad-material:list', 16, 1, '2026-08-22 04:33:26', '2026-08-22 04:33:26', NULL);
INSERT INTO `auth_menus` VALUES (349410715452444672, 349410715204980736, '新增', '', '', '', 'cms:ad-material:add', 50, 1, '2026-08-22 04:33:26', '2026-08-22 04:33:26', NULL);
INSERT INTO `auth_menus` VALUES (349410715897040896, 349410715204980736, '修改', '', '', '', 'cms:ad-material:edit', 40, 1, '2026-08-22 04:33:26', '2026-08-22 04:33:26', NULL);
INSERT INTO `auth_menus` VALUES (349410716345831424, 349410715204980736, '删除', '', '', '', 'cms:ad-material:remove', 30, 1, '2026-08-22 04:33:26', '2026-08-22 04:33:26', NULL);
INSERT INTO `auth_menus` VALUES (349411854067568640, 349393055427727360, '招聘职位', 'Suitcase', '/site/job', 'site/job/Index', 'cms:job:list', 12, 1, '2026-08-22 04:37:57', '2026-08-22 04:37:57', NULL);
INSERT INTO `auth_menus` VALUES (349411854356975616, 349411854067568640, '新增', '', '', '', 'cms:job:add', 50, 1, '2026-08-22 04:37:57', '2026-08-22 04:37:57', NULL);
INSERT INTO `auth_menus` VALUES (349411854814154752, 349411854067568640, '修改', '', '', '', 'cms:job:edit', 40, 1, '2026-08-22 04:37:57', '2026-08-22 04:37:57', NULL);
INSERT INTO `auth_menus` VALUES (349411855292305408, 349411854067568640, '删除', '', '', '', 'cms:job:remove', 30, 1, '2026-08-22 04:37:58', '2026-08-22 04:37:58', NULL);
INSERT INTO `auth_menus` VALUES (349414912587599872, 349393048444211200, '模型配置', '', '', '', 'ai:config', 40, 1, '2026-08-22 04:50:07', '2026-08-22 04:50:07', NULL);
INSERT INTO `auth_menus` VALUES (349443444818710528, 0, '产品管理', 'Goods', '/product', '', '', 200, 1, '2026-08-22 06:43:29', '2026-08-22 06:43:29', NULL);
INSERT INTO `auth_menus` VALUES (349443445351387136, 349443444818710528, '商品管理', 'ShoppingCart', '/product/list', 'product/Index', 'product:list', 40, 1, '2026-08-22 06:43:29', '2026-08-22 06:43:29', NULL);
INSERT INTO `auth_menus` VALUES (349443445636599808, 349443445351387136, '新增', '', '', '', 'product:add', 50, 1, '2026-08-22 06:43:29', '2026-08-22 06:43:29', NULL);
INSERT INTO `auth_menus` VALUES (349443446106361856, 349443445351387136, '修改', '', '', '', 'product:edit', 40, 1, '2026-08-22 06:43:29', '2026-08-22 06:43:29', NULL);
INSERT INTO `auth_menus` VALUES (349443446550958080, 349443445351387136, '删除', '', '', '', 'product:remove', 30, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443447234629632, 349443444818710528, '商品分类', 'Menu', '/product/category', 'product/category/Index', 'product:category:list', 30, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443447490482176, 349443447234629632, '新增', '', '', '', 'product:category:add', 50, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443447989604352, 349443447234629632, '修改', '', '', '', 'product:category:edit', 40, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443448434200576, 349443447234629632, '删除', '', '', '', 'product:category:remove', 30, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443449122066432, 349443444818710528, '品牌管理', 'Medal', '/product/brand', 'product/brand/Index', 'product:brand:list', 20, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443449377918976, 349443449122066432, '新增', '', '', '', 'product:brand:add', 50, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443449843486720, 349443449122066432, '修改', '', '', '', 'product:brand:edit', 40, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443450321637376, 349443449122066432, '删除', '', '', '', 'product:brand:remove', 30, 1, '2026-08-22 06:43:30', '2026-08-22 06:43:30', NULL);
INSERT INTO `auth_menus` VALUES (349443450984337408, 349443444818710528, '规格管理', 'SetUp', '/product/spec', 'product/spec/Index', 'product:spec:list', 10, 1, '2026-08-22 06:43:31', '2026-08-22 06:43:31', NULL);
INSERT INTO `auth_menus` VALUES (349443451240189952, 349443450984337408, '新增', '', '', '', 'product:spec:add', 50, 1, '2026-08-22 06:43:31', '2026-08-22 06:43:31', NULL);
INSERT INTO `auth_menus` VALUES (349443451693174784, 349443450984337408, '修改', '', '', '', 'product:spec:edit', 40, 1, '2026-08-22 06:43:31', '2026-08-22 06:43:31', NULL);
INSERT INTO `auth_menus` VALUES (349443452162936832, 349443450984337408, '删除', '', '', '', 'product:spec:remove', 30, 1, '2026-08-22 06:43:31', '2026-08-22 06:43:31', NULL);

-- ----------------------------
-- Table structure for auth_permissions
-- ----------------------------
DROP TABLE IF EXISTS `auth_permissions`;
CREATE TABLE `auth_permissions`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级权限ID，用于树形结构',
  `per_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限名称，如：用户删除',
  `per_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限唯一标识，如：user:delete',
  `per_type` enum('menu','button','api') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api' COMMENT '权限类型：menu=菜单，button=按钮，api=接口',
  `per_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端路由路径或API路径',
  `per_method` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'HTTP方法，仅 type=api 时有效',
  `per_icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单图标，仅 type=menu 时有效',
  `per_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重，值越大越靠前',
  `per_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_code`(`per_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE,
  INDEX `idx_type`(`per_type` ASC) USING BTREE,
  INDEX `idx_status`(`per_status` ASC) USING BTREE,
  INDEX `idx_deleted_at`(`deleted_at` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_permissions
-- ----------------------------
INSERT INTO `auth_permissions` VALUES (349395062418968576, 0, '首页', 'index', 'menu', '/index', '', 'HomeFilled', 990, 1, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `auth_permissions` VALUES (349395063035531264, 0, 'AI对话', 'ai', 'menu', '/ai', '', 'ChatDotRound', 980, 1, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `auth_permissions` VALUES (349395063656288256, 0, '系统管理', 'system', 'menu', '/system', '', 'Setting', 900, 1, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `auth_permissions` VALUES (349395064298016768, 349395063656288256, '用户管理', 'system:user:list', 'menu', '/system/user', '', 'User', 90, 1, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `auth_permissions` VALUES (349395064927162368, 349395064298016768, '新增', 'system:user:add', 'button', '', '', '', 50, 1, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `auth_permissions` VALUES (349395065531142144, 349395064298016768, '修改', 'system:user:edit', 'button', '', '', '', 40, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395066164482048, 349395064298016768, '删除', 'system:user:remove', 'button', '', '', '', 30, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395066806210560, 349395064298016768, '导入', 'system:user:import', 'button', '', '', '', 20, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395067418578944, 349395064298016768, '导出', 'system:user:export', 'button', '', '', '', 10, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395068022558720, 349395064298016768, '重置密码', 'system:user:resetPwd', 'button', '', '', '', 5, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395068609761280, 349395063656288256, '角色管理', 'system:role:list', 'menu', '/system/role', '', 'UserFilled', 80, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395069213741056, 349395068609761280, '新增', 'system:role:add', 'button', '', '', '', 50, 1, '2026-08-22 03:31:15', '2026-08-22 03:31:15', NULL);
INSERT INTO `auth_permissions` VALUES (349395069830303744, 349395068609761280, '修改', 'system:role:edit', 'button', '', '', '', 40, 1, '2026-08-22 03:31:16', '2026-08-22 03:31:16', NULL);
INSERT INTO `auth_permissions` VALUES (349395070434283520, 349395068609761280, '删除', 'system:role:remove', 'button', '', '', '', 30, 1, '2026-08-22 03:31:16', '2026-08-22 03:31:16', NULL);
INSERT INTO `auth_permissions` VALUES (349395071059234816, 349395063656288256, '菜单管理', 'system:menu:list', 'menu', '/system/menu', '', 'Menu', 70, 1, '2026-08-22 03:31:16', '2026-08-22 03:31:16', NULL);
INSERT INTO `auth_permissions` VALUES (349395071654825984, 349395063656288256, '部门管理', 'system:dept:list', 'menu', '/system/dept', '', 'OfficeBuilding', 60, 1, '2026-08-22 03:31:16', '2026-08-22 03:31:16', NULL);
INSERT INTO `auth_permissions` VALUES (349395072275582976, 349395071654825984, '新增', 'system:dept:add', 'button', '', '', '', 50, 1, '2026-08-22 03:31:16', '2026-08-22 03:31:16', NULL);
INSERT INTO `auth_permissions` VALUES (349395072917311488, 349395071654825984, '修改', 'system:dept:edit', 'button', '', '', '', 40, 1, '2026-08-22 03:31:16', '2026-08-22 03:31:16', NULL);
INSERT INTO `auth_permissions` VALUES (349395073567428608, 349395071654825984, '删除', 'system:dept:remove', 'button', '', '', '', 30, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395074221740032, 349395063656288256, '岗位管理', 'system:post', 'menu', '/system/post', '', 'Postcard', 50, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395074863468544, 349395063656288256, '字典管理', 'system:dict', 'menu', '/system/dict', '', 'Collection', 40, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395075513585664, 349395063656288256, '参数设置', 'system:config', 'menu', '/system/config', '', 'EditPen', 30, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395076176285696, 349395063656288256, '通知公告', 'system:notice', 'menu', '/system/notice', '', 'Bell', 20, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395076809625600, 349395063656288256, '日志管理', 'system:log', 'menu', '/system/log', '', 'Document', 10, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395077438771200, 349395076809625600, '操作日志', 'system:log:operlog', 'menu', '/system/log/operlog', '', '', 20, 1, '2026-08-22 03:31:17', '2026-08-22 03:31:17', NULL);
INSERT INTO `auth_permissions` VALUES (349395078072111104, 349395076809625600, '登录日志', 'system:log:logininfor', 'menu', '/system/log/logininfor', '', '', 10, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395078684479488, 0, '系统监控', 'monitor', 'menu', '/monitor', '', 'Monitor', 800, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395079334596608, 349395078684479488, '在线用户', 'monitor:online', 'menu', '/monitor/online', '', '', 40, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395079946964992, 349395078684479488, '定时任务', 'monitor:job', 'menu', '/monitor/job', '', '', 30, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395080559333376, 349395078684479488, '数据监控', 'monitor:druid', 'menu', '/monitor/druid', '', '', 20, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395081188478976, 349395078684479488, '服务监控', 'monitor:server', 'menu', '/monitor/server', '', '', 10, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395081821818880, 0, '系统工具', 'tool', 'menu', '/tool', '', 'Tools', 700, 1, '2026-08-22 03:31:18', '2026-08-22 03:31:18', NULL);
INSERT INTO `auth_permissions` VALUES (349395082455158784, 349395081821818880, '表单构建', 'tool:build', 'menu', '/tool/build', '', '', 30, 1, '2026-08-22 03:31:19', '2026-08-22 03:31:19', NULL);
INSERT INTO `auth_permissions` VALUES (349395083096887296, 349395081821818880, '代码生成', 'tool:gen', 'menu', '/tool/gen', '', '', 20, 1, '2026-08-22 03:31:19', '2026-08-22 03:31:19', NULL);
INSERT INTO `auth_permissions` VALUES (349395083730227200, 349395081821818880, '系统接口', 'tool:swagger', 'menu', '/tool/swagger', '', '', 10, 1, '2026-08-22 03:31:19', '2026-08-22 03:31:19', NULL);
INSERT INTO `auth_permissions` VALUES (349395084342595584, 0, '阳光官网', 'site', 'menu', '/site', '', 'Link', 100, 1, '2026-08-22 03:31:19', '2026-08-22 03:31:19', NULL);
INSERT INTO `auth_permissions` VALUES (349402384390295552, 349395063656288256, '岗位管理', 'system:post:list', 'menu', '/system/post', '', 'Postcard', 50, 1, '2026-08-22 04:00:20', '2026-08-22 04:00:20', NULL);
INSERT INTO `auth_permissions` VALUES (349402385036218368, 349402384390295552, '新增', 'system:post:add', 'button', '', '', '', 50, 1, '2026-08-22 04:00:20', '2026-08-22 04:00:20', NULL);
INSERT INTO `auth_permissions` VALUES (349402385694724096, 349402384390295552, '修改', 'system:post:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:00:20', '2026-08-22 04:00:20', NULL);
INSERT INTO `auth_permissions` VALUES (349402386340646912, 349402384390295552, '删除', 'system:post:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:00:20', '2026-08-22 04:00:20', NULL);
INSERT INTO `auth_permissions` VALUES (349402393462575104, 349395084342595584, '文章分类', 'cms:category:list', 'menu', '/site/category', '', 'FolderOpened', 20, 1, '2026-08-22 04:00:22', '2026-08-22 04:00:22', NULL);
INSERT INTO `auth_permissions` VALUES (349402394108497920, 349402393462575104, '新增', 'cms:category:add', 'button', '', '', '', 50, 1, '2026-08-22 04:00:22', '2026-08-22 04:00:22', NULL);
INSERT INTO `auth_permissions` VALUES (349402394746032128, 349402393462575104, '修改', 'cms:category:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:00:22', '2026-08-22 04:00:22', NULL);
INSERT INTO `auth_permissions` VALUES (349402395375177728, 349402393462575104, '删除', 'cms:category:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:00:22', '2026-08-22 04:00:22', NULL);
INSERT INTO `auth_permissions` VALUES (349402396016906240, 349395084342595584, '文章管理', 'cms:article:list', 'menu', '/site/article', '', 'Document', 10, 1, '2026-08-22 04:00:22', '2026-08-22 04:00:22', NULL);
INSERT INTO `auth_permissions` VALUES (349402396729937920, 349402396016906240, '新增', 'cms:article:add', 'button', '', '', '', 50, 1, '2026-08-22 04:00:23', '2026-08-22 04:00:23', NULL);
INSERT INTO `auth_permissions` VALUES (349402397371666432, 349402396016906240, '修改', 'cms:article:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:00:23', '2026-08-22 04:00:23', NULL);
INSERT INTO `auth_permissions` VALUES (349402398013394944, 349402396016906240, '删除', 'cms:article:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:00:23', '2026-08-22 04:00:23', NULL);
INSERT INTO `auth_permissions` VALUES (349404843955326976, 349395076809625600, '操作日志', 'system:operlog:list', 'menu', '/system/log/operlog', '', '', 20, 1, '2026-08-22 04:10:06', '2026-08-22 04:10:06', NULL);
INSERT INTO `auth_permissions` VALUES (349404844655775744, 349404843955326976, '删除', 'system:operlog:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:10:06', '2026-08-22 04:10:06', NULL);
INSERT INTO `auth_permissions` VALUES (349404849781215232, 349395084342595584, '网站配置', 'cms:config:list', 'menu', '/site/config', '', 'Setting', 40, 1, '2026-08-22 04:10:07', '2026-08-22 04:10:07', NULL);
INSERT INTO `auth_permissions` VALUES (349404850443915264, 349404849781215232, '修改', 'cms:config:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:10:08', '2026-08-22 04:10:08', NULL);
INSERT INTO `auth_permissions` VALUES (349404854348812288, 349395084342595584, '友情链接', 'cms:link:list', 'menu', '/site/link', '', 'Connection', 8, 1, '2026-08-22 04:10:08', '2026-08-22 04:10:08', NULL);
INSERT INTO `auth_permissions` VALUES (349404855011512320, 349404854348812288, '新增', 'cms:link:add', 'button', '', '', '', 50, 1, '2026-08-22 04:10:09', '2026-08-22 04:10:09', NULL);
INSERT INTO `auth_permissions` VALUES (349404855690989568, 349404854348812288, '修改', 'cms:link:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:10:09', '2026-08-22 04:10:09', NULL);
INSERT INTO `auth_permissions` VALUES (349404856374661120, 349404854348812288, '删除', 'cms:link:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:10:09', '2026-08-22 04:10:09', NULL);
INSERT INTO `auth_permissions` VALUES (349404857075109888, 349395084342595584, '留言反馈', 'cms:feedback:list', 'menu', '/site/feedback', '', 'ChatLineSquare', 5, 1, '2026-08-22 04:10:09', '2026-08-22 04:10:09', NULL);
INSERT INTO `auth_permissions` VALUES (349404857716838400, 349404857075109888, '回复', 'cms:feedback:reply', 'button', '', '', '', 40, 1, '2026-08-22 04:10:09', '2026-08-22 04:10:09', NULL);
INSERT INTO `auth_permissions` VALUES (349404858345984000, 349404857075109888, '删除', 'cms:feedback:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:10:09', '2026-08-22 04:10:09', NULL);
INSERT INTO `auth_permissions` VALUES (349410741645873152, 349395084342595584, '广告位', 'cms:ad-position:list', 'menu', '/site/ad-position', '', 'PictureFilled', 18, 1, '2026-08-22 04:33:32', '2026-08-22 04:33:32', NULL);
INSERT INTO `auth_permissions` VALUES (349410742346321920, 349410741645873152, '新增', 'cms:ad-position:add', 'button', '', '', '', 50, 1, '2026-08-22 04:33:32', '2026-08-22 04:33:32', NULL);
INSERT INTO `auth_permissions` VALUES (349410743009021952, 349410741645873152, '修改', 'cms:ad-position:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:33:32', '2026-08-22 04:33:32', NULL);
INSERT INTO `auth_permissions` VALUES (349410743671721984, 349410741645873152, '删除', 'cms:ad-position:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:33:33', '2026-08-22 04:33:33', NULL);
INSERT INTO `auth_permissions` VALUES (349410744351199232, 349395084342595584, '广告素材', 'cms:ad-material:list', 'menu', '/site/ad-material', '', 'Picture', 16, 1, '2026-08-22 04:33:33', '2026-08-22 04:33:33', NULL);
INSERT INTO `auth_permissions` VALUES (349410745005510656, 349410744351199232, '新增', 'cms:ad-material:add', 'button', '', '', '', 50, 1, '2026-08-22 04:33:33', '2026-08-22 04:33:33', NULL);
INSERT INTO `auth_permissions` VALUES (349410745664016384, 349410744351199232, '修改', 'cms:ad-material:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:33:33', '2026-08-22 04:33:33', NULL);
INSERT INTO `auth_permissions` VALUES (349410746330910720, 349410744351199232, '删除', 'cms:ad-material:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:33:33', '2026-08-22 04:33:33', NULL);
INSERT INTO `auth_permissions` VALUES (349411885046697984, 349395084342595584, '招聘职位', 'cms:job:list', 'menu', '/site/job', '', 'Suitcase', 12, 1, '2026-08-22 04:38:05', '2026-08-22 04:38:05', NULL);
INSERT INTO `auth_permissions` VALUES (349411885692620800, 349411885046697984, '新增', 'cms:job:add', 'button', '', '', '', 50, 1, '2026-08-22 04:38:05', '2026-08-22 04:38:05', NULL);
INSERT INTO `auth_permissions` VALUES (349411886355320832, 349411885046697984, '修改', 'cms:job:edit', 'button', '', '', '', 40, 1, '2026-08-22 04:38:05', '2026-08-22 04:38:05', NULL);
INSERT INTO `auth_permissions` VALUES (349411887022215168, 349411885046697984, '删除', 'cms:job:remove', 'button', '', '', '', 30, 1, '2026-08-22 04:38:05', '2026-08-22 04:38:05', NULL);
INSERT INTO `auth_permissions` VALUES (349414924059021312, 0, 'AI对话', 'ai:chat', 'menu', '/ai', '', 'ChatDotRound', 980, 1, '2026-08-22 04:50:09', '2026-08-22 04:50:09', NULL);
INSERT INTO `auth_permissions` VALUES (349414924663001088, 349414924059021312, '模型配置', 'ai:config', 'button', '', '', '', 40, 1, '2026-08-22 04:50:09', '2026-08-22 04:50:09', NULL);
INSERT INTO `auth_permissions` VALUES (349443478163427328, 0, '产品管理', 'product', 'menu', '/product', '', 'Goods', 200, 1, '2026-08-22 06:43:37', '2026-08-22 06:43:37', NULL);
INSERT INTO `auth_permissions` VALUES (349443478859681792, 349443478163427328, '商品管理', 'product:list', 'menu', '/product/list', '', 'ShoppingCart', 40, 1, '2026-08-22 06:43:37', '2026-08-22 06:43:37', NULL);
INSERT INTO `auth_permissions` VALUES (349443479555936256, 349443478859681792, '新增', 'product:add', 'button', '', '', '', 50, 1, '2026-08-22 06:43:37', '2026-08-22 06:43:37', NULL);
INSERT INTO `auth_permissions` VALUES (349443480235413504, 349443478859681792, '修改', 'product:edit', 'button', '', '', '', 40, 1, '2026-08-22 06:43:38', '2026-08-22 06:43:38', NULL);
INSERT INTO `auth_permissions` VALUES (349443480927473664, 349443478859681792, '删除', 'product:remove', 'button', '', '', '', 30, 1, '2026-08-22 06:43:38', '2026-08-22 06:43:38', NULL);
INSERT INTO `auth_permissions` VALUES (349443481585979392, 349443478163427328, '商品分类', 'product:category:list', 'menu', '/product/category', '', 'Menu', 30, 1, '2026-08-22 06:43:38', '2026-08-22 06:43:38', NULL);
INSERT INTO `auth_permissions` VALUES (349443482282233856, 349443481585979392, '新增', 'product:category:add', 'button', '', '', '', 50, 1, '2026-08-22 06:43:38', '2026-08-22 06:43:38', NULL);
INSERT INTO `auth_permissions` VALUES (349443482919768064, 349443481585979392, '修改', 'product:category:edit', 'button', '', '', '', 40, 1, '2026-08-22 06:43:38', '2026-08-22 06:43:38', NULL);
INSERT INTO `auth_permissions` VALUES (349443483582468096, 349443481585979392, '删除', 'product:category:remove', 'button', '', '', '', 30, 1, '2026-08-22 06:43:38', '2026-08-22 06:43:38', NULL);
INSERT INTO `auth_permissions` VALUES (349443484245168128, 349443478163427328, '品牌管理', 'product:brand:list', 'menu', '/product/brand', '', 'Medal', 20, 1, '2026-08-22 06:43:39', '2026-08-22 06:43:39', NULL);
INSERT INTO `auth_permissions` VALUES (349443484937228288, 349443484245168128, '新增', 'product:brand:add', 'button', '', '', '', 50, 1, '2026-08-22 06:43:39', '2026-08-22 06:43:39', NULL);
INSERT INTO `auth_permissions` VALUES (349443485625094144, 349443484245168128, '修改', 'product:brand:edit', 'button', '', '', '', 40, 1, '2026-08-22 06:43:39', '2026-08-22 06:43:39', NULL);
INSERT INTO `auth_permissions` VALUES (349443486308765696, 349443484245168128, '删除', 'product:brand:remove', 'button', '', '', '', 30, 1, '2026-08-22 06:43:39', '2026-08-22 06:43:39', NULL);
INSERT INTO `auth_permissions` VALUES (349443486988242944, 349443478163427328, '规格管理', 'product:spec:list', 'menu', '/product/spec', '', 'SetUp', 10, 1, '2026-08-22 06:43:39', '2026-08-22 06:43:39', NULL);
INSERT INTO `auth_permissions` VALUES (349443487701274624, 349443486988242944, '新增', 'product:spec:add', 'button', '', '', '', 50, 1, '2026-08-22 06:43:39', '2026-08-22 06:43:39', NULL);
INSERT INTO `auth_permissions` VALUES (349443488359780352, 349443486988242944, '修改', 'product:spec:edit', 'button', '', '', '', 40, 1, '2026-08-22 06:43:40', '2026-08-22 06:43:40', NULL);
INSERT INTO `auth_permissions` VALUES (349443489051840512, 349443486988242944, '删除', 'product:spec:remove', 'button', '', '', '', 30, 1, '2026-08-22 06:43:40', '2026-08-22 06:43:40', NULL);

-- ----------------------------
-- Table structure for auth_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE `auth_role`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '角色ID',
  `role_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `role_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色唯一标识',
  `role_type` tinyint NOT NULL DEFAULT 2 COMMENT '角色类型: 1=系统内置 2=用户自定义',
  `role_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序号',
  `data_scope` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '数据权限范围',
  `scope_departments` json NULL COMMENT '指定部门IDs，JSON格式',
  `role_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `role_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色备注',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_role_code`(`role_code` ASC, `deleted_at` ASC) USING BTREE,
  INDEX `idx_status`(`role_status` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_role
-- ----------------------------
INSERT INTO `auth_role` VALUES (349395085001101312, '超级管理员', 'super_admin', 1, 999, 1, '[]', 1, '拥有全部菜单和权限', '2026-08-22 03:31:19', '2026-08-22 03:31:19', NULL);
INSERT INTO `auth_role` VALUES (349436166606557184, '运营专员', 'yunying', 2, 2, 3, '[]', 1, '文章发布', '2026-08-22 06:14:34', '2026-08-22 06:14:34', NULL);
INSERT INTO `auth_role` VALUES (349436413189689344, '财务专员', 'finance', 2, 11, 3, '[]', 1, '财务管理', '2026-08-22 06:15:33', '2026-08-22 06:15:33', NULL);
INSERT INTO `auth_role` VALUES (349446175805542400, '产品专员', 'product', 2, 13, 3, '[]', 1, '', '2026-08-22 06:54:20', '2026-08-22 06:54:20', NULL);

-- ----------------------------
-- Table structure for auth_role_menus
-- ----------------------------
DROP TABLE IF EXISTS `auth_role_menus`;
CREATE TABLE `auth_role_menus`  (
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `menu_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_role_menus
-- ----------------------------
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393048200941568, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393048444211200, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393048695869440, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393048951721984, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393049182408704, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393049438261248, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393049710891008, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393049962549248, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393050197430272, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393050449088512, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393050675580928, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393050935627776, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393051178897408, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393051417972736, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393051648659456, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393051900317696, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393052156170240, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393052403634176, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393052663681024, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393052944699392, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393053259272192, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393053548679168, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393053800337408, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393054031024128, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393054286876672, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393054521757696, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393054748250112, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393054970548224, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393055192846336, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349393055427727360, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349395056127512576, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349395056555331584, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349395057016705024, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349395057880731648, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349395058304356352, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349395058715398144, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402368175116288, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402368598740992, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402369064308736, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402370196770816, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402370448429056, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402370926579712, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402371362787328, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402372025487360, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402372264562688, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402372700770304, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349402373149560832, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404818785308672, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404819603197952, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404819854856192, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404822144946176, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404822384021504, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404822816034816, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404823235465216, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404823877193728, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404824124657664, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349404824565059584, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410713317543936, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410713598562304, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410714072518656, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410714533892096, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410715204980736, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410715452444672, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410715897040896, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349410716345831424, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349411854067568640, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349411854356975616, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349411854814154752, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349411855292305408, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349414912587599872, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443444818710528, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443445351387136, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443445636599808, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443446106361856, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443446550958080, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443447234629632, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443447490482176, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443447989604352, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443448434200576, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443449122066432, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443449377918976, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443449843486720, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443450321637376, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443450984337408, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443451240189952, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443451693174784, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349395085001101312, 349443452162936832, '2026-08-22 07:35:43');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349393055427727360, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402370196770816, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402370448429056, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402370926579712, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402371362787328, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402372025487360, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402372264562688, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402372700770304, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349402373149560832, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404819603197952, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404819854856192, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404822144946176, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404822384021504, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404822816034816, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404823235465216, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404823877193728, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404824124657664, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349404824565059584, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410713317543936, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410713598562304, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410714072518656, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410714533892096, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410715204980736, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410715452444672, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410715897040896, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349410716345831424, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349411854067568640, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349411854356975616, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349411854814154752, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436166606557184, 349411855292305408, '2026-08-22 06:14:34');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393048695869440, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393048951721984, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393049182408704, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393049438261248, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393049710891008, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393049962549248, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393050197430272, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393050449088512, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393050675580928, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393050935627776, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393051178897408, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393051417972736, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393051648659456, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393051900317696, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393052156170240, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393052403634176, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393052663681024, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349393052944699392, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349395056127512576, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349395056555331584, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349395057016705024, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349395057880731648, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349395058304356352, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349395058715398144, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349402368175116288, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349402368598740992, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349402369064308736, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349436413189689344, 349404818785308672, '2026-08-22 06:15:33');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443444818710528, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443445351387136, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443445636599808, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443446106361856, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443446550958080, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443447234629632, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443447490482176, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443447989604352, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443448434200576, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443449122066432, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443449377918976, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443449843486720, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443450321637376, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443450984337408, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443451240189952, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443451693174784, '2026-08-22 06:54:20');
INSERT INTO `auth_role_menus` VALUES (349446175805542400, 349443452162936832, '2026-08-22 06:54:20');

-- ----------------------------
-- Table structure for auth_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `auth_role_permissions`;
CREATE TABLE `auth_role_permissions`  (
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `permission_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_role_permissions
-- ----------------------------
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395062418968576, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395063035531264, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395063656288256, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395064298016768, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395064927162368, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395065531142144, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395066164482048, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395066806210560, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395067418578944, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395068022558720, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395068609761280, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395069213741056, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395069830303744, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395070434283520, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395071059234816, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395071654825984, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395072275582976, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395072917311488, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395073567428608, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395074221740032, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395074863468544, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395075513585664, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395076176285696, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395076809625600, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395077438771200, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395078072111104, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395078684479488, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395079334596608, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395079946964992, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395080559333376, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395081188478976, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395081821818880, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395082455158784, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395083096887296, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395083730227200, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349395084342595584, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402384390295552, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402385036218368, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402385694724096, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402386340646912, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402393462575104, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402394108497920, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402394746032128, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402395375177728, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402396016906240, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402396729937920, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402397371666432, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349402398013394944, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404843955326976, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404844655775744, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404849781215232, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404850443915264, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404854348812288, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404855011512320, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404855690989568, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404856374661120, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404857075109888, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404857716838400, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349404858345984000, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410741645873152, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410742346321920, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410743009021952, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410743671721984, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410744351199232, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410745005510656, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410745664016384, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349410746330910720, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349411885046697984, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349411885692620800, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349411886355320832, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349411887022215168, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349414924059021312, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349414924663001088, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443478163427328, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443478859681792, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443479555936256, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443480235413504, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443480927473664, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443481585979392, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443482282233856, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443482919768064, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443483582468096, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443484245168128, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443484937228288, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443485625094144, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443486308765696, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443486988242944, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443487701274624, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443488359780352, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349395085001101312, 349443489051840512, '2026-08-22 07:35:43');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349395084342595584, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402393462575104, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402394108497920, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402394746032128, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402395375177728, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402396016906240, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402396729937920, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402397371666432, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349402398013394944, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404849781215232, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404850443915264, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404854348812288, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404855011512320, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404855690989568, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404856374661120, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404857075109888, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404857716838400, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349404858345984000, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410741645873152, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410742346321920, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410743009021952, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410743671721984, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410744351199232, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410745005510656, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410745664016384, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349410746330910720, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349411885046697984, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349411885692620800, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349411886355320832, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436166606557184, 349411887022215168, '2026-08-22 06:14:35');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395063656288256, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395064298016768, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395064927162368, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395065531142144, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395066164482048, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395066806210560, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395067418578944, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395068022558720, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395068609761280, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395069213741056, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395069830303744, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395070434283520, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395071059234816, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395071654825984, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395072275582976, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395072917311488, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395073567428608, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395074221740032, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395074863468544, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395075513585664, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395076176285696, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395076809625600, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395077438771200, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349395078072111104, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349402384390295552, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349402385036218368, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349402385694724096, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349402386340646912, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349404843955326976, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349436413189689344, 349404844655775744, '2026-08-22 06:15:34');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443478163427328, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443478859681792, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443479555936256, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443480235413504, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443480927473664, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443481585979392, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443482282233856, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443482919768064, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443483582468096, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443484245168128, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443484937228288, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443485625094144, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443486308765696, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443486988242944, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443487701274624, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443488359780352, '2026-08-22 06:54:21');
INSERT INTO `auth_role_permissions` VALUES (349446175805542400, 349443489051840512, '2026-08-22 06:54:21');

-- ----------------------------
-- Table structure for auth_user_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_user_role`;
CREATE TABLE `auth_user_role`  (
  `user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_user_role
-- ----------------------------
INSERT INTO `auth_user_role` VALUES (349435902843555840, 349436166606557184, '2026-08-22 06:17:58');
INSERT INTO `auth_user_role` VALUES (349436695722201088, 349436413189689344, '2026-08-22 06:16:40');
INSERT INTO `auth_user_role` VALUES (349445981730902016, 349446175805542400, '2026-08-22 06:54:40');
INSERT INTO `auth_user_role` VALUES (934035802554576899, 349395085001101312, '2026-08-22 07:35:54');

-- ----------------------------
-- Table structure for boss_job
-- ----------------------------
DROP TABLE IF EXISTS `boss_job`;
CREATE TABLE `boss_job`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键ID',
  `job_title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '职位名称',
  `department` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属部门',
  `workplace` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '工作地点',
  `experience` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '经验要求',
  `education` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '学历要求',
  `salary_range` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '薪资范围',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '职位描述',
  `requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '任职要求',
  `benefits` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '福利待遇',
  `is_hot` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否急聘',
  `job_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=待发布，2=发布中，3=已关闭',
  `expire_at` datetime NULL DEFAULT NULL COMMENT '过期时间',
  `view_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
  `job_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_status_hot_sort`(`job_status` ASC, `is_hot` ASC, `job_sort` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of boss_job
-- ----------------------------
INSERT INTO `boss_job` VALUES (349411861428572160, 'PHP 开发工程师', '研发部', '深圳', '3-5年', '本科', '15-25K', '负责公司官网与管理系统的后端开发与维护。', '熟悉 PHP / Laravel\n具备 MySQL 与 Redis 使用经验', '五险一金、弹性工作、年度体检', 1, 2, NULL, 0, 20, '2026-08-22 04:37:59', '2026-08-22 04:37:59', NULL);
INSERT INTO `boss_job` VALUES (349411861688619008, '前端开发工程师', '研发部', '深圳', '1-3年', '本科', '12-20K', '负责官网与后台管理系统的前端开发。', '熟悉 Vue 3 / Element Plus\n了解前端工程化', '五险一金、餐补、带薪年假', 0, 2, NULL, 0, 10, '2026-08-22 04:37:59', '2026-08-22 04:37:59', NULL);
INSERT INTO `boss_job` VALUES (349411861952860160, '市场专员', '市场部', '长沙', '不限', '大专', '8-12K', '负责市场活动策划与客户沟通。', '沟通能力强，有相关经验优先', '五险一金、绩效奖金', 0, 1, NULL, 0, 0, '2026-08-22 04:37:59', '2026-08-22 04:37:59', NULL);

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------
INSERT INTO `cache` VALUES ('sunny-company-cache-40836534a6a75b622d9d9d1565a1d83ed9419659', 'i:2;', 1787384317);
INSERT INTO `cache` VALUES ('sunny-company-cache-40836534a6a75b622d9d9d1565a1d83ed9419659:timer', 'i:1787384317;', 1787384317);
INSERT INTO `cache` VALUES ('sunny-company-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:2;', 1787382564);
INSERT INTO `cache` VALUES ('sunny-company-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1787382564;', 1787382564);

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for feedbacks
-- ----------------------------
DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE `feedbacks`  (
  `id` bigint UNSIGNED NOT NULL,
  `fb_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `fb_phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `fb_email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `fb_company` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '公司名称',
  `fb_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '留言标题',
  `fb_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '留言内容',
  `fb_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=未处理，1=已处理',
  `reply_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '回复内容',
  `replied_at` datetime NULL DEFAULT NULL COMMENT '回复时间',
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_status`(`fb_status` ASC) USING BTREE,
  INDEX `idx_created_at`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of feedbacks
-- ----------------------------
INSERT INTO `feedbacks` VALUES (920733863054423235, '张伟', '13812345678', 'zhangwei@qq.com', '深圳科技有限公司', '咨询企业版产品功能与报价', '你好，我公司目前正在选型企业级管理系统，看到贵公司的产品介绍后很感兴趣。请问企业版是否支持多租户？能否提供一份详细的功能清单和报价方案？我们预计采购50个用户，希望能在本月底前完成选型。', 0, NULL, NULL, '192.168.1.100', '2026-07-22 09:30:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423236, '李娜', '15987654321', 'lina@partner.com', '上海云创科技', '寻求渠道合作机会', '我司是专业的IT解决方案服务商，主要服务于华东地区的制造企业。看到贵公司的产品在行业内口碑很好，希望能洽谈渠道代理合作。请安排相关负责人与我联系，谢谢！', 0, NULL, NULL, '10.0.1.50', '2026-07-22 14:20:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423237, '王小明', '13524681357', 'wangxm@tech.com', '北京创新科技', '系统升级后数据无法同步', '我们使用的是贵公司产品3.0版本，上周升级后，发现部分数据无法正常同步到云端。已尝试重启服务但问题依旧，麻烦尽快安排技术人员协助排查，我们这边业务受到了影响。', 0, NULL, NULL, '172.16.0.30', '2026-07-21 16:45:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423238, '陈静', '13698765432', 'chenjing@mail.com', '广州越秀集团', '建议增加多语言支持功能', '我们是跨国企业，目前贵公司产品仅支持中英文，建议在后续版本中增加对日语和韩语的支持，方便我们海外团队使用。如果能提供多语言切换功能就更好了。期待产品越来越好！', 0, NULL, NULL, '192.168.2.80', '2026-07-21 11:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423239, '刘洋', '15234567890', 'liuyang@outlook.com', '', '咨询PHP开发岗位招聘信息', '您好！我看到贵公司招聘PHP高级开发工程师，我拥有6年PHP开发经验，熟悉Hyperf和ThinkPHP框架。想了解该岗位是否还在招人？以及面试流程是怎样的？期待您的回复！', 0, NULL, NULL, '10.0.2.15', '2026-07-20 09:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423240, '赵磊', '13765432198', 'zhaolei@abc.com', '杭州云创科技', '咨询产品价格与部署方案', '我司正在寻找一套企业级CRM解决方案，想咨询贵公司产品的价格体系和部署方式。同时希望能安排一次在线演示，让我们团队了解一下产品的实际使用体验。', 1, '尊敬的赵先生，您好！感谢您对产品的关注。相关产品资料和报价方案已发送至您的邮箱，并已安排销售顾问于明日10:00与您联系，届时将为您做详细的产品演示。如有其他问题，可随时联系我们的客服热线。祝工作顺利！', '2026-07-21 17:30:00', '192.168.3.20', '2026-07-19 15:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423241, '孙婷', '15824681357', 'sunting@qq.com', '成都互联科技', '希望能成为贵公司渠道伙伴', '我们公司在西南地区深耕企业服务多年，拥有超过500家客户资源。希望能成为贵公司在西南地区的渠道合作伙伴，共同开拓市场。请告知合作条件和流程。', 1, '尊敬的孙总，您好！非常欢迎合作意向。我们已安排渠道部负责人在今日内与您联系，详细沟通合作细节。同时，相关合作政策已发至您的邮箱，请您查收。期待我们的合作！', '2026-07-21 10:00:00', '192.168.4.40', '2026-07-19 11:30:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423242, '钱森', '13987654321', 'qiansen@tech.com', '苏州智能制造', '产品使用遇到性能瓶颈', '我们是贵公司的老客户，使用产品已有一年多。最近随着业务量增长，系统响应速度明显变慢。请问是否有性能优化的方案或建议？希望能得到专业指导。', 1, '钱先生，您好！感谢您长期以来的支持。针对您反馈的性能问题，建议从以下几个方面进行优化：1. 检查数据库索引配置；2. 开启缓存功能；3. 升级到最新版本（3.1版本已对性能有较大提升）。我们已在后台为您的账户开启了高级技术支持通道，相关优化文档也已发送至您的邮箱。如有疑问可随时联系我们。', '2026-07-20 14:30:00', '10.0.3.60', '2026-07-18 08:30:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423243, '周玥', '15012345678', 'zhouyue@mail.com', '厦门科技集团', '产品体验很好，特别感谢', '您好！我是贵公司的产品用户，最近使用产品感觉非常好，界面简洁，功能实用，极大提升了我们的工作效率。感谢你们团队做出这么优秀的产品！特此留言表达感谢。', 1, '周女士，您好！非常感谢您的认可和鼓励。客户的满意是我们最大的动力！我们会继续打磨产品，为您提供更好的使用体验。如有任何建议或需求，欢迎随时联系我们。祝您工作愉快！', '2026-07-20 09:00:00', '192.168.5.70', '2026-07-17 16:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` VALUES (920733863054423244, '吴强', '18965432109', 'wuqiang@tech.com', '武汉高新科技', '咨询产品定制化开发服务', '我公司需要一套定制化的企业管理系统，想咨询贵公司是否接受定制化开发？一般定制周期和费用大概是怎样的？希望能得到专业的解答。', 1, '已收到您的留言，我们会尽快跟进。', '2026-07-26 02:30:20', '172.16.1.25', '2026-07-16 10:30:00', '2026-07-26 02:30:20');
INSERT INTO `feedbacks` VALUES (920733863054423245, '郭靖', '130266611119', '', '赛格科技', '应聘：PHP高级开发工程师', '您好，我对「PHP高级开发工程师」（技术研发中心 / 深圳南山区科技园）职位感兴趣，期待沟通。', 0, NULL, NULL, '127.0.0.1', '2026-07-26 14:06:48', '2026-07-26 14:06:48');

-- ----------------------------
-- Table structure for friend_links
-- ----------------------------
DROP TABLE IF EXISTS `friend_links`;
CREATE TABLE `friend_links`  (
  `id` bigint UNSIGNED NOT NULL,
  `link_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '网站名称',
  `link_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '网站链接',
  `link_logo` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '网站Logo',
  `link_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '网站描述',
  `link_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序越小越前',
  `link_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_link_status`(`link_status` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of friend_links
-- ----------------------------
INSERT INTO `friend_links` VALUES (349404832219664384, 'Laravel', 'https://laravel.com', '', 'PHP Web 框架', 20, 1, '2026-08-22 04:10:03', '2026-08-22 04:10:03');
INSERT INTO `friend_links` VALUES (349404832471322624, 'Element Plus', 'https://element-plus.org', '', 'Vue 组件库', 10, 1, '2026-08-22 04:10:03', '2026-08-22 04:10:03');
INSERT INTO `friend_links` VALUES (920733863055413246, '百度', 'https://www.baidu.com', '/uploads/friendlinks/baidu.png', '全球最大的中文搜索引擎', 1, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413247, '腾讯云', 'https://cloud.tencent.com', '/uploads/friendlinks/tencent-cloud.png', '腾讯云 - 产业智变，云启未来', 2, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413248, '阿里云', 'https://www.aliyun.com', '/uploads/friendlinks/aliyun.png', '阿里云 - 上云就上阿里云', 3, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413249, '华为云', 'https://www.huaweicloud.com', '/uploads/friendlinks/huawei-cloud.png', '华为云 - 选择华为云，让您的业务更上一层楼', 4, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413250, 'CSDN', 'https://www.csdn.net', '/uploads/friendlinks/csdn.png', 'CSDN - 专业开发者技术社区', 5, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413251, '掘金', 'https://juejin.cn', '/uploads/friendlinks/juejin.png', '掘金 - 一个帮助开发者成长的社区', 6, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413252, '开源中国', 'https://www.oschina.net', '/uploads/friendlinks/oschina.png', '开源中国 - 中国最大的开源技术社区', 7, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413253, 'SegmentFault 思否', 'https://segmentfault.com', '/uploads/friendlinks/segmentfault.png', 'SegmentFault - 技术问答社区', 8, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413254, '牛客网', 'https://www.nowcoder.com', '/uploads/friendlinks/nowcoder.png', '牛客网 - 求职招聘与校招笔试面试平台', 9, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` VALUES (920733863055413255, '拉勾网', 'https://www.lagou.com', '/uploads/friendlinks/lagou.png', '拉勾网 - 专注互联网招聘的招聘平台', 10, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');

-- ----------------------------
-- Table structure for hr_department
-- ----------------------------
DROP TABLE IF EXISTS `hr_department`;
CREATE TABLE `hr_department`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '部门主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父部门ID，0=根节点',
  `dept_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '部门名称',
  `dept_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '部门唯一编码',
  `ancestors` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '祖先ID路径，逗号分隔',
  `dept_level` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '层级深度',
  `leader_user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人ID',
  `dept_phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `dept_sort` int NOT NULL DEFAULT 0 COMMENT '树形展示排序号',
  `dept_status` tinyint NOT NULL DEFAULT 1 COMMENT '状态 0禁用 1正常启用',
  `created_by` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_dept_code`(`dept_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hr_department
-- ----------------------------
INSERT INTO `hr_department` VALUES (349395059772362752, 0, '名杨科技', 'ROOT', '0', 1, 934035802554576899, '', 90, 1, 0, '2026-08-22 03:31:13', '2026-08-22 06:06:39', NULL);
INSERT INTO `hr_department` VALUES (349395060015632384, 349395059772362752, '深圳总公司', 'SZ', '0,349395059772362752', 2, 0, '', 80, 1, 0, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `hr_department` VALUES (349395060254707712, 349395059772362752, '长沙分公司', 'CS', '0,349395059772362752', 2, 0, '', 70, 1, 0, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `hr_department` VALUES (349395060506365952, 349395060015632384, '研发部门', 'SZ_RD', '0,349395059772362752,349395060015632384', 3, 0, '', 50, 1, 0, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `hr_department` VALUES (349395060770607104, 349395060015632384, '市场部门', 'SZ_MK', '0,349395059772362752,349395060015632384', 3, 0, '', 40, 1, 0, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `hr_department` VALUES (349395061026459648, 349395060015632384, '测试部门', 'SZ_QA', '0,349395059772362752,349395060015632384', 3, 0, '', 30, 1, 0, '2026-08-22 03:31:13', '2026-08-22 03:31:13', NULL);
INSERT INTO `hr_department` VALUES (349395061265534976, 349395060015632384, '财务部门', 'SZ_FN', '0,349395059772362752,349395060015632384', 3, 0, '', 20, 1, 0, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `hr_department` VALUES (349395061496221696, 349395060015632384, '运维部门', 'SZ_OPS', '0,349395059772362752,349395060015632384', 3, 0, '', 10, 1, 0, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `hr_department` VALUES (349395061739491328, 349395060254707712, '市场部门', 'CS_MK', '0,349395059772362752,349395060254707712', 3, 0, '', 20, 1, 0, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);
INSERT INTO `hr_department` VALUES (349395061995343872, 349395060254707712, '财务部门', 'CS_FN', '0,349395059772362752,349395060254707712', 3, 0, '', 10, 1, 0, '2026-08-22 03:31:14', '2026-08-22 03:31:14', NULL);

-- ----------------------------
-- Table structure for hr_post
-- ----------------------------
DROP TABLE IF EXISTS `hr_post`;
CREATE TABLE `hr_post`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '岗位主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级岗位ID，0=顶级根岗位',
  `post_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '岗位名称',
  `post_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '岗位唯一编码',
  `post_sort` int NOT NULL DEFAULT 0 COMMENT '排序号',
  `post_status` tinyint NOT NULL DEFAULT 1 COMMENT '状态 0=禁用 1=正常启用',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '岗位描述备注',
  `created_by` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_post_code`(`post_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hr_post
-- ----------------------------
INSERT INTO `hr_post` VALUES (349402374026170368, 0, '总经理', 'CEO', 90, 1, '', 0, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `hr_post` VALUES (349402374290411520, 349402374026170368, '部门经理', 'DEPT_MANAGER', 80, 1, '', 0, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `hr_post` VALUES (349402374550458368, 349402374290411520, '技术主管', 'TECH_LEAD', 70, 1, '', 0, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `hr_post` VALUES (349402374793728000, 349402374290411520, '前端开发', 'FE_DEV', 60, 1, '', 0, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);
INSERT INTO `hr_post` VALUES (349402375041191936, 349402374290411520, '财务专员', 'FINANCE', 50, 1, '', 0, '2026-08-22 04:00:17', '2026-08-22 04:00:17', NULL);

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_08_22_000000_create_user_account_table', 2);
INSERT INTO `migrations` VALUES (5, '2026_08_22_100000_create_auth_menus_table', 3);
INSERT INTO `migrations` VALUES (6, '2026_08_22_200000_create_rbac_tables', 4);
INSERT INTO `migrations` VALUES (7, '2026_08_22_300000_create_post_and_article_tables', 5);
INSERT INTO `migrations` VALUES (8, '2026_08_22_400000_add_post_id_to_user_account_table', 6);
INSERT INTO `migrations` VALUES (9, '2026_08_22_500000_create_site_and_log_tables', 7);
INSERT INTO `migrations` VALUES (10, '2026_08_22_600000_create_ad_tables', 8);
INSERT INTO `migrations` VALUES (11, '2026_08_22_700000_create_boss_job_table', 9);
INSERT INTO `migrations` VALUES (12, '2026_08_22_800000_create_ai_provider_table', 10);
INSERT INTO `migrations` VALUES (13, '2026_08_22_900000_create_product_tables', 11);
INSERT INTO `migrations` VALUES (14, '2026_08_22_910000_create_product_media_table', 12);

-- ----------------------------
-- Table structure for operation_log
-- ----------------------------
DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `operator_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `operator_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人名称',
  `biz_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '业务模块类型',
  `activity_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '活动类型',
  `action` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作类型 INSERT/UPDATE/DELETE/LOGIN',
  `biz_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '目标实体ID',
  `biz_label` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '高亮展示文本',
  `old_value` json NULL COMMENT '修改前的数据快照',
  `new_value` json NULL COMMENT '修改后的数据快照',
  `operator_status` tinyint NOT NULL DEFAULT 1 COMMENT '操作状态 0失败 1成功',
  `error_msg` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '错误信息',
  `client_ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '客户端IP',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户浏览器/设备信息',
  `request_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '触发日志的API URL',
  `method_fun` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '触发日志的方法名',
  `created_at` datetime(6) NULL DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_operator_id`(`operator_id` ASC) USING BTREE,
  INDEX `idx_biz`(`biz_type` ASC, `biz_id` ASC) USING BTREE,
  INDEX `idx_created_at`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of operation_log
-- ----------------------------
INSERT INTO `operation_log` VALUES (349412567124414464, 934035802554576899, '管理员', 'auth', 'logout', 'LOGOUT', 934035802554576899, 'admin', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/logout', 'App\\Http\\Controllers\\Admin\\AuthController@logout', '2026-08-22 04:40:47.000000');
INSERT INTO `operation_log` VALUES (349412578470006784, 934035802554576899, '管理员', 'auth', 'login', 'LOGIN', 934035802554576899, 'admin', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/login', 'App\\Http\\Controllers\\Admin\\AuthController@login', '2026-08-22 04:40:50.000000');
INSERT INTO `operation_log` VALUES (349435904215093248, 934035802554576899, '管理员', 'user', 'user_created', 'INSERT', 0, 'sunny', NULL, '{\"id\": null, \"dept_id\": \"349395061739491328\", \"post_id\": \"349402374793728000\", \"role_ids\": [], \"real_name\": \"阳光\", \"user_name\": \"sunny\", \"user_email\": \"git@163.com\", \"lock_reason\": null, \"user_mobile\": \"13026661119\", \"user_status\": 1, \"real_auth_status\": 0, \"register_channel\": \"web\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/users', 'App\\Http\\Controllers\\Admin\\UserController@store', '2026-08-22 06:13:31.000000');
INSERT INTO `operation_log` VALUES (349436180330319872, 934035802554576899, '管理员', 'role', 'role_created', 'INSERT', 0, '运营专员', NULL, '{\"id\": null, \"menu_ids\": [\"349393055427727360\", \"349404819603197952\", \"349404819854856192\", \"349402370196770816\", \"349402370448429056\", \"349402370926579712\", \"349402371362787328\", \"349410713317543936\", \"349410713598562304\", \"349410714072518656\", \"349410714533892096\", \"349410715204980736\", \"349410715452444672\", \"349410715897040896\", \"349410716345831424\", \"349411854067568640\", \"349411854356975616\", \"349411854814154752\", \"349411855292305408\", \"349402372025487360\", \"349402372264562688\", \"349402372700770304\", \"349402373149560832\", \"349404822144946176\", \"349404822384021504\", \"349404822816034816\", \"349404823235465216\", \"349404823877193728\", \"349404824124657664\", \"349404824565059584\"], \"role_code\": \"yunying\", \"role_name\": \"运营专员\", \"role_sort\": 2, \"role_type\": 2, \"data_scope\": 3, \"role_remark\": \"文章发布\", \"role_status\": 1, \"permission_ids\": [\"349395084342595584\", \"349404849781215232\", \"349404850443915264\", \"349402393462575104\", \"349402394108497920\", \"349402394746032128\", \"349402395375177728\", \"349410741645873152\", \"349410742346321920\", \"349410743009021952\", \"349410743671721984\", \"349410744351199232\", \"349410745005510656\", \"349410745664016384\", \"349410746330910720\", \"349411885046697984\", \"349411885692620800\", \"349411886355320832\", \"349411887022215168\", \"349402396016906240\", \"349402396729937920\", \"349402397371666432\", \"349402398013394944\", \"349404854348812288\", \"349404855011512320\", \"349404855690989568\", \"349404856374661120\", \"349404857075109888\", \"349404857716838400\", \"349404858345984000\"], \"scope_departments\": []}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/roles', 'App\\Http\\Controllers\\Admin\\RoleController@store', '2026-08-22 06:14:37.000000');
INSERT INTO `operation_log` VALUES (349436426431107072, 934035802554576899, '管理员', 'role', 'role_created', 'INSERT', 0, '财务专员', NULL, '{\"id\": null, \"menu_ids\": [\"349393048695869440\", \"349393048951721984\", \"349393049182408704\", \"349393049438261248\", \"349393049710891008\", \"349393049962549248\", \"349393050197430272\", \"349393050449088512\", \"349393050675580928\", \"349395056127512576\", \"349395056555331584\", \"349395057016705024\", \"349393050935627776\", \"349393051178897408\", \"349395057880731648\", \"349395058304356352\", \"349395058715398144\", \"349393051417972736\", \"349402368175116288\", \"349402368598740992\", \"349402369064308736\", \"349393051648659456\", \"349393051900317696\", \"349393052156170240\", \"349393052403634176\", \"349393052663681024\", \"349404818785308672\", \"349393052944699392\"], \"role_code\": \"finance\", \"role_name\": \"财务专员\", \"role_sort\": 11, \"role_type\": 2, \"data_scope\": 3, \"role_remark\": \"财务管理\", \"role_status\": 1, \"permission_ids\": [\"349395063656288256\", \"349395064298016768\", \"349395064927162368\", \"349395065531142144\", \"349395066164482048\", \"349395066806210560\", \"349395067418578944\", \"349395068022558720\", \"349395068609761280\", \"349395069213741056\", \"349395069830303744\", \"349395070434283520\", \"349395071059234816\", \"349395071654825984\", \"349395072275582976\", \"349395072917311488\", \"349395073567428608\", \"349395074221740032\", \"349402384390295552\", \"349402385036218368\", \"349402385694724096\", \"349402386340646912\", \"349395074863468544\", \"349395075513585664\", \"349395076176285696\", \"349395076809625600\", \"349395077438771200\", \"349404843955326976\", \"349404844655775744\", \"349395078072111104\"], \"scope_departments\": []}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/roles', 'App\\Http\\Controllers\\Admin\\RoleController@store', '2026-08-22 06:15:36.000000');
INSERT INTO `operation_log` VALUES (349436521411121152, 934035802554576899, '管理员', 'user', 'user_updated', 'UPDATE', 349435902843555840, 'sunny', NULL, '{\"id\": \"349435902843555840\", \"dept_id\": \"349395061739491328\", \"post_id\": \"349402374793728000\", \"role_ids\": [\"349436166606557184\"], \"real_name\": \"阳光\", \"user_name\": \"sunny\", \"user_email\": \"git@163.com\", \"lock_reason\": null, \"user_mobile\": \"13026661119\", \"user_status\": 1, \"real_auth_status\": 0, \"register_channel\": \"web\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/users/349435902843555840', 'App\\Http\\Controllers\\Admin\\UserController@update', '2026-08-22 06:15:58.000000');
INSERT INTO `operation_log` VALUES (349436697374756864, 934035802554576899, '管理员', 'user', 'user_created', 'INSERT', 0, 'caiwu', NULL, '{\"id\": null, \"dept_id\": \"349395061265534976\", \"post_id\": \"349402375041191936\", \"role_ids\": [\"349436413189689344\"], \"real_name\": \"财务\", \"user_name\": \"caiwu\", \"user_email\": \"caiwu@163.com\", \"lock_reason\": null, \"user_mobile\": \"13800138666\", \"user_status\": 1, \"real_auth_status\": 0, \"register_channel\": \"web\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/users', 'App\\Http\\Controllers\\Admin\\UserController@store', '2026-08-22 06:16:40.000000');
INSERT INTO `operation_log` VALUES (349436779440508928, 934035802554576899, '管理员', 'auth', 'logout', 'LOGOUT', 934035802554576899, 'admin', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/logout', 'App\\Http\\Controllers\\Admin\\AuthController@logout', '2026-08-22 06:17:00.000000');
INSERT INTO `operation_log` VALUES (349436813045272576, 0, 'yunying', 'auth', 'login', 'LOGIN', 0, 'yunying', NULL, NULL, 0, '账号或密码错误', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/login', 'App\\Http\\Controllers\\Admin\\AuthController@login', '2026-08-22 06:17:08.000000');
INSERT INTO `operation_log` VALUES (349436849858678784, 349436695722201088, '财务', 'auth', 'login', 'LOGIN', 349436695722201088, 'caiwu', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/login', 'App\\Http\\Controllers\\Admin\\AuthController@login', '2026-08-22 06:17:17.000000');
INSERT INTO `operation_log` VALUES (349436927012900864, 349436695722201088, '财务', 'auth', 'logout', 'LOGOUT', 349436695722201088, 'caiwu', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/logout', 'App\\Http\\Controllers\\Admin\\AuthController@logout', '2026-08-22 06:17:35.000000');
INSERT INTO `operation_log` VALUES (349436940849909760, 934035802554576899, '管理员', 'auth', 'login', 'LOGIN', 934035802554576899, 'admin', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/login', 'App\\Http\\Controllers\\Admin\\AuthController@login', '2026-08-22 06:17:38.000000');
INSERT INTO `operation_log` VALUES (349437024014569472, 934035802554576899, '管理员', 'user', 'user_updated', 'UPDATE', 349435902843555840, 'yunying', NULL, '{\"id\": \"349435902843555840\", \"dept_id\": \"349395061739491328\", \"post_id\": \"349402374793728000\", \"role_ids\": [\"349436166606557184\"], \"real_name\": \"阳光\", \"user_name\": \"yunying\", \"user_email\": \"git@163.com\", \"lock_reason\": null, \"user_mobile\": \"13026661119\", \"user_status\": 1, \"real_auth_status\": 0, \"register_channel\": \"web\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'http://localhost:8000/api/admin/users/349435902843555840', 'App\\Http\\Controllers\\Admin\\UserController@update', '2026-08-22 06:17:58.000000');
INSERT INTO `operation_log` VALUES (349445983286988800, 934035802554576899, '管理员', 'user', 'user_created', 'INSERT', 0, 'product', NULL, '{\"id\": null, \"dept_id\": \"349395060770607104\", \"post_id\": \"349402374793728000\", \"role_ids\": [\"349436413189689344\"], \"real_name\": \"产品\", \"user_name\": \"product\", \"user_email\": \"githup@163.com\", \"lock_reason\": null, \"user_mobile\": \"15632323212\", \"user_status\": 1, \"real_auth_status\": 0, \"register_channel\": \"web\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/users', 'App\\Http\\Controllers\\Admin\\UserController@store', '2026-08-22 06:53:34.000000');
INSERT INTO `operation_log` VALUES (349446184085098496, 934035802554576899, '管理员', 'role', 'role_created', 'INSERT', 0, '产品专员', NULL, '{\"id\": null, \"menu_ids\": [\"349443444818710528\", \"349443445351387136\", \"349443445636599808\", \"349443446106361856\", \"349443446550958080\", \"349443447234629632\", \"349443447490482176\", \"349443447989604352\", \"349443448434200576\", \"349443449122066432\", \"349443449377918976\", \"349443449843486720\", \"349443450321637376\", \"349443450984337408\", \"349443451240189952\", \"349443451693174784\", \"349443452162936832\"], \"role_code\": \"product\", \"role_name\": \"产品专员\", \"role_sort\": 13, \"role_type\": 2, \"data_scope\": 3, \"role_remark\": null, \"role_status\": 1, \"permission_ids\": [\"349443478163427328\", \"349443478859681792\", \"349443479555936256\", \"349443480235413504\", \"349443480927473664\", \"349443481585979392\", \"349443482282233856\", \"349443482919768064\", \"349443483582468096\", \"349443484245168128\", \"349443484937228288\", \"349443485625094144\", \"349443486308765696\", \"349443486988242944\", \"349443487701274624\", \"349443488359780352\", \"349443489051840512\"], \"scope_departments\": []}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/roles', 'App\\Http\\Controllers\\Admin\\RoleController@store', '2026-08-22 06:54:22.000000');
INSERT INTO `operation_log` VALUES (349446262199816192, 934035802554576899, '管理员', 'user', 'user_updated', 'UPDATE', 349445981730902016, 'product', NULL, '{\"id\": \"349445981730902016\", \"dept_id\": \"349395060770607104\", \"post_id\": \"349402374793728000\", \"role_ids\": [\"349446175805542400\"], \"real_name\": \"产品\", \"user_name\": \"product\", \"user_email\": \"githup@163.com\", \"lock_reason\": null, \"user_mobile\": \"15632323212\", \"user_status\": 1, \"real_auth_status\": 0, \"register_channel\": \"web\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/users/349445981730902016', 'App\\Http\\Controllers\\Admin\\UserController@update', '2026-08-22 06:54:41.000000');
INSERT INTO `operation_log` VALUES (349446448854732800, 934035802554576899, '管理员', 'prod_cat', 'prod_cat_created', 'INSERT', 0, '数码', NULL, '{\"id\": null, \"unit\": \"部\", \"parent_id\": \"0\", \"cat_remark\": null, \"cat_status\": 1, \"sort_order\": 2, \"category_name\": \"数码\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-categories', 'App\\Http\\Controllers\\Admin\\ProductCategoryController@store', '2026-08-22 06:55:25.000000');
INSERT INTO `operation_log` VALUES (349446501270949888, 934035802554576899, '管理员', 'prod_cat', 'prod_cat_created', 'INSERT', 0, '手机', NULL, '{\"id\": null, \"unit\": \"部\", \"parent_id\": \"349446448322056192\", \"cat_remark\": null, \"cat_status\": 1, \"sort_order\": 3, \"category_name\": \"手机\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-categories', 'App\\Http\\Controllers\\Admin\\ProductCategoryController@store', '2026-08-22 06:55:38.000000');
INSERT INTO `operation_log` VALUES (349446586067193856, 934035802554576899, '管理员', 'brand', 'brand_updated', 'UPDATE', 349443457351290880, '恒大', NULL, '{\"id\": \"349443457351290880\", \"alias\": \"hengda\", \"is_show\": 1, \"is_system\": 1, \"brand_code\": \"BR000002\", \"brand_name\": \"恒大\", \"created_at\": \"2026-08-22 06:43:32\", \"sort_order\": 2, \"brand_remark\": null}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-brands/349443457351290880', 'App\\Http\\Controllers\\Admin\\ProductBrandController@update', '2026-08-22 06:55:58.000000');
INSERT INTO `operation_log` VALUES (349446652358168576, 934035802554576899, '管理员', 'brand', 'brand_created', 'INSERT', 0, '苹果Apple', NULL, '{\"id\": null, \"alias\": \"apple\", \"is_show\": 1, \"is_system\": 1, \"brand_code\": \"BR000002\", \"brand_name\": \"苹果Apple\", \"created_at\": \"2026-08-22 06:43:32\", \"sort_order\": 4, \"brand_remark\": null}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-brands', 'App\\Http\\Controllers\\Admin\\ProductBrandController@store', '2026-08-22 06:56:14.000000');
INSERT INTO `operation_log` VALUES (349446752341987328, 934035802554576899, '管理员', 'prod_spec', 'prod_spec_created', 'INSERT', 349443458374701056, '新增成功', NULL, '{\"id\": null, \"isNew\": true, \"value\": \"1.8m\", \"editing\": true, \"spec_id\": \"349443458374701056\", \"sort_order\": 11, \"value_status\": 1}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-specs/349443458374701056/values', 'App\\Http\\Controllers\\Admin\\ProductSpecificationController@createValue', '2026-08-22 06:56:38.000000');
INSERT INTO `operation_log` VALUES (349446867861508096, 934035802554576899, '管理员', 'prod_spec', 'prod_spec_updated', 'UPDATE', 349443458643136512, '修改成功', NULL, '{\"id\": \"349443458643136512\", \"value\": \"天青色\", \"editing\": true, \"spec_id\": \"349443458127237120\", \"sort_order\": 20, \"value_code\": \"GV000001\", \"value_status\": 1}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-specs/349443458127237120/values/349443458643136512', 'App\\Http\\Controllers\\Admin\\ProductSpecificationController@updateValue', '2026-08-22 06:57:05.000000');
INSERT INTO `operation_log` VALUES (349446887658622976, 934035802554576899, '管理员', 'prod_spec', 'prod_spec_updated', 'UPDATE', 349443458643136512, '修改成功', NULL, '{\"id\": \"349443458643136512\", \"value\": \"天青色\", \"editing\": true, \"spec_id\": \"349443458127237120\", \"sort_order\": 20, \"value_code\": \"GV000001\", \"value_status\": 1}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-specs/349443458127237120/values/349443458643136512', 'App\\Http\\Controllers\\Admin\\ProductSpecificationController@updateValue', '2026-08-22 06:57:10.000000');
INSERT INTO `operation_log` VALUES (349446925029871616, 934035802554576899, '管理员', 'prod_spec', 'prod_spec_updated', 'UPDATE', 349443458898989056, '修改成功', NULL, '{\"id\": \"349443458898989056\", \"value\": \"珍珠白\", \"editing\": true, \"spec_id\": \"349443458127237120\", \"sort_order\": 10, \"value_code\": \"GV000002\", \"value_status\": 1}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/product-specs/349443458127237120/values/349443458898989056', 'App\\Http\\Controllers\\Admin\\ProductSpecificationController@updateValue', '2026-08-22 06:57:19.000000');
INSERT INTO `operation_log` VALUES (349448704811470848, 934035802554576899, '管理员', 'product', 'product_updated', 'UPDATE', 349443459406499840, '名杨布艺沙发', NULL, '{\"id\": \"349443459406499840\", \"skus\": [{\"id\": \"349443459683323904\", \"price\": 2999, \"volume\": \"0.0000\", \"weight\": \"0.00\", \"sku_code\": \"SK000001\", \"spec_text\": \"颜色:天青色 / 尺寸:2.2m\", \"stock_num\": 20, \"cost_price\": \"1800.00\", \"sort_order\": 10, \"sale_status\": 1, \"market_price\": \"3999.00\", \"spec_value_ids\": [\"349443458643136512\", \"349443459159035904\"]}, {\"id\": \"349443460429910016\", \"price\": 3099, \"volume\": \"0.0000\", \"weight\": \"0.00\", \"sku_code\": \"SK000002\", \"spec_text\": \"颜色:珍珠白 / 尺寸:2.2m\", \"stock_num\": 12, \"cost_price\": \"1850.00\", \"sort_order\": 9, \"sale_status\": 1, \"market_price\": \"4099.00\", \"spec_value_ids\": [\"349443458898989056\", \"349443459159035904\"]}], \"media\": [{\"id\": null, \"file_key\": null, \"file_url\": \"blob:http://localhost:8000/56b277f4-4881-4abb-a1e1-64014e43a0f0\", \"extension\": null, \"file_name\": \"头.jpg\", \"file_size\": 0, \"file_type\": null, \"media_type\": 1, \"sort_order\": 100, \"storage_provider\": \"local\"}, {\"id\": null, \"file_key\": null, \"file_url\": \"blob:http://localhost:8000/b2ad7a92-d757-4f7c-b34d-c77cf36b97dc\", \"extension\": null, \"file_name\": \"it.png\", \"file_size\": 0, \"file_type\": null, \"media_type\": 2, \"sort_order\": 100, \"storage_provider\": \"local\"}], \"filling\": \"高弹海绵\", \"brand_id\": \"349443457099632640\", \"auto_code\": \"SP000001\", \"min_price\": \"2999.00\", \"sku_count\": 2, \"stock_num\": 32, \"brand_name\": \"名杨\", \"created_at\": \"2026-08-22 06:43:33\", \"short_desc\": \"总结：请重点检查你 PHP 代码中处理该 ID 的 json_decode 或 PDO 查询环节，确保它在进入 PHP 内存时就是字符串格式。你可以先在接口返回处 var_dump($data[\'id\']) 看一下它的类型是不是 string。\", \"sort_order\": 10, \"category_id\": \"349443457862995968\", \"product_name\": \"名杨布艺沙发\", \"category_name\": \"沙发\", \"product_model\": \"MY-SF-01\", \"main_image_url\": \"blob:http://localhost:8000/56b277f4-4881-4abb-a1e1-64014e43a0f0\", \"product_status\": 1, \"material_quality\": \"棉麻\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/products/349443459406499840', 'App\\Http\\Controllers\\Admin\\ProductController@update', '2026-08-22 07:04:23.000000');
INSERT INTO `operation_log` VALUES (349449683376148480, 934035802554576899, '管理员', 'auth', 'logout', 'LOGOUT', 934035802554576899, 'admin', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/logout', 'App\\Http\\Controllers\\Admin\\AuthController@logout', '2026-08-22 07:08:16.000000');
INSERT INTO `operation_log` VALUES (349449718478278656, 349445981730902016, '产品', 'auth', 'login', 'LOGIN', 349445981730902016, 'product', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/login', 'App\\Http\\Controllers\\Admin\\AuthController@login', '2026-08-22 07:08:25.000000');
INSERT INTO `operation_log` VALUES (349449912812965888, 349445981730902016, '产品', 'auth', 'logout', 'LOGOUT', 349445981730902016, 'product', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/logout', 'App\\Http\\Controllers\\Admin\\AuthController@logout', '2026-08-22 07:09:11.000000');
INSERT INTO `operation_log` VALUES (349449929200111616, 934035802554576899, '管理员', 'auth', 'login', 'LOGIN', 934035802554576899, 'admin', NULL, NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/login', 'App\\Http\\Controllers\\Admin\\AuthController@login', '2026-08-22 07:09:15.000000');
INSERT INTO `operation_log` VALUES (349450307538915328, 934035802554576899, '管理员', 'config', 'config_updated', 'UPDATE', 0, '保存成功', NULL, '{\"values\": {\"site_icp\": \"粤ICP备2026110666号\", \"seo_title\": \"名杨科技\", \"site_logo\": null, \"site_name\": \"名杨科技\", \"contact_name\": null, \"seo_keywords\": \"名杨科技企业管理\", \"social_weibo\": null, \"contact_email\": null, \"contact_phone\": null, \"social_douyin\": null, \"social_wechat\": null, \"site_copyright\": \"Copyright © 名杨科技\", \"contact_address\": null, \"seo_description\": \"名杨科技官方网站\"}}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/site-configs', 'App\\Http\\Controllers\\Admin\\SiteConfigController@update', '2026-08-22 07:10:45.000000');
INSERT INTO `operation_log` VALUES (349452959337287680, 934035802554576899, '管理员', 'ad_material', 'ad_material_updated', 'UPDATE', 349410722486292480, '阳光管理系统上线', NULL, '{\"id\": \"349410722486292480\", \"title\": \"阳光管理系统上线\", \"status\": 1, \"target\": \"_self\", \"end_time\": null, \"link_url\": \"/admin\", \"image_url\": \"/storage/ads/2026/08/22/349452948864110592.jpg\", \"created_at\": \"2026-08-22 04:33:27\", \"sort_order\": 10, \"start_time\": null, \"position_id\": \"349410721982976000\", \"position_code\": \"home_top_banner\", \"position_name\": \"首页顶部轮播图\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/ad-materials/349410722486292480', 'App\\Http\\Controllers\\Admin\\AdMaterialController@update', '2026-08-22 07:21:18.000000');
INSERT INTO `operation_log` VALUES (349453055026139136, 934035802554576899, '管理员', 'ad_material', 'ad_material_created', 'INSERT', 0, '轮播图2', NULL, '{\"id\": null, \"title\": \"轮播图2\", \"status\": 1, \"target\": \"_blank\", \"end_time\": null, \"link_url\": null, \"image_url\": \"/storage/ads/2026/08/22/349453032066519040.jpg\", \"created_at\": \"2026-08-22 04:33:27\", \"sort_order\": 0, \"start_time\": null, \"position_id\": \"349410721982976000\", \"position_code\": \"home_top_banner\", \"position_name\": \"首页顶部轮播图\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/ad-materials', 'App\\Http\\Controllers\\Admin\\AdMaterialController@store', '2026-08-22 07:21:40.000000');
INSERT INTO `operation_log` VALUES (349453099020193792, 934035802554576899, '管理员', 'ad_material', 'ad_material_updated', 'UPDATE', 349453054296330240, '轮播图2', NULL, '{\"id\": \"349453054296330240\", \"title\": \"轮播图2\", \"status\": 1, \"target\": \"_blank\", \"end_time\": null, \"link_url\": \"/admin\", \"image_url\": \"/storage/ads/2026/08/22/349453032066519040.jpg\", \"created_at\": \"2026-08-22 07:21:40\", \"sort_order\": 0, \"start_time\": null, \"position_id\": \"349410721982976000\", \"position_code\": \"home_top_banner\", \"position_name\": \"首页顶部轮播图\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/ad-materials/349453054296330240', 'App\\Http\\Controllers\\Admin\\AdMaterialController@update', '2026-08-22 07:21:51.000000');
INSERT INTO `operation_log` VALUES (349457091544616960, 934035802554576899, '管理员', 'product', 'product_updated', 'UPDATE', 349443459406499840, '名杨布艺沙发', NULL, '{\"id\": \"349443459406499840\", \"skus\": [{\"id\": \"349443459683323904\", \"price\": 2999, \"volume\": \"0.0000\", \"weight\": \"0.00\", \"sku_code\": \"SK000001\", \"spec_text\": \"颜色:天青色 / 尺寸:2.2m\", \"stock_num\": 20, \"cost_price\": \"1800.00\", \"sort_order\": 10, \"sale_status\": 1, \"market_price\": \"3999.00\", \"spec_value_ids\": [\"349443458643136512\", \"349443459159035904\"]}, {\"id\": \"349443460429910016\", \"price\": 3099, \"volume\": \"0.0000\", \"weight\": \"0.00\", \"sku_code\": \"SK000002\", \"spec_text\": \"颜色:珍珠白 / 尺寸:2.2m\", \"stock_num\": 12, \"cost_price\": \"1850.00\", \"sort_order\": 9, \"sale_status\": 1, \"market_price\": \"4099.00\", \"spec_value_ids\": [\"349443458898989056\", \"349443459159035904\"]}], \"media\": [{\"id\": null, \"file_key\": null, \"file_url\": \"blob:http://localhost:8000/f1f419ea-c50c-45c9-bc9a-6401970e73af\", \"extension\": null, \"file_name\": \"1.png\", \"file_size\": 0, \"file_type\": null, \"media_type\": 1, \"sort_order\": 100, \"storage_provider\": \"local\"}, {\"id\": null, \"file_key\": null, \"file_url\": \"blob:http://localhost:8000/20975b72-2f32-4b37-994a-36d89349bf93\", \"extension\": null, \"file_name\": \"7.png\", \"file_size\": 0, \"file_type\": null, \"media_type\": 2, \"sort_order\": 100, \"storage_provider\": \"local\"}], \"filling\": \"高弹海绵\", \"brand_id\": \"349443457099632640\", \"auto_code\": \"SP000001\", \"min_price\": \"2999.00\", \"sku_count\": 2, \"stock_num\": 32, \"brand_name\": \"名杨\", \"created_at\": \"2026-08-22 06:43:33\", \"short_desc\": \"总结：请重点检查你 PHP 代码中处理该 ID 的 json_decode 或 PDO 查询环节，确保它在进入 PHP 内存时就是字符串格式。你可以先在接口返回处 var_dump($data[\'id\']) 看一下它的类型是不是 string。\", \"sort_order\": 10, \"category_id\": \"349443457862995968\", \"product_name\": \"名杨布艺沙发\", \"category_name\": \"沙发\", \"product_model\": \"MY-SF-01\", \"main_image_url\": \"blob:http://localhost:8000/f1f419ea-c50c-45c9-bc9a-6401970e73af\", \"product_status\": 1, \"material_quality\": \"棉麻\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'http://localhost:8000/api/admin/products/349443459406499840', 'App\\Http\\Controllers\\Admin\\ProductController@update', '2026-08-22 07:37:43.000000');

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `auto_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码SP000001',
  `product_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `product_model` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品型号',
  `category_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品分类ID',
  `brand_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '品牌ID',
  `material_quality` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '材质',
  `filling` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '填充',
  `short_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '商品简短描述',
  `main_image_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '主图URL',
  `product_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=已下架 1=已上架',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_product_auto_code`(`auto_code` ASC) USING BTREE,
  INDEX `product_category_id_index`(`category_id` ASC) USING BTREE,
  INDEX `product_brand_id_index`(`brand_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product
-- ----------------------------
INSERT INTO `product` VALUES (349443459406499840, 'SP000001', '名杨布艺沙发', 'MY-SF-01', 349443457862995968, 349443457099632640, '棉麻', '高弹海绵', '总结：请重点检查你 PHP 代码中处理该 ID 的 json_decode 或 PDO 查询环节，确保它在进入 PHP 内存时就是字符串格式。你可以先在接口返回处 var_dump($data[\'id\']) 看一下它的类型是不是 string。', 'blob:http://localhost:8000/f1f419ea-c50c-45c9-bc9a-6401970e73af', 1, 10, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 07:37:41.000000', 934035802554576899, NULL, NULL);

-- ----------------------------
-- Table structure for product_brand
-- ----------------------------
DROP TABLE IF EXISTS `product_brand`;
CREATE TABLE `product_brand`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `brand_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码BR000001',
  `brand_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌名称',
  `alias` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '英文别名',
  `is_system` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否系统预设',
  `is_show` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `brand_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_brand_code`(`brand_code` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_brand
-- ----------------------------
INSERT INTO `product_brand` VALUES (349443457099632640, 'BR000001', '名杨', 'MingYang', 1, 1, 20, '', '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:43:32.000000', NULL, NULL, NULL);
INSERT INTO `product_brand` VALUES (349443457351290880, 'BR000002', '恒大', 'hengda', 1, 1, 2, '', '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:55:58.000000', 934035802554576899, NULL, NULL);
INSERT INTO `product_brand` VALUES (349446651821297664, 'BR000003', '苹果Apple', 'apple', 0, 1, 4, '', '2026-08-22 06:56:14.000000', 934035802554576899, '2026-08-22 06:56:14.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `category_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码FL000001',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级分类ID',
  `level` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '级别',
  `product_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品数量',
  `unit` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数量单位',
  `cat_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `cat_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_category_code`(`category_code` ASC) USING BTREE,
  INDEX `category_parent_id_index`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_category
-- ----------------------------
INSERT INTO `product_category` VALUES (349443457611337728, 'FL000001', '家具', 0, 1, 0, '件', 1, 20, '', '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:43:32.000000', NULL, NULL, NULL);
INSERT INTO `product_category` VALUES (349443457862995968, 'FL000002', '沙发', 349443457611337728, 2, 1, '套', 1, 10, '', '2026-08-22 06:43:32.000000', NULL, '2026-08-22 07:37:42.000000', NULL, NULL, NULL);
INSERT INTO `product_category` VALUES (349446448322056192, 'FL000003', '数码', 0, 1, 0, '部', 1, 2, '', '2026-08-22 06:55:25.000000', 934035802554576899, '2026-08-22 06:55:25.000000', NULL, NULL, NULL);
INSERT INTO `product_category` VALUES (349446500713107456, 'FL000004', '手机', 349446448322056192, 2, 0, '部', 1, 3, '', '2026-08-22 06:55:38.000000', 934035802554576899, '2026-08-22 06:55:38.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_media
-- ----------------------------
DROP TABLE IF EXISTS `product_media`;
CREATE TABLE `product_media`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `product_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
  `media_type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型 1=主图 2=详情图 3=视频 4=资质文件 5=其他附件',
  `file_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '文件URL',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '原始文件名',
  `file_key` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '存储键/路径',
  `storage_provider` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储提供方',
  `extension` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件拓展名',
  `file_size` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '字节大小',
  `file_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'MimeType',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `media_product_id_index`(`product_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_media
-- ----------------------------
INSERT INTO `product_media` VALUES (349448701032402944, 349443459406499840, 1, 'blob:http://localhost:8000/56b277f4-4881-4abb-a1e1-64014e43a0f0', '头.jpg', '', 'local', '', 0, '', 100, '2026-08-22 07:04:22.000000', 934035802554576899, '2026-08-22 07:37:42.000000', NULL, '2026-08-22 07:37:42.000000', 934035802554576899);
INSERT INTO `product_media` VALUES (349448701225340928, 349443459406499840, 2, 'blob:http://localhost:8000/b2ad7a92-d757-4f7c-b34d-c77cf36b97dc', 'it.png', '', 'local', '', 0, '', 100, '2026-08-22 07:04:22.000000', 934035802554576899, '2026-08-22 07:37:42.000000', NULL, '2026-08-22 07:37:42.000000', 934035802554576899);
INSERT INTO `product_media` VALUES (349457087417421824, 349443459406499840, 1, 'blob:http://localhost:8000/f1f419ea-c50c-45c9-bc9a-6401970e73af', '1.png', '', 'local', '', 0, '', 100, '2026-08-22 07:37:42.000000', 934035802554576899, '2026-08-22 07:37:42.000000', NULL, NULL, NULL);
INSERT INTO `product_media` VALUES (349457087597776896, 349443459406499840, 2, 'blob:http://localhost:8000/20975b72-2f32-4b37-994a-36d89349bf93', '7.png', '', 'local', '', 0, '', 100, '2026-08-22 07:37:42.000000', 934035802554576899, '2026-08-22 07:37:42.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_sku
-- ----------------------------
DROP TABLE IF EXISTS `product_sku`;
CREATE TABLE `product_sku`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `product_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
  `sku_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SKU编码',
  `price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '销售价',
  `market_price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '划线价',
  `cost_price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '成本价',
  `stock_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '库存数量',
  `weight` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '重量(KG)',
  `volume` decimal(10, 4) UNSIGNED NOT NULL DEFAULT 0.0000 COMMENT '体积(m³)',
  `sale_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '销售状态',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sku_code_unique`(`sku_code` ASC) USING BTREE,
  INDEX `sku_product_id_index`(`product_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_sku
-- ----------------------------
INSERT INTO `product_sku` VALUES (349443459683323904, 349443459406499840, 'SK000001', 2999.00, 3999.00, 1800.00, 20, 0.00, 0.0000, 1, 10, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 07:04:22.000000', 934035802554576899, NULL, NULL);
INSERT INTO `product_sku` VALUES (349443460429910016, 349443459406499840, 'SK000002', 3099.00, 4099.00, 1850.00, 12, 0.00, 0.0000, 1, 9, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 07:04:22.000000', 934035802554576899, NULL, NULL);

-- ----------------------------
-- Table structure for product_sku_spec_value
-- ----------------------------
DROP TABLE IF EXISTS `product_sku_spec_value`;
CREATE TABLE `product_sku_spec_value`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `sku_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联SKU表ID',
  `spec_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联规格维度ID',
  `spec_value_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联规格值ID',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sku_spec_value_unique`(`sku_id` ASC, `spec_id` ASC, `spec_value_id` ASC) USING BTREE,
  INDEX `sku_spec_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `sku_spec_value_id_index`(`spec_value_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_sku_spec_value
-- ----------------------------
INSERT INTO `product_sku_spec_value` VALUES (349443459930787840, 349443459683323904, 349443458127237120, 349443458643136512, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 06:43:33.000000', NULL, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (349443460165668864, 349443459683323904, 349443458374701056, 349443459159035904, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 06:43:33.000000', NULL, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (349443460698345472, 349443460429910016, 349443458127237120, 349443458898989056, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 06:43:33.000000', NULL, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (349443460937420800, 349443460429910016, 349443458374701056, 349443459159035904, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 06:43:33.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_specification
-- ----------------------------
DROP TABLE IF EXISTS `product_specification`;
CREATE TABLE `product_specification`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `spec_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码GL000001',
  `spec_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格名称',
  `spec_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `spec_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_spec_code`(`spec_code` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specification
-- ----------------------------
INSERT INTO `product_specification` VALUES (349443458127237120, 'GL000001', '颜色', '', 1, 20, '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:43:32.000000', NULL, NULL, NULL);
INSERT INTO `product_specification` VALUES (349443458374701056, 'GL000002', '尺寸', '', 1, 10, '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:43:32.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_specification_value
-- ----------------------------
DROP TABLE IF EXISTS `product_specification_value`;
CREATE TABLE `product_specification_value`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键(雪花ID)',
  `spec_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格ID',
  `value_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码GV000001',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格值',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `value_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_value_code`(`value_code` ASC) USING BTREE,
  INDEX `spec_value_spec_id_index`(`spec_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specification_value
-- ----------------------------
INSERT INTO `product_specification_value` VALUES (349443458643136512, 349443458127237120, 'GV000001', '天青色', 20, 1, '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:57:05.000000', 934035802554576899, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (349443458898989056, 349443458127237120, 'GV000002', '珍珠白', 10, 1, '2026-08-22 06:43:32.000000', NULL, '2026-08-22 06:57:19.000000', 934035802554576899, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (349443459159035904, 349443458374701056, 'GV000003', '2.2m', 10, 1, '2026-08-22 06:43:33.000000', NULL, '2026-08-22 06:43:33.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (349446751805116416, 349443458374701056, 'GV000004', '1.8m', 11, 1, '2026-08-22 06:56:38.000000', 934035802554576899, '2026-08-22 06:56:38.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('kFwsbjT3edXdNKGndBgcQdCT1Pwv49MkJ0dNEZt7', 934035802554576899, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJvbEJOMzVUcmtZRlp3NzlYNTdEUVU1eEp2T0I5aFgwN3A4cEx0YWFhIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcLy53ZWxsLWtub3duXC9hcHBzcGVjaWZpY1wvY29tLmNocm9tZS5kZXZ0b29scy5qc29uIiwicm91dGUiOm51bGx9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk5fQ==', 1787384283);
INSERT INTO `sessions` VALUES ('WGVYeaL5oaEWS1NHtQd10DrmcukRg0DmLMv6tPVe', 934035802554576899, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'eyJfdG9rZW4iOiJhMlQxeGhjcWVzcmM3ckhtTkVaS29JY2NMdVVkc1hzMURMRk0ybVJGIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FkbWluXC9zeXN0ZW1cL2xvZ1wvb3BlcmxvZyIsInJvdXRlIjpudWxsfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkzNDAzNTgwMjU1NDU3Njg5OX0=', 1787383962);

-- ----------------------------
-- Table structure for site_configs
-- ----------------------------
DROP TABLE IF EXISTS `site_configs`;
CREATE TABLE `site_configs`  (
  `id` bigint UNSIGNED NOT NULL,
  `conf_group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic' COMMENT '配置分组：basic, seo, contact, social',
  `conf_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置键名',
  `conf_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '配置值',
  `conf_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `input_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text' COMMENT '输入类型：text, textarea, image, file, json',
  `conf_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_conf_key`(`conf_key` ASC) USING BTREE,
  INDEX `idx_conf_group`(`conf_group` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of site_configs
-- ----------------------------
INSERT INTO `site_configs` VALUES (349404825663967232, 'basic', 'site_name', '名杨科技', '网站名称', 'text', 90, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404826100174848, 'basic', 'site_logo', '', '网站Logo', 'image', 80, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404826540576768, 'basic', 'site_icp', '粤ICP备2026110666号', '备案号', 'text', 70, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404826989367296, 'basic', 'site_copyright', 'Copyright © 名杨科技', '版权信息', 'textarea', 60, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404827442352128, 'seo', 'seo_title', '名杨科技', 'SEO标题', 'text', 90, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404827886948352, 'seo', 'seo_keywords', '名杨科技企业管理', 'SEO关键词', 'text', 80, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404828331544576, 'seo', 'seo_description', '名杨科技官方网站', 'SEO描述', 'textarea', 70, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404828776140800, 'contact', 'contact_name', '', '联系人', 'text', 90, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404829224931328, 'contact', 'contact_phone', '', '联系电话', 'text', 80, '2026-08-22 04:10:02', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404829711470592, 'contact', 'contact_email', '', '联系邮箱', 'text', 70, '2026-08-22 04:10:03', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404830168649728, 'contact', 'contact_address', '', '公司地址', 'textarea', 60, '2026-08-22 04:10:03', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404830634217472, 'social', 'social_wechat', '', '微信', 'text', 90, '2026-08-22 04:10:03', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404831108173824, 'social', 'social_weibo', '', '微博', 'text', 80, '2026-08-22 04:10:03', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349404831577935872, 'social', 'social_douyin', '', '抖音', 'text', 70, '2026-08-22 04:10:03', '2026-08-22 07:10:45');
INSERT INTO `site_configs` VALUES (349456035087519744, 'basic', 'about_intro', '名杨科技专注家居与智能办公产品的设计与制造。我们以材料、结构与工艺为根基，持续打磨每一件产品的耐用度与使用体验，服务企业空间与家庭生活。', '关于我们简介', 'textarea', 50, '2026-08-22 07:33:31', '2026-08-22 07:33:31');

-- ----------------------------
-- Table structure for user_account
-- ----------------------------
DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account`  (
  `id` bigint UNSIGNED NOT NULL COMMENT '用户唯一主键ID（雪花ID，不自增，分布式安全）',
  `user_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账号用户名，唯一，可用于登录',
  `real_name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '真名',
  `user_mobile` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号，唯一索引，登录首选',
  `user_email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱，唯一索引，找回密码',
  `password_hash` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'BCrypt/Argon2加密密码，禁止明文存储',
  `password_salt` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '自定义盐值（BCrypt自带盐可留空）',
  `user_status` tinyint NOT NULL DEFAULT 1 COMMENT '账号状态：0-禁用 1-正常 2-冻结 3-注销',
  `lock_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '冻结/封禁原因（风控、违规、人工封禁）',
  `lock_expire_time` datetime NULL DEFAULT NULL COMMENT '限时冻结到期时间，NULL=永久封禁',
  `last_login_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_region` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP归属地',
  `last_login_at` datetime NULL DEFAULT NULL COMMENT '最后登录时间',
  `register_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册IP',
  `register_device` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册设备标识',
  `register_channel` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '注册渠道：web/app/mini/ios/android',
  `real_auth_status` tinyint NOT NULL DEFAULT 0 COMMENT '实名状态：0未实名 1待审核 2已实名 3实名驳回',
  `dept_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门ID',
  `post_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属岗位ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间（软删除记录）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_username`(`user_name` ASC) USING BTREE,
  UNIQUE INDEX `uk_mobile`(`user_mobile` ASC) USING BTREE,
  UNIQUE INDEX `uk_email`(`user_email` ASC) USING BTREE,
  INDEX `idx_status_auth`(`user_status` ASC, `real_auth_status` ASC) USING BTREE,
  INDEX `idx_deleted_time`(`created_at` ASC) USING BTREE,
  INDEX `idx_dept_id`(`dept_id` ASC) USING BTREE,
  INDEX `idx_post_id`(`post_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_account
-- ----------------------------
INSERT INTO `user_account` VALUES (349435902843555840, 'yunying', '阳光', '13026661119', 'git@163.com', '$2y$12$aT0WC62ATTQtYqneXchat.e6a2ufEX5cnA.H4Zb2oZ4P.omm2zdyW', '', 1, '', NULL, '', '', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'web', 0, 349395061739491328, 349402374793728000, '2026-08-22 06:13:31', '2026-08-22 06:17:58', NULL);
INSERT INTO `user_account` VALUES (349436695722201088, 'caiwu', '财务', '13800138666', 'caiwu@163.com', '$2y$12$vSMy9F9xUBNU9FGOiCWxPu0QpUdxEuxOOuhJQefdjVY.UKi1TAJfi', '', 1, '', NULL, '127.0.0.1', '', '2026-08-22 06:17:17', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'web', 0, 349395061265534976, 349402375041191936, '2026-08-22 06:16:40', '2026-08-22 06:17:17', NULL);
INSERT INTO `user_account` VALUES (349445981730902016, 'product', '产品', '15632323212', 'githup@163.com', '$2y$12$6d0uKNaSaS8hawix0DadheHQyqftD2.SotL9u7dFPOcvibX.cMJFW', '', 1, '', NULL, '127.0.0.1', '', '2026-08-22 07:08:25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'web', 0, 349395060770607104, 349402374793728000, '2026-08-22 06:53:34', '2026-08-22 07:08:25', NULL);
INSERT INTO `user_account` VALUES (934035802554576899, 'admin', '管理员', '13800000000', 'admin@example.com', '$2y$12$bUFOSbva4Flqm1LrV9RXZOGVJr2yYrhRazMILnskmWn15Kg71f9iS', '', 1, '', NULL, '127.0.0.1', '', '2026-08-22 07:09:15', '35.17.9.4', 'factory', 'web', 0, 349395059772362752, 349402374026170368, '2026-08-22 02:38:25', '2026-08-22 07:09:15', NULL);

SET FOREIGN_KEY_CHECKS = 1;
