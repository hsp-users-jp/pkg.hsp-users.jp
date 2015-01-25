<h1>新規登録</h1>

<hr>

<?php if ($provider): ?>
<div class="alert alert-info">
	<strong>ようこそ</strong>
	<p><?php echo e(Session::get('auth-strategy.user.nickname', '').'@'.$provider) ?> を使用してアカウントを登録しようとしています。</p>
</div>
<?php endif; ?>

<div class="row">
<div class="col-md-offset-3 col-md-6">

<div class="panel panel-default">
  <div class="panel-body">

<form role="form" method="post">
  <?php echo Form::csrf(); ?>
  <div class="form-group <?php echo Arr::get($state,'username') ?>">
    <label for="form_username">ユーザー名 <span class="label label-danger">必須</span></label>
    <?php echo Form::input('username', Input::post('username'),
                           array('class' => 'form-control', 'placeholder' => 'ユーザー名を入力してください')); ?>
    <div class="alert alert-warning" role="alert" style="margin-top: 0.5em">
        <i class="fa fa-exclamation-triangle"></i> 登録後ユーザー名の変更をすることは出来ません
    </div>
  </div>
<?php if (!$provider): ?>
  <div class="form-group <?php echo Arr::get($state,'password') ?>">
    <label for="form_password">パスワード <span class="label label-danger">必須</span></label>
    <?php echo Form::password('password', Input::post('password'),
                              array('class' => 'form-control', 'placeholder' => 'パスワードを入力してください')); ?>
  </div>
  <div class="form-group <?php echo Arr::get($state,'password2') ?>">
    <label for="form_password2">パスワード(確認) <span class="label label-danger">必須</span></label>
    <?php echo Form::password('password2', Input::post('password2'),
                              array('class' => 'form-control', 'placeholder' => '上と同じ内容を入力してください')); ?>
  </div>
<?php endif; ?>
  <div class="form-group <?php echo Arr::get($state,'fullname') ?>">
    <label for="form_fullname">名前 <span class="label label-danger">必須</span></label>
    <?php echo Form::input('fullname', Input::post('fullname'),
                           array('class' => 'form-control', 'placeholder' => '名前を入力してください')); ?>
<?php if (!$provider): ?>
    <div class="alert alert-warning" role="alert" style="margin-top: 0.5em">
        <i class="fa fa-exclamation-triangle"></i> 連携しているアカウントに同期して変更されることはありません
    </div>
<?php endif; ?>
  </div>
  <div class="form-group <?php echo Arr::get($state,'email') ?>">
    <label for="form_email">メールアドレス <span class="label label-danger">必須</span></label>
    <?php echo Form::input('email', Input::post('email'),
                           array('type' => 'email', 'class' => 'form-control', 'placeholder' => 'メールアドレスを入力してください')); ?>
  </div>
  <button type="submit" class="btn btn-default">登録</button>
</form>

</div>
</div>

</div>
</div>
