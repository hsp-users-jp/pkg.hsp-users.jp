$(document).ready(function(){
	<?php $csrf_token_key = Config::get('security.csrf_token_key'); ?>

	$.fn.editable.defaults.mode = 'inline';
	$.fn.editable.defaults.url  = '<?php echo Uri::create("package/edit/".$package->id); ?>';
	$.fn.editable.defaults.pk   = '<?php echo $package->id; ?>';

	var commonOptions = {
			ajaxOptions: { dataType: 'json' },  
			error: function(response, newValue) {
console.log('error:');
console.log(response);
				if(response.status === 500) {
					return 'サーバーが利用できません。しばらくしてから試してみてください。';
				} else {
					return 'エラーが発生しました。';
				}
			},
			success: function(response, newValue) {
console.log('success:');
console.log(response);
				$('#form_<?php echo $csrf_token_key; ?>')
					.attr('value', response.csrf_token);
				$(this).data('response', response);
				if (!response.success)
					return response.message;
			},
			params: function(params) {
				params.<?php echo $csrf_token_key; ?>
					= $('#form_<?php echo $csrf_token_key; ?>').attr('value');
				return params;
			}
		};

	$('#description, #name, #version')
		.editable($.extend(true, commonOptions, {
		}));
	$('#type')
		.editable($.extend(true, commonOptions, {
			value: <?php echo $package->common->type->id; ?>,
			source: '<?php echo Uri::create("ajax?t=package.type"); ?>',
			display: function(value, sourceData) {
				var response = $(this).data('response');
				var selected = $.fn.editableutils.itemsByValue(value, sourceData);
				if (response) {
					if (selected.length) {
						$(this).html('<span class="' + response.icon + '"></span> ' + selected[0].text);
					} else {
						$(this).empty();
					}
				}
			}
		}));
	$('#license')
		.editable($.extend(true, commonOptions, {
			value: <?php echo $package->version->license->id; ?>,
			source: '<?php echo Uri::create("ajax?t=package.license"); ?>',
			display: function(value, sourceData) {
				var response = $(this).data('response');
				var selected = $.fn.editableutils.itemsByValue(value, sourceData);
				if (response) {
					if (response.license.url) {
						$('#license-url')
							.html(' ( <a href="'+response.license.url+'"><span class="fa fa-external-link"></span> 詳細</a> )');
					} else {
						$('#license-url')
							.html('');
					}
					$('#license-description')
						.text(response.license.description);
					if (selected.length) {
						$(this).html(selected[0].text);
					} else {
						$(this).empty();
					}
				}
			}
		}));

	$('#description, #version, #type, #license')
		.on('shown', function(e, editable) {
			// 決定 or 中止 ボタンを右側から下側に移動し
			// 編集用のコントロールを幅ぴったしに広げる
			editable.input.$input
					.css('width', '100%')
				.parent() // <div class="editable-input"></div>
					.css('display', 'block')
					.css('margin-bottom', '10px')
				.next() // <div class="editable-buttons"></div>
					.css('margin-left', '0')
					.css('margin-bottom', '10px')
				.parent() // <div />
				.parent() // <div class="control-group form-group"></div>
					.css('width', '100%')
				.parent() // <form class="form-inline editableform"></form>
				.parent() // <div />
				.parent() // <span class="editable-container editable-inline"></span>
					.css('width', '100%')
				;
		});


/*
$('a[data-edit]')
	.removeClass('hidden')
	.bind('click', function(){
			var this_ = this;
			var editAction = $(this).attr('href');
			var editTo = $(this).attr('data-edit-to');
			$(this)
				.addClass('hidden')
				.attr('data-edit-original', $('#'+editTo).text());
			$('<span class="pull-right hidden fa fa-spinner fa-spin fa-lg"></span>')
				.insertAfter($(this_));
			$('<a class="pull-right" title="保存"><span class="fa fa-save fa-fw fa-lg"></span></a>')
				.insertAfter($(this))
				.bind('click', function(){
						$(this_)
							.nextAll(':eq(2)')
							.removeClass('hidden');
						$(this_)
							.nextAll(':lt(2)')
							.addClass('hidden');
						$('#'+editTo)
							.attr('disabled', 'disabled');
						var postData = [];
						postData.push('field='+editTo);
						postData.push('vlaue='+$('#'+editTo).val());
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: '<?php echo Uri::create("package/edit/0"); ?>',
							data: postData.join('&'),
							error: function(XMLHttpRequest, textStatus, errorThrown){
								// エラーが出たので非表示に
								console.log('****');
								$(this_)
									.next()
									.click();
							},
							success: function(json){
								console.log('***');
								if ('success' == json.status) {
									$(this_)
										.attr('data-edit-original', '****')
								}
								$(this_)
									.next()
									.click();
							}
						});
					});
			$('<a class="pull-right" title="キャンセル"><span class="fa fa-times fa-lg"></span></a>')
				.insertAfter($(this))
				.bind('click', function(){
						$('#'+editTo)
							.replaceWith('<div id="'+editTo+'"class="panel-body">'
							             + $(this_).attr('data-edit-original') + '</div>');
						$(this_)
							.removeClass('hidden')
							.nextAll(':lt(3)')
							.remove();
					});
			$('#'+editTo)
				.replaceWith('<textarea id="'+editTo+'"class="form-control panel-body" rows="4">'
				             + $(this).attr('data-edit-original') + '</textarea>');
			return false;
		});
*/
})