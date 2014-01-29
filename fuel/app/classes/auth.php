<?php

class Auth extends \Auth\Auth
{
	// 指定のユーザーIDがログイン中のユーザーと一致するか？
	static public function is_login_user($userid)
	{
		if (false !== ($user_info = Auth::get_user_id()))
		{
			return $user_info[1] == $userid;
		}
		return false;
	}

	static public function get_profile_fields_by_id($userid, $field = null, $default = null)
	{
		$results = array();

		$metadatas
			= \Auth\Model\Auth_Metadata::query()
				->where('parent_id', $userid)
				->get();
		foreach ($metadatas as $metadatas)
		{
			$results[$metadatas->key] = $metadatas->value;
		}

		return \Arr::get($results, $field, $default);
	}
}