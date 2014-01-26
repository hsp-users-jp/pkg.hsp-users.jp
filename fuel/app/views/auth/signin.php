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
      <input type="checkbox"> ログイン状態を維持
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
  <div class="panel-heading">SNSでログイン</div>
  <div class="panel-body">
		<a href="<?php echo Uri::create('oauth/twitter'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-twitter"></span> Twitter で認証</a>
		<a href="<?php echo Uri::create('oauth/google'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-google-plus"></span> Google で認証</a>
		<a href="<?php echo Uri::create('oauth/facebook'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-facebook"></span> Facebook で認証</a>
		<a href="<?php echo Uri::create('oauth/github'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-github"></span> GitHub で認証</a>
  </div>
</div>

</div>
</div>
