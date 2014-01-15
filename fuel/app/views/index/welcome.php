<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "index" ); ?>'><?php echo Html::anchor('index/index','Index');?></li>
	<li class='<?php echo Arr::get($subnav, "welcome" ); ?>'><?php echo Html::anchor('index/welcome','Welcome');?></li>
	<li class='<?php echo Arr::get($subnav, "dashboard" ); ?>'><?php echo Html::anchor('index/dashboard','Dashboard');?></li>

</ul>
<p>Welcome</p>