/* Replace this file with actual dump of your database */
DROP DATABASE IF EXISTS `testaccount`;
CREATE DATABASE `testaccount`CHARACTER SET utf8 COLLATE utf8_general_ci;
use testaccount;
/*---用户登录帐号表---*/
CREATE TABLE IF NOT EXISTS `account` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) DEFAULT NULL COMMENT '用户ID',
  `bind_type` CHAR(1) DEFAULT NULL COMMENT '绑定数据类型（手机号码、邮箱、第三方OPENID等，每种登录方式对应一个类型',
  `bind_value` VARCHAR(100) DEFAULT NULL COMMENT '绑定字段值',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='用户登录帐号表';

/*---用户帐号资料表---*/
CREATE TABLE IF NOT EXISTS `account_info` (
  `user_id` bigint(20) NOT NULL COMMENT '用户资料编号',
  `password` varchar(64) DEFAULT NULL COMMENT '用户登录密码',
  `nick_name` varchar(100) DEFAULT NULL COMMENT '用户昵称',
  `email` varchar(100) DEFAULT NULL COMMENT '用户邮箱，接收验证邮件使用',
  `email_verify` char(1) DEFAULT NULL COMMENT '邮箱验证状态',
  `city_id` int(11) DEFAULT NULL COMMENT '城市ID',
  `counties_id` int(11) DEFAULT NULL COMMENT '区县ID',
  `channel_id` bigint(20) DEFAULT NULL COMMENT '帐号注册来源渠道',
  `user_type` tinyint(4) DEFAULT NULL COMMENT '用户类型（集团用户、普通用户）',
  `avatar` varchar(100) DEFAULT NULL COMMENT '头像文件相对路径',
  `address` varchar(200) DEFAULT NULL COMMENT '用户家庭住址',
  `creation_date` datetime DEFAULT NULL COMMENT '用户注册时间',
  `modified_date` datetime DEFAULT NULL COMMENT '用户资料修改时间；请不要设置为更新时修改值',
  `account_status` tinyint(4) DEFAULT NULL COMMENT '用户状态，激活、禁用',
  `last_login` date DEFAULT NULL COMMENT '用户最后登录时间',
  `modified_count` tinyint(4) DEFAULT NULL COMMENT '用户帐号修改次数',
  `account_modified_date` datetime DEFAULT NULL COMMENT '帐号最后修改时间',
  `is_delete` char(1) DEFAULT 'N' COMMENT '帐号删除标记，N表示正常，D表示删除',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资料表';

/*----帐号ID计数器---*/
CREATE TABLE IF NOT EXISTS `userid_counter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `one` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*----家庭成员帐号表---*/
CREATE TABLE IF NOT EXISTS `family_members` (
  `member_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '家庭成员ID',
  `user_id` bigint(20) DEFAULT NULL COMMENT '帐号ID',
  `mobile` varchar(12) DEFAULT NULL COMMENT '手机号码',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `true_name` varchar(100) DEFAULT NULL COMMENT '真实姓名',
  `sex` char(1) DEFAULT NULL COMMENT '性别',
  `relation` char(1) DEFAULT NULL COMMENT '与主帐号关系',
  `job` varchar(100) DEFAULT NULL COMMENT '职业',
  `channel_id` bigint(20) DEFAULT NULL COMMENT '数据来源渠道',
  `unit_id` bigint(20) DEFAULT NULL COMMENT '数据来源医院',
  `blood_type` tinyint(4) DEFAULT NULL COMMENT '血型',
  `marital_status` char(1) DEFAULT NULL COMMENT '婚姻状态',
  `creation_date` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_date` datetime DEFAULT NULL COMMENT '修改时间',
  `is_delete` char(1) DEFAULT 'N' COMMENT '成员删除标记，N表示正常，D表示删除',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='家庭成员表';

/*----成员卡号表---*/
CREATE TABLE IF NOT EXISTS `member_card` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned DEFAULT NULL COMMENT '成员编号',
  `card_type` int(11) DEFAULT NULL COMMENT '卡类型',
  `card` varchar(100) DEFAULT NULL COMMENT '卡号',
  `card_verify` char(1) DEFAULT NULL COMMENT '验证状态',
  `creation_date` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成员卡号表';

/*---卡片类型表---*/
CREATE TABLE IF NOT EXISTS `card_type` (
  `type_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '卡片类型ID',
  `card_name` varchar(100) DEFAULT NULL COMMENT '卡片标识',
  `sort_id` int(11) DEFAULT NULL COMMENT '类型名称ID，程序中维护了一个对照表',
  `unit_id` bigint(20) DEFAULT NULL COMMENT '关联ID，可以为医院ID，城市ID等',
  `is_delete` char(1) DEFAULT 'N' COMMENT '卡类型删除标记，N表示正常，D表示删除',
  `validator` varchar(100) DEFAULT NULL COMMENT '卡号验证器名称',
  `comment` text DEFAULT NULL COMMENT '卡的描述信息',
  `creation_date` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卡片类型表';

/*----用户token表；此表会存放到redis内----*/
CREATE TABLE IF NOT EXISTS `tokens` (
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `token` varchar(40) DEFAULT NULL COMMENT '用户的验证token',
  `expire` datetime DEFAULT NULL COMMENT 'token的过期时间',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户验证token表，保存邮件、短信验证的验证码';

/*--系统黑名单表--*/
CREATE TABLE IF NOT EXISTS `black_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL COMMENT '用户ID',
  `app_forbid` varchar(100) DEFAULT NULL COMMENT '被禁止访问的应用，若为空则全站禁用',
  `expire` datetime DEFAULT NULL COMMENT '过期时间',
  `creation_date` datetime DEFAULT NULL COMMENT '添加时间',
  `modified_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统黑名单表';
