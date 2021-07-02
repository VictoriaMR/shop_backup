<?php $this->load('common/header');?>
<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="col-md-12 pt10">
			<div class="form-group mt10 mr20">
				<label for="short_name">SPU:</label>
				<input type="text" class="form-control" name="spu_id" value="<?php echo empty($spuId) ? '' : $spuId;?>" placeholder="SPU ID" autocomplete="off">
			</div>
			<div class="form-group mt10 mr20">
				<label for="short_name">状态:</label>
				<select class="form-control" name="status" style="min-width:180px;">
					<option value="-1">请选择</option>
					<?php if (!empty($statusList)) {
						foreach ($statusList as $key => $value) {?>
					<option <?php if ($status==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php } }?>
				</select>
			</div>
			<div class="form-group mt10 mr20">
				<label for="short_name">站点:</label>
				<select class="form-control" name="site" style="min-width:180px;">
					<option value="-1">请选择</option>
					<?php if (!empty($siteList)) {
						foreach ($siteList as $key => $value) {?>
					<option <?php if ($site==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php } }?>
				</select>
			</div>
			<div class="form-group mt10 mr20">
				<label for="short_name">分类:</label>
				<select class="form-control" name="cate" style="min-width:180px;">
					<option value="-1">请选择</option>
					<?php if (!empty($cateList)) {
						foreach ($cateList as $key => $value) {?>
					<option <?php if ($cate==$value['cate_id']){ echo 'selected';}?> value="<?php echo $value['cate_id'];?>" <?php if ($value['level'] === 0){ echo 'disabled="disabled"';}?>><?php echo $value['level']>0 ? '&nbsp;&nbsp;&nbsp;': '';?><?php echo $value['name'];?></option>
					<?php } }?>
				</select>
			</div>
		</div>
		<div class="col-md-12 pt10">
			<div class="mr20 form-group mt10">
				<label for="contact">日期:</label>
				<input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
				<input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
<?php if (!empty($list)) { ?>
<div class="container-fluid">
	<div id="spu-list" class="w100 mt20">
		<?php foreach ($list as $value) { ?>
		<div class="spu-item">
			<a href="<?php echo $value['url'];?>" class="block">
				<div class="spu-image">
					<img src="<?php echo $value['avatar'];?>">
				</div>
			</a>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	<?php echo page($size, $total);?>
</div>
<?php } ?>
<?php $this->load('common/footer');?>