<?php

class Controller_Base extends Controller_Template
{
	public function before()
	{
		parent::before();

		// CSRFチェック
		if (Input::method() === 'POST' &&
			!Security::check_token())
		{
			Log::warning('Expire form session!');
			Session::set_flash('error_message', 'セッションの有効期限が切れました。処理をおこなってください。');
			Response::redirect_back('signin');
		}
	}
}
