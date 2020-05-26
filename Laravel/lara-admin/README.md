# Laravel-Admin 后台学习
Laravel-Admin 后台文档地址： https://laravel-admin.org/docs/zh/         

文件有版本控制，数据表需要手动备份了。      
最方便的是直接把整个数据库导出来，然后再导入，反正都是测试用的。        

# 模型表格
## 基本使用
创建数据表
```sql
CREATE TABLE `movies` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`director` INT(10) UNSIGNED NOT NULL,
	`describe` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`rate` TINYINT(3) UNSIGNED NOT NULL,
	`released` ENUM('0','1') NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`release_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) 
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
```

创建用户及角色表
```sql
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp 
CREATE TABLE `role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `profiles` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`age` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`gender` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`created_at` TIMESTAMP NULL DEFAULT NULL,
`updated_at` TIMESTAMP NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

文章表以及评论表
```sql
CREATE TABLE `posts` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `comments` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`post_id` int(10) unsigned NOT NULL,
`content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

创建一个表，专门用来显示列的内容            
```sql
CREATE TABLE `column_show` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '标题',
`gender` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '性别',
`email` varchar(255) COLLATE utf8_unicode_ci NULL COMMENT '邮箱',
`cost` decimal(8,2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.00' COMMENT '价格',
`content` varchar(255) COLLATE utf8_unicode_ci NULL COMMENT '内容',
`first_name` varchar(255) COLLATE utf8_unicode_ci NULL COMMENT '名',
`last_name` varchar(255) COLLATE utf8_unicode_ci NULL COMMENT '姓',
`file_size` bigint(12) unsigned NOT NULL DEFAULT 0 COMMENT '文件尺寸' ,
`file_path` varchar(255)  NOT NULL DEFAULT '' COMMENT '下载链接' ,
`picture` varchar(255)  NOT NULL DEFAULT '' COMMENT '图片路径' ,
`link` varchar(255)  NOT NULL DEFAULT 'https://runoob.com' COMMENT '跳转链接' ,
`images` text   NULL COMMENT '轮播图' ,
`status` tinyint(1) unsigned  NOT NULL DEFAULT 1 COMMENT '状态 ' ,
`approved` tinyint(1) unsigned  NOT NULL DEFAULT 1 COMMENT '是否同意' ,
`trashed` tinyint(1) unsigned  NOT NULL DEFAULT 0 COMMENT '被软删除的数据' ,
`progress` int(10) unsigned  NOT NULL DEFAULT 50 COMMENT '进度' ,
`created_at` timestamp NULL DEFAULT NULL COMMENT '发布时间',
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```
