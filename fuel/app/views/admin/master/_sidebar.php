<div class="list-group">
	<?php echo Html::anchor('admin/master/hspspec',    'HSP バージョン',array('class'=>'list-group-item'.(Uri::segment(3)!='hspspec'     ?'':' active'))); ?>
	<?php echo Html::anchor('admin/master/hspcategory','HSP カテゴリ',  array('class'=>'list-group-item'.(Uri::segment(3)!='hspcategory'?'':' active'))); ?>
	<?php echo Html::anchor('admin/master/license',    'ライセンス',    array('class'=>'list-group-item'.(Uri::segment(3)!='license'     ?'':' active'))); ?>
	<?php echo Html::anchor('admin/master/packagetype','パッケージ種別', array('class'=>'list-group-item'.(Uri::segment(3)!='packagetype' ?'':' active'))); ?>
</div>
