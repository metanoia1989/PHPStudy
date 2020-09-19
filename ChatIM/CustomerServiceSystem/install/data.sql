SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wolive_admin`;
CREATE TABLE `wolive_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  `is_delete` smallint(6) NOT NULL DEFAULT '0',
  `app_max_count` int(11) NOT NULL DEFAULT '0',
  `permission` longtext,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '账户有效期至，0表示永久',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_admin_token`;
CREATE TABLE `wolive_admin_token` (
  `token` varchar(50) NOT NULL COMMENT 'Token',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expiretime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Token表';

DROP TABLE IF EXISTS `wolive_business`;
CREATE TABLE `wolive_business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(100) NOT NULL COMMENT '商家标识符',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '底部版权信息',
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `video_state` enum('close','open') NOT NULL DEFAULT 'close' COMMENT '是否开启视频',
  `voice_state` enum('close','open') NOT NULL DEFAULT 'open' COMMENT '是否开启提示音',
  `audio_state` enum('close','open') NOT NULL DEFAULT 'close' COMMENT '是否开启音频',
  `template_state` enum('close','open') NOT NULL DEFAULT 'close' COMMENT '是否开启模板消息',
  `distribution_rule` enum('auto','claim') DEFAULT 'auto' COMMENT 'claim:认领，auto:自动分配',
  `voice_address` varchar(255) NOT NULL DEFAULT '/upload/voice/default.mp3' COMMENT '提示音文件地址',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `expire_time` int(11) NOT NULL DEFAULT '0',
  `max_count` int(11) NOT NULL DEFAULT '0',
  `push_url`  varchar(255) NOT NULL DEFAULT '' COMMENT '推送url' ,
  `state` enum('close','open') NOT NULL DEFAULT 'open' COMMENT '''open'': 打开该商户 ，‘close’：禁止该商户',
  `is_recycle` tinyint(2) NOT NULL DEFAULT '0',
  `is_delete` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `bussiness` (`business_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_chats`;
CREATE TABLE `wolive_chats` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `visiter_id` varchar(200) NOT NULL COMMENT '访客id',
  `service_id` int(11) NOT NULL COMMENT '客服id',
  `business_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家id',
  `content` mediumtext NOT NULL COMMENT '内容',
  `timestamp` int(11) NOT NULL,
  `state` enum('readed','unread') NOT NULL DEFAULT 'unread' COMMENT 'unread 未读；readed 已读',
  `direction` enum('to_visiter','to_service') DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_group`;
CREATE TABLE `wolive_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(255) DEFAULT NULL,
  `business_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_message`;
CREATE TABLE `wolive_message` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '留言内容',
  `name` varchar(255) NOT NULL COMMENT '留言人姓名',
  `moblie` varchar(255) NOT NULL COMMENT '留言人电话',
  `email` varchar(255) NOT NULL COMMENT '留言人邮箱',
  `business_id` int(11) DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`),
  KEY `timestamp` (`timestamp`),
  KEY `web` (`business_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_option`;
CREATE TABLE `wolive_option` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL DEFAULT '0',
  `group` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_id` (`business_id`) USING BTREE,
  KEY `group` (`group`) USING BTREE,
  KEY `name` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_question`;
CREATE TABLE `wolive_question` (
  `qid` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL DEFAULT '0',
  `question` longtext NOT NULL,
  `keyword`  varchar(12) NOT NULL DEFAULT '' COMMENT '关键词',
  `sort`  int(11) NOT NULL DEFAULT '0',
  `answer` longtext NOT NULL,
  `answer_read` longtext NOT NULL,
  PRIMARY KEY (`qid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_queue`;
CREATE TABLE `wolive_queue` (
  `qid` int(11) NOT NULL AUTO_INCREMENT,
  `visiter_id` varchar(200) NOT NULL COMMENT '访客id',
  `service_id` int(11) NOT NULL COMMENT '客服id',
  `groupid` int(11) DEFAULT '0' COMMENT '客服分类id',
  `business_id` int(11) NOT NULL DEFAULT '0',
  `state` enum('normal','complete','in_black_list') NOT NULL DEFAULT 'normal' COMMENT 'normal：正常接入,‘complete’:已经解决，‘in_black_list’:黑名单',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remind_tpl`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否已发送模板消息' ,
  `remind_comment`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否已推送评价' ,
  PRIMARY KEY (`qid`),
  KEY `se` (`service_id`) USING BTREE,
  KEY `vi` (`visiter_id`) USING BTREE,
  KEY `business` (`business_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_reply`;
CREATE TABLE `wolive_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_sentence`;
CREATE TABLE `wolive_sentence` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '内容',
  `service_id` int(11) NOT NULL COMMENT '所属客服id',
  `state` enum('using','unuse') DEFAULT 'unuse' COMMENT 'unuse: 未使用 ，using：使用中',
  PRIMARY KEY (`sid`),
  KEY `se` (`service_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_service`;
CREATE TABLE `wolive_service` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL COMMENT '用户名',
  `nick_name` varchar(255) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `groupid` varchar(225) DEFAULT '0' COMMENT '客服分类id',
  `phone` varchar(255) DEFAULT '' COMMENT '手机',
  `open_id` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT '' COMMENT '邮箱',
  `business_id` int(11) NOT NULL DEFAULT '0',
  `avatar` varchar(1024) NOT NULL DEFAULT '/assets/images/admin/avatar-admin2.png' COMMENT '头像',
  `level` enum('super_manager','manager','service') NOT NULL DEFAULT 'service' COMMENT 'super_manager: 超级管理员，manager：商家管理员 ，service：普通客服',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属商家管理员id',
  `offline_first` tinyint(2) NOT NULL DEFAULT '0',
  `state` enum('online','offline') NOT NULL DEFAULT 'offline' COMMENT 'online：在线，offline：离线',
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `user_name` (`user_name`,`business_id`) USING BTREE,
  KEY `pid` (`parent_id`) USING BTREE,
  KEY `web` (`business_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_tablist`;
CREATE TABLE `wolive_tablist` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'tab的名称',
  `content_read` text,
  `content` text NOT NULL,
  `business_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_visiter`;
CREATE TABLE `wolive_visiter` (
  `vid` int(11) NOT NULL AUTO_INCREMENT,
  `visiter_id` varchar(200) NOT NULL COMMENT '访客id',
  `visiter_name` varchar(255) NOT NULL COMMENT '访客名称',
  `channel` varchar(255) NOT NULL COMMENT '用户游客频道',
  `avatar` varchar(1024) NOT NULL COMMENT '头像',
  `name`  varchar(255) NOT NULL DEFAULT '' COMMENT '用户自己填写的姓名',
  `tel`  varchar(32) NOT NULL DEFAULT '' COMMENT '用户自己填写的电话',
  `login_times`  int(11) NOT NULL DEFAULT 1 COMMENT '登录次数' ,
  `connect` text COMMENT '联系方式',
  `comment` text COMMENT '备注',
  `ip` varchar(255) NOT NULL COMMENT '访客ip',
  `from_url` varchar(255) NOT NULL COMMENT '访客浏览地址',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '访问时间',
  `business_id` int(11) NOT NULL DEFAULT '0',
  `state` enum('online','offline') NOT NULL DEFAULT 'offline' COMMENT 'offline：离线，online：在线',
  PRIMARY KEY (`vid`),
  UNIQUE KEY `id` (`visiter_id`,`business_id`) USING BTREE,
  KEY `visiter` (`visiter_id`) USING BTREE,
  KEY `time` (`timestamp`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_wechat_platform`;
CREATE TABLE `wolive_wechat_platform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL DEFAULT '0' COMMENT '客服系统id',
  `app_id` varchar(255) NOT NULL COMMENT '公众号appid',
  `app_secret` varchar(255) NOT NULL COMMENT '公众号appsecret',
  `visitor_tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '新访客模板消息',
  `msg_tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '新消息提示模板消息',
  `customer_tpl`  varchar(255) NOT NULL DEFAULT '' COMMENT '访客模板消息' ,
  `desc` varchar(255) NOT NULL COMMENT '公共号说明、备注',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `is_delete` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_id` (`business_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信公众号';

DROP TABLE IF EXISTS `wolive_weixin`;
CREATE TABLE `wolive_weixin` (
  `wid` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL COMMENT '客服id',
  `open_id` varchar(255) NOT NULL COMMENT '微信用户id',
  PRIMARY KEY (`wid`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_vgroup`;
CREATE TABLE `wolive_vgroup` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `business_id`  int(11) NOT NULL DEFAULT 0 ,
  `service_id`  int(11) NOT NULL DEFAULT 0 ,
  `group_name`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
  `create_time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `status`  tinyint(4) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

DROP TABLE IF EXISTS `wolive_visiter_vgroup`;
CREATE TABLE `wolive_visiter_vgroup` (
  `vid`  int(11) NOT NULL ,
  `business_id`  int(11) NOT NULL DEFAULT 0 ,
  `service_id`  int(11) NOT NULL DEFAULT 0 ,
  `group_id`  int(11) NOT NULL DEFAULT 0 ,
  `create_time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`vid`, `group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

DROP TABLE IF EXISTS `wolive_comment_setting`;
CREATE TABLE `wolive_comment_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id`  int(11) NOT NULL DEFAULT 0 ,
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '评价说明',
  `comments` text NOT NULL COMMENT '评价条目',
  `word_switch` enum('close','open') NOT NULL DEFAULT 'close',
  `word_title` varchar(32) NOT NULL DEFAULT '',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_comment`;
CREATE TABLE `wolive_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL DEFAULT '0',
  `service_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `visiter_id` varchar(200) NOT NULL DEFAULT '',
  `visiter_name` varchar(255) NOT NULL DEFAULT '',
  `word_comment` text NOT NULL COMMENT '文字评价',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_comment_detail`;
CREATE TABLE `wolive_comment_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned NOT NULL,
  `title` varchar(32) NOT NULL DEFAULT '',
  `score` tinyint(4) NOT NULL DEFAULT '1' COMMENT '分数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wolive_rest_setting`;
CREATE TABLE `wolive_rest_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL DEFAULT '0',
  `state` enum('open','close') NOT NULL DEFAULT 'open',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `week` varchar(32) NOT NULL DEFAULT '',
  `reply` varchar(255) NOT NULL DEFAULT '',
  `name_state` enum('open','close') NOT NULL DEFAULT 'open',
  `tel_state` enum('open','close') NOT NULL DEFAULT 'open',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
