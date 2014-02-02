<?php

class Auth extends \Auth\Auth
{
	// 指定のユーザーIDのみを取得
	static public function get_user_id_only()
	{
		if (false !== ($user_info = Auth::get_user_id()))
		{
			return $user_info[1];
		}
		return false;
	}

	// 指定のユーザーIDがログイン中のユーザーと一致するか？
	static public function is_login_user($userid)
	{
		if (false !== ($user_info = Auth::get_user_id()))
		{
			return $user_info[1] == $userid;
		}
		return false;
	}

	// 指定したユーザーIDに関連づけられているプロファイルを取得
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

	// 指定したプロファイルフィールドに関連づけられているユーザーを取得
	static public function get_id_by_profile_field($field, $value)
	{
		$results = array();

		$metadatas
			= \Auth\Model\Auth_Metadata::query()
				->where('key', $field)
				->where('value', $value)
				->get();
		foreach ($metadatas as $metadatas)
		{
			$results[] = $metadatas->parent_id;
		}

		return $results;
	}
}