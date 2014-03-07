<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Archelon
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2013-2014 sharkpp
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
