
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_order
-- ----------------------------
DROP TABLE IF EXISTS `epay_order`;
CREATE TABLE `epay_order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键ID',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `tradeNo` bigint(22) UNSIGNED NOT NULL COMMENT '平台订单自建订单ID',
  `tradeNoOut` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商家订单ID',
  `notify_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '异步回调地址',
  `return_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '同步通知地址',
  `money` bigint(20) NOT NULL COMMENT '支付金额\r\n默认两位小数\r\n1元 = 100',
  `type` int(2) UNSIGNED NOT NULL COMMENT '支付类型\r\n0 未知支付方式\r\n1 微信支付\r\n2 腾讯财付通支付\r\n3 支付宝支付',
  `productName` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `ipv4` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户访问ipv4地址',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '订单状态 0 未支付 1 已支付',
  `isShield` bit(1) NULL DEFAULT b'0' COMMENT '是否被屏蔽订单',
  `createTime` datetime NOT NULL COMMENT '订单创建时间',
  `endTime` datetime NULL DEFAULT NULL COMMENT '订单完成支付时间 如未支付则为null',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `交易单号`(`tradeNo`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `订单ID`(`uid`, `tradeNo`) USING BTREE,
  INDEX `endTime`(`endTime`) USING BTREE,
  INDEX `money`(`money`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1260043 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_settle
-- ----------------------------
DROP TABLE IF EXISTS `epay_settle`;
CREATE TABLE `epay_settle`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `pid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `clearType` int(1) NOT NULL DEFAULT 1 COMMENT '结算类型\r\n0 未知类型\r\n1 银行转账\r\n2 微信转账\r\n3 支付宝转账',
  `account` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '结算账号',
  `username` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '结算名称',
  `money` bigint(20) NOT NULL COMMENT '结算金额',
  `fee` bigint(20) NOT NULL COMMENT '结算手续费  所收取的手续费',
  `status` bit(1) NOT NULL DEFAULT b'0' COMMENT '结算状态 0 未结算 1已结算',
  `createTime` datetime NOT NULL COMMENT '创建时间',
  `updateTime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `createtime`(`createTime`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2096 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for epay_user
-- ----------------------------
DROP TABLE IF EXISTS `epay_user`;
CREATE TABLE `epay_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户密匙',
  `rate` int(5) NOT NULL COMMENT '用户费率 最高100默认两位小数',
  `account` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '结算账号',
  `username` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '结算名称',
  `balance` bigint(20) NOT NULL COMMENT '用户余额默认两位小数',
  `email` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户邮箱账号',
  `phone` bigint(15) UNSIGNED NULL DEFAULT NULL COMMENT '用户手机号码',
  `qq` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户QQ号码',
  `domain` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户对接域名',
  `clearType` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结算类型\r\n0 未知类型\r\n1 银行转账\r\n2 微信转账\r\n3 支付宝转账',
  `isApply` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否通过申请账号 0 不是 1是',
  `isClear` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否允许结算 0 不允许 1允许',
  `isBan` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否被封禁 0正常 1被封禁',
  `createTime` datetime NOT NULL COMMENT '账号创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `email`(`email`) USING BTREE,
  INDEX `phone`(`phone`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1144 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

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
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Compact;

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
