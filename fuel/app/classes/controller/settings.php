<?php

class Controller_Settings extends Controller_Template
{

	public function action_account()
	{
		$data["subnav"] = array('account'=> 'active' );
		$this->template->title = 'Settings &raquo; Account';
		$this->template->content = View::forge('settings/account', $data);
	}

	public function action_notifications()
	{
		$data["subnav"] = array('notifications'=> 'active' );
		$this->template->title = 'Settings &raquo; Notifications';
		$this->template->content = View::forge('settings/notifications', $data);
	}

	public function action_security()
	{
		$data["subnav"] = array('security'=> 'active' );
		$this->template->title = 'Settings &raquo; Security';
		$this->template->content = View::forge('settings/security', $data);
	}

	public function action_packages()
	{
		$data["subnav"] = array('packages'=> 'active' );
		$this->template->title = 'Settings &raquo; Packages';
		$this->template->content = View::forge('settings/packages', $data);
	}

}
