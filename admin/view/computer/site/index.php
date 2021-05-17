<?php $this->load('common/header');?>
<div class="container-fluid" id="site-page">
	<form class="form-inline">
		<input type="hidden" name="opn" value="editSite">
		<div class="col-md-12 pt10">
			<div class="f14 f600">title: <span class="glyphicon glyphicon-globe" data-id="title"></span></div>
			<textarea class="form-control mt2" name="title" rows="3" cols="100"><?php echo $info['title'] ?? '';?></textarea>
		</div>
		<div class="col-md-12 pt10">
			<div class="f14 f600">keyword: <span class="glyphicon glyphicon-globe" data-id="keyword"></div>
			<textarea class="form-control mt2" name="keyword" rows="3" cols="100"><?php echo $info['keyword'] ?? '';?></textarea>
		</div>
		<div class="col-md-12 pt10">
			<div class="f14 f600">description: <span class="glyphicon glyphicon-globe" data-id="description"></div>
			<textarea class="form-control mt2" name="description" rows="3" cols="100"><?php echo $info['description'] ?? '';?></textarea>
		</div>
		<div class="col-md-12 pt10">
			<button type="button" class="btn btn-primary w30 save-btn">保存</button>
		</div>
	</form>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	        <button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">多语言配置</div>
	        <input type="hidden" name="name" value="">
	        <input type="hidden" name="opn" value="editLanguage">
	        <table class="table table-bordered table-hover">
	        	<tbody>
	        		<tr>
	        			<th style="width:88px">语言名称</th>
	        			<th>文本 <span class="glyphicon glyphicon-transfer right f16" title="自动翻译"></span></th>
	        		</tr>
	        		<?php if (empty($language)){?>
	        		<tr><td colspan="2"><div class="tc co">没有获取到语言配置</div></td></tr>
        			<?php } else { ?>
        			<?php foreach ($language as $key => $value) {?>
	        		<tr data-id="<?php echo $value['tr_code'];?>">
	        			<th>
	        				<span><?php echo $value['name'];?></span>
	        			</th>
	        			<td class="p0">
	        				<textarea name="language[<?php echo $value['lan_id'];?>]" class="form-control" autocomplete="off"></textarea>
	        			</td>
	        		</tr>
        			<?php } ?>
        			<?php } ?>
	        	</tbody>
	        </table>
	        <button type="botton" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
	    </form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	SITE.init();
});
</script>
<?php $this->load('common/footer');?>