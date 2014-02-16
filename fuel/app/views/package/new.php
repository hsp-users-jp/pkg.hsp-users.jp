<h1>パッケージの追加</h1>
<hr>

<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
	<?php echo Form::csrf(); ?>

	<div class="form-group <?php echo Arr::get($state,'title') ?>">
		<label for="form_file" class="col-sm-2 control-label">パッケージ</label>
		<div class="col-sm-10">
			<p>アップロード時の制限：
				<ul class="fa-ul">
					<li><span class="fa-li fa fa-exclamation-circle"></span><?php
					    echo e(Num::format_bytes(
					           min(Num::bytes(ini_get('upload_max_filesize')),
					               Num::bytes(ini_get('post_max_size')))
					    )); ?> までアップロードすることが出来ます。</li>
				</ul>
			</p>
			<div id="form_package">
				<div class="fallback">
					<input name="file" type="file" multiple />
				</div>
			</div>
		</div>
	</div>

	<div class="form-group <?php echo Arr::get($state,'title') ?>">
		<label for="form_title" class="col-sm-2 control-label">名称</label>
		<div class="col-sm-10">
			<?php echo Form::input('title', Input::post('title'),
			                       array('class' => 'form-control', 'placeholder' => 'パッケージの名称を入力してください')); ?>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'description') ?>">
		<label for="form_description" class="col-sm-2 control-label">説明</label>
		<div class="col-sm-10">
			<?php echo Form::textarea('description', Input::post('description'),
			                          array('class' => 'form-control', 'placeholder' => 'パッケージに関しての説明を入力してください。')); ?>
※ HTMLタグは使用できません。
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'url') ?>">
		<label for="form_url" class="col-sm-2 control-label">外部リンク</label>
		<div class="col-sm-10">
			<?php echo Form::input('url', Input::post('url'),
			                       array('class' => 'form-control',
			                       'placeholder' => 'パッケージに関連のある外部リンクを指定してください')); ?>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'version') ?>">
		<label for="form_version" class="col-sm-2 control-label">バージョン</label>
		<div class="col-sm-10">
			<?php echo Form::input('version', Input::post('version'),
			                       array('class' => 'form-control', 'placeholder' => 'パッケージのバージョンを入力してください')); ?>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'package_type') ?>">
		<label for="form_package_type" class="col-sm-2 control-label">パッケージ種別</label>
		<div class="col-sm-10">
			<?php foreach ($package_type_list as $package_type_id => $package_type_name): ?>
				<label class="radio-inline">
				<?php echo Form::radio('package_type', $package_type_id, Input::post('package_type')) . ' ' .
				                       e($package_type_name);
?>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="form-group <?php echo Arr::get($state,'license') ?>">
		<label for="form_license" class="col-sm-2 control-label">ライセンス</label>
		<div class="col-sm-10">
			<?php echo Form::select('license', Input::post('license'),
			                        $license_list,
			                        array('class' => 'form-control')); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">動作環境</label>
		<div class="col-sm-10">

<table class="table table-striped table-condensed table-bordered" style="width: auto;">
	<tr>
<?php foreach ($hsp_category as $hsp_category_id => $hsp_category_name): ?>
		<td><?php echo e($hsp_category_name); ?></td>
<?php endforeach; ?>
	</tr>
<?php for ($i = 0; $i < $hsp_spec_max_row; ++$i): ?>
	<tr>
<?php foreach ($hsp_category as $hsp_category_id => $hsp_category_name): ?>
<?php if ($i < count($hsp_spec[$hsp_category_id])): ?>
		<td><?php $cell = array_slice($hsp_spec[$hsp_category_id], $i, 1, true);
			      list($hsp_spec_name) = array_values($cell);
			      list($hsp_spec_id)   = array_keys($cell);
			      $id = sprintf('hsp_spec[%d]', $hsp_spec_id);
			      $id_= sprintf('hsp_spec.%d',  $hsp_spec_id);
			      echo Form::checkbox($id, $hsp_spec_id, Input::post($id_)) . ' ' .
			           Form::label($hsp_spec_name, $id);
			      ?></td>
<?php else: ?>
		<td></td>
<?php endif; ?>
<?php endforeach; ?>
	</tr>
<?php endfor; ?>

</table>
		</div>
	</div>
	<div class="form-group">
		<label for="inputEmail3" class="col-sm-2 control-label">スクリーンショット</label>
		<div class="col-sm-10">
			<?php echo Form::file('ss',
			                            array('multiple' => 'multiple')); ?>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<a href="<?php echo Uri::create(Uri::segment_replace('*')) ?>" class="btn btn-default">キャンセル</a>
			<button type="submit" class="btn btn-primary">追加</button>
		</div>
	</div>
</form>

<div class="modal fade" id="package-validating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin-top: 25%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">アップロードファイルの検証中</h4>
			</div>
			<div class="modal-body">
				<div class="progress progress-striped active">
					<div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
						<span class="sr-only">100% Complete</span>
					</div>
				</div>
				<p class="text-center">しばらくそのままでお待ちください。</p>
			</div>
		</div>
	</div>
</div>
