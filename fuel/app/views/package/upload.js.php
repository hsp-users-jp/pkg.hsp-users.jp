$(document).ready(function(){

Dropzone.autoDiscover = false; // class='dropzone' を自動でアタッチしないように...

$("#form_package")
	.addClass('dropzone')
	.css('min-height', '200px')
	.dropzone({
			// アップロードできるサイズの制限(値はサーバー側から取得)
			maxFilesize: <?php echo intval(min(Num::bytes(ini_get('upload_max_filesize')),
			                                   Num::bytes(ini_get('post_max_size'))) / 1048576); ?>,
			// 送信先
			url: "<?php echo Uri::create('package/upload'); ?>",
			// 初期化処理
			init: function() {<?php $csrf_token_key = Config::get('security.csrf_token_key'); ?>
				var uploadedFiles = [];

<?php foreach ($package_uploaded as $uploaded): ?>
				var file_ = { name: "<?php echo e($uploaded['name']); ?>",
				              size: <?php echo $uploaded['size']; ?> };
				this.files.push(file_);
				this.emit("addedfile", file_);
<?php endforeach; ?>

				// 以前のファイルを削除
				this.on("drop", function(e){
					this.removeAllFiles(true);
				});
				// 送信前にCSRFトークンを追加
				this.on("sending", function(file, xhr, formData){
					formData.append('<?php echo $csrf_token_key; ?>',
					                $('#form_<?php echo $csrf_token_key; ?>').attr('value'));
				});
				// 送信成功(CSRFトークン更新)
				this.on("success", function(file, responseText){
					var json = JSON.parse(responseText);
					uploadedFiles = uploadedFiles.concat(json.success || []);
					$('#form_<?php echo $csrf_token_key; ?>').attr('value', json.csrf_token);
				});
				// 送信完了
				this.on("complete", function(file){
					if (0 == this.getQueuedFiles().length &&
					    0 == this.getUploadingFiles().length)
					{
						// 解析中表示
						$('#package-validating').modal({
								'backdrop': 'static',
								'keyboard': false,
								'show': true,
							});
						// アップロード済みのファイルの解析を行う
						var postData = [];
						postData.push('<?php echo $csrf_token_key; ?>='
						              + $('#form_<?php echo $csrf_token_key; ?>').attr('value'));
						postData.push('uploaded=' + uploadedFiles.join(','));
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: '<?php echo Uri::create("package/validate"); ?>',
							data: postData.join('&'),
							error: function(XMLHttpRequest, textStatus, errorThrown){
								// エラーが出たので非表示に
								$('#package-validating').modal('hide');
							},
							success: function(json){
								if ('success' == json.status) {
									$('#package-validating').modal('hide');
									$('#form_<?php echo $csrf_token_key; ?>').attr('value', json.csrf_token);
									// 解析OKだったらフォームに代入
									json.form = json.form || {};
									for (key in json.form)
									{
										var form_ctrl = $('[name="'+key+'"]');
										var val = 1 < form_ctrl.length ? [ json.form[key] ] : json.form[key];
<?php if ($is_update): ?>
										if (!form_ctrl.val())
<?php endif; ?>
										form_ctrl.val(val);
									}
								} else {
									// エラーが出たので非表示に
						    		$('#package-validating').modal('hide');
								}
							}
						});
					}
				});
			}
		});

$('#toggle-form-all')
	.on('click', function(){
		$('[data-from-toggle="1"]')
			.toggleClass('hidden');
		return false;
	});

})