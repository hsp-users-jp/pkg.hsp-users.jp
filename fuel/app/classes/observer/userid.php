<?php
/**
 * Observer to specify the id of the currently logged-in user for FuelPHP 1.x
 *
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2013-2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

/*
 * EXAMPLE
 *   protected static $_observers = array(
 *       'Observer_UserId' => array(
 *           'events' => array('before_save')
 *       ),
 *   );
 */

class Observer_UserId extends Orm\Observer
{

	/**
	 * @var  string  default property to set the user id
	 */
	public static $property = 'user_id';

	/**
	 * @var  string  property to set the user id
	 */
	protected $_property;

	/**
	 * Set the properties for this observer instance, based on the parent model's
	 * configuration or the defined defaults.
	 *
	 * @param  string  Model class this observer is called on
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_property = isset($props['property']) ? $props['property'] : static::$property;
	}

	/**
	 * Set the UserId property to the current user id.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_insert(Orm\Model $obj)
	{
		if (false !== ($userid = \Auth::instance()->get_user_id()))
		{
			list(, $obj->{$this->_property}) = $userid;
		}
	}

	/**
	 * Set the UserId property to the current user id.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_save(Orm\Model $obj)
	{
		if (false !== ($userid = \Auth::instance()->get_user_id()))
		{
			list(, $obj->{$this->_property}) = $userid;
		}
	}
}
