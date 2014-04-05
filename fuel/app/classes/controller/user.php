<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

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
		$data['providers'] = Auth::get_related_providers($user->id);
			
		$query
			= Model_Package::query()
				->where('user_id', $user->id)
				->related('user');
		$data['rows'] = $query->get();

		$this->template->title = $data['user']->username;
		$this->template->breadcrumb = array( '/' => 'トップ', 'author' => '作者一覧', '' => $this->template->title );
		$this->template->content = View::forge('user/index', $data);
	}
}
