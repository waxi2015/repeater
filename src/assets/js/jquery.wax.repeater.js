;(function($){

	var plugins = {};

	$.fn.waxrepeater = function ( action, parameters, onSuccess ) {
	
		var elem = this;

		var id = this.attr('id');

		var type = this.attr('data-type');

		if (plugins[id] === undefined) {
			plugins[id] = {
				page : null,
				params : {},
				callback : null,
				inited : null,
				descriptor : null,
				filters : null,
			}
		}

		var init = function () {
			if (type == 'pages') {
				initPages();
			} else {
				initMore();
			}

			initActions();

			if (plugins[id].inited == false && typeof plugins[id].callback == 'function') {
				plugins[id].callback.call(this);
			}

			plugins[id].inited = true;
		}

		var initActions = function () {
			$(document).off('change', '#'+id+' .wax-repeater-order select').on('change', '#'+id+' .wax-repeater-order select', {} ,function(){
				$.post('/wax/repeater/changeorder', {descriptor:plugins[id].descriptor, id:$(this).attr('data-id'), order:$(this).val(), locale:Lang.getLocale(), _token:$('#' + id).find('[name="_token"]').val()}, function (response){
					refresh(function(){
						if (toastr !== undefined) {
							toastr.success(Lang.get(response.message), Lang.get('repeater.success_msg_title'));
						}
					});
				})
			})

			$(document).off('click', '#'+id+' .wax-repeater-delete').on('click', '#'+id+' .wax-repeater-delete', {} ,function(e){
				e.preventDefault();

				var refreshUrl = true;

				if ($(this).hasClass('dont-refresh-url')) {
					refreshUrl = false;
				}

				if (!confirm(Lang.get('repeater.confirm_delete_element'))) {
					return false;
				}
				
				$.post('/wax/repeater/delete', {descriptor:plugins[id].descriptor, id:$(this).attr('data-id'), locale:Lang.getLocale(), _token:$('#' + id).find('[name="_token"]').val()}, function (response){
					refresh(function(){
						if (toastr !== undefined) {
							toastr.success(Lang.get(response.message), Lang.get('repeater.success_msg_title'));
						}
					}, {
						refreshUrl : refreshUrl
					});
				})
			})

			$(document).off('click', '#'+id+' .wax-repeater-edit-popup').on('click', '#'+id+' .wax-repeater-edit-popup', {} ,function(e){
				e.preventDefault();
				
				$.post('/wax/repeater/edit', {descriptor:plugins[id].descriptor, id:$(this).attr('data-id'), locale:Lang.getLocale(), _token:$('#' + id).find('[name="_token"]').val()}, function (response){
					$('#edit-'+id+' .modal-body').html(response.html);
					$('#edit-'+id+'').modal('show');
				})
			})

			$(document).off('click', '#'+id+' .wax-repeater-change-order').on('click', '#'+id+' .wax-repeater-change-order', {} ,function(e){
				e.preventDefault();

				plugins[id].params.page = 1;
				plugins[id].params.order = $(this).attr('data-order');
				plugins[id].params.orderBy = $(this).attr('data-by');

				refresh();
			})
		}

		var initPages = function () {
			initHistoryChange();

			$(document).off('click', '#'+id+' .wax-repeater-page').on('click', '#'+id+' .wax-repeater-page', {} ,function(e){
				e.preventDefault();

				plugins[id].params.page = null;
				plugins[id].page = $(this).attr('data-page');

				refresh(function(){
					// @experimental
					if (plugins[id].params.refreshUrl === undefined || plugins[id].params.refreshUrl == true) {
						window.history.pushState(false, document.title, getUrlWithoutPage() + plugins[id].page);
					}
					$("html, body").animate({ scrollTop: $('#' + id).offset().top }, 500);
				})
			})
		}

		var initHistoryChange = function () {
			$(window).unbind('popstate').bind('popstate', function() {
				plugins[id].page = getPageFromUrl();

				refresh();
			})
		}

		var getPageFromUrl = function () {
			var url = window.location.href;
			var urlParts = url.split('/');

			if (isNaN(urlParts[urlParts.length-1])) {
				return 1;
			} else {
				return urlParts[urlParts.length-1];
			}
		}

		var getUrlWithoutPage = function () {
			var urlParts = window.location.href.split('/');

			var url = '';
			for (var i = 0; i < urlParts.length; i++) {
				if (i + 1 < urlParts.length || isNaN(urlParts[urlParts.length-1])) {
					url += urlParts[i] + '/';
				}
			}

			return url;
		}

		var initMore = function () {
			$(document).off('click', '#'+id+' .wax-repeater-more').on('click', '#'+id+' .wax-repeater-more', {}, function(e){
				e.preventDefault();

				$(elem).find('.wax-repeater-more').hide();
				$(elem).find('.wax-repeater-more-loader').show();

				if (plugins[id].page == null) {
					if (plugins[id].params.page !== undefined) {
						plugins[id].page = plugins[id].params.page;
					} else {
						plugins[id].page = 1;
					}
				}

				plugins[id].page++;

				removeFromParams('page');

				$.post('/wax/repeater/more', {descriptor:plugins[id].descriptor, page:plugins[id].page, params:plugins[id].params, locale:Lang.getLocale(), _token:$('#' + id).find('[name="_token"]').val()}, function (response) {

					$(elem).find('.wax-repeater-more, .wax-repeater-more-loader').remove();

					var html = $(response.html).filter('#' + id).html();

					if ($(html).filter('table').length > 0) {
						var table = $(html).filter('table').html();

						if ($(table).find('tr:first').find('th').length > 0) {
							table = $(table).find('tr:first').remove().end().html();
						}

						if ($(table).filter('tbody').length > 0) {
							table = $(table).filter('tbody').html();
						}

						var container = 'table';
						if ($(elem).find('tbody').length > 0) {
							container = 'tbody';
						}

						$(elem).find(container).append(table);

						var htmlWithoutTable = $(response.html).clone().find('table').remove().end().html();
						
						$(elem).append(htmlWithoutTable);
					} else {
						$(elem).append(html);
					}


					if (typeof plugins[id].callback == 'function') {
						plugins[id].callback.call(this);
					}

				})
			})
		}

		var removeFromParams = function (key) {
			var newParams = {};
			$.each(plugins[id].params, function (k,v){
				if (k != key) {
					newParams[k] = v;
				}
			})
			plugins[id].params = newParams;
		}

		var refresh = function (onRefresh, params) {
			if (plugins[id].params.page !== undefined && plugins[id].params.page !== null) {
				plugins[id].page = plugins[id].params['page'];
			}

			if (type == 'pages') {
				refreshPages(onRefresh, params);
			} else {
				refreshMore();
			}
		}

		var refreshPages = function (onRefresh, params) {
			$.post('/wax/repeater', {page:plugins[id].page, descriptor:plugins[id].descriptor, params:plugins[id].params, locale:Lang.getLocale(), _token:$('#' + id).find('[name="_token"]').val()}, function (response) {

				var html = $(response.html).filter('#' + id).html();

				if (html.length == 0) {
					html = $(response.html).find('#' + id).html();
				}

				$(elem).html(html);

				if (typeof plugins[id].callback == 'function') {
					plugins[id].callback.call(this);
				}

				if (typeof onRefresh == 'function') {
					onRefresh.call(this);
				}

				// @experimental

				// var refreshUrl = true;

				// if (params !== undefined && params.refreshUrl !== undefined && params.refreshUrl == false) {
				// 	refreshUrl = false;
				// }

				// if (refreshUrl) {
				// 	window.history.pushState(false, document.title, getUrlWithoutPage() + plugins[id].page);
				// }
			});
		}

		var refreshMore = function () {
			$.post('/wax/repeater/more', {descriptor:plugins[id].descriptor, page:plugins[id].page, refresh:1, params:plugins[id].params, locale:Lang.getLocale(), _token:$('#' + id).find('[name="_token"]').val()}, function (response) {
				$(elem).find('.wax-repeater-more, .wax-repeater-more-loader').remove();

				$(elem).html($(response.html).filter('#' + id).html());

				if (typeof plugins[id].callback == 'function') {
					plugins[id].callback.call(this);
				}
			})
		}

		var getParams = function () {
			return plugins[id].params;
		}

		var setDescriptor = function (varName) {
			if (varName == 'action') {
				if (action.descriptor !== undefined) {
					plugins[id].descriptor = action.descriptor;
					delete action.descriptor;
				}
			}

			if (varName == 'parameters') {
				if (parameters.descriptor !== undefined) {
					plugins[id].descriptor = parameters.descriptor;
					delete parameters.descriptor;
				}
			}
		}

		switch (typeof action) {

			// first param is a CALLBACK
			case 'function':
				plugins[id].callback = action;
				init();
				break;

			// first param is PARAMS
			case 'object':
				setDescriptor('action');

				plugins[id].params = $.extend(plugins[id].params, action);

				// second param is a CALLBACK
				if (parameters !== undefined) {
					plugins[id].callback = parameters;
				}

				init();
				break;

			// first param is a API CALL
			case 'string':
				if (parameters !== undefined) {
					switch (typeof parameters) {
						// second param is PARAMS
						case 'object':
							setDescriptor('parameters');

							plugins[id].params = $.extend(plugins[id].params, parameters);
							break;

						// second param is A CALLBACK
						case 'function':
							plugins[id].callback = onSuccess;
							break;
					}
				}

				// third param is A CALLBACK
				if (action != 'refresh' && onSuccess !== undefined) {
					plugins[id].callback = onSuccess;
				}

				switch (action) {
					// do REFRESH
					case 'refresh':
						refresh(onSuccess, parameters);
						break;

					case 'getParams':
						return getParams();
						break;

					// just INIT
					default:
						init();
						break;
				}
				break;

			default:
				// just INIT
				init();
				break;
		}

		return this;
	}

	
}(jQuery))