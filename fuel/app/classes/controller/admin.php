<?php

class Controller_Admin extends Controller_Base
{

	public function action_dashboard()
	{
		$data["subnav"] = array('dashboard'=> 'active' );
		$this->template->title = 'Admin &raquo; Dashboard';
		$this->template->content = View::forge('admin/dashboard', $data);
	}

	public function action_master()
	{
		$data["subnav"] = array('master'=> 'active' );
		$this->template->title = 'Admin &raquo; Master';
		$this->template->content = View::forge('admin/master', $data);
	}

	public function action_user()
	{
		$data["subnav"] = array('user'=> 'active' );
		$this->template->title = 'Admin &raquo; User';
		$this->template->content = View::forge('admin/user', $data);
	}

	public function action_package()
	{
		$data["subnav"] = array('package'=> 'active' );
		$this->template->title = 'Admin &raquo; Package';
		$this->template->content = View::forge('admin/package', $data);
	}

}
