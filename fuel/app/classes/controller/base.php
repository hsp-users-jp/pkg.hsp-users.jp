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

		// 非ログイン状態でアクセスをフィルタ
		$list = array(
				'settings',
				'package/new',
				'package/update',
			);
		if (!Auth::check() &&
			count(array_filter($list, function($v){ return \Str::starts_with(Uri::string(), $v); })))
		{
			Messages::error('Login required!', 'ログインが必要なページにアクセスしようとしています。');
			Response::redirect_back('signin');
		}
	}
}
