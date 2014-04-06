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

	static public function count_of_registerd()
	{
		return
			parent::query()
				->where('group_id', '!=', Auth::get_group_by_name('Banned')->id)
				->count();
	}

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

	static public function count_of_banned()
	{
		return
			parent::query()
				->where('group_id', '=', Auth::get_group_by_name('Banned')->id)
				->count();
	}

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
