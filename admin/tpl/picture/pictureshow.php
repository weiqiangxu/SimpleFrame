<?php include_once(mvc::$cfg['PATH_TPL'].'public/headBase.php');?>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 图片管理 <span class="c-gray en">&gt;</span> 图片展示 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"> <a href="javascript:;" onclick="edit()" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe6df;</i> 编辑</a> <a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> </span> <span class="r">共有数据：<strong>54</strong> 条</span> </div>
	<div class="portfolio-content">
		<ul class="cl portfolio-area">
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/keting.jpg" data-lightbox="gallery" data-title="客厅1"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/keting.jpg"></a></div>
					<div class="textbox">客厅 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox "><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/keting2.jpg" data-lightbox="gallery" data-title="客厅2"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/keting2.jpg"></a></div>
					<div class="textbox">客厅 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/keting3.jpg" data-lightbox="gallery" data-title="客厅3"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/keting3.jpg"></a></div>
					<div class="textbox">客厅 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/keting4.jpg" data-lightbox="gallery" data-title="客厅4"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/keting4.jpg"></a></div>
					<div class="textbox">客厅 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/chufang.jpg" data-lightbox="gallery" data-title="厨房"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/chufang.jpg"></a></div>
					<div class="textbox">厨房 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/shufang.jpg" data-lightbox="gallery" data-title="书房"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/shufang.jpg"></a></div>
					<div class="textbox">书房 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/woshi.jpg" data-lightbox="gallery" data-title="卧室"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/woshi.jpg"></a></div>
					<div class="textbox">卧室 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/weishengjian.jpg" data-lightbox="gallery" data-title="卫生间1"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/weishengjian.jpg"></a></div>
					<div class="textbox">卫生间1 </div>
				</div>
			</li>
			<li class="item">
				<div class="portfoliobox">
					<input class="checkbox" name="" type="checkbox" value="">
					<div class="picbox"><a href="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/big/weishengjian2.jpg" data-lightbox="gallery" data-title="卫生间2"><img src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>temp/Thumb/weishengjian2.jpg"></a></div>
					<div class="textbox">卫生间2 </div>
				</div>
			</li>
		</ul>
	</div>
</div>
<?php include_once(mvc::$cfg['PATH_TPL'].'public/foot.php');?>

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="<?php echo mvc::$cfg['PATH_HUI_ADMIN'];?>lib/lightbox2/2.8.1/js/lightbox.min.js"></script> 
<script type="text/javascript">
$(function(){
	$(".portfolio-area li").Huihover();
});
</script>
</body>
</html>