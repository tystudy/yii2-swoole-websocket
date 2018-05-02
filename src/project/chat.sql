SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fd
-- ----------------------------
DROP TABLE IF EXISTS `fd`;
CREATE TABLE `fd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `fd` int(11) NOT NULL DEFAULT '0' COMMENT '绑定id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COMMENT='用户绑定表';


-- ----------------------------
-- Table structure for msg
-- ----------------------------
DROP TABLE IF EXISTS `msg`;
CREATE TABLE `msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '内容',
  `tid` int(11) NOT NULL DEFAULT '0' COMMENT '接收用户id',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '发送用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='消息表';


