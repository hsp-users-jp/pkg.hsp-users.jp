<?php

class Controller_User extends Controller_Base
{

	public function action_new()
	{
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'User &raquo; New';
		$this->template->content = View::forge('user/new', $data);
	}

	public function action_edit()
	{
		$data["subnav"] = array('edit'=> 'active' );
		$this->template->title = 'User &raquo; Edit';
		$this->template->content = View::forge('user/edit', $data);
	}

	public function action_remove()
	{
		$data["subnav"] = array('remove'=> 'active' );
		$this->template->title = 'User &raquo; Remove';
		$this->template->content = View::forge('user/remove', $data);
	}

	public function action_config()
	{
		$data["subnav"] = array('config'=> 'active' );
		$this->template->title = 'User &raquo; Config';
		$this->template->content = View::forge('user/config', $data);
	}

}
