<div id="top-jumbotron" class="jumbotron">
	<h1>HSP Package DB</h1>
	<p>Hot Soup Processor&trade; のための 拡張プラグイン、モジュール、ツール、サンプル データベース</p>
</div>

<?php echo View::forge('auth/activation_warning')->render(); ?>

<?php if (Session::get_flash('success')): ?>
			<div class="alert alert-success">
				<strong>成功</strong>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
				</p>
			</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
			<div class="alert alert-danger">
				<strong>エラー</strong>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
				</p>
			</div>
<?php endif; ?>

<div class="well">

<h2>検索</h2>

<form class="form-inline" role="form" method="get" action="<?php echo Uri::create('search') ?>">

<div class="input-group">
	<input type="text" name="q" class="form-control">
	<span class="input-group-btn">
		<button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
	</span>
</div><!-- /input-group -->

</form>

</div>

<div class="row">
	<div class="col-md-4">
		<div class="page-header">
			<h2>ようこそ</h2>
		</div>
		<p>HSP Package DB は、<abbr title="Hot Soup Processor&trade;">HSP</abbr> 用の 拡張プラグインや、モジュール、ツール、サンプル などを登録、検索、ダウンロードが出来るサービスです。</p>
		<p>ダウンロードするためにログインは必要ありませんが、ログインすることでパッケージの登録や評価などを行うことが出来ます。ぜひ、登録をしてみてください。</p>
	</div>
	<div class="col-md-4">
		<div class="page-header">
			<h2><span class="fa fa-arrow-circle-o-up"></span> 最近の更新 <small><a href="#" style="color: #f39800"><span class="fa fa-rss-square"></span></a></small></h2>
		</div>
		<div class="row">
<?php foreach ($recents_top as $row): ?>
			<div class="col-md-6">
				<?php echo Html::anchor('package/'.$row->id, e($row->common->name)); ?>
			</div>
			<div class="col-md-6 text-right">
				<small><?php echo e(Date::create_from_string($row->version->created_at ?: $row->version->updated_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d %H:%M:%S')); ?></small>
			</div>
<?php endforeach ?>
		</div>
		<p class="text-right">
			<a href="<?php echo Uri::create('package?sort=recent'); ?>">続き…</a>
		</p>
	</div>
	<div class="col-md-4">
		<div class="page-header">
			<h2><span class="glyphicon glyphicon-star"></span> 人気のダウンロード <small><a href="#" style="color: #f39800"><span class="fa fa-rss-square"></span></a></small></h2>
		</div>
		<ol>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
		</ol>
		<p class="text-right">
			<a href="<?php echo Uri::create('package?sort=popular'); ?>">続き…</a>
		</p>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="page-header">
			<h2><span class="fa fa-file-text"></span> ニュース <small><a href="#"><span class="fa fa-rss-square" style="color: #f39800"></span></a></small></h2>
		</div>
		<ul class="list-unstyled">
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
			<li><a href="#">あああ</a></li>
		</ul>
		<p class="text-right">
			<a href="#">続き…</a>
		</p>
	</div>
	<div class="col-md-8">
		<div class="page-header">
			<h2><span class="fa fa-tags"></span> 人気のタグ <small><a href="#"><span class="fa fa-rss-square" style="color: #f39800"></span></a></small></h2>
		</div>
		<ul class="list-inline">
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> ああああああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary">Primary</span></a></li>
			<li><a href="#"><span class="label label-primary"><span class="fa fa-tag"></span> Primary</span></a></li>
		</ul>
		<p class="text-right">
			<a href="#">続き…</a>
		</p>
	</div>
</div>
