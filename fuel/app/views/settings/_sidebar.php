<div class="list-group">
<?php foreach (array(
			'account' =>       'アカウント',
			'profile' =>       'プロフィール',
			'password' =>      'パスワード',
//			'notifications' => '通知設定',
//			'security' =>      'セキュリティ',
		) as $segment => $name): ?>
	<?php echo Html::anchor('settings/' . $segment, $name,
                            array('class' => 'list-group-item' . (Uri::segment(2) != $segment ? '' : ' active'))); ?>
<?php endforeach; ?>
</div>
