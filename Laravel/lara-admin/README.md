# Laravel-Admin 后台学习
Laravel-Admin 后台文档地址： https://laravel-admin.org/docs/zh/         

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
