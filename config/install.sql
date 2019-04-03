/*
 Navicat Premium Data Transfer

 Source Server         : 本地Mysql
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : 127.0.0.1:3306
 Source Schema         : epay

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 03/04/2019 09:52:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for epay_callback
-- ----------------------------
DROP TABLE IF EXISTS `epay_callback`;
CREATE TABLE `epay_callback`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户uid',
  `url` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '回调地址',
  `errorMessage` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '错误提示',
  `status` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 初始化 1 已经处理',
  `createTime` datetime NOT NULL COMMENT '创建时间',
  `updateTime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_config
-- ----------------------------
DROP TABLE IF EXISTS `epay_config`;
CREATE TABLE `epay_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值名',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '参数内容',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1339 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_file_info
-- ----------------------------
DROP TABLE IF EXISTS `epay_file_info`;
CREATE TABLE `epay_file_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT 'uid',
  `hash` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '文件hash',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '文件路径',
  `fileType` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '文件类型',
  `createTime` datetime NOT NULL COMMENT 'createTime',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `hash`(`hash`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_log
-- ----------------------------
DROP TABLE IF EXISTS `epay_log`;
CREATE TABLE `epay_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户uid',
  `type` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作类型',
  `ipv4` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作目标ipv4地址',
  `createTime` datetime NOT NULL COMMENT '操作时间',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '操作数据',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `type`(`uid`, `type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1072 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_order
-- ----------------------------
DROP TABLE IF EXISTS `epay_order`;
CREATE TABLE `epay_order`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键ID',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `tradeNo` bigint(22) UNSIGNED NOT NULL COMMENT '平台订单自建订单ID',
  `tradeNoOut` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商家订单ID',
  `notify_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '异步回调地址',
  `return_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '同步通知地址',
  `money` bigint(20) NOT NULL COMMENT '支付金额\r\n默认两位小数\r\n1元 = 100',
  `type` int(2) UNSIGNED NOT NULL COMMENT '支付类型\r\n0 未知支付方式\r\n1 微信支付\r\n2 腾讯财付通支付\r\n3 支付宝支付',
  `productName` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品名称',
  `ipv4` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户访问ipv4地址',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '订单状态 0 未支付 1 已支付',
  `isShield` bit(1) NULL DEFAULT b'0' COMMENT '是否被屏蔽订单',
  `createTime` datetime NOT NULL COMMENT '订单创建时间',
  `endTime` datetime NULL DEFAULT NULL COMMENT '订单完成支付时间 如未支付则为null',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `交易单号`(`tradeNo`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `endTime`(`endTime`) USING BTREE,
  INDEX `money`(`money`) USING BTREE,
  INDEX `createTime`(`createTime`) USING BTREE,
  INDEX `tradeNoOut`(`tradeNoOut`) USING BTREE,
  INDEX `查询用户未屏蔽金额`(`endTime`, `uid`, `status`) USING BTREE,
  INDEX `定时统计用户金额`(`isShield`, `uid`, `status`, `endTime`) USING BTREE,
  INDEX `查询当日总金额`(`type`, `status`, `endTime`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 288426 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_order_attr
-- ----------------------------
DROP TABLE IF EXISTS `epay_order_attr`;
CREATE TABLE `epay_order_attr`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT 'uid',
  `tradeNo` bigint(22) UNSIGNED NOT NULL COMMENT '平台订单号',
  `attrKey` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '键名',
  `attrValue` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '键值',
  `createTime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `attrKey`(`attrKey`) USING BTREE,
  INDEX `search1`(`tradeNo`, `attrKey`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_settle
-- ----------------------------
DROP TABLE IF EXISTS `epay_settle`;
CREATE TABLE `epay_settle`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `clearType` int(1) NOT NULL DEFAULT 1 COMMENT '结算类型\r\n0 未知类型\r\n1 银行转账（手动）\r\n2 微信转账（手动）\r\n3 支付宝转账（手动）\r\n4 支付宝转账（自动）',
  `addType` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新增类型\r\n0未知类型\r\n1系统0时自动结算\r\n2支付宝自动结算\r\n3用户手动提交结算',
  `account` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '结算账号',
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '结算名称',
  `money` bigint(20) NOT NULL COMMENT '结算金额',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '转账备注',
  `fee` bigint(20) NOT NULL COMMENT '结算手续费  所收取的手续费',
  `status` bit(1) NOT NULL DEFAULT b'0' COMMENT '结算状态 0 未结算 1已结算',
  `createTime` datetime NOT NULL COMMENT '创建时间',
  `updateTime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`uid`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `createtime`(`createTime`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8285 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_user
-- ----------------------------
DROP TABLE IF EXISTS `epay_user`;
CREATE TABLE `epay_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户密匙',
  `rate` int(5) UNSIGNED NOT NULL COMMENT '用户费率 最高100默认两位小数',
  `account` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '结算账号',
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '结算名称',
  `balance` bigint(20) NOT NULL COMMENT '用户余额默认两位小数',
  `email` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户邮箱账号',
  `phone` bigint(15) UNSIGNED NULL DEFAULT NULL COMMENT '用户手机号码',
  `qq` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户QQ号码',
  `domain` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户对接域名',
  `clearType` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结算类型\r\n0 未知类型\r\n1 银行转账\r\n2 微信转账\r\n3 支付宝转账',
  `clearMode` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结算模式\r\n0 凌晨自动结算\r\n1 手动提交结算\r\n2 系统自动转账',
  `isApply` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否通过申请账号 0 不是 1是',
  `isBan` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否被封禁 0正常 1被封禁',
  `createTime` datetime NOT NULL COMMENT '账号创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `email`(`email`) USING BTREE,
  INDEX `phone`(`phone`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1223 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_user_attr
-- ----------------------------
DROP TABLE IF EXISTS `epay_user_attr`;
CREATE TABLE `epay_user_attr`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '键名',
  `value` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '值内容',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `key`(`uid`, `key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 187 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_verify_code
-- ----------------------------
DROP TABLE IF EXISTS `epay_verify_code`;
CREATE TABLE `epay_verify_code`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '验证账号 手机邮箱等',
  `code` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '验证码',
  `endTime` datetime NOT NULL COMMENT '验证码到期时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `验证账号`(`account`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
