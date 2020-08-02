# 自定义PHP框架
Lonicera 是忍冬属植物的学名，金银花、海仙花等都属于忍冬属。     

# 相关数据表
本来想直接用已经有的数据表，结果其无用字段太多，导致一条新数据都插不进去，还是得建立专门的数据库和表。  

```sql
CREATE DATABASE `lonicera` COLLATE 'utf8mb4_general_ci';

CREATE TABLE IF NOT EXISTS `o2o_user`(
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) NOT NULL COMMENT '用户名' COLLATE 'utf8mb4_general_ci',
    `age` INT(3) NOT NULL COMMENT '年龄' COLLATE 'utf8mb4_general_ci',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` TIMESTAMP NULL COMMENT '更新时间',
    PRIMARY KEY (`id`)
)
COMMENT='用户表'
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;
``` 