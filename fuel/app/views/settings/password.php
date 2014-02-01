<div class="row">
	<div class="col-md-3">
<?php echo View::forge('settings/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">パスワードの変更</h3>
				<small class="text-muted"></small>
			</div>
			<div class="panel-body">
<form class="form-horizontal" role="form" method="post">
	<?php echo Form::csrf(); ?>
	<div class="form-group <?php echo Arr::get($state,'cur_password') ?>">
		<label class="col-sm-3 control-label" for="form_cur_password">現在のパスワード</label>
		<div class="col-sm-9">
<?php echo Form::password('cur_password', Input::post('cur_password'),
                          array('class' => 'form-control', 'placeholder' => '現在のパスワードを入力してください')); ?>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'new_password') ?>">
		<label class="col-sm-3 control-label" for="form_new_password">新しいパスワード</label>
		<div class="col-sm-9">
<?php echo Form::password('new_password', Input::post('new_password'),
                          array('class' => 'form-control', 'placeholder' => '新しいパスワードを入力してください')); ?>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'new_password2') ?>">
		<label class="col-sm-3 control-label" for="form_new_password2">新しいパスワード<br/>(確認用)</label>
		<div class="col-sm-9">
<?php echo Form::password('new_password2', Input::post('new_password2'),
                          array('class' => 'form-control', 'placeholder' => '新しいパスワードを再度入力してください')); ?>
		</div>
	</div>
	<hr>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default">パスワードを更新</button>
		</div>
	</div>
</form>
			</div>
		</div>
	</div>
</div>
