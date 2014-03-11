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
	public static function forge($name = 'default', array　$config = array())
	{//Fatal error: Class declarations may not be nested in /var/www/html/pkg.hsp-users.jp/fuel/core/classes/session.php on line 24 になる
		return parent::forge($name, $config);
	}

	public function add($name, $label = '', array $attributes = array(), array $rules = array())
	{
		$class = explode(' ', \Arr::get($this->config, 'form_attributes.class', ''));
		if (in_array('form-horizontal', $class))
		{
			$attributes['class'] = explode(' ', \Arr::get($attributes, 'class', ''));
			if (!in_array('control-form', $attributes['class']))
			{
				$attributes['class'][] = 'control-form';
			}
			$attributes['class'] = implode(' ', $attributes['class']);
		}
		return parent::add($name, $label, $attributes, $rules);
	}
}