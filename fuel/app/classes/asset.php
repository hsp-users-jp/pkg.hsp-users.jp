<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Asset extends \Fuel\Core\Asset
{
	// GravatarのイメージURLを取得
	static public function gravatar($email, Array $attr = array(), Array $options = array())
	{
		// http://ja.gravatar.com/site/implement/hash/
		$hash = md5(strtolower(trim($email)));

		$type = \Arr::get($options, 'type', '');
		$type = $type ? '.' . $type : $type;
		\Arr::delete($options, 'type');

		if (\Arr::get($options, 'size'))
		{
			$attr['width']  = \Arr::get($options, 'size');
			$attr['height'] = \Arr::get($options, 'size');
		}

		$query= http_build_query($options);
		$query= $query ? '?' . $query : $query;
		$url  = strtolower(Input::protocol()) . '://www.gravatar.com/avatar/' . $hash . $type . $query;
		return Html::img($url, $attr);
	}
}