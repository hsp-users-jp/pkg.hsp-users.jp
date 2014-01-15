<?php

class Controller_Package extends Controller_Template
{

	public function action_detail()
	{
		$data["subnav"] = array('detail'=> 'active' );
		$this->template->title = 'Package &raquo; Detail';
		$this->template->content = View::forge('package/detail', $data);
	}

	public function action_new()
	{
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'Package &raquo; New';
		$this->template->content = View::forge('package/new', $data);
	}

	public function action_edit()
	{
		$data["subnav"] = array('edit'=> 'active' );
		$this->template->title = 'Package &raquo; Edit';
		$this->template->content = View::forge('package/edit', $data);
	}

	public function action_remove()
	{
		$data["subnav"] = array('remove'=> 'active' );
		$this->template->title = 'Package &raquo; Remove';
		$this->template->content = View::forge('package/remove', $data);
	}

}
