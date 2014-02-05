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

	static private function is_xxx($name, $userid = null)
	{
		$group = self::get_group_by_name($name);
		if (!$group)
		{
			return false;
		}

		if (!is_null($userid))
		{
			$user
				= \Auth\Model\Auth_User::query()
					->where('id', $userid)
					->get_one();
			if (!$user)
			{
				return false;
			}

			return $group->id == $user->group_id;
		}

		return parent::member($group);
	}

	// 現在のもしくは指定したユーザーが Super admin か？
	static public function is_super_admin($userid = null)
	{
		return self::is_xxx('Super Admins', $userid);
	}

	// 現在のもしくは指定したユーザーが admin か？
	static public function is_admin($userid = null)
	{
		return self::is_xxx('Administrators', $userid);
	}

	// 現在のもしくは指定したユーザーが Ban 状態か？
	static public function is_bannd($userid = null)
	{
		return self::is_xxx('Banned', $userid);
	}
}