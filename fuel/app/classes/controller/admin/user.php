<?php

class Controller_Admin_User extends Controller_Base
{
	public function action_index()
	{
		$pagination = Pagination::forge('page', array(
				'total_items' => \Auth\Model\Auth_User::count(),
				'uri_segment' => 'page',
			));

		$provider_table = Config::get('ormauth.table_name', 'users').'_providers';

		$cur_login_user_id = Auth::get_user_id_only();
		$data['users'] = array();
		foreach (DB::select(DB::expr('*, COUNT(*) as count_of_packages, '.
		                             \Auth\Model\Auth_User::table().'.id as user_id_'))
				->from(\Auth\Model\Auth_User::table())
				->join(Model_Package::table(), 'left')
				->on(\Auth\Model\Auth_User::table().'.id', '=', Model_Package::table().'.user_id')
			//	->where(Model_Package::table().'.user_id', '!=', null) // IS NOT NULL
			//	->order_by(\Auth\Model\Auth_User::table().'.id')
				->group_by(\Auth\Model\Auth_User::table().'.id')
				->offset($pagination->offset)
				->limit($pagination->per_page)
			//	->as_object('\\Auth\\Model\\Auth_User')
				->execute() as $user)
		{
			$fields = Auth::get_profile_fields_by_id($user['user_id_']);
			$tmp = array(
					'id'                => $user['user_id_'],
					'username'          => $user['username'],
					'fullname'          => Arr::get($fields, 'fullname', ''),
					'count_of_packages' => is_null($user['package_common_id']) ? 0 : $user['count_of_packages'],
					'activate_waiting'  => '' != Arr::get($fields, 'activate_hash', ''),
					'provider'          => array(),
					'mine'              => $cur_login_user_id == $user['user_id_'],
					'super_admin'       => Auth::is_super_admin($user['user_id_']),
					'banned'            => Auth::is_bannd($user['user_id_']),
				);
			foreach (DB::select('id', 'parent_id', 'provider')
						->from($provider_table)
						->where('parent_id', $user['user_id_'])
						->execute() as $provider)
			{
				$tmp['provider'][strtolower($provider['provider'])] = true;
			}
			$data['users'][] = $tmp;
		}

		$data["subnav"] = array('user'=> 'active' );
		$this->template->title = 'Admin &raquo; User';
		$this->template->content = View::forge('admin/user/index', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	public function action_ban($userid)
	{
		$user
			= \Auth\Model\Auth_User::query()
				->where('id', $userid)
				->get_one();
		if (!$user)
		{
			throw new HttpNotFoundException;
		}

		$val = Validation::forge('val');
		$val->add('id', '')
			->add_rule('required')
			->add_rule('match_value', $userid);

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					$banned_group = Auth::get_group_by_name('Banned');
					$cur_group = Auth::get_group_by_id($user->group_id);

					if (Auth::update_user(
							array(
									'group_id' => $banned_group->id,
									'old_group' => $cur_group->name,
								),
							$user->username))
					{
						Messages::success(
							sprintf('%s(%s) は Ban されました',
								$user->username,
								Auth::get_profile_fields_by_id($user->id,'fullname', '不明')));
					}
					else
					{
						Messages::error('ユーザー状態の更新に失敗しました');
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage());
				}
			}
			else
			{
				Messages::error('エラー発生');
			}

			Response::redirect('admin/user');
		}

		$data['id'] = $user->id;
		$data['username'] = $user->username;
		$data['fullname'] = Auth::get_profile_fields_by_id($user->id, 'fullname');

		if (Input::is_ajax())
		{
			return View::forge('admin/user/ban.ajax', array('data' => $data));
		}

		$this->template->title = '';
		$this->template->content = View::forge('admin/user/ban', array('data' => $data));
	}

	public function action_lift($userid)
	{
		$user
			= \Auth\Model\Auth_User::query()
				->where('id', $userid)
				->get_one();
		if (!$user)
		{
			throw new HttpNotFoundException;
		}

		$val = Validation::forge('val');
		$val->add('id', '')
			->add_rule('required')
			->add_rule('match_value', $userid);

		if (Input::post())
		{
			if ($val->run())
			{
				$group = Auth::get_group_by_name(
							Auth::get_profile_fields_by_id(
								$user->id, 'old_group', 'Users'));

				try
				{
					if (Auth::update_user(
							array('group_id' => $group->id),
							$user->username))
					{
						Messages::success(
							sprintf('%s(%s) の Ban を解除されました',
								$user->username,
								Auth::get_profile_fields_by_id($user->id,'fullname', '不明')));
					}
					else
					{
						Messages::error('ユーザー状態の更新に失敗しました');
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage());
				}
			}
			else
			{
				Messages::error('エラー発生');
			}

			Response::redirect('admin/user');
		}

		$data['id'] = $user->id;
		$data['username'] = $user->username;
		$data['fullname'] = Auth::get_profile_fields_by_id($user->id, 'fullname');

		if (Input::is_ajax())
		{
			return View::forge('admin/user/lift.ajax', array('data' => $data));
		}

		$this->template->title = '';
		$this->template->content = View::forge('admin/user/lift', array('data' => $data));
	}

}
