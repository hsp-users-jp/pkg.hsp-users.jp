<h1>パッケージの追加</h1>
<hr>

<p>パッケージとして登録するファイルを指定してください。</p>
<p>スクリーンショットも同時に指定しアップロードすることが出来ます。</p>
<p>アップロード時の制限：
<ul class="fa-ul">
  <li><span class="fa-li fa fa-exclamation-circle"></span><?php
	echo e(Num::format_bytes(
				min(Num::bytes(ini_get('upload_max_filesize')),
				    Num::bytes(ini_get('post_max_size')))
			)); ?> までアップロードすることが出来ます。</li>
</ul>
</p>

<p>
<form action="<?php echo Uri::create('package/upload'); ?>"
      class="dropzone" id="my-awesome-dropzone" method="post" enctype="multipart/form-data">
  <?php echo Form::csrf(); ?>
  <div class="fallback">
    <input name="file" type="file" multiple />
  </div>
</form>
</p>

<div class="modal fade" id="package-validating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 25%;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">アップロードファイルの検証中</h4>
      </div>
      <div class="modal-body">
        <div class="progress progress-striped active">
          <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="sr-only">100% Complete</span>
          </div>
        </div>
        <p class="text-center">しばらくそのままでお待ちください。</p>
      </div>
<!--
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
-->
    </div>
  </div>
</div>
