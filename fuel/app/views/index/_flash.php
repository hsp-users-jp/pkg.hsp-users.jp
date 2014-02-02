<?php if (Session::get_flash('success')): ?>
	<div class="alert alert-success">
		<p><?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?></p>
	</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
	<div class="alert alert-danger">
		<strong><h2 style="margin-top: 0;"><span class="fa fa-ban"></span> エラー</h2></strong>
		<p><?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?></p>
	</div>
<?php endif; ?>
