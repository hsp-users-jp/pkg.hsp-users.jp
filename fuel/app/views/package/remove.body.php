<?php if ('all' == Uri::segment(4)): ?>
	<p><?php echo e($package->name); ?> を削除して良いですか？</p>
<?php else: ?>
	<p><?php echo e($package->name); ?> <?php echo e($package->version); ?> を削除して良いですか？</p>
<?php endif; ?>

<p class="text-danger"><span class="fa fa-exclamation-circle fa-fw"></span>削除を取り消すことは出来ません！</p>

<form id="ban-form" role="form" method="post" action="<?php echo Uri::current(); ?>" class="hidden">
	<?php echo Form::csrf(); ?>
	<?php echo Form::hidden('id', $package->revision_id); ?>
	<button id="yes" type="submit" class="btn btn-default" data-dismiss="modal">はい</button>
</form>
