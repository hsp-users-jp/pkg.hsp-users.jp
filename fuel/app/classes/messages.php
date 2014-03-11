<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Messages
{
	static function debug($msg)
	{
		$msg = !is_array($msg) ? $msg : implode("\n", $msg);
		\Log::debug(str_replace("\n", "\\n", $msg));
	}

	static function success($msg)
	{
		\Log::info(!is_array($msg) ? $msg : implode("\\n", $msg));
		\Session::set_flash('success', $msg);
	}

	static function error($msg, $msg_for_display = null)
	{
		$msg_for_display = $msg_for_display ?: $msg;
		\Log::error(!is_array($msg) ? $msg : implode("\\n", $msg));
		\Session::set_flash('error', $msg_for_display);
	}
}