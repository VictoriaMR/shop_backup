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
				<select class="form-control" name="status">
					<option value="-1">请选择</option>
					<?php if (!empty($statusList)) {
						foreach ($statusList as $key => $value) {?>
					<option <?php if ($status==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php } }?>
				</select>
			</div>
			<div class="form-group mt10 mr20">
				<label for="short_name">站点:</label>
				<select class="form-control" name="site">
					<option value="-1">请选择</option>
					<?php if (!empty($siteList)) {
						foreach ($siteList as $key => $value) {?>
					<option <?php if ($site==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php } }?>
				</select>
			</div>
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
<?php $this->load('common/footer');?>