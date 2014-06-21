<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Settings extends Controller_Base
{

	public function action_account()
	{
		$data["state"] = array();

		$val = Validation::forge('val');
//		$val->add('username', 'ユーザー名')
//			->add_rule('required');
		$val->add('email', 'メールアドレス')
			->add_rule('valid_email')
			->add_rule('required');

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					if (Auth::update_user(
							array(
								'email' => $val->validated('email'),
							)))
					{
						Messages::success('アカウントの変更を保存しました');
					}

					return Response::redirect(Uri::string());
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生したため保存できませんでした');
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
	
				Messages::error($errors);
			}
		}
		else
		{
			$_POST['email'] = Auth::get('email', '');
		}

		$data['username'] = Auth::get('username', '');

		$data['provider'] = array();
		$provider_table = Config::get('ormauth.table_name', 'users').'_providers';
		foreach (DB::select('id', 'parent_id', 'provider')
					->from($provider_table)
					->where('parent_id', Auth::get_user_id_only())
					->execute() as $provider)
		{
			$data['provider'][strtolower($provider['provider'])] = true;
		}

		$this->template->title = 'アカウント情報の変更';
		$this->template->breadcrumb = array( '/' => 'トップ', 'settings' => '設定', '' => $this->template->title );
		$this->template->content = View::forge('settings/account', $data);
		$this->template->js = View::forge('settings/account.js', $data);
	}

	public function action_profile()
	{
		$data["state"] = array();

		$val = Validation::forge('val');
		$val->add('fullname', '名前')
			->add_rule('required');
		$val->add('url', 'ホームページ');

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					if (Auth::update_user(
							array(
								'fullname' => $val->validated('fullname'),
								'url' => $val->validated('url')
							)))
					{
						Messages::success('プロフィールの変更を保存しました');
					}

					return Response::redirect(Uri::string());
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生したため保存できませんでした');
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
	
				Messages::error($errors);
			}
		}
		else
		{
			$_POST['fullname'] = Auth::get('fullname', '');
			$_POST['url']      = Auth::get('url', '');
		}

		$data['email'] = Auth::get('email', '');

		$this->template->title = 'プロフィールの変更';
		$this->template->breadcrumb = array( '/' => 'トップ', 'settings' => '設定', '' => $this->template->title );
		$this->template->content = View::forge('settings/profile', $data);
	}


	public function action_password()
	{
		$data["state"] = array();

		$val = Validation::forge('val');
		$val->add('cur_password', '現在のパスワード')
			->add_rule('required');
		$val->add('new_password', '新しいパスワード')
			->add_rule('required');
		$val->add('new_password2', '新しいパスワード(確認用)')
			->add_rule('match_field', 'new_password')
			->add_rule('required');

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					if (Auth::update_user(
							array(
								'password'     => $val->validated('new_password'),
								'old_password' => $val->validated('cur_password')
							)))
					{
						Messages::success('プロフィールの変更を保存しました');
					}

					return Response::redirect(Uri::string());
				}
				catch (\SimpleUserWrongPassword $e)
				{
					Messages::error($e->getMessage(), '現在のパスワードが一致しません');
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生したため保存できませんでした');
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
	
				Messages::error($errors);
			}
		}

		$this->template->title = 'パスワードの変更';
		$this->template->breadcrumb = array( '/' => 'トップ', 'settings' => '設定', '' => $this->template->title );
		$this->template->content = View::forge('settings/password', $data);
	}

	public function action_notifications()
	{
		$data["subnav"] = array();
		$this->template->title = '通知の変更';
		$this->template->breadcrumb = array( '/' => 'トップ', 'settings' => '設定', '' => $this->template->title );
		$this->template->content = View::forge('settings/notifications', $data);
	}

	public function action_security()
	{
		$data["subnav"] = array();
		$this->template->title = 'セキュリティ';
		$this->template->breadcrumb = array( '/' => 'トップ', 'settings' => '設定', '' => $this->template->title );
		$this->template->content = View::forge('settings/security', $data);
	}

	public function action_activation()
	{
		if ('' == Auth::get('activate_hash', ''))
		{ // 既に本登録済み
			throw new HttpNotFoundException;
		}

		// アクティベーションメールを送信
		Model_User::send_activativation_mail();

		$data = array();

		if (Input::is_ajax())
		{
			return View::forge('settings/activation.ajax', array('data' => $data));
		}

		$this->template->title = 'アクティベーションメールの送信';
		$this->template->breadcrumb = array( '/' => 'トップ', 'settings' => '設定', '' => $this->template->title );
		$this->template->content = View::forge('settings/activation', array('data' => $data));
	}

}
