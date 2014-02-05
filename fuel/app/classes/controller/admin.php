<?php

class Controller_Admin extends Controller_Base
{

	public function action_dashboard()
	{
		$data["subnav"] = array('dashboard'=> 'active' );
		$this->template->title = 'Admin &raquo; Dashboard';
		$this->template->content = View::forge('admin/dashboard', $data);
	}

	public function action_package()
	{
		$data["subnav"] = array('package'=> 'active' );
		$this->template->title = 'Admin &raquo; Package';
		$this->template->content = View::forge('admin/package', $data);
	}
}
