<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Response extends \Fuel\Core\Response
{
	static private $backurl_sesstion_key = 'backurl';

	// 戻りURLを指定
	static public function set_backurl($url)
	{
		Session::set(self::$backurl_sesstion_key, $url);
	}

	// 戻りURLを指定
	static public function reset_backurl()
	{
		Session::delete(self::$backurl_sesstion_key);
	}

	// 戻りURLが指定されていればそこに戻る
	static public function redirect_backurl($url = '', $method = 'location', $redirect_code = 302)
	{
		$url = Session::get(self::$backurl_sesstion_key, $url);
		self::reset_backurl();
		// URL検証
		$url = trim(parse_url($url, PHP_URL_PATH), '/');
		// 転送
		return parent::redirect($url, $method, $redirect_code);
	}
}