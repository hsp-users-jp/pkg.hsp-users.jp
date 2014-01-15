<?php

class Controller_Auth extends Controller_Base
{

	public function action_signup()
	{
		$data["subnav"] = array('signup'=> 'active' );
		$this->template->title = 'Auth &raquo; Signup';
		$this->template->content = View::forge('auth/signup', $data);
	}

	public function action_signin()
	{
		$data["subnav"] = array('signin'=> 'active' );
		$this->template->title = 'Auth &raquo; Signin';
		$this->template->content = View::forge('auth/signin', $data);
	}

	public function action_signout()
	{
		$data["subnav"] = array('signout'=> 'active' );
		$this->template->title = 'Auth &raquo; Signout';
		$this->template->content = View::forge('auth/signout', $data);
	}

	public function action_join()
	{
	// あとからSNSアカウントを追加
		$data["subnav"] = array('signout'=> 'active' );
		$this->template->title = 'Auth &raquo; Signout';
		$this->template->content = View::forge('auth/join', $data);
	}

}
