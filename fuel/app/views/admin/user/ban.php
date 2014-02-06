<p><?php echo e(sprintf('%s(%s)', $username, $fullname)); ?> を Ban して良いですか？</p>
<p class="text-warning"><span class="fa fa-exclamation-triangle fa-fw"></span>Ban すると該当のユーザーはログインすることが出来なくなります。</p>

<form id="ban-form" role="form" method="post" action="<?php echo Uri::current(); ?>" class="hidden">
	<?php echo Form::csrf(); ?>
	<?php echo Form::hidden('id', $id); ?>
	<button id="yes" type="submit" class="btn btn-default" data-dismiss="modal">はい</button>
</form>
