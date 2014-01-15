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
	<li style="padding-left: 1em;"><span class="<?php echo e($package->common->type->icon); ?>"></span> <?php echo e($package->common->type->name); ?></li>
	<li style="padding: 0;" class="dropdown-header">作者</li>
	<li style="padding-left: 1em;">
		<div class="media">
			<a class="pull-left" href="#">
				<img class="media-object" data-src="assets/js/holder.js/24x24/auto/#666:#666" alt="24x24">
			</a>
			<div class="media-body">
				<h4 class="media-heading"><?php echo e($package->common->name); ?></h4>
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


<!--
<div class="row">
	<div class="col-md-4">
		バージョン：<?php echo e($package->version->version); ?>
	</div>
	<div class="col-md-4">
		更新日時：<?php echo e(Date::create_from_string($package->version->created_at ?: $package->version->updated_at, '%Y-%m-%d %H:%M:%S')->format('%Y-%m-%d')); ?>
	</div>
	<div class="col-md-4">
		<div class="media">
			<a class="pull-left" href="#">
				<img class="media-object" data-src="assets/js/holder.js/24x24/auto/#666:#666" alt="24x24">
			</a>
			<div class="media-body">
				<h4 class="media-heading"><?php echo e($package->common->name); ?></h4>
			</div>
		</div>
	</div>
</div>

	<div>
		<?php echo implode('<br/>', explode("\n", e(Str::truncate($package->common->description, 30)))); ?>
	</div>

	<div class="text-right">
		<?php echo Html::anchor('package/'.$package->id, '詳細…'); ?>
	</div>
-->

</div>
</div>
 
