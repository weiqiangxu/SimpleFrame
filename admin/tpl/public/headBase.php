<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="Bookmark" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/favicon.ico" >
<link rel="Shortcut Icon" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/favicon.ico" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5shiv.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>static/h-ui.admin/css/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->

<!-- 局部静态资源css加载 -->
<?php if(!empty($HEAD_CSSJS['css'])){ ?>
	<?php foreach($HEAD_CSSJS['css'] as $key=>$Css){ ?>
		<link rel="stylesheet" type="text/css" href="<?php echo mvc::$cfg['PATH_STATIC_ADMIN'];?><?php echo $Css;?>?ver=<?php echo time();?>">
	<?php }?>
<?php } ?>


<!-- 局部静态资源js加载 -->
<?php if(!empty($HEAD_CSSJS['js'])){ ?>
	<?php foreach($HEAD_CSSJS['js'] as $key=>$Js){ ?>
		<script type="text/javascript" src="<?php echo mvc::$cfg['PATH_STATIC_ADMIN'];?><?php echo $Js;?>?ver=<?php echo time();?>"></script>
	<?php } ?>
<?php } ?>


<title><?php echo $headArr['title'];?></title>
<meta name="keywords" content="<?php echo $headArr['keyword'];?>"/>
<meta name="description" content="<?php echo $headArr['des'];?>"/>

</head>
<body>