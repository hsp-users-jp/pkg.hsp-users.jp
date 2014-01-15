<?php
return array(
	'_root_'  => 'index/dashboard',  // The default route
	'_404_'   => 'index/404',    // The main 404 route

	'(signup|signin|signout|join)' => 'auth/$1',

	'about' => 'index/about',
	'redirect' => 'index/redirect',

	'package' => 'package/list',
	'package/(:num)' => 'package/detail/$1',

	'recents' => 'search/recent',
	'popular' => 'search/popular',

	'admin/master' => 'index/redirect/admin/master/hspspec',
	'admin/master/(:segment)' => 'admin/master/$1/index',

//	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);