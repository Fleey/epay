/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : 127.0.0.1:3306
 Source Schema         : epay

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 17/08/2019 12:57:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for epay_ad_content
-- ----------------------------
DROP TABLE IF EXISTS `epay_ad_content`;
CREATE TABLE `epay_ad_content`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '标题',
  `hrefUrl` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '转跳链接',
  `imgUrl` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '图片链接',
  `visitsCount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '访问次数',
  `status` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 0 不显示 1 显示',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `search1`(`title`) USING BTREE,
  INDEX `search2`(`status`, `createTime`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_cron_callback
-- ----------------------------
DROP TABLE IF EXISTS `epay_cron_callback`;
CREATE TABLE `epay_cron_callback`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户uid',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '回调地址',
  `method` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '请求模式',
  `data` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '请求数据',
  `errorMessage` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '错误提示',
  `status` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

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
  `createTime` datetime(0) NOT NULL COMMENT 'createTime',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `hash`(`hash`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 691 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_file_info_attr
-- ----------------------------
DROP TABLE IF EXISTS `epay_file_info_attr`;
CREATE TABLE `epay_file_info_attr`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `fileID` int(10) UNSIGNED NOT NULL COMMENT '文件ID',
  `attrKey` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '键名',
  `attrValue` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '内容',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `search1`(`fileID`, `attrKey`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_log
-- ----------------------------
DROP TABLE IF EXISTS `epay_log`;
CREATE TABLE `epay_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户uid',
  `type` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作类型',
  `ipv4` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作目标ipv4地址',
  `createTime` datetime(0) NOT NULL COMMENT '操作时间',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '操作数据',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `type`(`uid`, `type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66907 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

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
  `ipv4` varchar(46) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户访问ipv4 or ipv6地址',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '订单状态 0 未支付 1 已支付 2 已冻结 3 退款中 4 已退款',
  `isShield` bit(1) NULL DEFAULT b'0' COMMENT '是否被屏蔽订单',
  `createTime` datetime(0) NOT NULL COMMENT '订单创建时间',
  `endTime` datetime(0) NULL DEFAULT NULL COMMENT '订单完成支付时间 如未支付则为null',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `tradeNo`(`tradeNo`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `tradeNoOut`(`tradeNoOut`) USING BTREE,
  INDEX `endTime`(`endTime`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10013634 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_order_attr
-- ----------------------------
DROP TABLE IF EXISTS `epay_order_attr`;
CREATE TABLE `epay_order_attr`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tradeNo` bigint(22) UNSIGNED NOT NULL COMMENT '平台订单号',
  `attrKey` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '键名',
  `attrValue` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '键值',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `search1`(`tradeNo`, `attrKey`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27364570 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

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
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`uid`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `createtime`(`createTime`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2769 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

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
  `balance` decimal(13, 2) NOT NULL DEFAULT 0.00 COMMENT '用户余额默认两位小数',
  `email` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户邮箱账号',
  `phone` bigint(15) UNSIGNED NULL DEFAULT NULL COMMENT '用户手机号码',
  `qq` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户QQ号码',
  `domain` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户对接域名',
  `clearType` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结算类型\r\n0 未知类型\r\n1 银行转账\r\n2 微信转账\r\n3 支付宝转账',
  `clearMode` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结算模式\r\n0 凌晨自动结算\r\n1 手动提交结算\r\n2 系统自动转账',
  `isApply` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否通过申请账号 0 不是 1是',
  `isBan` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否被封禁 0正常 1被封禁',
  `createTime` datetime(0) NOT NULL COMMENT '账号创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `email`(`email`) USING BTREE,
  INDEX `phone`(`phone`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1516 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 4665 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_user_money_log
-- ----------------------------
DROP TABLE IF EXISTS `epay_user_money_log`;
CREATE TABLE `epay_user_money_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `uid` int(10) UNSIGNED NOT NULL COMMENT 'uid',
  `money` bigint(20) NOT NULL COMMENT '操作金额',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '操作备注',
  `createTime` datetime(0) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `time`(`createTime`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 817 CHARACTER SET = utf8 COLLATE = utf8_bin COMMENT = '用户金额扣除记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_verify_code
-- ----------------------------
DROP TABLE IF EXISTS `epay_verify_code`;
CREATE TABLE `epay_verify_code`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '验证账号 手机邮箱等',
  `code` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '验证码',
  `endTime` datetime(0) NOT NULL COMMENT '验证码到期时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `验证账号`(`account`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_wxx_account_list
-- ----------------------------
DROP TABLE IF EXISTS `epay_wxx_account_list`;
CREATE TABLE `epay_wxx_account_list`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `apiCertID` int(10) NOT NULL COMMENT '证书公钥文件ID',
  `apiKeyID` int(10) NOT NULL COMMENT '证书密匙文件ID',
  `appID` varchar(18) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '应用ID',
  `mchID` int(10) NOT NULL COMMENT 'mchID 商户服务号查看',
  `appKey` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'appKey 商户服务号设置',
  `appSecret` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'appSecret 公众号专属',
  `desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '备注',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mchID`(`mchID`) USING BTREE,
  INDEX `appID`(`appID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_wxx_apply_info
-- ----------------------------
DROP TABLE IF EXISTS `epay_wxx_apply_info`;
CREATE TABLE `epay_wxx_apply_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '当申请信息类型 为 2 时必填',
  `type` int(1) UNSIGNED NOT NULL COMMENT '申请信息类型 0 无效类型 1 集体号 2 独立号',
  `idCardCopy` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '身份证正面',
  `idCardNational` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '身份证国徽面照片',
  `idCardName` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '身份证姓名',
  `idCardNumber` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '身份证号码',
  `idCardValidTime` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '身份证有效期限  例子 [\"1970-01-01\",\"长期\"]',
  `accountName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '开户名称',
  `accountBank` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '开户银行',
  `bankAddressCode` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '开户银行省市编码',
  `bankName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '开户银行全称（含支行） 17家直连银行无需填写，其他银行请务必填写',
  `accountNumber` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '银行账号',
  `storeName` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '门店名称',
  `storeAddressCode` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '门店省市编码',
  `storeStreet` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '门店街道名称',
  `storeEntrancePic` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '门店门口照片',
  `indoorPic` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '店内环境照片',
  `merchantShortName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '商户简称',
  `servicePhone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '客服电话',
  `productDesc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '售卖商品/提供服务描述',
  `rate` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '费率',
  `contact` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '联系人姓名',
  `contactPhone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '手机号码',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `idCardNumber`(`idCardNumber`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 275 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_wxx_apply_list
-- ----------------------------
DROP TABLE IF EXISTS `epay_wxx_apply_list`;
CREATE TABLE `epay_wxx_apply_list`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `accountID` int(10) UNSIGNED NOT NULL COMMENT '申请的服务商号ID',
  `applyInfoID` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '申请信息ID',
  `businessCode` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '业务编号',
  `subMchID` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '子商户服务号',
  `status` tinyint(1) NOT NULL COMMENT '子商户状态-1 申请失败 0 待审核 1 待签约 2 申请成功',
  `desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '描述',
  `applyData` text CHARACTER SET utf8 COLLATE utf8_bin NULL COMMENT '申请返回数据',
  `rounds` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '循环轮数 用于金额统计',
  `tempMoney` int(10) NULL DEFAULT 0 COMMENT '缓存金额 单位分',
  `money` int(10) NULL DEFAULT 0 COMMENT '当日交易金额 单位分',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `accountID`(`accountID`) USING BTREE,
  INDEX `业务代码`(`businessCode`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `applyInfoID`(`applyInfoID`) USING BTREE,
  INDEX `mchID`(`subMchID`) USING BTREE,
  INDEX `search1`(`money`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5751 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_wxx_area_list
-- ----------------------------
DROP TABLE IF EXISTS `epay_wxx_area_list`;
CREATE TABLE `epay_wxx_area_list`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `areaID` int(10) UNSIGNED NOT NULL COMMENT 'areaID',
  `parentID` int(10) UNSIGNED NOT NULL COMMENT '父级ID',
  `areaName` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'areaName',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parentID`(`parentID`) USING BTREE,
  INDEX `areaName`(`areaName`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3559 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_wxx_search_content
-- ----------------------------
DROP TABLE IF EXISTS `epay_wxx_search_content`;
CREATE TABLE `epay_wxx_search_content`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `type` int(1) UNSIGNED NULL DEFAULT 1 COMMENT '类型\r\n1 为开户行支行搜索索引',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 142711 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for epay_wxx_trade_record
-- ----------------------------
DROP TABLE IF EXISTS `epay_wxx_trade_record`;
CREATE TABLE `epay_wxx_trade_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `subMchID` int(10) UNSIGNED NOT NULL COMMENT '子商户服务号',
  `totalMoney` int(10) UNSIGNED NOT NULL COMMENT '总共交易金额 分为单位',
  `createTime` datetime(0) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `search1`(`subMchID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7037 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
