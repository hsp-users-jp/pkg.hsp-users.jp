<p><?php echo e(sprintf('%s(%s)', $username, $fullname)); ?> の Ban を解除して良いですか？</p>

<form id="ban-form" role="form" method="post" action="<?php echo Uri::current(); ?>" class="hidden">
	<?php echo Form::csrf(); ?>
	<?php echo Form::hidden('id', $id); ?>
	<button id="yes" type="submit" class="btn btn-default" data-dismiss="modal">はい</button>
</form>
