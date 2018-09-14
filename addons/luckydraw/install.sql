
CREATE TABLE IF NOT EXISTS `__PREFIX__luckydraw_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `price` varchar(10) NOT NULL COMMENT '奖品名称',
  `rank` int(11) NOT NULL DEFAULT '0' COMMENT '中奖级别',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1兑换 2作废',
  `redeemtime` int(11) DEFAULT '0' COMMENT '兑奖时间',
  `createtime` int(11) DEFAULT '0' COMMENT '抽奖时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='抽奖记录表';

