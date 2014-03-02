$(document).ready(function(){

<?php if ($is_update): ?>
	$('[data-from-toggle="1"]')
		.toggleClass('hidden');
<?php endif; ?>

$('#toggle-form-all')
	.on('click', function(){
		$('[data-from-toggle="1"]')
			.toggleClass('hidden');
		return false;
	});

Dropzone.autoDiscover = false; // class='dropzone' を自動でアタッチしないように...

$("#form_package_content, #form_ss_content")
	.addClass('dropzone')
	.css('min-height', '200px')
	.dropzone({
			// エラーメッセージなど
			dictCancelUpload: "Cancel upload",
			dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
			dictDefaultMessage: "Drop files here to upload",
			dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
			dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
			dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
			dictInvalidFileType: "この種類のファイルはアップロードできません。",
			dictMaxFilesExceeded: "これ以上のファイルをアップロードすることはできません。",
			dictRemoveFile: "Remove file",
			dictRemoveFileConfirmation: null,
			dictResponseError: "Server responded with {{statusCode}} code.",
			// セッションが破棄されてしまうので並列でのアップロードを無効に
			parallelUploads: 1,
			// まとめてアップロードする、、、そのうちサイズを見て詰めれるだけ詰めてアップロードするみたいなことをしたい @todo
		//	parallelUploads: 2, uploadMultiple: true,
			processingmultiple: function(file){ console.log(file); },
			// アップロードできるサイズの制限(値はサーバー側から取得)
			maxFilesize: <?php echo intval(min(Num::bytes(ini_get('upload_max_filesize')),
			                                   Num::bytes(ini_get('post_max_size'))) / 1048576); ?>,
			// 送信先
			url: "<?php echo Uri::create('package/upload'); ?>",
		//	acceptedFiles:'image/*',
			// 送信可能ファイル
		//	accept: function(file, done){
		//		console.log(file);
		//		console.log(this);
		//		done('xxx');
		//	},
			// 初期化処理
			init: function() {<?php $csrf_token_key = Config::get('security.csrf_token_key'); ?>
				var content_id = $(this.element).attr('id');
				var uploadedFiles = [];

				if (content_id == "form_package_content") {
					this.options.acceptedFiles = ['.as', '.hsp', '.zip', '.lzh', '.rar'].join(',');
					this.options.paramName = "package";
				} else {
					this.options.acceptedFiles = 'image/*';
					this.options.paramName = "ss";
				}

				// 以前のファイルを削除
				if (content_id == "form_package_content") {
					this.on("drop", function(e){
						this.removeAllFiles(true);
					});
				}
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
						if (content_id != "form_package_content") {
							return;
						}
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
				// アップロードの取り消し
				if (content_id != "form_package_content") {
					this.on("addedfile", function(file) {
						// Create the remove button
						var removeButton = Dropzone.createElement(
								'<div class="text-center"><button class="btn btn-danger btn-xs" style="width: 100%">' +
									'<span class="fa fa-trash-o fa-fw"></span>' +
									'取り消し' +
								'</button></div>');
						// Capture the Dropzone instance as closure.
						var _this = this;
						// Listen to the click event
						removeButton.addEventListener("click", function(e) {
							// Make sure the button click doesn't submit the form:
							e.preventDefault();
							e.stopPropagation();
							// Remove the file preview.
							_this.removeFile(file);
							// If you want to the delete the file on the server as well,
							// you can do the AJAX request here.
							var postData = [];
							postData.push('<?php echo $csrf_token_key; ?>='
							              + $('#form_<?php echo $csrf_token_key; ?>').attr('value'));
							postData.push('cancel=' + file.name);
							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: '<?php echo Uri::create("package/cancel"); ?>',
								data: postData.join('&'),
								error: function(XMLHttpRequest, textStatus, errorThrown){
								},
								success: function(json){
									$('#form_<?php echo $csrf_token_key; ?>').attr('value', json.csrf_token);
									if ('success' == json.status) {
									} else {
									}
								}
							});
						});
						// Add the button to the file preview element.
						file.previewElement.appendChild(removeButton);
					});
				}

<?php foreach ($uploaded as $file): ?>
				if (content_id == "form_<?php echo e($file['field']); ?>_content") {
					var file_ = { name: "<?php echo e($file['name']); ?>",
					              size: <?php echo $file['size']; ?> };
					this.files.push(file_);
					this.emit("addedfile", file_);
<?php if ($file['url']): ?>
					this.emit("thumbnail", file_, "<?php echo e($file['url']) ?>");
<?php endif; ?>
				}
<?php endforeach; ?>
			}
		});

})