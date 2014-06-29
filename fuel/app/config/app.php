<?php

return array(
	'temp_dir' => APPPATH.'tmp/',
	'package_dir' => APPPATH.'../upload/', // あらかじめ属性777として存在している必要がある
	'screenshot_dirname' => 'images', // DOCROOT配下
	'user' => array(
		'default' => array(
			'group' => 3,
		),
		'activate' => array(
			'expired_limit' => 2*7*24*60*60, // 仮登録状態の期限は2週間
			'hash_expired_limit' => 24*60*60, // アクティベーション用のハッシュの期限は1日
		),
	),
);
