<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Input extends \Fuel\Core\Input
{
	public static function reset()
	{
		// reset parent class static propties
		parent::$detected_uri = null;
		parent::$detected_ext = null;
		parent::$input = null;
		parent::$put_patch_delete = null;
		parent::$php_input = null;
		parent::$json = null;
		parent::$xml = null;

		// and, reset super variables
		$_GET = array();
		$_POST = array();
	}
}
