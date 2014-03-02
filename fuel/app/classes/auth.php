<?php

class Auth extends \Auth\Auth
{
	// 指定のユーザーIDのみを取得
	static public function get_user_id_only()
	{
		if (false !== ($user_info = parent::get_user_id()))
		{
			return $user_info[1];
		}
		return false;
	}

	// 指定のユーザーIDがログイン中のユーザーと一致するか？
	static public function is_login_user($userid)
	{
		if (false !== ($user_info = parent::get_user_id()))
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

	// グループを名称から取得
	static public function get_group_by_name($name)
	{
		$group
			= \Auth\Model\Auth_Group::query()
				->where('name', $name)
				->get_one();
		return $group;
	}

	// グループ名称を取得
	static public function get_group_by_id($id)
	{
		$group
			= \Auth\Model\Auth_Group::query()
				->where('id', $id)
				->get_one();
		return $group;
	}

	// 関連付けられているoAuthプロバイダを取得
	static public function get_related_providers($userid_or_object = null)
	{
		if (is_null($userid_or_object))
		{
			$userid_or_object = self::get_user_id_only();
		}

		if ($userid_or_object instanceof \Auth\Model\Auth_User)
		{
			$user = $userid_or_object;
		}
		else
		{
			$user
				= \Auth\Model\Auth_User::query()
					->where('id', $userid_or_object)
					->get_one();
		}

		if (!$user)
		{
			return false;
		}

		$result = array();
		foreach (DB::select('id', 'parent_id', 'provider', 'uid')
					->from(Config::get('ormauth.table_name', 'users').'_providers')
					->where('parent_id', $user->id)
					->execute() as $provider)
		{
			$result[strtolower($provider['provider'])] = $provider['uid'];
		}

		return $result;
	}

	static private function is_xxx($name, $userid_or_object = null)
	{
		$group = self::get_group_by_name($name);
		if (!$group)
		{
			return false;
		}

		if (is_null($userid_or_object))
		{
			$userid_or_object = self::get_user_id_only();
		}

		if ($userid_or_object instanceof \Auth\Model\Auth_User)
		{
			$user = $userid_or_object;
		}
		else
		{
			$user
				= \Auth\Model\Auth_User::query()
					->where('id', $userid_or_object)
					->get_one();
		}

		if (!$user)
		{
			return false;
		}

		return $group->id == $user->group_id;
	}

	// 現在のもしくは指定したユーザーが Super admin か？
	static public function is_super_admin($userid_or_object = null)
	{
		return self::is_xxx('Super Admins', $userid_or_object);
	}

	// 現在のもしくは指定したユーザーが admin か？
	static public function is_admin($userid_or_object = null)
	{
		return self::is_xxx('Administrators', $userid_or_object);
	}

	// 現在のもしくは指定したユーザーが Ban 状態か？
	static public function is_banned($userid_or_object = null)
	{
		return self::is_xxx('Banned', $userid_or_object);
	}
}