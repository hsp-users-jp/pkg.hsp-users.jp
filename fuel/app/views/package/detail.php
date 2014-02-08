<?php $first_version = null;
      foreach ($package->versions as $version) { $first_version = $version; break; }Log::debug(print_r($first_version,true));
      if ($first_version->version != $package->current->version): ?>
<div class="alert alert-warning">
  <span class="fa fa-exclamation-triangle"></span> <?php echo Html::anchor('package/'.$package->current->id, '最新バージョン', array('class' => 'alert-link')) ?>が利用可能です。
  特別な理由がない限り最新のバージョンの利用を推奨します。
</div>
<?php endif ?>

<div>
	<h1><?php echo e($package->current->name); ?></h1>
</div>

<ul class="list-inline">
	<li><a href="tag/aaa"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
	<li><a href="tag/iii"><span class="label label-primary"><span class="fa fa-tag"></span> ああああああ</span></a></li>
	<li><a href="tag/uuu"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
</ul>

<hr>

<div class="row">
<div class="col-md-3 col-md-push-9">

<div class="panel panel-default">
  <div class="panel-body">

	<ul class="nav nav-pills nav-stacked">
		<li style="padding: 0;" class="dropdown-header">バージョン</li>
		<li style="padding-left: 1em;"><?php echo e($package->current->version); ?></li>
		<li style="padding: 0;" class="dropdown-header">更新日時</li>
		<li style="padding-left: 1em;"><?php echo e(Date::create_from_string($package->current->updated_at ?: $package->current->created_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></li>
		<li style="padding: 0;" class="dropdown-header">種別</li>
		<li style="padding-left: 1em;"><span class="<?php echo e($package->current->type->icon); ?>"></span> <?php echo e($package->current->type->name); ?></li>
		<li><?php echo Html::anchor('package/download/'.$package->version->id, '<span class="fa fa-download"></span> ダウンロード',
			                        array('class' => 'btn btn-primary')); ?></li>
<?php if ($package->current->url): ?>
		<li><?php echo Html::anchor($package->current->url, '<span class="fa fa-external-link"></span> ホームページ'); ?></li>
<?php else: ?>
		<li class="disabled"><a href="#"><span class="fa fa-external-link"></span> ホームページ</a></li>
<?php endif; ?>
		<li class="dropdown">
			<a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
				<span class="fa fa-share"></span> 共有 <span class="caret"></span>
			</a>
		  <div class="dropdown-menu" role="menu" aria-labelledby="dLabel">
		  <p>
		    aaa...aa...aa...aa…<br/>
		    aaa...aa...aa...aa…<br/>
		    aaa...aa...aa...aa…<br/>
		    aaa...aa...aa...aa…<br/>
		    aaa...aa...aa...aa…<br/>
		    </p>
		  </div>
		</li>
	</ul>
  </div>
</div>

<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">作者</h3>
  </div>
  <div class="panel-body">
<div class="media">
  <a class="pull-left" href="#">
    <?php echo Asset::gravatar($package->current->user->email, array(), array('size' => 48, 'd' => 'identicon')); ?>
  </a>
  <div class="media-body">
    <h4 class="media-heading"><?php echo Html::anchor('author/'.urlencode($package->current->user->username), e($package->current->user->username)); ?></h4>
    <div><?php echo e(Auth::get_profile_fields_by_id($package->current->user->id, 'fullname', '不明')) ?></div>
  </div>
</div>
  </div>
</div>

<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">ライセンス</h3>
  </div>
  <div class="panel-body">
    <div><?php echo e($package->current->license->name); ?>
      <span id="license-url">
<?php if (!empty($package->current->license->url)): ?>
  ( <?php echo Html::anchor($package->current->license->url,
                           '<span class="fa fa-external-link"></span> 詳細'); ?> )
<?php endif; ?>
      </span>
    </div>
    <div><small id="license-description"><?php echo e($package->current->license->description); ?></small></div>
  </div>
</div>

<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">動作環境</h3>
  </div>
  <div class="panel-body">
    <p><?php echo Html::anchor('package/requirement/'.$package->current->id, '詳細',
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

<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">説明</h3>
  </div>
  <div id="description_" class="panel-body">
     <?php echo implode('<br/>', explode("\n", e($package->current->description))); ?>
  </div>
</div>

<?php if (!empty($package->current->screenshots)): ?>
<div class="panel panel-info">
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

<div class="panel panel-info">
  <div class="panel-heading">
  	<h3 class="panel-title">バージョン</h3>
  </div>
  <table class="table table-striped">
<?php foreach ($package->versions as $version): ?>
    <tr>
<?php if ($version->version == $package->current->version): ?>
      <td><?php echo e($version->version); ?></td>
<?php elseif ($first_version->version == $version->version): ?>
      <td><?php echo Html::anchor(Uri::string(), e($version->version)); ?></td>
<?php else: ?>
      <td><?php echo Html::anchor(Uri::update_query_string(array('v'=>e($version->version))), e($version->version)); ?></td>
<?php endif; ?>
      <td><?php echo e(Date::create_from_string($version->date, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></td>
      <td>こめんとてきななにか</td>
    </tr>
<?php endforeach; ?>
  </table>
</div>

</div>
</div>
