const common_url = 'https://lmr.admin.cn/';
//公共方法
const HELPER = {
	//向背景脚本请求
	request: function(data, callback) {
		chrome.runtime.sendMessage(this.getExtId(), data,
			function(response) {
			if (callback) {
				callback(response);
			}
		});
	},
	getExtId: function() {
		return localStorage.getItem('chrome_helper_ext_id');
	},
};
//自定义页面
const POP_PAGE = {
	init: function() {
		const _this = this;
		HELPER.request({action: 'requestCache', value: 'common/getCrawlerData', cache_key: 'crawler_data_cache'}, function(res) {
			console.log(res, 'res')
			if (res.code === 200 || res.code === '200') {
				let head,content;
				head = document.getElementsByTagName('head')[0];
				content = document.createElement('script');
				content.src = common_url+'googleHelper/crawler.js?version='+res.data.version;
				content.type = 'text/javascript';
				content.id = 'googleHelper_crawler_js';
				head.appendChild(content);
				content.onload=content.onreadystatechange=function() {
					head = document.getElementsByTagName('head')[0];
					content = document.createElement('link');
					content.href = common_url+'googleHelper/crawler.css?version='+res.data.version;
					content.rel = 'stylesheet';
					content.type = 'text/css';
					content.id = 'googleHelper_crawler_css';
					head.appendChild(content);
					let crawlerBody = _this.init_crawlerBody(true);
					if (domain === 'taobao.com') {
						crawlerBody.style['z-index'] = '100000099';
						crawlerBody.style.right = '45px';
					} else if (domain === 'tmall.com') {
						crawlerBody.style.right = '5px';
					}
					_this.init_content(res.data);
				}
			}
		});
	},
	init_content: function(data) {
		let crawlerBody = this.init_crawlerBody();
		let html = `<div class="userInfo-content">
						<div class="crawler-reload">
							<button onclick="POP_PAGE.reload_crawlerPage()">刷新</button>
							<button id="crawler-show-btn">展开</button>
							<button id="add-category">添加分类</button>
						</div>
					</div>`;
		crawlerBody.innerHTML += html;
		POP_PAGE.init_crawPage(data);
	},
	init_crawlerBody: function(empty) {
		let crawlerBody = document.getElementById('crawler_body');
		if (!crawlerBody) {
			let body = document.getElementsByTagName('body')[0];
			crawlerBody = document.createElement('div');
			crawlerBody.id = 'crawler_body';
			body.appendChild(crawlerBody);
		}
		if (empty) {
			crawlerBody.innerHTML = '';
		}
		return crawlerBody;
	},
	reload_crawlerPage: function() {
		//删除缓存
		HELPER.request('delete_cache', '', {}, function() {
			document.getElementById('googleHelper_crawler_js').remove();
			document.getElementById('googleHelper_crawler_css').remove();
			window.postMessage({ type: 'reload_page_js'}, "*");
		});
	},
	//页面爬取信息
	init_crawPage: function(info) {
		const _this = this;
		getCrawData(function(code, data, msg) {
			if (code === 0) {
				const category = info.category;
				const site = info.site;
				let crawlerBody = _this.init_crawlerBody();
				let crawlerPage = document.getElementById('crawler-page');
				if (!crawlerPage) {
					crawlerBody.innerHTML += '<div id="crawler-page" style="max-height:' + (window.innerHeight - 130) + 'px;display:none;"></div>';
					crawlerBody = document.getElementById('crawler-page');
				}
				if (data.sku) {
					let html = `<form id="crawler_form">
									<input type="hidden" name="bc_shop_name" value="` + data.shop_name + `" />
									<input type="hidden" name="bc_shop_url" value="` + data.shop_url + `" />
									<div class="productAttLine">
										<div class="label">供应商:</div>
										<div class="fill_in">
											<input type="text" name="bc_site_id" value="` + domain.replace('.com', '') + `" />
										</div>
										<div class="clear"></div>
									</div>
									<div class="productAttLine">
										<div class="label">产品ID:</div>
										<div class="fill_in">
											<input type="text" name="bc_product_id" value="` + data.item_id + `" />
										</div>
										<div class="clear"></div>
									</div>
									<div class="productAttLine">
										<div class="label">站点:</div>
										<div class="fill_in">
											<select name="bc_product_site">
												<option value="">请选择站点</option>
												` + _this.getSiteHtml(site) + `
											</select>
										</div>
										<div class="clear"></div>
									</div>
									<div id="category">
										<div class="productAttLine">
											<div class="label">产品分类:</div>
											<div class="fill_in">
												<select name="bc_product_category[0]">
													<option value="">请选择分类</option>
													` + _this.getCategoryHtml(category) + `
												</select>
											</div>
											<div class="clear"></div>
										</div>
									</div>
									<div class="productAttLine">
										<div class="label">产品名称:</div>
										<div class="fill_in">
											<input name="bc_product_name" value="` + data.name + `" />
										</div>
										<div class="clear"></div>
									</div>
									<div class="productAttLine">
										<div class="label">产品URL:</div>
										<div class="fill_in">
											<input type="text" name="bc_product_url" value="` + data.product_url + `" />
										</div>
										<div class="clear"></div>
									</div>`;
					if (data.multi_sku) {
						//sku
						html += `<div class="productAttLine">
									<div class="picTitle" style="margin-bottom: 0;">SKU：</div>
									<div class="pdtPicHere">`;
						let count = 0;
						for (let i in data.sku) {
							html += `<div class="sku-item flex">
										<div class="cancel-btn">x</div>
										<div class="flex w100">
										<div class="sku_img">`;
							if (data.sku[i].sku_img) {
								html += `<img src="` + data.sku[i].sku_img + `">
												<input type="hidden" name="bc_sku[` + count + `][img]" value="` + data.sku[i].sku_img + `"/>`;
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
									for (let j=0;j<data.sku[i].pvs.length;j++) {
										html += `<div>
													<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku[i].pvs[j].text + `"/>
													<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku[i].pvs[j].img + `"/>
												</div>`;
									}
								} else {
									for (let j in data.sku[i].pvs) {
										html += `<div>
													<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku[i].pvs[j].text + `"/>
													<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku[i].pvs[j].img + `"/>
												</div>`;
									}
								}
								html += `</div></div>`;
							}
							html += `<div class="flex price-stock">
												<div style="margin-right: 12px;">价格: <input name="bc_sku[` + count + `][price]" value="` + data.sku[i].price + `"/></div>
												<div>库存: <input name="bc_sku[` + count + `][stock]" value="` + data.sku[i].stock + `"/></div>
											</div>
										</div>
									</div></div>`;
							count++;
						}
						html += `</div></div>`;
					} else {
						html += `<div class="productAttLine">
									<div class="picTitle" style="margin-bottom: 0px;">SKU：</div>
									<div class="pdtPicHere">
										<div class="sku-item flex">
											<div class="cancel-btn">x</div>
											<div class="flex w100">
									<div class="sku_img">`;
						if (data.sku.sku_img) {
							html += `<img src="` + data.sku.sku_img + `">
										<input type="hidden" name="bc_sku[` + count + `][img]" value="` + data.sku.sku_img + `"/>`;
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
								for (let j=0;j<data.sku.pvs.length;j++) {
									html += `<div>
												<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku.pvs[j].text + `"/>
												<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku.pvs[j].img + `"/>
											</div>`;
								}
							} else {
								for (let j in data.sku.pvs) {
									html += `<div>
												<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku.pvs[j].text + `"/>
												<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku.pvs[j].img + `"/>
											</div>`;
								}
							}
							html += `</div></div>`;
						}
						html += `<div class="flex price-stock">
											<div style="margin-right: 12px;">价格: <input name="bc_sku[` + count + `][price]" value="` + data.sku.price + `"/></div>
											<div>库存: <input name="bc_sku[` + count + `][stock]" value="` + data.sku.stock + `"/></div>
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
												<input type="hidden" name="bc_product_img" class="bc_product_picture" value="` + data.pdt_picture.join(',') + `"/>`;
						for (let i=0;i< data.pdt_picture.length;i++) {
							html += `<img class="imgList" src="` + data.pdt_picture[i] + `" />`;
						}
						html += `</div></div>`;
					}
					if (data.des_picture) {
						html += `<div class="clear"></div>
										<div class="productMainPic">
											<div class="picTitle">产品详情图：<span style="color:red;font-size:12px;"></span></div>
											<div class="pdtPicHere" id="pdt_desc_picture">
												<input type="hidden" name="bc_product_des_picture" class="bc_product_picture" value="` + data.des_picture.join(',') + `"/>`;
						for (let i=0;i<data.des_picture.length;i++) {
							html += `<img class="imgList" src="` + data.des_picture[i] + `" />`;
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
												<input type="text" name="bc_des_text[` + count + `][key]" value="` + i + `"> - 
												<input type="text" name="bc_des_text][` + count + `][value]" value="` + data.des_text[i] + `">
												<div class="cancel-btn">x</div>
											</div>`;
							count++;
						}
						html += `</div></div>`;
					}
					html += '</form>';
					crawlerPage.innerHTML = html;
					if (document.getElementById('postProduct-btn') === null) {
						html = `<div class="postProduct" id="postProduct-btn">上传产品</div>`;
						crawlerBody.innerHTML += html;
					}
					_this.init_crawpage_show(localStorage.crawpage_show_status);
					_this.init_click();
				} else {
					let html = `<div class="tc">
									<a href="javascript:location.reload();" class="error-msg">获取产品信息失败, 请刷新重试</a>
								</div>`;
					crawlerBody.innerHTML += html;
				}
			} else {
				HELPER.request({action: 'alert', value: msg});
			}
		});
	},
	init_crawlerPageShow: function(status) {
		if (status === '1') {
			document.getElementById('crawler-page').style.display = 'block';
			document.getElementById('crawler-show-btn').innerHTML = '收起';
		} else {
			document.getElementById('crawler-page').style.display = 'none';
			document.getElementById('crawler-show-btn').innerHTML = '展开';
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
		document.getElementById('crawler-show-btn').onclick = function() {
			if (localStorage.crawpage_show_status === '1') {
				localStorage.crawpage_show_status = '0';
				this.innerHTML = '展开';
				var status = '0';
			} else {
				localStorage.crawpage_show_status = '1';
				this.innerHTML = '收起';
				var status = '1';
			}
			POP_PAGE.init_crawlerPageShow(status);
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