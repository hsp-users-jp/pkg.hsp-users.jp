<?php

class Controller_Admin extends Controller_Base
{

	public function action_dashboard()
	{
		$data["subnav"] = array('dashboard'=> 'active' );
		$this->template->title = 'Admin &raquo; Dashboard';
		$this->template->content = View::forge('admin/dashboard', $data);
	}

	public function action_user()
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
		$this->template->content = View::forge('admin/user', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	public function action_package()
	{
		$data["subnav"] = array('package'=> 'active' );
		$this->template->title = 'Admin &raquo; Package';
		$this->template->content = View::forge('admin/package', $data);
	}

}
