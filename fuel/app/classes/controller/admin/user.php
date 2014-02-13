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
		                             \Auth\Model\Auth_User::table().'.id as user_id_, '.
		                             Model_Package::table().'.id as package_id'))
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
			$is_banned = Auth::is_banned($user['user_id_']);
			$tmp = array(
					'id'                => $user['user_id_'],
					'username'          => $user['username'],
					'fullname'          => Arr::get($fields, 'fullname', ''),
					'email'             => $user['email'],
					'created_at'        => $user['created_at'],
					'loggedin_at'       => $user['last_login'],
					'count_of_packages' => is_null($user['package_id']) ? 0 : $user['count_of_packages'],
					'activate_waiting'  => '' != Arr::get($fields, 'activate_hash', ''),
					'provider'          => array(),
					'mine'              => $cur_login_user_id == $user['user_id_'],
					'super_admin'       => Auth::is_super_admin($user['user_id_']),
					'deleted'           => $is_banned && Arr::get($fields, 'deleted', false),
					'banned'            => $is_banned,
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

	private function ban_or_lift_or_delete($userid, $view, $work)
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
					DB::start_transaction();

					if (call_user_func($work, $user))
					{
						DB::commit_transaction();
					}
					else
					{
						Messages::error('ユーザー状態の更新に失敗しました');

						// 未決のトランザクションクエリをロールバックする
						DB::rollback_transaction();
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage());

					// 未決のトランザクションクエリをロールバックする
					DB::rollback_transaction();

throw $e;
				}
			}

			Response::redirect('admin/user');
		}

		$data['id'] = $user->id;
		$data['username'] = $user->username;
		$data['fullname'] = Auth::get_profile_fields_by_id($user->id, 'fullname');

		if (Input::is_ajax())
		{
			return View::forge('admin/user/confirm.ajax', array('view' => $view, 'data' => $data));
		}

		$this->template->title = '';
		$this->template->content = View::forge('admin/user/confirm', array('view' => $view, 'data' => $data));
	}

	public function action_ban($userid)
	{
		return $this->ban_or_lift_or_delete(
			$userid,
			'admin/user/ban',
			function($user){
				$banned_group = Auth::get_group_by_name('Banned');
				$cur_group    = Auth::get_group_by_id($user->group_id);

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
					return true;
				}
				else
				{
					return false;
				}
			});
	}

	public function action_lift($userid)
	{
		return $this->ban_or_lift_or_delete(
			$userid,
			'admin/user/lift',
			function($user){

				$fields = Auth::get_profile_fields_by_id($user->id);
				$group = Auth::get_group_by_name(Arr::get($fields, 'old_group', 'Users'));

				if (Arr::get($fields, 'deleted', false))
				{
					// パッケージを復元
					$packages = Model_Package::find_deleted('all',
									array(
										'where' => array('user_id' => $user->id),
									//	'related' => array('common', 'version')
										));
					foreach ($packages as $package)
					{
						$package->restore();
					}
				}

				// ユーザーを復元
				if (Auth::update_user(
						array('group_id' => $group->id),
						$user->username))
				{
					Messages::success(
						sprintf('%s(%s) の Ban を解除されました',
							$user->username,
							Arr::get($fields, 'fullname', '不明')));
					return true;
				}
				else
				{
					return false;
				}
			});
	}

	public function action_delete($userid)
	{
		return $this->ban_or_lift_or_delete(
			$userid,
			'admin/user/delete',
			function($user){

				$banned_group = Auth::get_group_by_name('Banned');
				$cur_group = Auth::get_group_by_id($user->group_id);

				// パッケージを削除
				$packages = Model_Package::find('all',
								array(
									'where' => array('user_id' => $user->id),
									));
				foreach ($packages as $package)
				{
Log::debug('delete: '.print_r($package,true));
					$package->delete();
				}

				// ユーザーを削除
				if (Auth::update_user(
						array(
								'group_id' => $banned_group->id,
								'old_group' => $cur_group->name,
								'deleted' => true, // Ban と区別するためにフラグを付ける
							),
						$user->username))
				{
					Messages::success(
						sprintf('%s(%s) は削除されました',
							$user->username,
							Auth::get_profile_fields_by_id($user->id,'fullname', '不明')));
					return true;
				}
				else
				{
					return false;
				}
			});
	}

}
