<?php

class Controller_Auth extends Controller_Base
{

	public function action_signup()
	{
		$data['state'] = array();

		$val = Validation::forge('val');
		$val->add('username', 'ユーザー名')
			->add_rule('required');
		$val->add('password', 'パスワード')
			->add_rule('required');
		$val->add('password2', 'パスワード(確認)')
			->add_rule('match_field', 'password')
			->add_rule('required');
		$val->add('email', 'メールアドレス')
			->add_rule('required');

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					$activate_hash = 'a'; // @todo ハッシュを作成する

					if (Auth::create_user(
							$val->validated('username'),
							$val->validated('password'),
							$val->validated('email'),
							Config::get('app.user.default.group', 1),
							array(
								'activate_hash' => $activate_hash,
							)
						))
					{
						// 登録されたメールに対して本登録のメールを送る
						// $email = Email::forge(); @todo ちゃんとやる

						Session::set('activate_hash', $activate_hash);
						Response::redirect('');
					}
					else
					{
						$errors = array('登録に失敗しました');
						Log::error(implode("\n", $errors));
						Session::set_flash('error', $errors);
					}
				}
				catch (\SimpleUserUpdateException $e)
				{
					switch ($e->getCode())
					{
					case 2: // Email address already exists
						$errors = array('同じメールアドレスが既に登録されているため登録できませんでした');
						break;
					case 3: // Username already exists
						$errors = array('同じユーザー名が既に登録されているため登録できませんでした');
						break;
					default:
						$errors = array($e->getMessage());
						break;
					}
					Log::error(implode("\n", $errors));
					Session::set_flash('error', $errors);
				}
				catch (\Exception $e)
				{
					$errors = array($e->getMessage());
					Log::error(implode("\n", $errors));
					Session::set_flash('error', $errors);
				}
			}
			else
			{
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Session::set_flash('error', $errors);
			}
		}

		$this->template->title = '新規登録';
		$this->template->content = View::forge('auth/signup', $data);
	}

	public function action_signin()
	{
//		$provider = Input::get('provider');
//		if (null !== $provider)
//		{
//			if (empty($provider))
//			{
//				Session::set_flash('error', 'プロバイダの指定がありません。');
//				Response::redirect_back();
//			}
//
//			$config['provider'] = $provider;
//			Auth_Opauth::forge($config);
//
//			return;
//		}

		$data['state'] = array();
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

	public function action_oauth($provider = null)
	{
		// bail out if we don't have an OAuth provider to call
		if ($provider === null)
		{
			Session::set_flash('error', 'プロバイダの指定がありません。');
			Response::redirect_back();
		}

		try
		{
			// Opauth を読み込む、プロバイダのストラテジーを読み込むことが出来たならプロバイダにリダイレクトします。
			Auth_Opauth::forge();
		}
		catch (\Exception $e)
		{ // 大文字小文字を間違えても例外発生でここに来る
			Log::error($e->getMessage());
			throw new HttpNotFoundException;
		}
	}

	public function action_callback()
	{
		// Opauth can throw all kinds of nasty bits, so be prepared
		try
		{
			// get the Opauth object
			$opauth = \Auth_Opauth::forge(false);

			// and process the callback
			$status = $opauth->login_or_register();

			// fetch the provider name from the opauth response so we can display a message
			$provider = $opauth->get('auth.provider', '?');

			// deal with the result of the callback process
			switch ($status)
			{
				// a local user was logged-in, the provider has been linked to this user
				case 'linked':
					// inform the user the link was succesfully made
					\Messages::success(sprintf(__('login.provider-linked'), ucfirst($provider)));
					// and set the redirect url for this status
					$url = ''; //dashboard
				break;

				// the provider was known and linked, the linked account as logged-in
				case 'logged_in':
					// inform the user the login using the provider was succesful
					\Messages::success(sprintf(__('login.logged_in_using_provider'), ucfirst($provider)));
					// and set the redirect url for this status
					$url = ''; //dashboard
				break;

				// we don't know this provider login, ask the user to create a local account first
				case 'register':
					// inform the user the login using the provider was succesful, but we need a local account to continue
					\Messages::info(sprintf(__('login.register-first'), ucfirst($provider)));
					// and set the redirect url for this status
					$url = 'users/register';
				break;

				// we didn't know this provider login, but enough info was returned to auto-register the user
				case 'registered':
					// inform the user the login using the provider was succesful, and we created a local account
					\Messages::success(__('login.auto-registered'));
					// and set the redirect url for this status
					$url = ''; //dashboard
				break;

				default:
					throw new \FuelException('Auth_Opauth::login_or_register() has come up with a result that we dont know how to handle.');
			}

			// redirect to the url set
			\Response::redirect($url);
		}

		// deal with Opauth exceptions
		catch (\OpauthException $e)
		{
			Log::error($e->getMessage());
			Session::set_flash('error', $e->getMessage());
			Response::redirect_back();
		}

		// catch a user cancelling the authentication attempt (some providers allow that)
		catch (\OpauthCancelException $e)
		{
			// you should probably do something a bit more clean here...
			exit('It looks like you canceled your authorisation.'.\Html::anchor('users/oath/'.$provider, 'Click here').' to try again.');
		}
	}
}
