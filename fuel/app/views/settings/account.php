<div class="row">
	<div class="col-md-3">
<?php echo View::forge('settings/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">アカウント</h3>
				<small class="text-muted"></small>
			</div>
			<div class="panel-body">
<?php if (true): ?>
	<div class="alert alert-warning">
			<p class="pull-right"><a href="#" class="btn btn-warning">アクティベーションメールを再送信する</a></p>
			<p><span class="fa fa-exclamation-triangle"></span> アカウントの本登録が完了されていません。</p>
	</div>
	<hr>
<?php endif ?>
<form class="form-horizontal" role="form" method="post">
	<?php echo Form::csrf(); ?>
	<div class="form-group <?php echo Arr::get($state,'username') ?>">
		<label class="col-sm-3 control-label" for="form_username">ユーザー名</label>
		<div class="col-sm-9">
<?php /*
<?php echo Form::input('username', Input::post('username'),
                       array('class' => 'form-control', 'placeholder' => 'ユーザー名を入力してください')); ?>
*/ ?>
			<p class="form-control-static"><?php echo e($username); ?></p>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'email') ?>">
		<label class="col-sm-3 control-label" for="form_email">メールアドレス</label>
		<div class="col-sm-9">
<?php echo Form::input('email', Input::post('email'),
                       array('type' => 'email', 'class' => 'form-control', 'placeholder' => 'メールアドレスを入力してください')); ?>
		</div>
	</div>
	<hr>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<a href="<?php echo Uri::create('oauth/twitter'); ?>"
			   class="btn btn-default btn-lg btn-block"><span class="fa fa-twitter"></span> Twitter と連携</a>
			<a href="<?php echo Uri::create('oauth/google'); ?>"
			   class="btn btn-default btn-lg btn-block"><span class="fa fa-google-plus"></span> Google と連携</a>
			<a href="<?php echo Uri::create('oauth/facebook'); ?>"
			   class="btn btn-default btn-lg btn-block"><span class="fa fa-facebook"></span> Facebook と連携</a>
			<a href="<?php echo Uri::create('oauth/github'); ?>"
			   class="btn btn-default btn-lg btn-block"><span class="fa fa-github"></span> GitHub と連携</a>
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
