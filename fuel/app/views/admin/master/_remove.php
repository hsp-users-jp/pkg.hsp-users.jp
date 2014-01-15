<div class="row">
	<div class="col-md-2">
<?php echo View::forge('admin/master/_sidebar')->render(); ?>
	</div>
	<div class="col-md-10">

<h1><?php echo $title ?><small>マスターテーブルから削除</small></h1>
<hr>

<form class="form-horizontal" role="form" method="post">
<?php echo Form::csrf(); ?>

<p class="text-center">
削除してよろしいですか？
</p>

<p class="text-center">
      <a href="<?php echo Uri::create(Uri::segment_replace('*/*/*')) ?>" class="btn btn-default">キャンセル</a>
      <button type="submit" class="btn btn-primary">削除</button>
</p>

</form>

	</div>
</div>
