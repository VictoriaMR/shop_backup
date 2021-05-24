const TRANSFER = {
	init: function() {
		//新增按钮
		$('.btn.reload').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'reloadCache'}, function(data) {
				_thisobj.button('reset');
			});
		});
		//修改按钮
		$('.btn.modify').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'getInfo', id: id}, function(data) {
				_thisobj.button('reset');
				TRANSFER.initModel(data);
			});
		});
		//自动翻译
		$('#dealbox .glyphicon-transfer').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'autoTransfer'}, function(data) {
				_thisobj.button('reset');
			});
		});
	},
	initModel: function(data) {
		if (!data) {
			data = {
				name: '',
				tran_id: '0',
				type: '',
				value: '',
				type_name: ''
			};
		}
		const modelobj = $('#dealbox');
		for (const i in data) {
			modelobj.find('[name="'+i+'"]').val(data[i]);
		}
		modelobj.dealboxShow();
	}
};