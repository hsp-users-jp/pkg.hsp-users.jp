<h1>新規登録</h1>

<hr>

<div class="row">
<div class="col-md-offset-3 col-md-6">

<div class="panel panel-default">
  <div class="panel-body">

<form role="form" method="post">
  <?php echo Form::csrf(); ?>
  <div class="form-group <?php echo Arr::get($state,'username') ?>">
    <label for="form_username">ユーザー名</label>
    <?php echo Form::input('username', Input::post('username'),
                           array('class' => 'form-control', 'placeholder' => 'ユーザー名を入力してください')); ?>
  </div>
  <div class="form-group <?php echo Arr::get($state,'password') ?>">
    <label for="form_password">パスワード</label>
    <?php echo Form::password('password', Input::post('password'),
                              array('class' => 'form-control', 'placeholder' => 'パスワードを入力してください')); ?>
  </div>
  <div class="form-group <?php echo Arr::get($state,'password2') ?>">
    <label for="form_password2">パスワード(確認)</label>
    <?php echo Form::password('password2', Input::post('password2'),
                              array('class' => 'form-control', 'placeholder' => '上と同じ内容を入力してください')); ?>
  </div>
  <div class="form-group <?php echo Arr::get($state,'email') ?>">
    <label for="form_email">メールアドレス</label>
    <?php echo Form::input('email', Input::post('email'),
                           array('type' => 'email', 'class' => 'form-control', 'placeholder' => 'メールアドレスを入力してください')); ?>
  </div>
  <button type="submit" class="btn btn-default">登録</button>
</form>

</div>
</div>

</div>
</div>
