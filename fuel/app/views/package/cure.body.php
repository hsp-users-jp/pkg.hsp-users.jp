<p><?php echo e($package->name); ?> <?php echo e($package->version); ?> を復元して良いですか？</p>

<form id="ban-form" role="form" method="post" action="<?php echo Uri::current(); ?>" class="hidden">
	<?php echo Form::csrf(); ?>
	<?php echo Form::hidden('id', $package->revision_id); ?>
	<button id="yes" type="submit" class="btn btn-default" data-dismiss="modal">はい</button>
</form>
