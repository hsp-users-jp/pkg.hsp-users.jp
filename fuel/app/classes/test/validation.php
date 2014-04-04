<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Validation extends \Fuel\Core\Validation
{
	public static function reset()
	{
		// reset parent class static propties
		parent::$active = null;
		parent::$active_field = null;

		// and, reset Fieldset
		Fieldset::reset();
	}
}
