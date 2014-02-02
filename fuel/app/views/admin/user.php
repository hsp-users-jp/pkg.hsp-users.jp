<div class="row">
	<div class="col-md-3">
<?php echo View::forge('admin/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">

<h1>ユーザー管理</h1>
<hr>

<div class="text-center">
<?php echo $pagination ?>
</div>

<div class="table-responsive">
	<table class="table table-striped">
		<tr>
			<th class="text-center" style="width: 5em;">&nbsp</th>
			<th>状態</th>
			<th>ユーザー名</th>
			<th>名前</th>
			<th>認証</th>
		</tr>
<?php foreach ($users as $user): ?>
		<tr>
			<td class="text-center">
<?php if ($user['mine']): ?>
<?php else: ?>
				<a href="<?php echo Uri::create(Uri::string().'/edit/:id', array('id'=>$user['id'])) ?>"
				   title="BAN"><span class="fa fa-ban fa-lg"></span></a>
<?php endif; ?>
			</td>
			<td class="text-center">
				<span class="fa fa-user fa-lg" title="管理者"></span>
				<span class="fa fa-ban fa-lg text-danger" title="BAN済み"></span>
<?php if ($user['activate_waiting']): ?>
				<span class="fa fa-clock-o fa-lg" title="アクティベーション待ち"></span>
<?php endif; ?>
<?php if (0 < $user['count_of_packages']): ?>
				<span class="fa fa-list fa-lg" title="パッケージ作者"></span>
<?php endif; ?>
				<span class="fa fa-trash-o fa-lg" title="削除済み"></span>
			</td>
			<td><?php echo e($user['username']); ?></td>
			<td><?php echo e($user['fullname']); ?></td>
			<td class="text-center">
				<span class="fa fa-key fa-lg" title="パスワードで認証済み"></span>
<?php if (Arr::get($user, 'provider.twitter')): ?>
				<span class="fa fa-twitter fa-lg" title="Twitterで認証済み"></span>
<?php endif; ?>
<?php if (Arr::get($user, 'provider.google')): ?>
				<span class="fa fa-google-plus fa-lg" title="Googleで認証済み"></span>
<?php endif; ?>
<?php if (Arr::get($user, 'provider.facebook')): ?>
				<span class="fa fa-facebook fa-lg" title="Facebookで認証済み"></span>
<?php endif; ?>
<?php if (Arr::get($user, 'provider.github')): ?>
				<span class="fa fa-github fa-lg" title="GitHubで認証済み"></span>
<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</table>
</div>

<div class="text-center">
<?php echo $pagination ?>
</div>


	</div>
</div>
