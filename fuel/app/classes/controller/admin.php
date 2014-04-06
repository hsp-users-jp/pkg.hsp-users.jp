<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Admin extends Controller_Base
{

	public function action_dashboard()
	{
		$data['published_package_count'] = Model_Package::count_of_published();
		$data['removed_package_count'] = Model_Package::count_of_removed();

		$data['registerd_user_count'] = Model_User::count_of_registerd();
		$data['author_count'] = Model_User::count_of_author();
		$data['banned_user_count'] = Model_User::count_of_banned();
		$data['inactivate_user_count'] = Model_User::count_of_inactivate();

		$this->template->title = 'ダッシュボード';
		$this->template->breadcrumb = array( '/' => 'トップ', 'admin' => '管理', '' => $this->template->title );
		$this->template->content = View::forge('admin/dashboard', $data);
	}

}
