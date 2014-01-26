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
			Messages::error('Expire form session!', 'セッションの有効期限が切れました。ログイン処理をおこなってください。');
			Response::redirect_back('signin');
		}
	}
}
