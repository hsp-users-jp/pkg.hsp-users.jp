<?php
/**
 * Observer_UpdatedAt with before_insert notify for FuelPHP 1.x
 *
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Observer_UpdatedAt extends \Orm\Observer_UpdatedAt
{
	/**
	 * Set the UserId property to the current user id.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_insert(Orm\Model $obj)
	{
		$this->before_update($obj);
	}
}
