<div class="list-group">
<?php foreach (array(
			'dashboard' =>    'ダッシュボード',
			'user' =>         'ユーザー管理',
			'package' =>      'パッケージ管理',
			'master' =>       'マスターテーブル編集',
		) as $segment => $name): ?>
	<?php echo Html::anchor('admin/' . $segment, $name,
                            array('class' => 'list-group-item' . (Uri::segment(2) != $segment ? '' : ' active'))); ?>
<?php endforeach; ?>
</div>
