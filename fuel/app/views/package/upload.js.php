$(document).ready(function(){

	Dropzone.options.myAwesomeDropzone = {
	  maxFilesize: <?php echo intval(min(Num::bytes(ini_get('upload_max_filesize')),
	                                     Num::bytes(ini_get('post_max_size'))) / 1048576); ?>,
	  init: function() {
		var uploadedFiles = [];
		<?php $csrf_token_key = Config::get('security.csrf_token_key'); ?>
		this.on("success", function(file, responseText){
			// Handle the responseText here. For example, add the text to the preview element:
			//file.previewTemplate.appendChild(document.createTextNode(responseText));
			var json = JSON.parse(responseText);
			uploadedFiles = uploadedFiles.concat(json.success || []);
			$('#form_<?php echo $csrf_token_key; ?>').attr('value', json.csrf_token);
console.log("success");
console.log(file);
console.log(json);
console.log(uploadedFiles);
		});
	    this.on("complete", function(file){
console.log("complete");
console.log(file);
	    	if (0 == this.getQueuedFiles().length &&
	    	    0 == this.getUploadingFiles().length)
	    	{
console.log("all complete");
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
console.log('error');
						$('#package-validating').modal('hide');
					},
					success: function(json){
						if ('success' == json.status) {
console.log('success');
							$('#package-validating').modal('hide');
							// 解析OKだったら次ぎにいく
							$('<form/>', { method: 'post', action: '<?php echo Uri::create($next_url); ?>' })
								.append($('<input/>', { type: 'hidden', name: '<?php echo $csrf_token_key; ?>', value: json.csrf_token }))
								.appendTo(document.body)
								.submit();
						} else {
console.log('error #2');
							// エラーが出たので非表示に
				    		$('#package-validating').modal('hide');
						}
					}
				});
        	}
		});

/*      this.on("addedfile", function(file) {
        // 削除ボタン追加
        var removeButton = Dropzone.createElement(
        					  "<button class=\"btn btn-default btn-sm\" style=\"width: 100%;\">"
        					+   "<span class=\"fa fa-trash-o\"></span> 削除"
        					+ "</button>");
        

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
        });

        // Add the button to the file preview element.
        file.previewElement.appendChild(removeButton);
      });*/

	  }
	};

})