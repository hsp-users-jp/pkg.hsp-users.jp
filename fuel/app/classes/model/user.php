<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Model_User extends Auth\Model\Auth_User
{
	// アクティベーションメールを送信する
	static public function send_activativation_mail($user_id = null)
	{
		if (!$user_id)
		{ // 今ログイン中のユーザーIDを取得
			$user_id = Auth::get_user_id_only();
		}
		
		$metadatas = Auth::get_metadata_by_id($user_id);

		// アクティベーションハッシュを生成
		$activate_hash = str_replace('=', '', str_replace('/', '!', str_replace('+', '-', \Auth::instance()->hash_password(\Str::random()))));
		$expire = time() + 24 * 60 * 60;

		$activate_url = Uri::create('activate/:hash', array('hash' => $activate_hash));
		Log::debug('User('.$user_id.') Activate URL:' . $activate_url);

		$email = Email::forge();
		$email->from('user-registration@hsp-users.jp', 'HSP Package DB');
		$email->to(Arr::get($metadatas, 'email'),
		           Arr::get($metadatas, 'username'));
		$email->subject('HSP Package DB 仮登録のお知らせ');
		$email->body(View::forge('mail/activativation', array(
						'activate_url' => $activate_url,
						'activate_expire' => Date::forge($expire)->format('%Y年%m月%d日 %H:%M'),
						'data' => array()
					))->render());

		try
		{
			$email->send();

			// ハッシュなどをデータベースに保存
			\Auth::update_user(
					array(
						'activate_hash' => $activate_hash,
						'activate_hash_expire' => $expire
						),
					\Auth::get_metadata_by_id($user_id, 'username')
				);
		}
		catch(\EmailValidationFailedException $e)
		{
			// バリデーションが失敗したとき
		}
		catch(\EmailSendingFailedException $e)
		{
			// ドライバがメールを送信できなかったとき
		}
	}

	// 登録完了メールを送信
	static public function send_activated_mail($user_id = null)
	{
		if (!$user_id)
		{ // 今ログイン中のユーザーIDを取得
			$user_id = Auth::get_user_id_only();
		}

		$metadatas = Auth::get_metadata_by_id($user_id);

		$email = Email::forge();
		$email->from('user-registration@hsp-users.jp', 'HSP Package DB');
		$email->to(Arr::get($metadatas, 'email'),
		           Arr::get($metadatas, 'username'));
		$email->subject('HSP Package DB 登録完了のお知らせ');
		$email->body(View::forge('mail/activated', array(
						'data' => array()
					))->render());

		try
		{
			$email->send();

			// ハッシュなどをデータベースに保存
			\Auth::update_user(
					array(
						'activate_hash' => '', // 空文字で方登録完了済み、のこと
						),
					\Auth::get_metadata_by_id($user_id, 'username')
				);
		}
		catch(\EmailValidationFailedException $e)
		{
			// バリデーションが失敗したとき
		}
		catch(\EmailSendingFailedException $e)
		{
			// ドライバがメールを送信できなかったとき
		}
	}

	// 登録済みユーザー数を取得
	static public function count_of_registerd()
	{
		return
			parent::query()
				->where('group_id', '!=', Auth::get_group_by_name('Banned')->id)
				->count();
	}

	// 登録済み作者数を取得
	static public function count_of_author()
	{
		$subQuery
			= DB::select(DB::expr('MAX(revision_id)'))
				->from(Model_Package::table())
				->where('deleted_at', '=', null)
				->group_by('id');
		$authors
			= DB::select(DB::expr('count(DISTINCT '.Model_Package::table().'.user_id) as count'))
				->from(Model_Package::table());
		$authors = $authors
				->join(Model_User::table(), 'inner')
				->on(Model_User::table().'.id', '=', Model_Package::table().'.user_id')
				->join(Model_Package_Base::table(), 'inner')
				->on(Model_Package::table().'.id', '=', Model_Package_Base::table().'.id')
				->where(Model_User::table().'.group_id', '!=', Auth::get_group_by_name('Banned')->id)
				->where(Model_Package_Base::table().'.deleted_at', '=', null);
		$authors = $authors
				->group_by(Model_Package::table().'.user_id')
				->execute();

		return \Arr::get($authors, '0.count', 0);
	}

	// Ban済みユーザー数を取得
	static public function count_of_banned()
	{
		return
			parent::query()
				->where('group_id', '=', Auth::get_group_by_name('Banned')->id)
				->count();
	}

	// 未アクティベートユーザー数を取得
	static public function count_of_inactivate()
	{
		return
			parent::query()
				->where('group_id', '!=', Auth::get_group_by_name('Banned')->id)
				->related('metadata')
				->where('metadata.key', 'activate_hash')
				->where('metadata.value', '!=', '')
				->count();
	}

	// アカウントが仮登録状態で期限切れしているユーザーの一覧を探す
	static public function find_account_expired()
	{
		return
			parent::query()
				->where('group_id', '!=', Auth::get_group_by_name('Banned')->id)
				->where('created_at', '<', time() - \Config::get('app.user.activate.expired_limit'))
				->related('metadata')
				->where('metadata.key', 'activate_hash')
				->where('metadata.value', '!=', '')
				->get();
	}
}
