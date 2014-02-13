<?php

class Controller_User extends Controller_Base
{

	public function action_index($username)
	{
		$user
			= \Auth\Model\Auth_User::query()
				->where('username', $username);
		if (!Auth::is_super_admin())
		{
			$user = $user
				->where('group_id', '!=', Auth::get_group_by_name('Banned')->id);
		}
		$user = $user->get_one();

		if (!$user)
		{
			throw new HttpNotFoundException;
		}
		$data['user'] = $user;

		$query
			= Model_Package::query()
				->where('user_id', $user->id)
				->related('user');
		$data['rows'] = $query->get();

		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'User &raquo; New';
		$this->template->content = View::forge('user/index', $data);
	}
}
