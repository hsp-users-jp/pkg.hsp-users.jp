<div id="top-jumbotron" class="jumbotron">
	<h1>HSP Package DB</h1>
	<p>Hot Soup Processor&trade; のための 拡張プラグイン、モジュール、ツール、サンプル データベース</p>
</div>

<div id="top-well" class="well"
<?php if (1): ?>
     data-step="7" data-intro="名称や作者などでパッケージをすぐに検索することができます。"
<?php endif; ?>
>
	<form role="form" method="get" action="<?php echo Uri::create('search') ?>">
		<div class="input-group">
			<input type="text" name="q" class="form-control">
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
			</span>
		</div>
	</form>
</div>

<?php echo View::forge('auth/activation_warning')->render(); ?>
<?php echo View::forge('index/_flash')->render(); ?>

<div class="row">
	<div class="col-md-4">
		<div class="page-header">
			<h2>ようこそ</h2>
		</div>
		<p>HSP Package DB は、<abbr title="Hot Soup Processor&trade;">HSP</abbr> 用の 拡張プラグインや、モジュール、ツール、サンプル などを登録、検索、ダウンロードが出来るサービスです。</p>
		<p>ダウンロードするためにログインは必要ありませんが、ログインすることでパッケージの登録や評価などを行うことが出来ます。</p>
	</div>
	<div class="col-md-4"
<?php if (1): ?>
     data-step="8" data-intro="パッケージが最近更新があった順番に並びます。"
<?php endif; ?>
>
		<div class="page-header">
			<h2><span class="fa fa-arrow-circle-o-up fa-fw"></span>最近の更新
				<small><a href="<?php echo Uri::create('feed/recent'); ?>" style="color: #f39800"><span class="fa fa-rss-square"></span></a></small></h2>
		</div>
		<div class="row">
			<ul class_="list-unstyled" style="list-style-type: none; padding-left: 15px; padding-right: 15px;">
<?php foreach ($recents_top as $package): ?>
				<li>
<?php if (!$package->base): ?>
					<?php echo Html::anchor('package/'.$package->id, e($package->name), array('class' => 'text-muted')); ?>
					<span class="fa fa-trash-o fa-fw" title="削除済み"></span>
<?php else: ?>
					<?php echo Html::anchor('package/'.$package->id, e($package->name)); ?>
<?php endif; ?>
					<div class="pull-right"
						><span id="<?php echo 'package_rating_'.$package->id;?>"
						       data-score="<?php echo $package->rating->rating; ?>"></span
						>&nbsp;<small><?php echo e(Date::create_from_string($package->updated_at ?: $package->created_at, '%Y-%m-%d %H:%M:%S')
						                      ->format('%Y-%m-%d %H:%M:%S')); ?></small
					></div>
				</li>
<?php endforeach ?>
			</ul>
		</div>
		<p class="text-right">
			<a href="<?php echo Uri::create('package?sort=recent'); ?>"
<?php if (1): ?>
     data-step="9" data-intro="パッケージの詳細が確認できます。"
<?php endif; ?>
>続き…</a>
		</p>
	</div>
	<div class="col-md-4">
		<div class="page-header">
			<h2><span class="glyphicon glyphicon-star fa-fw"></span><span style="font-size: 80%">人気のダウンロード</span>
				<small><a href="<?php echo Uri::create('feed/popular'); ?>" style="color: #f39800"><span class="fa fa-rss-square"></span></a></small></h2>
		</div>
		<ol>
<?php foreach ($popular_top as $package): ?>
			<li>
<?php if (!$package->base): ?>
				<?php echo Html::anchor('package/'.$package->id, e($package->name), array('class' => 'text-muted')); ?>
				<span class="fa fa-trash-o fa-fw" title="削除済み"></span>
<?php else: ?>
				<?php echo Html::anchor('package/'.$package->id, e($package->name)); ?>
<?php endif; ?>
				<div class="pull-right"
					><span id="<?php echo 'package_rating_'.$package->id;?>"
					       data-score="<?php echo $package->rating->rating; ?>"></span
				></div>
			</li>
<?php endforeach ?>
		</ol>
		<p class="text-right">
			<a href="<?php echo Uri::create('package?sort=popular'); ?>">続き…</a>
		</p>
	</div>
</div>

<?php /*
<div class="row">
	<div class="col-md-4">
		<div class="page-header">
			<h2><span class="fa fa-file-text fa-fw"></span>ニュース <small><a href="<?php echo Uri::create('feed/news'); ?>"><span class="fa fa-rss-square" style="color: #f39800"></span></a></small></h2>
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
			<h2><span class="fa fa-tags fa-fw"></span>人気のタグ <small><a href="<?php echo Uri::create('feed/tags'); ?>"><span class="fa fa-rss-square" style="color: #f39800"></span></a></small></h2>
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

*/ ?>

<?php if (0): ?>
<div data-step="10" data-intro="dummy"></div>
<div data-step="11" data-intro="dummy"></div>
<div data-step="12" data-intro="dummy"></div>
<div data-step="13" data-intro="dummy"></div>
<?php endif; ?>
