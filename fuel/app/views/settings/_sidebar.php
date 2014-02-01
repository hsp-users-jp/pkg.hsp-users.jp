<div class="list-group">
	<?php echo Html::anchor('settings/account',      'アカウント',   array('class'=>'list-group-item'.(Uri::segment(2)!='account'      ?'':' active'))); ?>
	<?php echo Html::anchor('settings/profile',      'プロフィール',  array('class'=>'list-group-item'.(Uri::segment(2)!='profile'      ?'':' active'))); ?>
	<?php echo Html::anchor('settings/password',     'パスワード',   array('class'=>'list-group-item'.(Uri::segment(2)!='password'     ?'':' active'))); ?>
	<?php echo Html::anchor('settings/notifications','通知設定',     array('class'=>'list-group-item'.(Uri::segment(2)!='notifications'?'':' active'))); ?>
	<?php echo Html::anchor('settings/security',     'セキュリティ',  array('class'=>'list-group-item'.(Uri::segment(2)!='security'     ?'':' active'))); ?>
	<?php echo Html::anchor('settings/packages',     'パッケージ一覧',array('class'=>'list-group-item'.(Uri::segment(2)!='packages'     ?'':' active'))); ?>
</div>
