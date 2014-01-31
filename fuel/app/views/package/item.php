<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?php
        echo Html::anchor('package/'.$package->id, e($package->common->name));
      ?></h3>
  </div>
  <div class="panel-body">

<div class="row">
	<div class="col-md-2">

<ul class="nav nav-pills nav-stacked">
	<li style="padding: 0;" class="dropdown-header">バージョン</li>
	<li style="padding-left: 1em;"><?php echo e($package->version->version); ?></li>
	<li style="padding: 0;" class="dropdown-header">更新日時</li>
	<li style="padding-left: 1em;"><?php echo e(Date::create_from_string($package->version->created_at ?: $package->version->updated_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?></li>
	<li style="padding: 0;" class="dropdown-header">種別</li>
	<li style="padding-left: 1em;"><span><a href="<?php echo Uri::create('search?q=type:' . urlencode($package->common->type->name)); ?>"><span class="<?php echo e($package->common->type->icon); ?>"></span> <?php echo e($package->common->type->name); ?></a></span></li>
	<li style="padding: 0;" class="dropdown-header">作者</li>
	<li style="padding-left: 1em;">
		<div class="media"><?php $auther = Auth::get_profile_fields_by_id($package->user->id, 'fullname', '不明'); ?>
			<a class="pull-left" href="<?php echo Uri::create('search?q=author:' . urlencode($auther)); ?>">
				<?php echo Asset::gravatar($package->user->email, array(), array('size' => 24, 'd' => 'identicon')); ?>
			</a>
			<div class="media-body">
				<a href="<?php echo Uri::create('search?q=author:' . urlencode($auther)); ?>"><h4 class="media-heading"><?php echo e($auther) ?></h4></a>
			</div>
		</div>
	</li>
</ul>

	</div>
	<div class="col-md-10">
		<div>
			<?php echo implode('<br/>', explode("\n", e(Str::truncate($package->common->description, 30)))); ?>
		</div>
	
		<div class="text-right" style="bottom: 0;">
			<?php echo Html::anchor('package/'.$package->id, '詳細…'); ?>
		</div>
	</div>
</div>

</div>
</div>
 
