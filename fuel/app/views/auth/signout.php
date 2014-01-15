<ul class="nav nav-pills">
	<li class='<?php echo Arr::get($subnav, "signup" ); ?>'><?php echo Html::anchor('auth/signup','Signup');?></li>
	<li class='<?php echo Arr::get($subnav, "signin" ); ?>'><?php echo Html::anchor('auth/signin','Signin');?></li>
	<li class='<?php echo Arr::get($subnav, "signout" ); ?>'><?php echo Html::anchor('auth/signout','Signout');?></li>

</ul>
<p>Signout</p>