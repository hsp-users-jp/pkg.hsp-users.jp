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
<?php if ('' != \Auth::get('activate_hash', '')): ?>
	<div class="alert alert-warning">
			<p class="pull-right">
				<a id="send-activation-mail" href="<?php echo Uri::create('settings/activation') ?>"
				   data-toggle="modal" data-target="#Modal" data-backdrop="true" class="btn btn-warning"
				  >アクティベーションメールを再送信</span></a>
			</p>
			<p><span class="fa fa-exclamation-triangle fa-fw fa-2x"></span> アカウントの本登録が完了されていません。</p>
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
                       <span class="text-muted">ここで登録されたアドレスは公開されません</span>
		</div>
	</div>
	<hr>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
<?php $provider_count = array_sum($provider);
      $provider_rest  = $provider_count;
      foreach (array('Twitter' => 'fa-twitter', 'Google' => 'fa-google-plus',
                     'Facebook' => 'fa-facebook', 'GitHub' => 'fa-github') as $name => $icon): ?>
<?php if (Arr::get($provider, strtolower($name), false)): ?>
<?php /*if (1 == $provider_rest && 1 == $provider_count):*/ ?>
			<a href="#"
			   class="btn btn-default btn-lg btn-block" disabled="disabled"><span class="fa <?php echo $icon; ?>"></span> <?php echo $name; ?> と連携済み</a>
<?php /*else: ?>
			<a href="#"
			   class="btn btn-danger btn-lg btn-block"><span class="fa <?php echo $icon; ?>"></span> <?php echo $name; ?> と連携を解除</a>
<?php endif;*/ ?>
<?php else: ?>
			<a href="<?php echo Uri::create('oauth/'.strtolower($name)); ?>"
			   class="btn btn-default btn-lg btn-block"><span class="fa fa-fw <?php echo $icon; ?>"></span> <?php echo $name; ?> と連携</a>
<?php endif; ?>
<?php $provider_rest--; endforeach; ?>
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
