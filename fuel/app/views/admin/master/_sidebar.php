<div class="list-group">
<?php foreach (array(
			'hspspec' =>     'HSP バージョン',
			'hspcategory' => 'HSP カテゴリ',
			'license' =>     'ライセンス',
			'packagetype' => 'パッケージ種別',
		) as $segment => $name): ?>
	<?php echo Html::anchor('admin/master/' . $segment, $name,
                            array('class' => 'list-group-item' . (Uri::segment(3) != $segment ? '' : ' active'))); ?>
<?php endforeach; ?>
</div>
