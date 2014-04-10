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

		if (!($activate_hash = Auth::get_metadata_by_id($user_id, 'activate_hash')))
		{
			return;
		}

		$email = Email::forge();
		$email->from('user-registration@hsp-users.jp', 'My Name');
		$email->to(Auth::get_metadata_by_id($user_id, 'email'),
		           Auth::get_metadata_by_id($user_id, 'username'));
		$email->subject('This is the subject');
	//	$email->to(array(
	//	    'example@mail.com',
	//	    'another@mail.com' => 'With a Name',
	//	));
		$email->body('This is my message');
Log::debug(print_r($email,true));
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
