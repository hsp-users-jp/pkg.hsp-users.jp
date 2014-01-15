<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "detail" ); ?>'><?php echo Html::anchor('package/detail','Detail');?></li>
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('package/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('package/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "remove" ); ?>'><?php echo Html::anchor('package/remove','Remove');?></li>

</ul>
<p>Remove</p>