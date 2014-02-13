<div class="panel panel-default">
<div class="panel-body">
	<div class="media"><?php
$author = Auth::get_profile_fields_by_id($user->id, 'fullname', '不明');
$url = Auth::get_profile_fields_by_id($user->id, 'url', ''); ?>
		<span class="pull-left">
			<?php echo Asset::gravatar($user->email, array(), array('size' => 96, 'd' => 'identicon')); ?>
		</span>
		<div class="media-body">
<?php if (Auth::is_banned($user)): ?>
			<h4 class="media-heading"><span class="fa fa-ban fa-lg text-danger" title="BAN済み"></span> <?php echo e($user->username) ?></h4>
<?php else: ?>
			<h4 class="media-heading"><?php echo e($user->username) ?></h4>
<?php endif; ?>
			<p><?php echo e($author) ?></p>
<?php if ($url): ?>
			<p><a href="<?php echo e($url); ?>"><span class="fa fa-home fa-2x"></span> <?php echo e($url); ?></a></p>
<?php else: ?>
			<p><span class="fa fa-home fa-2x text-mute"></span></p>
<?php endif; ?>
		</div>
	</div>
</div>
</div>

<?php /*
<?php foreach ($rows as $row): ?>
<?php echo View::forge('package/item', array('package' => $row, 'without_author' => true))->render(); ?>
<?php endforeach; ?>
*/ ?>

<?php $i = 0; $rows_count = count($rows); foreach ($rows as $row): ?>
<?php if (0 == $i % 2): ?>
<div class="row">
<?php endif; ?>
	<div class="col-sm-6">
<?php echo View::forge('package/item', array('package' => $row, 'without_author' => true, 'x' => 2))->render(); ?>
	</div>
<?php if (2 == $i % 3 || $i + 1 == $rows_count): ?>
</div>
<?php endif; ?>
<?php $i++; endforeach; ?>
