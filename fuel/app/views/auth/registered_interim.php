<h1>仮登録</h1>

<hr>

<p>HSP Package DB へご登録ありがとうございます。</p>
<p>現在は仮登録状態です。</p>
<p>仮登録の状態でもお使い頂けますが、登録から２週間でアカウントはコメントやパッケージを含め削除されてしまうのでお早めに本登録をすることをお勧めします。</p>
<?php if ('noop' != Email::get_config('driver')): ?>
<p>すでに本登録ご案内のメールは送信されているのでメールボックスをご確認ください。もし、しばらくしても届かない場合は、<?php echo Html::anchor('settings/account', 'アカウント情報の変更'); ?>からメールアドレスを確認し<?php echo Html::anchor('settings/activation', 'アクティベーションメールを再送信'); ?>を行ってください。</p>
<?php endif; ?>

<hr>

<p class="text-center">
<?php echo Html::anchor('tutorial', 'チュートリアルを表示', array('class' => 'btn btn-primary')); ?>
</p>
