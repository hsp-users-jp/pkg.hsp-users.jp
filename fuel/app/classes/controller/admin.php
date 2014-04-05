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
		$data["subnav"] = array('dashboard'=> 'active' );
		$this->template->title = 'Admin &raquo; Dashboard';
		$this->template->content = View::forge('admin/dashboard', $data);
	}

}
