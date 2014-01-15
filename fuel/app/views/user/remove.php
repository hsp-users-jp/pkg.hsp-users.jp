<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "new" ); ?>'><?php echo Html::anchor('user/new','New');?></li>
	<li class='<?php echo Arr::get($subnav, "edit" ); ?>'><?php echo Html::anchor('user/edit','Edit');?></li>
	<li class='<?php echo Arr::get($subnav, "remove" ); ?>'><?php echo Html::anchor('user/remove','Remove');?></li>
	<li class='<?php echo Arr::get($subnav, "config" ); ?>'><?php echo Html::anchor('user/config','Config');?></li>

</ul>
<p>Remove</p>