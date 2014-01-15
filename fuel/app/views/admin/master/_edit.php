<div class="row">
	<div class="col-md-2">
<?php echo View::forge('admin/master/_sidebar')->render(); ?>
	</div>
	<div class="col-md-10">

<h1><?php echo $title ?><small>マスターテーブルを編集</small></h1>
<hr>

<form class="form-horizontal" role="form" method="post">
<?php echo Form::csrf(); ?>

<?php foreach ($cols as $col): ?>
<?php if ('id' == $col): ?>
<?php else: ?>
	<div class="form-group <?php echo Arr::get($state, $col); ?>">
		<label for="inputEmail3" class="col-sm-2 control-label"><?php echo e($col); ?></label>
		<div class="col-sm-10">
			<input type="input" class="form-control" name="<?php echo e($col); ?>"
			       value="<?php echo e(Input::post($col, $row[$col])); ?>" placeholder="<?php echo e($col); ?>">
		</div>
	</div>
<?php endif; ?>
<?php endforeach; ?>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <a href="<?php echo Uri::create(Uri::segment_replace('*/*/*')) ?>" class="btn btn-default">キャンセル</a>
      <button type="submit" class="btn btn-primary">保存</button>
    </div>
  </div>
</form>

	</div>
</div>
