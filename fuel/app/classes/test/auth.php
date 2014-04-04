<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Test_Auth extends \Auth\Auth
{
	public static function reset()
	{
		// reset parent class static propties
		parent::$_instance = null;
		parent::$_instances = array();
		parent::$_verified = array();
		parent::$_verify_multiple = false;
		parent::$_drivers = array(
			'group'  => 'member',
			'acl'    => 'has_access',
		);

		// and, call static init methos
		parent::_init();
	}
}
