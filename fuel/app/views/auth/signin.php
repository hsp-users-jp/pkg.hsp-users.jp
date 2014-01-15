<h1>ログイン</h1>

<hr>

<div class="row">
<div class="col-md-6">

<div class="panel panel-default">
  <div class="panel-heading">ユーザー名とパスワードでログイン</div>
  <div class="panel-body">

<form role="form">
  <div class="form-group">
    <label for="exampleInputEmail1">ユーザー名もしくはメールアドレス</label>
    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="ユーザー名もしくはメールアドレスを入力してください">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">パスワード</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="パスワードを入力してください">
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
		<a href="<?php echo Uri::create('signin?provider=twitter'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-twitter"></span> Twitter で認証</a>
		<a href="<?php echo Uri::create('signin?provider=github'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-github"></span> GitHub で認証</a>
		<a href="<?php echo Uri::create('signin?provider=facebook'); ?>"
		   class="btn btn-default btn-lg btn-block"><span class="fa fa-facebook"></span> Facebook で認証</a>
  </div>
</div>

</div>
</div>
