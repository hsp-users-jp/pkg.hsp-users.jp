<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Fieldset extends \Fuel\Core\Fieldset
{
	public static function reset()
	{
		// reset Input class static propties
		parent::$_instance = null;
		parent::$_instances = array();

		// and, reset super variables
		$_GET = array();
		$_POST = array();
	}
}
