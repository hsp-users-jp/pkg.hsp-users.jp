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
	static public function send_activativation_mail($user_id = null)
	{
		if (!$user_id)
		{
			$user_id = Auth::get_user_id_only();
		}
		
		$metadatas = Auth::get_metadata_by_id($user_id);

		if (!($activate_hash = Arr::get($metadatas, 'activate_hash')))
		{
			return;
		}

		$activate_url = Uri::create('activate/:hash', array('hash' => $activate_hash));
		Log::debug('User('.$user_id.') Activate URL:' . $activate_url);

		$email = Email::forge();
		$email->from('user-registration@hsp-users.jp', 'HSP Package DB');
		$email->to(Arr::get($metadatas, 'email'),
		           Arr::get($metadatas, 'username'));
		$email->subject('HSP Package DB 仮登録のお知らせ');
		$email->body(
				'HSP Package DB にご登録ありがとうございます。' . PHP_EOL .
				'引き続き、下記のURLへアクセスし登録を完了をしてください。' . PHP_EOL .
				'' . PHP_EOL .
				$activate_url . PHP_EOL .
				'' . PHP_EOL .
				Date::time_ago(strtotime("01 March 2012"), strtotime("12 April 1964")) . 'を過ぎるとこのURLは無効になります。' . PHP_EOL .
				'お手数ですが、アカウントページから再度メールを送信してください。' . PHP_EOL .
				'' . PHP_EOL .
				'もし、URLが改行されている場合は、一行につなげアクセスをしてください。' . PHP_EOL .
				'' . PHP_EOL .
				'※このメールにお心当たりがない場合は、メールの破棄をお願いいたします。' . PHP_EOL .
				'※また、登録をキャンセルする場合は、２週間後に自動的にアカウントが' . PHP_EOL .
				'　破棄されるため、特に操作を頂く必要はございません。' . PHP_EOL .
				'' . PHP_EOL .
				str_pad('', 60, ';') . PHP_EOL .
				'; ' . 'HSP Package DB' . PHP_EOL .
				'; ' . Uri::create('/') . PHP_EOL .
				'; お問い合わせ: https://twitter.com/hsp_users_jp' . PHP_EOL .
				'; つぶやく: ' . Uri::create('https://twitter.com/intent/tweet', array(),
					array('text' => '　',
					      'hashtags' => 'HSPpkgDB',
					      'user_id' => '2266918100'))  . PHP_EOL .
				str_pad('', 60, ';') . PHP_EOL
			);

		try
		{
			$email->send();
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
}
