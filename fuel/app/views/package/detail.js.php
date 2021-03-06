$(document).ready(function(){
	<?php $csrf_token_key = Config::get('security.csrf_token_key'); ?>

	$.fn.editable.defaults.mode = 'inline';
	$.fn.editable.defaults.url  = '<?php echo Uri::create("package/edit/".$package->current->id); ?>';
	$.fn.editable.defaults.pk   = '<?php echo $package->current->id; ?>';

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

	$('#description, #title, #version, #comment')
		.editable($.extend(true, commonOptions, {
		}));
	$('#type')
		.editable($.extend(true, commonOptions, {
			value: <?php echo $package->current->type->id; ?>,
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
			value: <?php echo $package->current->license->id; ?>,
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

	$('[id^="package_rating_"]')
		.raty({
			space: false,
			cancel: true,
			cancelPlace : 'right',
			hints: ['だめ', 'いまいち', '普通', 'よい', 'すばらしい'],
			noRatedMsg: 'まだ誰も評価していません',
			path: '<?php echo Uri::create("assets/images"); ?>',
			readOnly: <?php echo $is_loggedin && !$is_author ? 'false' : 'true'; ?>,
			score: <?php echo $package_favo_score; ?>,
			click: function(score, evt) {
				$(this).raty('readOnly', true);
				$.ajax({
					url: "<?php echo Uri::create('package/rating/:id',
					                             array('id' => $package->current->id)); ?>",
					data: {
						score: score,
						<?php echo $csrf_token_key; ?>: $('#form_<?php echo $csrf_token_key; ?>').attr('value')
					},
					type: 'post',
					dataType: 'json',
					success: function(data, dataType){
						console.log(data);
						$('#form_<?php echo $csrf_token_key; ?>')
							.attr('value', data.csrf_token);
						$('#rate').raty('readOnly', false);
						$('#rate').raty('score', data.score);
					},
					complete: function(XMLHttpRequest, textStatus) {
						$('#rate').raty('readOnly', false);
					}
				});
				return true;
			}
		});
	$('[id^="package_rating_"] img').removeAttr('title');
})