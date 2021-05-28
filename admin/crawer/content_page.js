var common_url = 'https://lmr.admin.cn/';
//公共方法
var HELPER = {
	//向背景脚本请求
	request: function(action, value, param, callback, cache_key) {
		chrome.runtime.sendMessage(this.getExtId(), {action: action, value: value, param: param, cache_key: cache_key},
			function(response) {
			if (callback) {
				callback(response);
			}
		});
	},
	getExtId: function() {
		return localStorage.getItem('crawer_chrome_runtime_id');
	},
	getData: function(callback) {
		this.request('request_api', 'common/getCrawerData', {}, function(res) {
			if (res && res.code == 200) {
				callback(res.data);
			} else {
				POP_PAGE.error_page('获取登录信息失败, 请刷新重试');
			}
		}, 'crawer_data_cache');
	},
	getCategory: function(callback) {
		this.getData(function(data) {
			callback(data.category);
		});
	},
	getVersion: function(callback) {
		this.getData(function(data) {
			callback(data.version);
		});
	},
	content_request: function(param) {
		window.postMessage(param, '*');
	}
};
function isNumber(str) {
	var rules = /^[0-9]+$/;
	if(!rules.exec(str)){
		return false;
	}
	return true;
}
//自定义页面
var POP_PAGE = {
	init: function() {
		var _this = this;
		HELPER.getVersion(function(version) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');
			script.src = common_url+'crawer/crawer.js?version='+version;
			script.type = 'text/javascript';
			script.charset = 'utf-8';
			script.id = 'crawer_crawer_js';
			head.appendChild(script);
			script.onload=script.onreadystatechange=function() {
				var inpageCSS = common_url+'crawer/crawer.css?version='+version;
				var head = document.getElementsByTagName('head')[0];
				var link = document.createElement('link');
				link.href = inpageCSS;
				link.rel = 'stylesheet';
				link.type = 'text/css';
				link.id = 'crawer_crawer_css';
				head.appendChild(link);
				var crawerbody = POP_PAGE.init_crawerbody(true);
				if (domain == 'taobao.com') {
					crawerbody.style['z-index'] = '100000099';
					crawerbody.style.right = '45px';
				} else if (domain == 'tmall.com') {
					crawerbody.style.right = '5px';
				}
				POP_PAGE.init_content();
			}
		});
	},
	init_content: function() {
		var crawerbody = this.init_crawerbody();
		var html = `<div class="userinfo-content">`;
					//重新刷新按钮
					html += `<div class="crawer-reload">
								<button onclick="POP_PAGE.reload_crawpage()">刷新</button>
								<button id="crawer-show-btn">展开</button>
								<button id="add-category">添加分类</button>
							</div>`;
				html += `</div>`;
		crawerbody.innerHTML += html;
		POP_PAGE.init_crawPage();
	},
	init_crawerbody: function(empty) {
		var crawerbody = document.getElementById('crawerbody');
		if (crawerbody === null) {
			var body = document.getElementsByTagName('body')[0];
			crawerbody = document.createElement('div');
			crawerbody.id = 'crawerbody';
			body.appendChild(crawerbody);
		}
		if (empty) {
			crawerbody.innerHTML = '';
		}
		return crawerbody;
	},
	reload_crawpage: function() {
		//删除缓存
		HELPER.request('delete_cache', '', {}, function() {
			document.getElementById('crawer_crawer_js').remove();
			document.getElementById('crawer_crawer_css').remove();
			window.postMessage({ type: 'reload_page_js'}, "*");
			return;
		});
	},
	//错误信息
	error_page: function(msg) {
		var crawerbody = this.init_crawerbody();
		var html = `<a href="javascript:location.reload();" class="error-msg">`+msg+`</a>`;
		crawerbody.innerHTML = html;
	},
	//页面爬取信息
	init_crawPage: function() {
		var _this = this;
		getCrawData(function(code, data, msg) {
			if (code === 0) {
				console.log(data, 'data')
				_this.data = data;
				HELPER.getData(function(cacheData) {
					const category = cacheData.category;
					const site = cacheData.site;
					var crawerbody = _this.init_crawerbody();
					var crawerPage = document.getElementById('crawer-page');
					if (crawerPage === null) {
						var html = '<div id="crawer-page" style="max-height:'+(window.innerHeight - 130)+'px;display:none;"></div>';
						crawerbody.innerHTML += html;
						crawerPage = document.getElementById('crawer-page')
					}
					if (data.sku) {
						var bc_site_id = localStorage.bc_site_id || '';
						html = '<form id="crawer_form">';
						html += `<input type="hidden" name="bc_shop_name" value="`+data.shop_name+`" />
								<input type="hidden" name="bc_shop_url" value="`+data.shop_url+`" />`;
						html += `<div class="productAttLine">
									<div class="label">供应商:</div>
									<div class="fillin">
										<input type="text" name="bc_site_id" value="`+domain.replace('.com', '')+`" />
									</div>
									<div class="clear"></div>
								</div>
								<div class="productAttLine">
									<div class="label">产品ID:</div>
									<div class="fillin">
										<input type="text" name="bc_product_id" value="`+data.item_id+`" />
									</div>
									<div class="clear"></div>
								</div>
								<div class="productAttLine">
									<div class="label">站点:</div>
									<div class="fillin">
										<select name="bc_product_site">
											<option value="">请选择站点</option>
											`+_this.getSiteHtml(site)+`
										</select>
									</div>
									<div class="clear"></div>
								</div>
								<div id="category">
									<div class="productAttLine">
										<div class="label">产品分类:</div>
										<div class="fillin">
											<select name="bc_product_category[0]">
												<option value="">请选择分类</option>
												`+_this.getCategoryHtml(category)+`
											</select>
										</div>
										<div class="clear"></div>
									</div>
								</div>
								<div class="productAttLine">
									<div class="label">产品名称:</div>
									<div class="fillin">
										<input name="bc_product_name" value="`+data.name+`" />
									</div>
									<div class="clear"></div>
								</div>
								<div class="productAttLine">
									<div class="label">产品URL:</div>
									<div class="fillin">
										<input type="text" name="bc_product_url" value="`+data.product_url+`" />
									</div>
									<div class="clear"></div>
								</div>`;
						if (data.multi_sku) {
							//sku
							html += `<div class="productAttLine">
										<div class="picTitle" style="margin-bottom: 0px;">SKU：</div>
										<div class="pdtPicHere">`;
							var count = 0;
							for (var i in data.sku) {
								html += `<div class="sku-item flex">
											<div class="cancel-btn">x</div>
											<div class="flex w100">`;
								html += `<div class="sku_img">`;
								if (data.sku[i].sku_img) {
									html += `<img src="`+data.sku[i].sku_img+`">
											<input type="hidden" name="bc_sku[`+count+`][img]" value="`+data.sku[i].sku_img+`"/>`;
								}
								html += `</div>`;
								if (data.sku[i].pvs) {
									html += '<div class="flex1">';
									html += `<div class="flex">
												<div style="width:32px;">
												<span>属性:</span>
											</div>
											<div class="flex1 sku-attr">`;
												if (data.sku[i].pvs.length) {
													for (var j =0;j < data.sku[i].pvs.length; j++) {
														html += `<div>
																	<input name="bc_sku[`+count+`][attr][`+j+`][text]" value="`+data.sku[i].pvs[j].text+`"/>
																	<input type="hidden" name="bc_sku[`+count+`][attr][`+j+`][img]" value="`+data.sku[i].pvs[j].img+`"/>
																</div>`;
													}
												} else {
													for (var j in  data.sku[i].pvs) {
														html += `<div>
																	<input name="bc_sku[`+count+`][attr][`+j+`][text]" value="`+data.sku[i].pvs[j].text+`"/>
																	<input type="hidden" name="bc_sku[`+count+`][attr][`+j+`][img]" value="`+data.sku[i].pvs[j].img+`"/>
																</div>`;
													}
												}
									html += `</div></div>`;
								}
								html += `<div class="flex price-stock">
											<div style="margin-right: 12px;">价格: <input name="bc_sku[`+count+`][price]" value="`+data.sku[i].price+`"/></div>
											<div>库存: <input name="bc_sku[`+count+`][stock]" value="`+data.sku[i].stock+`"/></div>
										</div>
									</div>
								</div></div>`;
								count ++;
							}
							html += `</div></div>`;
						} else {
							html += `<div class="productAttLine">
										<div class="picTitle" style="margin-bottom: 0px;">SKU：</div>
										<div class="pdtPicHere">`;
							html += `<div class="sku-item flex">
										<div class="cancel-btn">x</div>
										<div class="flex w100">`;
							html += `<div class="sku_img">`;
							if (data.sku.sku_img) {
								html += `<img src="`+data.sku.sku_img+`">
										<input type="hidden" name="bc_sku[`+count+`][img]" value="`+data.sku.sku_img+`"/>`;
							}
							html += `</div>`;
							if (data.sku.pvs) {
								html += '<div class="flex1">';
								html += `<div class="flex">
											<div style="width:32px;">
											<span>属性:</span>
										</div>
										<div class="flex1 sku-attr">`;
											if (data.sku.pvs.length) {
												for (var j =0;j < data.sku.pvs.length; j++) {
													html += `<div>
																<input name="bc_sku[`+count+`][attr][`+j+`][text]" value="`+data.sku.pvs[j].text+`"/>
																<input type="hidden" name="bc_sku[`+count+`][attr][`+j+`][img]" value="`+data.sku.pvs[j].img+`"/>
															</div>`;
												}
											} else {
												for (var j in  data.sku.pvs) {
													html += `<div>
																<input name="bc_sku[`+count+`][attr][`+j+`][text]" value="`+data.sku.pvs[j].text+`"/>
																<input type="hidden" name="bc_sku[`+count+`][attr][`+j+`][img]" value="`+data.sku.pvs[j].img+`"/>
															</div>`;
												}
											}
								html += `</div></div>`;
							}
							html += `<div class="flex price-stock">
										<div style="margin-right: 12px;">价格: <input name="bc_sku[`+count+`][price]" value="`+data.sku.price+`"/></div>
										<div>库存: <input name="bc_sku[`+count+`][stock]" value="`+data.sku.stock+`"/></div>
									</div>
								</div>
							</div></div>`;
							html += `</div></div>`;
						}
						if (data.pdt_picture) {
							html += `<div class="clear"></div>
									<div class="productMainPic">
										<div class="picTitle">产品图：</div>
										<div class="pdtPicHere" id="pdt_picture">
											<input type="hidden" name="bc_product_img" class="bc_product_picture" value="`+data.pdt_picture.join(',')+`"/>`;
							for (var i = 0; i < data.pdt_picture.length; i++) {
								html += `<img class="imgList" src="`+data.pdt_picture[i]+`" />`;
							}
							html += `</div></div>`;
						}
						if (data.des_picture) {
							html += `<div class="clear"></div>
									<div class="productMainPic">
										<div class="picTitle">产品详情图：<span style="color:red;font-size:12px;"></span></div>
										<div class="pdtPicHere" id="pdt_desc_picture">
											<input type="hidden" name="bc_product_des_picture" class="bc_product_picture" value="`+data.des_picture.join(',')+`"/>`;
							for (var i = 0; i < data.des_picture.length; i++) {
								html += `<img class="imgList" src="`+data.des_picture[i]+`" />`;
							}
							html += `</div></div>`;
						}
						if (data.des_text) {
							html += `<div class="clear"></div>
									<div class="productMainPic">
										<div class="picTitle">产品描述属性：</div>
										<div id="pdt_des_text">`;
							let count = 0
							for (const i in data.des_text) {
								html += `<div class="sku-attr">
											<input type="text" name="bc_des_text[`+count+`][key]" value="`+i+`"> - 
											<input type="text" name="bc_des_text][`+count+`][value]" value="`+data.des_text[i]+`">
											<div class="cancel-btn">x</div>
										</div>`;
								count ++;
							}
							html += `</div></div>`;
						}
						html += '</form>';
						crawerPage.innerHTML = html;
						if (document.getElementById('postProduct-btn') === null) {
							html = `<div class="postProduct" id="postProduct-btn">上传产品</div>`;
							crawerbody.innerHTML += html;
						}
						_this.init_crawpage_show(localStorage.crawpage_show_status);
						_this.init_click();
					} else {
						html = `<div class="tc">
									<a href="javascript:location.reload();" class="error-msg">获取产品信息失败, 请刷新重试</a>
								</div>`;
						crawerbody.innerHTML += html;
					}
				});
			} else {
				POP_PAGE.error_page(msg);
			}
		});
	},
	init_crawpage_show: function(status) {
		if (status === '1') {
			document.getElementById('crawer-page').style.display = 'block';
			document.getElementById('crawer-show-btn').innerHTML = '收起';
		} else {
			document.getElementById('crawer-page').style.display = 'none';
			document.getElementById('crawer-show-btn').innerHTML = '展开';
		}
	},
	getSiteHtml: function(list) {
		console.log(list, 'list')
		let html = '';
		for (let i = 0; i < list.length; i++) {
			html += '<option value="'+list[i].site_id+'">'+list[i].name+'</option>';
		}
		return html;
	},
	getCategoryHtml: function(list) {
		this.categoryHtml = '';
		for (let i = 0; i < list.length; i++) {
			let padding = '';
			for (let j = 0; j < list[i].level; j++) {
				padding += '&nbsp;&nbsp;&nbsp;';
			}
			let disabled = '';
			if (list[i].level == 0) {
				disabled = 'disabled="disabled"';
			}
			this.categoryHtml += '<option '+disabled+' value="'+list[i].cate_id+'">'+padding+list[i].name+'</option>';
		}
		return this.categoryHtml;
	},
	init_click: function() {
		var _this = this;
		//上传产品按钮
		document.getElementById('postProduct-btn').onclick = function () {
			if (this.className.indexOf('loading') !== -1) {
				return false;
			}
			var param = POP_PAGE.serializeForm(document.getElementById('crawer_form'));
			var _thisobj = this;
			_thisobj.innerHTML = '数据发送中...';
			_thisobj.classList.add('loading');
			console.log(param, 'param');
			HELPER.request('request_api', 'product/create', param, function(res) {
				_thisobj.classList.remove('loading');
				_thisobj.innerHTML = '上传产品';
			});
		}
		//图片按钮点击删除
		var obj = document.getElementById('pdt_picture');
		if (obj) {
			tobj = obj.getElementsByTagName('img');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this)
					POP_PAGE.initPdtImgValue(obj);
				}
			}
		}
		//图片介绍图
		var obj = document.getElementById('pdt_desc_picture');
		if (obj) {
			tobj = obj.getElementsByTagName('img');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this)
					POP_PAGE.initPdtImgValue(obj);
				}
			}
		}
		var obj = document.getElementById('pdt_des_text');
		if (obj) {
			tobj = obj.querySelectorAll('.sku-attr .close');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this);
				}
			}
		}
		// sku 点击删除
		var skuCancelObj = document.getElementsByClassName('cancel-btn');
		if (skuCancelObj) {
			for (var i = 0; i < skuCancelObj.length; i++) {
				skuCancelObj[i].onclick = function(event) {
					this.parentNode.remove();
				}
			}
		}
		//展开/关闭详情
		document.getElementById('crawer-show-btn').onclick = function() {
			if (localStorage.crawpage_show_status === '1') {
				localStorage.crawpage_show_status = '0';
				this.innerHTML = '展开';
				var status = '0';
			} else {
				localStorage.crawpage_show_status = '1';
				this.innerHTML = '收起';
				var status = '1';
			}
			POP_PAGE.init_crawpage_show(status);
		}
		//添加分类
		document.getElementById('add-category').onclick = function() {
			const count = document.querySelectorAll('#category .productAttLine').length;
			let div = document.createElement('div');
			div.setAttribute('class', 'productAttLine');
			div.innerHTML = `<div class="label">产品分类:</div>
							<div class="fillin">
								<select name="bc_product_category[`+count+`]">
									<option value="">请选择分类</option>
									`+_this.categoryHtml+`
								</select>
							</div>
							<div class="clear"></div>`;
			document.getElementById('category').appendChild(div);
		}
	},
	serializeForm: function(formobj) {
		var formData = new FormData(formobj);
		return Object.fromEntries(formData.entries());
	},
	initPdtImgValue: function(pobj) {
		var imgValueObj = pobj.querySelectorAll('.bc_product_picture')[0];
		if (imgValueObj === null) {
			pobj.innerHTML += '<input type="hidden" name="bc_product_img" class="bc_product_picture" value=""/>';
			imgValueObj = pobj.getElementsByClassName('bc_product_picture')[0];
		}
		var imgobj = pobj.getElementsByTagName('img');
		var value = '';
		for (var i = 0; i < imgobj.length; i++) {
			if (i > 0) {
				value += ',';
			}
			value += imgobj[i].src;
		}
		imgValueObj.value = value;
	}
};
POP_PAGE.init();