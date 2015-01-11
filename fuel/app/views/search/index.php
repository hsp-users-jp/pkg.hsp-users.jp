<h1>検索 <small
    ></small></h1>
<hr>

<form method="get">

<div class="row">
	<div class="col-md-6 col-md-offset-3">

		<div class="input-group" style="padding-right: 3em">
			<?php echo Form::input('q', Input::get('q'), array('class' => 'form-control')); ?>
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
			</span>
		</div>

		<div style="display: inline-block;
		            position: absolute;
		            right: 15px;
		            top: 0px;
		            width: 3em;
		            padding-right: 1em;
		            padding-left: 1em;
		            line-height: 2em;">
			<a href="#" tabindex="0" class_="label label-default" data-placement="left" data-html="true"
			   data-toggle="popover" data-container="body" data-trigger="focus" role="button"
			   title="検索条件のヒント"
			   data-content="<dl>
			                   <dt>type:〜</dt><dd>パッケージの種別を条件に検索</dd>
			                   <dt>author:〜</dt><dd>作者を条件に検索</dd>
			                 </dl>"
			 ><i class="fa fa-question-circle fa-lg"></i></a>
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
