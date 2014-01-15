<ul class="nav nav-pills nav-stacked">
	<li class='<?php echo Uri::segment(3)!="hspspec"    ?:"active" ?>'><?php echo Html::anchor('admin/master/hspspec','HSP バージョン');?></li>
	<li class='<?php echo Uri::segment(3)!="hspcategory"?:"active" ?>'><?php echo Html::anchor('admin/master/hspcategory','HSP カテゴリ');?></li>
	<li class='<?php echo Uri::segment(3)!="license"    ?:"active" ?>'><?php echo Html::anchor('admin/master/license','ライセンス');?></li>
	<li class='<?php echo Uri::segment(3)!="packagetype"?:"active" ?>'><?php echo Html::anchor('admin/master/packagetype','パッケージ種別');?></li>
</ul>
