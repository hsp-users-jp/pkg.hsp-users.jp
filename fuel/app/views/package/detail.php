<?php $first_version = null;
      foreach ($package->versions as $version) { $first_version = $version; break; }
      if ($first_version->id != $package->version->id): ?>
<div class="alert alert-warning">
  <?php echo Html::anchor('package/'.$package->id, '最新バージョン', array('class' => 'alert-link')) ?>が利用可能です。
  特別な理由がない限り最新のバージョンの利用を推奨します。
</div>
<?php endif ?>

<div class="row">
	<div class="col-md-10">
		<h1><?php echo e($package->common->name); ?></h1>
	</div>
	<div class="col-md-2">
		<div class="btn-group pull-right">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		    <span class="fa fa-cog"></sapn> <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" role="menu">
		    <li><?php echo Html::anchor('package/update/'.$package->id, '<span class="fa fa-arrow-circle-o-up"></span> 新しいバージョンに更新'); ?></li>
		    <li><?php echo Html::anchor('package/edit/'.$package->id, '<span class="fa fa-edit"></span> パッケージの情報を更新'); ?></li>
		    <li class="divider"></li>
		    <li><?php echo Html::anchor('package/remove/'.$package->id, '<span class="text-danger"><span class="fa fa-trash-o"></span> 削除</span>'); ?></li>
		  </ul>
		</div>
	</div>
</div>

<div class="row">
<div class="col-md-3 col-md-push-9">

<div class="panel panel-default">
  <div class="panel-body">

	<ul class="nav nav-pills nav-stacked">
		<li style="padding: 0;" class="dropdown-header">バージョン</li>
		<li style="padding-left: 1em;"><?php echo e($package->version->version); ?></li>
		<li style="padding: 0;" class="dropdown-header">更新日時</li>
		<li style="padding-left: 1em;"><?php echo e(Date::create_from_string($package->version->created_at ?: $package->version->updated_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></li>
		<li style="padding: 0;" class="dropdown-header">種別</li>
		<li style="padding-left: 1em;"><span class="<?php echo e($package->common->type->icon); ?>"></span> <?php echo e($package->common->type->name); ?></li>
		<li><a href="#" data-loading-text="Loading..." class="btn btn-primary"><span class="fa fa-download"></span> ダウンロード</a></li>
<?php if ($package->common->url): ?>
		<li><?php echo Html::anchor($package->common->url, '<span class="fa fa-external-link"></span> ホームページ'); ?></li>
<?php else: ?>
		<li class="disabled"><a href="#"><span class="fa fa-external-link"></span> ホームページ</a></li>
<?php endif; ?>
		<li><a href="#"><span class="fa fa-share"></span> 共有</a></li>
	</ul>
  </div>
</div>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">作者</h3>
  </div>
  <div class="panel-body">
<div class="media">
  <a class="pull-left" href="#">
    <img class="media-object" data-src="assets/js/holder.js/48x48/auto/#666:#666" alt="48x48">
  </a>
  <div class="media-body">
    <h4 class="media-heading">hogehoge</h4>
    ffugafuga
  </div>
</div>
  </div>
</div>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">ライセンス</h3>
  </div>
  <div class="panel-body">
    <div>
      <?php echo e($package->version->license->name); ?>
<?php if (!empty($package->version->license->url)): ?>
  ( <?php echo Html::anchor($package->version->license->url,
                           '<span class="fa fa-external-link"></span> 詳細'); ?> )
<?php endif; ?>
    </div>
    <div><small><?php echo e($package->version->license->description); ?></small></div>
  </div>
</div>

<div class="panel panel-primary">
  <div class="panel-heading">動作環境</div>
  <div class="panel-body">
    <p><?php echo Html::anchor('package/requirement/'.$package->version->id, '詳細',
                               array('data-toggle' => 'modal', 'data-target' => '#Modal')); ?></p>
  </div>
  <table class="table table-striped">
    <tr>
      <th>バージョン</th>
      <th>動作環境</th>
      <th>利用者報告</th>
    </tr>
<?php foreach ($hsp_categories as $hsp_category): ?>
	<tr>
		<td style="white-space: nowrap;"><span class="<?php echo e($hsp_category->icon); ?>"><span> <?php echo e($hsp_category->name); ?></td>
<?php for ($i = 0; $i < 2; ++$i): ?>
		<td class="text-center">
<?php
	switch ($package_support[$hsp_category->id][$i])
	{
	case Model_Working_Report::StatusUnknown:
		echo ' - ';
		break;
	case Model_Working_Report::StatusSupported:
		echo '<span class="label label-success"><span class="fa fa-check"><span></span>';
		break;
	}
?>
		</td>
<?php endfor; ?>
    </tr>
<?php endforeach; ?>
  </table>
</div>

</div>
<div class="col-md-9 col-md-pull-3">

<ul class="list-inline">
	<li><a href="tag/aaa"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
	<li><a href="tag/iii"><span class="label label-primary"><span class="fa fa-tag"></span> ああああああ</span></a></li>
	<li><a href="tag/uuu"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
</ul>

<hr>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">説明</h3>
  </div>
  <div class="panel-body"><?php echo implode('<br/>', explode("\n", e($package->common->description))); ?></div>
</div>

<?php if (!empty($package->screenshots)): ?>
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">スクリーンショット</h3>
  </div>
  <div class="panel-body">
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <div class="item active">
      <img data-src="assets/js/holder.js/900x500/auto/#666:#666" alt="900x500">
      <div class="carousel-caption">
        <h3>aaaa</h3>
        <p>aaa</p>
      </div>
    </div>
    <div class="item">
      <img data-src="assets/js/holder.js/900x500/auto/#666:#666" alt="900x500">
      <div class="carousel-caption">
        <h3>bbbb</h3>
        <p>aaa</p>
      </div>
    </div>
    <div class="item">
      <img data-src="assets/js/holder.js/900x500/auto/#666:#666" alt="900x500">
      <div class="carousel-caption">
        <h3>cccc</h3>
        <p>aaa</p>
      </div>
    </div>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>
  </div>
</div>
<?php endif; ?>

<div class="panel panel-primary">
  <div class="panel-heading">バージョン</div>
  <table class="table table-striped">
<?php foreach ($package->versions as $version): ?>
    <tr>
<?php if ($version->id == $package->version->id): ?>
      <td><?php echo e($version->version); ?></td>
<?php elseif ($first_version->id == $version->id): ?>
      <td><?php echo Html::anchor(Uri::string(), e($version->version)); ?></td>
<?php else: ?>
      <td><?php echo Html::anchor(Uri::update_query_string(array('v'=>e($version->version))), e($version->version)); ?></td>
<?php endif; ?>
      <td><?php echo e(Date::create_from_string($package->version->created_at ?: $package->version->updated_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></td>
    </tr>
<?php endforeach; ?>
  </table>
</div>

</div>
</div>


