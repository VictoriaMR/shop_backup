var SITE = {
	init: function() {
		$('#site-page .btn.save-btn').on('click', function(){
			var obj = $(this);
			post(URI+'site', obj.parents('form').serializeArray(), function(){

			});
		});
		$('#site-page .glyphicon-globe').on('click', function(){
			var obj = $(this);
			var id = obj.data('id');
	    	post(URI+'site', {opn: 'getSiteLanguage', name: id}, function(data){
	    		var obj = $('#dealbox-language');
	    		obj.find('input[name="name"]').val(id);
	    		obj.find('table textarea').val('');
	    		for (var i in data) {
	    			obj.find('table textarea[name="language['+data[i].lan_id+']"]').val(data[i].value);
	    		}
	    		obj.dealboxShow(id);
			});
		});
		//保存语言
	    $('#dealbox-language .save-btn').on('click', function(){
	    	var obj = $(this);
	    	obj.button('loading');
	    	post(URI+'site', $('#dealbox-language form').serializeArray(), function(){
	    		obj.button('reset');
	    		$('#dealbox-language').dealboxHide();
	    	});
	    	return false;
	    });
	    //自动翻译
	    $('#dealbox-language .glyphicon-transfer').on('click', function() {
	    	var name = $(this).parents('form').find('input[name="name"]').val();
	    	var value = $('#site-page textarea[name="'+name+'"]').val();
	    	if (!value) {
	    		errorTips('没有设置值');
	    		return false;
	    	}
	    	$(this).parents('form').find('table tr').each(function(){
	    		var obj = $(this);
	    		var code = obj.data('id');
	    		var val = obj.find('textarea').val();
	    		if (code && !val) {
	    			post(URI+'site', {opn: 'getTransfer', value: value, code: code}, function(data) {
			    		if (data) {
			    			obj.find('textarea').val(data);
			    		}
			    	});
	    		}
	    	});
	    });
	}
};