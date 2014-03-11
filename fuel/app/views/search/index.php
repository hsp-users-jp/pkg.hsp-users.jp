<h1>検索</h1>
<hr>

<form method="get">

<div class="row">
	<div class="col-md-6 col-md-offset-3">

		<div class="input-group">
			<?php echo Form::input('q', Input::get('q'), array('class' => 'form-control')); ?>
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
			</span>
		</div>

	</div>
</div>

</form>

<hr>

<?php if (count($rows) <= 0): ?>
<?php  if (Input::get('q')): ?>

<p class="text-center">「<?php echo e(Input::get('q')); ?>」に一致するパッケージは見つかりませんでした。</p>

<?php  endif; ?>
<?php else: ?>

<div class="text-center">
<?php echo $pagination ?>
</div>

<?php foreach ($rows as $row): ?>
<?php echo View::forge('package/item', array('package' => $row))->render(); ?>
<?php endforeach; ?>

<div class="text-center">
<?php echo $pagination ?>
</div>

<?php endif; ?>
