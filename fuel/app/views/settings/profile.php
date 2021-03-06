<div class="row">
	<div class="col-md-3">
<?php echo View::forge('settings/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">プロフィール</h3>
				<small class="text-muted">設定した内容はコメントやパッケージなどに関連付けられて表示されます</small>
			</div>
			<div class="panel-body">
<form class="form-horizontal" role="form" method="post">
	<?php echo Form::csrf(); ?>
	<div class="form-group">
		<label class="col-sm-3 control-label">アカウントアイコン</label>
		<div class="col-sm-9">
			<div class="media">
				<a class="media-left" href="#">
					<?php echo Asset::gravatar($email, array(), array('size' => 48, 'd' => 'identicon')); ?>
				</a>
				<div class="media-body">
					<p class="pull-left"><a href="http://gravatar.com/">Gravatar</a> でアイコンを変更</p>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'fullname') ?>">
		<label class="col-sm-3 control-label" for="form_fullname">名前</label>
		<div class="col-sm-9">
<?php echo Form::input('fullname', Input::post('fullname'),
                       array('class' => 'form-control', 'placeholder' => '名前を入力してください')); ?>
			<label>
<?php echo Form::checkbox('fullname_sync_sns', true, Input::post('fullname_sync_sns', true),
                          array()); ?>
				SNSアカウントでのログイン時に更新
			</label>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'url') ?>">
		<label class="col-sm-3 control-label" for="form_email">ホームページ</label>
		<div class="col-sm-9">
<?php echo Form::input('url', Input::post('url'),
                       array('class' => 'form-control', 'placeholder' => 'ホームページを指定してください')); ?>
		</div>
	</div>
	<hr>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default">変更を保存</button>
		</div>
	</div>
</form>
			</div>
		</div>
	</div>
</div>
