<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "dashboard" ); ?>'><?php echo Html::anchor('admin/dashboard','Dashboard');?></li>
	<li class='<?php echo Arr::get($subnav, "master" ); ?>'><?php echo Html::anchor('admin/master','Master');?></li>
	<li class='<?php echo Arr::get($subnav, "user" ); ?>'><?php echo Html::anchor('admin/user','User');?></li>
	<li class='<?php echo Arr::get($subnav, "package" ); ?>'><?php echo Html::anchor('admin/package','Package');?></li>

</ul>
<p>User</p>