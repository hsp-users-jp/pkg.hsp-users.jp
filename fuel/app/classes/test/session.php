<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Session extends \Fuel\Core\Session
{
	public static function reset()
	{
		// reset parent class static propties
		parent::$_instance = null;
		parent::$_instances = array();
		parent::$_defaults = array(
			'driver'                    => 'cookie',
			'match_ip'                  => false,
			'match_ua'                  => true,
			'cookie_domain'             => '',
			'cookie_path'               => '/',
			'cookie_http_only'          => null,
			'encrypt_cookie'            => true,
			'expire_on_close'           => false,
			'expiration_time'           => 7200,
			'rotation_time'             => 300,
			'flash_id'                  => 'flash',
			'flash_auto_expire'         => true,
			'flash_expire_after_get'    => true,
			'post_cookie_name'          => ''
		);

		// and, call static init methos
		parent::_init();
	}
}
