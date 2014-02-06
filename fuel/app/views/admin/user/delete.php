<p><?php echo e(sprintf('%s(%s)', $username, $fullname)); ?> を削除して良いですか？</p>
<p class="text-warning"><span class="fa fa-exclamation-triangle fa-fw"></span>削除すると該当のユーザーはログインすることが出来なくなります。</p>
<p class="text-warning"><span class="fa fa-exclamation-triangle fa-fw"></span>ユーザーを削除するとパッケージも関連して削除されます。</p>

<form id="ban-form" role="form" method="post" action="<?php echo Uri::current(); ?>" class="hidden">
	<?php echo Form::csrf(); ?>
	<?php echo Form::hidden('id', $id); ?>
	<button id="yes" type="submit" class="btn btn-default" data-dismiss="modal">はい</button>
</form>
