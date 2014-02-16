<div class="row">
	<div class="col-md-3">
<?php echo View::forge('admin/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">

<h1>パッケージ管理</h1>
<hr>

<div class="text-center">
<?php echo $pagination ?>
</div>

<div class="table-responsive">
	<table class="table table-striped">
		<tr>
			<th class="text-center" style="width: 1em;">&nbsp</th>
			<th>パッケージ名</th>
			<th>種別</th>
			<th>バージョン</th>
			<th>最終更新</th>
			<th>作者</th>
			<th>状態</th>
		</tr>
<?php foreach ($packages as $package): ?>
		<tr>
			<td class="text-left">
<?php if ($package->deleted): ?>
				<a href="<?php echo Uri::create(Uri::string().'/cure/:id', array('id'=>$package->id)) ?>"
				   data-toggle="modal" data-target="#Modal" data-backdrop="true" title="復元します"
				  ><span class="fa fa-circle-o fa-lg"></span></a>
<?php else: ?>
				<a href="<?php echo Uri::create(Uri::string().'/destroy/:id', array('id'=>$package->id)) ?>"
				   data-toggle="modal" data-target="#Modal" data-backdrop="true" title="削除します"
				  ><span class="fa fa-trash-o fa-lg"></span></a>
<?php endif; ?>
			</td>
			<td class="text-left"><?php
					echo Html::anchor('package/'.$package->id, e($package->name));
				?></td>
			<td class="text-center"><?php
					echo Html::anchor('search?q=type:'.urlencode($package->type->name),
					                  '<span class="'.e($package->type->icon).' fa-fw"></span>'.e($package->type->name));
				?></td>
			<td class="text-center"><?php echo e($package->version); ?></td>
			<td style="white-space: nowrap;"><?php echo e($package->updated_at
			                                            ? Date::create_from_string($package->updated_at, '%Y-%m-%d %H:%M:%S')
			                                                  ->format('%Y-%m-%d') : ''); ?></td>
			<td style="white-space: nowrap;"><?php
					echo Html::anchor('author/'.urlencode($package->user->username),
					                  e($package->user->username));
				?></td>
			</td>
			<td class="text-left">
<?php if ($package->deleted): ?>
				<span class="fa fa-trash-o fa-lg" title="削除済み"></span>
<?php else: ?>
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
