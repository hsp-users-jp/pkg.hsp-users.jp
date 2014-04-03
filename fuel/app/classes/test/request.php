<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Request extends \Fuel\Core\Request
{
	public static function reset()
	{
		// reset Input class static propties
		parent::$main = false;
		parent::$active = false;

		// and, reset super variables
		$_GET = array();
		$_POST = array();
	}
}
