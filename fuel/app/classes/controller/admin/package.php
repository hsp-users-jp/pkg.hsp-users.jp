<?php

class Controller_Admin_Package extends Controller_Base
{
	public function action_index()
	{
		$pagination = Pagination::forge('page', array(
				'total_items' => Model_Package::count(),
				'uri_segment' => 'page',
			));

		$data['packages'] = array();
//		foreach (DB::select(DB::expr('*, COUNT(*) as count_of_packages, '.
//		                             \Auth\Model\Auth_User::table().'.id as user_id_, '.
//		                             Model_Package::table().'.id as package_id'))
//				->from(\Auth\Model\Auth_User::table())
//				->join(Model_Package::table(), 'left')
//				->on(\Auth\Model\Auth_User::table().'.id', '=', Model_Package::table().'.user_id')
//				->group_by(\Auth\Model\Auth_User::table().'.id')
//				->offset($pagination->offset)
//				->limit($pagination->per_page)
//			//	->as_object('\\Auth\\Model\\Auth_User')
//				->execute() as $user)
		foreach (Model_Package::query()
				->related(array('type', 'user'))
				->offset($pagination->offset)
				->limit($pagination->per_page)
			//	->as_object('\\Auth\\Model\\Auth_User')
				->get() as $package)
		{
			$tmp = array(
					'id'         => $package->id,
					'name'       => $package->name,
					'type'       => $package->type,
					'version'    => $package->version,
					'updated_at' => $package->updated_at,
					'user'       => $package->user,
					'deleted'    => !is_null($package->deleted_at),
				);
			$data['packages'][] = $tmp;
		}

		$data['packages'] = Prop::forge($data['packages']);

		$data["subnav"] = array('user'=> 'active' );
		$this->template->title = 'Admin &raquo; Package';
		$this->template->content = View::forge('admin/package/index', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	private function delete_or_undelete($package_id, $view, $work)
	{
		$user
			= Model_Package::query()
				->where('id', $package_id)
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

			Response::redirect('admin/package');
		}

		$data['id'] = $package->id;
		$data['name'] = $package->name;

		if (Input::is_ajax())
		{
			return View::forge('admin/package/confirm.ajax', array('view' => $view, 'data' => $data));
		}

		$this->template->title = '';
		$this->template->content = View::forge('admin/package/confirm', array('view' => $view, 'data' => $data));
	}

	public function action_delete($package_id)
	{
		return $this->delete_or_undelete(
			$userid,
			'admin/package/delete',
			function($package){
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

	public function action_undelete($package_id)
	{
		return $this->delete_or_undelete(
			$userid,
			'admin/package/undelete',
			function($package){

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
}
