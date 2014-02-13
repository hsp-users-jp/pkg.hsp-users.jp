<h1>ログイン</h1>

<hr>

<div class="row">
<div class="col-md-6">

<div class="panel panel-default">
  <div class="panel-heading">ユーザー名とパスワードでログイン</div>
  <div class="panel-body">

<form role="form" method="post">
  <?php echo Form::csrf(); ?>
  <div class="form-group <?php echo Arr::get($state,'username') ?>">
    <label for="form_username">ユーザー名もしくはメールアドレス</label>
    <?php echo Form::input('username', Input::post('username'),
                           array('class' => 'form-control', 'placeholder' => 'ユーザー名もしくはメールアドレスを入力してください')); ?>
  </div>
  <div class="form-group <?php echo Arr::get($state,'password') ?>">
    <label for="form_password">パスワード</label>
    <?php echo Form::password('password', Input::post('password'),
                              array('class' => 'form-control', 'placeholder' => 'パスワードを入力してください')); ?>
  </div>
  <div class="checkbox">
    <label>
      <?php echo Form::checkbox('remember_me', '1', Input::post('remember_me')); ?>
      ログイン状態を維持
    </label>
  </div>
  <button type="submit" class="btn btn-default">ログイン</button>
</form>

<hr>

<div class="text-right">
	<a href="<?php echo Uri::create('signup'); ?>"
	   ><span class="fa fa-sign-in"></span> 新規登録</a>
</div>

</div>
</div>

</div>
<div class="col-md-6">

<div class="panel panel-default">
  <div class="panel-heading">SNSのアカウントでログイン</div>
  <div class="panel-body">
<?php foreach (array('Twitter' => 'fa-twitter', 'Google' => 'fa-google-plus',
                     'Facebook' => 'fa-facebook', 'GitHub' => 'fa-github') as $name => $icon): ?>
		<a href="<?php echo Uri::create('oauth/'.strtolower($name)); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-fw <?php echo $icon; ?>"></span> <?php echo $name; ?> で認証</a>
<?php endforeach; ?>
  </div>
</div>

</div>
</div>
