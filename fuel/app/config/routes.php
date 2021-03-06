<?php
return array(
	'_root_'  => 'index/dashboard',  // The default route
	'_404_'   => 'index/404',    // The main 404 route

	'(signup|signin|signout|join)' => 'auth/$1',
	'(oauth|activate)/(:any)' => 'auth/$1/$2',

	'(about|tutorial|redirect|ajax)' => 'index/$1',

	'author' => 'search/author',
	'author/(:any)' => 'user/index/$1',
	'user/(:any)' => 'user/index/$1',

	'package' => 'package/list',
	'package/(:num)' => 'package/detail/$1',

	'recents' => 'search/recent',
	'popular' => 'search/popular',

	'settings' => 'index/redirect/settings/account',

	'admin' => 'index/redirect/admin/user',
	'admin/user' => 'admin/user/index',
	'admin/master' => 'index/redirect/admin/master/hspspec',
	'admin/master/(:segment)' => 'admin/master/$1/index',

//	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);