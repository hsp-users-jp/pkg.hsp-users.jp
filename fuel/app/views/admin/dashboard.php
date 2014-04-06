<div class="row">
	<div class="col-md-3">
<?php echo View::forge('admin/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">

<h1>管理者ダッシュボード</h1>
<hr>

<div class="row">
	<div class="col-md-4">
<div class="panel panel-default">
	<div class="panel-body">
		<div class="media">
			<span class="pull-left">
				<span class="fa fa-list fa-5x"></span>
			</span>
			<div class="media-body">
				<h4 class="media-heading">パッケージ数</h4>
				<ul class="list-unstyled">
					<li>公開：<?php echo $published_package_count; ?></li>
					<li>削除：<?php echo $removed_package_count; ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
	</div>
	<div class="col-md-4">
<div class="panel panel-default">
	<div class="panel-body">
		<div class="media">
			<span class="pull-left">
				<span class="fa fa-users fa-5x"></span>
			</span>
			<div class="media-body">
				<h4 class="media-heading">ユーザー数</h4>
				<ul class="list-unstyled">
					<li>登録：<?php echo $registerd_user_count; ?></li>
					<li>作者：<?php echo $author_count; ?></li>
					<li>Ban：<?php echo $banned_user_count; ?></li>
					<li>未アクティベート：<?php echo $inactivate_user_count; ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
	</div>
	<div class="col-md-4">
	</div>
</div>

	</div>
</div>
