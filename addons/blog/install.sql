SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__blog_category`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__blog_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父分类ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '分类名称',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '分类昵称',
  `flag` set('hot','index','recommend') NOT NULL DEFAULT '' COMMENT '分类标志',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `diyname` varchar(30) NOT NULL DEFAULT '' COMMENT '自定义名称',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `weigh` (`weigh`,`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='博客分类表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `__PREFIX__blog_category`
--

INSERT INTO `__PREFIX__blog_category` (`id`, `pid`, `name`, `nickname`, `flag`, `image`, `keywords`, `description`, `diyname`, `createtime`, `updatetime`, `weigh`, `status`) VALUES
(1, 0, '默认分类', 'default', '', '/assets/img/qrcode.png', '', '', '', 1502112587, 1502112587, 0, 'normal');

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__blog_comment`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__blog_comment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `post_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `website` varchar(100) NOT NULL DEFAULT '' COMMENT '网址',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `content` text NOT NULL COMMENT '内容',
  `comments` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(50) NOT NULL DEFAULT '' COMMENT 'User Agent',
  `subscribe` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订阅',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`,`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='博客评论表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `__PREFIX__blog_comment`
--

INSERT INTO `__PREFIX__blog_comment` (`id`, `post_id`, `pid`, `username`, `email`, `website`, `avatar`, `content`, `comments`, `ip`, `useragent`, `subscribe`, `createtime`, `updatetime`, `status`) VALUES
(1, 4, 0, 'Lily', '', '', '', '博客的出现才是近几年的事情，但是要书写博客历史，却不是一件轻松的事情。许多史料必须像挖掘“古董”一样去求证，而且分歧和争议颇多。', 0, '', '', 0, 1502112587, 1502112587, 'normal'),
(2, 4, 0, '约翰', '', '', '', '博客天然的草根性，也决定了人们很难来认定一个正式的“博客之父”，也没有人敢于戴上这顶帽子，否则，一定会打得头破血流。', 0, '', '', 0, 1502112587, 1502112587, 'normal'),
(3, 4, 0, '小杜', '', '', '', 'Blog中最简单的形式。单个的作者对于特定的话题提供相关的资源，发表简短的评论。', 0, '', '', 0, 1502112587, 1502112587, 'normal'),
(4, 4, 2, 'John', '', '', '', '按照博客主人的知名度、博客文章受欢迎的程度，可以将博客分为名人博客、一般博客、热门博客等。', 0, '', '', 0, 1502112587, 1502112587, 'normal');

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__blog_post`
--

CREATE TABLE IF NOT EXISTS `__PREFIX__blog_post` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  `flag` set('hot','index','recommend') NOT NULL DEFAULT '' COMMENT '标志',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `summary` varchar(1500) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` text NOT NULL COMMENT '内容',
  `thumb` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '大图',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击',
  `comments` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='博客日志表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `__PREFIX__blog_post`
--

INSERT INTO `__PREFIX__blog_post` (`id`, `category_id`, `flag`, `title`, `summary`, `content`, `thumb`, `image`, `keywords`, `description`, `views`, `comments`, `createtime`, `updatetime`, `weigh`, `status`) VALUES
(1, 1, 'index', '关于FastAdmin', '<p>FastAdmin是一款基于ThinkPHP5和Bootstrap的极速后台开发框架，其强大的CRUD一键生成功能和丰富的插件扩展，让你的后台开发更加简单、快速。</p>', '<p>FastAdmin是一款基于ThinkPHP5和Bootstrap的极速后台开发框架，其强大的CRUD一键生成功能和丰富的插件扩展，让你的后台开发更加简单、快速。</p>', 'https://cdn.fastadmin.net/uploads/20180507/cf20d5c69f357e12ad5777f922308eef.jpg', 'https://cdn.fastadmin.net/uploads/20180507/cf20d5c69f357e12ad5777f922308eef.jpg', '', '', 157, 2, 1502112587, 1525574837, 0, 'normal'),
(2, 1, 'index', '博客的来源', '<p>        博客最早是由Jorn Barger在1997年12月提出博客这个名称，但是在1998年，互联网上的博客网站却屈指可数。那时，Infosift的编辑Jesse J.Garrett想列举一个博客类似站点的名单，便在互联网上开始了艰难的搜索， 终于在1998年的12月，他的搜集好了部分网站的名单。他把这份名单发给了Cameron Barrett，Cameron觉得这份名单非常有用，就将它在Camworld网站上公布于众。其它的博客站点维护者发现此举后，也纷纷把自己的网址和网站名称、主要特色都发了过来，这个名单也就日渐丰富。到了1999年初，Jesse的“完全博客站点”名单所列的站点已达23个。</p>', '<div>\r\n<p>        博客最早是由Jorn Barger在1997年12月提出博客这个名称，但是在1998年，互联网上的博客网站却屈指可数。那时，Infosift的编辑Jesse J.Garrett想列举一个博客类似站点的名单，便在互联网上开始了艰难的搜索， 终于在1998年的12月，他的搜集好了部分网站的名单。他把这份名单发给了Cameron Barrett，Cameron觉得这份名单非常有用，就将它在Camworld网站上公布于众。其它的博客站点维护者发现此举后，也纷纷把自己的网址和网站名称、主要特色都发了过来，这个名单也就日渐丰富。到了1999年初，Jesse的“完全博客站点”名单所列的站点已达23个。</p>\r\n<p>        由于Cameron与Jesse共同维护的博客站点列表既有趣又易于阅读，吸引了很多人的眼球。在这种情况下，Peter Merholz宣称：“这个新鲜事物必将引起大多数人的注意。作为未来的一个常用词语，web-log将不可避免地被简称为blog，而那些编写网络日志的人，也就顺理成章地成为blogger——博客”。这代表着博客被正式命名。</p>\r\n<p>        随着博客数量的增多，每个博客网站上编写的网络日志的内容也混杂起来，以至把每一个新出的站点主要内容和特色都不可能搞清楚。Cameron后来就只在网站上登载熟悉的博客站点了。时隔不久，Brigitte Eaton也搜集出了一个名叫“Eaton网络门户”的博客站点名单，并且提出应该以日期为基础组织内容。这也建立了blog分类排列的一大标准。</p>\r\n<p>        1999年7月，一个专门制作博客站点的“Pitas”免费工具软件发布了，这对于博客站点的快速搭建起着很关键的作用。随后，上百个同类工具也如雨后春笋般制作出来。这种工具对于加速建立博客站点的数量，是意义重大的。同年的8月份，Pyra发布了Blogger网站，Groksoup也投入运营，使用这些企业所提供的简单的基于互联网的工具，博客站点的数量终于出现了一种爆炸性增长。1999年末，软件研发商Dave Winer向大家推荐Edit This Page网站，Jeff A. Campbell发布了Velocinews网站。所有的这些服务都是免费的，他们的目的也很明确：让更多的人成为博客，来网上发表意见和见解。</p>\r\n<div> </div>\r\n</div>', 'https://cdn.fastadmin.net/uploads/20180507/29fe9cadfa98ddba1669da1cae0951ed.jpg', 'https://cdn.fastadmin.net/uploads/20180507/29fe9cadfa98ddba1669da1cae0951ed.jpg', '', '', 45, 0, 1502111289, 1525574829, 0, 'normal'),
(3, 1, 'index', '博客的崛起', '<p>        这几年，对于所有新闻媒体来说，都品尝到了技术变革的滋味。如今，再没有任何人会否认互联网对媒体带来的革命，但是，好像也没有多少人感知到互联网的神奇：颠覆性的力量似乎并没有来到人间。所有的核心在于时间。对于性急的人来说，时间如同缓慢的河流，对于从容的人来说，时间又是急流。互联网的力量的确还没有充分施展，因为互联网的商业化起始，到今天仅仅才10年；互联网作为一种新的媒体方式，从尝试到今天，也刚刚跨过10年。</p>', '<p>        这几年，对于所有新闻媒体来说，都品尝到了技术变革的滋味。如今，再没有任何人会否认互联网对媒体带来的革命，但是，好像也没有多少人感知到互联网的神奇：颠覆性的力量似乎并没有来到人间。</p>\r\n<p>        所有的核心在于时间。对于性急的人来说，时间如同缓慢的河流，对于从容的人来说，时间又是急流。互联网的力量的确还没有充分施展，因为互联网的商业化起始，到今天仅仅才10年；互联网作为一种新的媒体方式，从尝试到今天，也刚刚跨过10年。<br />        对于一种全新的媒体形式来说，10年实在过于短暂。但是，10年也足以让人们感受到势不可挡的力量，以及依然静静潜伏着的冲击力。而今，随着博客的崭露头角，网络媒体异常的力量开始展现了，声势逐渐发大。虽然，博客依然在大多数人的视野之外，但是，他们改变历史的征程已经启动。<br />1998年，个人博客网站“德拉吉报道”率先捅出克林顿莱温斯基绯闻案；<br />2001年，911事件使得博客成为重要的新闻之源，而步入主流；<br />2002年12月，多数党领袖洛特的不慎之言被博客网站盯住，而丢掉了乌纱帽；<br />2003年，围绕新闻报道的传统媒体和互联网上的伊拉克战争也同时开打，美国传统媒体公信力遭遇空前质疑，博客大获全胜；<br />2003年6月，《纽约时报》执行主编和总编辑也被“博客”揭开的真相而下台，引爆了新闻媒体史上最大的丑闻之一；<br />2004年4月，轰动一时的Gmail测试者大部分从bloggers中产生；<br />……</p>\r\n<p>这一系列发源于博客世界的颠覆性力量，不但塑造着博客自身全新的形象，而且，也在深刻地改变着媒体的传统和未来走向。</p>', 'https://cdn.fastadmin.net/uploads/20180507/cb259c4ecb78d4d3295eac6f20d7d707.jpg', 'https://cdn.fastadmin.net/uploads/20180507/cb259c4ecb78d4d3295eac6f20d7d707.jpg', '', '', 63, 0, 1502112074, 1525575324, 0, 'normal'),
(4, 1, '', '博客的时代正在到来', '<p>        全世界每天传播的媒体内容，有一半是由6大媒体巨头所控制。其利益驱动、意识形态以及传统的审查制度，使得这些经过严重加工处理的内容已经越来越不适应人们的需求。媒体的工业化，内容出口的工厂化，都在严重影响其发展。<br />比如，以美联社为例，有近4000人专业记者，每天“制造并出厂”2000万字的内容，每天发布在8500多种报纸、杂志和广播中，把读者当作“信息动物”一样。这种大教堂式的模式主导了整个媒体世界。这时，以个人为中心的博客潮流却开始有力冲击传统媒体，尤其是对新闻界多年形成的传统观念和道德规范。</p>', '<p>        全世界每天传播的媒体内容，有一半是由6大媒体巨头所控制。其利益驱动、意识形态以及传统的审查制度，使得这些经过严重加工处理的内容已经越来越不适应人们的需求。媒体的工业化，内容出口的工厂化，都在严重影响其发展。<br />比如，以美联社为例，有近4000人专业记者，每天“制造并出厂”2000万字的内容，每天发布在8500多种报纸、杂志和广播中，把读者当作“信息动物”一样。这种大教堂式的模式主导了整个媒体世界。这时，以个人为中心的博客潮流却开始有力冲击传统媒体，尤其是对新闻界多年形成的传统观念和道德规范。<br />        博客是一种满足“五零”条件(零编辑、零技术、零体制、零成本、零形式)而实现的“零进入壁垒”的网上个人出版方式，从媒体价值链最重要的三个环节：作者、内容和读者三大层次，实现了“源代码的开放”。并同时在道德规范、运作机制和经济规律等层次，将逐步完成体制层面的真正开放，使未来媒体世界完成从大教堂模式到集市模式的根本转变。<br />        博客的出现集中体现了互联网时代媒体界所体现的商业化垄断与非商业化自由，大众化传播与个性化(分众化，小众化)表达，单向传播与双向传播3个基本矛盾、方向和互动。这几个矛盾因为博客引发的开放源代码运动，至少在技术层面上得到了根本的解决。</p>\r\n<p><img src=\"/assets/addons/blog/img/thumb.jpg\" alt=\"\" /></p>', 'https://cdn.fastadmin.net/uploads/20180507/d197f62611ff69b738e114add9f3183b.jpg', 'https://cdn.fastadmin.net/uploads/20180507/d197f62611ff69b738e114add9f3183b.jpg', '', '', 83, 5, 1502111626, 1525574812, 0, 'normal');
COMMIT;