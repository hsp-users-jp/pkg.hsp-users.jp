<?php if ($package->lastest->revision_id != $package->current->revision_id): ?>
<div class="alert alert-warning">
  <span class="fa fa-exclamation-triangle fa-fw fa-2x"></span> <?php echo Html::anchor('package/'.$package->current->id, '最新バージョン', array('class' => 'alert-link')) ?>が利用可能です。
  特別な理由がない限り最新のバージョンの利用を推奨します。
</div>
<?php endif ?>

<div>
<?php if ($is_author && $is_editable): ?>
	<div class="dropdown pull-right">
		<a data-toggle="dropdown" href="#"><span class="fa fa-cog fa-2x"></sapn></a>
		<ul class="dropdown-menu" role="menu">
			<li><?php echo Html::anchor('package/update/'.$package->current->id,
			                            '<span class="fa fa-arrow-circle-o-up"></span> パッケージを更新'); ?></li>
			<li class="divider"></li>
<?php if ($is_super_admin && !$package->current->base): ?>
			<li><?php echo Html::anchor('admin/package/cure/'.$package->current->id.'/all',
			                            '<span><span class="fa fa-circle-o"></span> 復元</span>',
			                            array('data-toggle' => 'modal',
			                                  'data-target' => '#Modal')); ?></li>
<?php else: ?>
			<li><?php echo Html::anchor('package/remove/'.$package->current->revision_id.'/all',
			                            '<span class="text-danger"><span class="fa fa-trash-o"></span> 削除</span>',
			                            array('data-toggle' => 'modal',
			                                  'data-target' => '#Modal')); ?></li>
<?php endif; ?>
		</ul>
	</div>
	<h1><?php if (!$package->current->base): ?>
<span class="fa fa-trash-o fa-fw" title="削除済み"></span>
		<?php endif; ?><a href="#" id="name" data-type="text"
	                          data-title="名称の変更"
	                          data-tpl="<input type='text' require>"
	      ><?php echo e($package->current->name); ?></a></h1>
<?php else: ?>
	<h1><?php if (!$package->current->base): ?>
<span class="fa fa-trash-o fa-fw" title="削除済み"></span>
	<?php endif; ?><?php echo e($package->current->name); ?></h1>
<?php endif; ?>
</div>

<ul class="list-inline">
	<li><a href="tag/aaa"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
	<li><a href="tag/iii"><span class="label label-primary"><span class="fa fa-tag"></span> ああああああ</span></a></li>
	<li><a href="tag/uuu"><span class="label label-primary"><span class="fa fa-tag"></span> あああ</span></a></li>
</ul>
<?php if ($is_author && $is_editable): ?>
<?php else: ?>
<?php endif; ?>

<hr>

<div class="row">
<div class="col-md-3 col-md-push-9">

<div class="panel panel-default">
  <div class="panel-body">

	<ul class="nav nav-pills nav-stacked">
		<li style="padding: 0;" class="dropdown-header">バージョン</li>
<?php if ($is_author && $is_editable): ?>
		<li style="padding-left: 1em;"><span><a href="#" id="version" data-type="text"
		                                                        data-title="バージョンの編集"
		                                                        data-tpl="<input type='text' require>"
		                                  ><?php echo e($package->current->version); ?></a></span></li>
<?php else: ?>
		<li style="padding-left: 1em;"><?php echo e($package->current->version); ?></li>
<?php endif; ?>
		<li style="padding: 0;" class="dropdown-header">更新日時</li>
		<li style="padding-left: 1em;"><?php echo e(Date::create_from_string($package->current->updated_at ?: $package->current->created_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></li>
		<li style="padding: 0;" class="dropdown-header">種別</li>
<?php if ($is_author && $is_editable): ?>
		<li style="padding-left: 1em;"><span><a href="#" id="type" data-type="select"
		                                                           data-title="種別の編集"
		><span class="<?php echo e($package->current->type->icon); ?>"></span> <?php echo e($package->current->type->name); ?></a></span></li>
<?php else: ?>
		<li style="padding-left: 1em;"><span class="<?php echo e($package->current->type->icon); ?>"></span> <?php echo e($package->current->type->name); ?></li>
<?php endif; ?>
<?php if ($package->current->url): ?>
		<li style="padding: 0;" class="dropdown-header">リンク</li>
		<li><?php echo Html::anchor($package->current->url, '<span class="fa fa-external-link"></span> ホームページ'); ?></li>
<?php else: ?>
<!--
		<li class="disabled"><a href="#"><span class="fa fa-external-link"></span> ホームページ</a></li>
-->
<?php endif; ?>
		<li style="padding: 0;" class="dropdown-header">共有</li>
		<li style="padding-left: 1em;">
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
				<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
				<a class="addthis_button_facebook_share" fb:share:layout="button_count"></a>
				<a class="addthis_button_tweet" tw:via="hsp_users_jp" tw:layout="default"></a>
				<a class="addthis_button_google_plusone" g:plusone:size="middle"></a>
				<a class="addthis_button_hatena"></a>
				<a class="addthis_button_mixi"></a>
				<a class="addthis_button_compact"></a>
			</div>
			<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo COnfig::get('addthis.pubid'); ?>" async="async"></script>
		</li>
		<li style="padding: 0;" class="dropdown-header"><hr /></li>
		<li><?php echo Html::anchor('package/download/'.$package->current->revision_id, '<span class="fa fa-download"></span> ダウンロード',
			                        array('class' => 'btn btn-primary')); ?></li>
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
    <div><?php echo e(Auth::get_metadata_by_id($package->current->user->id, 'fullname', '不明')) ?></div>
  </div>
</div>
  </div>
</div>

<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">ライセンス</h3>
  </div>
  <div class="panel-body">
<?php if ($is_author && $is_editable): ?>
    <div><a href="#" id="license" data-type="select"
	                              data-title="ライセンスの変更"
	       ><?php echo e($package->current->license->name); ?></a>
<?php else: ?>
    <div><?php echo e($package->current->license->name); ?>
<?php endif; ?>
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
    <p><?php echo Html::anchor('package/requirement/'.$package->current->revision_id, '詳細',
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
<?php if ($is_author && $is_editable): ?>
  <a href="#" id="description" data-type="textarea"
                               data-title="説明の編集"
                               data-rows="5"
     ><?php echo implode('<br/>', explode("\n", e($package->current->description))); ?></a>
<?php else: ?>
     <?php echo implode('<br/>', explode("\n", e($package->current->description))); ?>
<?php endif; ?>
  </div>
</div>

<?php if (!empty($package->current->screenshots)): ?>
<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">スクリーンショット</h3>
  </div>
  <div class="panel-body">
<div id="carousel-screenshots" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
<?php $i = 0; foreach ($package->current->screenshots as $screenshot): ?>
    <li data-target="#carousel-screenshots" data-slide-to="<?php echo $i; ?>" <?php echo $i ? '' : 'class="active"'; ?> ></li>
<?php $i++; endforeach; ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
<?php $i = 0; foreach ($package->current->screenshots as $screenshot): ?>
    <div class="item <?php echo $i ? '' : 'active'; ?>">
      <div class="text-center">
      <?php echo Asset::img($screenshot->name, array('style' => 'max-height: 450px')); ?>
      </div>
<?php /*
      <div class="carousel-caption">
        <h3>aaaa</h3>
        <p>aaa</p>
      </div>
*/ ?>
    </div>
<?php $i++; endforeach; ?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-screenshots" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-screenshots" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>
  </div>
</div>
<?php endif; ?>

<div class="panel panel-info">
  <div class="panel-heading">
<?php if ($is_author && $is_editable): ?>
	<a class="pull-right" href="<?php echo Uri::create('package/update/'.$package->current->id) ?>" title="パッケージを新しいバージョンに更新"><span class="fa fa-plus-circle fa-lg"></sapn></a>
<?php else: ?>
<?php endif; ?>
  	<h3 class="panel-title">バージョン</h3>
  </div>
  <table class="table table-striped">
<?php foreach ($package->versions as $version): ?>
    <tr>
<?php if ($version->revision_id == $package->current->revision_id): ?>
      <td><?php echo e($version->version); ?></td>
<?php elseif ($package->lastest->revision_id == $version->revision_id): ?>
      <td><?php echo Html::anchor(Uri::string(), e($version->version)); ?></td>
<?php else: ?>
      <td><?php echo Html::anchor(Uri::update_query_string(array('v'=>urlencode($version->version))), e($version->version)); ?></td>
<?php endif; ?>
      <td><?php echo e(Date::create_from_string($version->date, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></td>
<?php if ($is_author && $is_editable): ?>
      <td>こめんとてきななにか <a href="#" title="コメントを変更"><span class="fa fa-edit"></sapn></a></td>
<?php else: ?>
      <td>こめんとてきななにか</td>
<?php endif; ?>
<?php if ($is_author): ?>
<?php if ($is_super_admin && $version->deleted): ?>
      <td style="width: 1em;"><?php echo Html::anchor('package/cure/'.$version->revision_id,
                                                      '<span class="fa fa-circle-o"></sapn>',
                                                      array('title' => 'このバージョンを復元',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#Modal')); ?></td>
<?php else: ?>
      <td style="width: 1em;"><?php echo Html::anchor('package/remove/'.$version->revision_id,
                                                      '<span class="fa fa-trash-o"></sapn>',
                                                      array('title' => 'このバージョンを削除',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#Modal')); ?></td>
<?php endif; ?>
<?php endif; ?>
    </tr>
<?php endforeach; ?>
  </table>
</div>

</div>
</div>

<?php if ($is_editable): ?>
<?php echo Form::csrf(); ?>
<?php endif; ?>
