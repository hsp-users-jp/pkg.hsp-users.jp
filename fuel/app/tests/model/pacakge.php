<?php

/**
 * Model MyTemporal class tests
 * 
 * @group App
 */
class Test_Model_Package extends TestCase
{
	public function setup()
	{
		\Migrate::version(0, 'default', 'app');
		@ unlink(implode(DS, array(APPPATH,'config','test','migrations.php')));
		\Migrate::version(null, 'default', 'app');
	}

	public function test_save()
	{

		try
		{
		//	$this->assertTrue(0 == \Model_Package::query()->count());

			\DB::start_transaction();

			$pkg = new \Model_Package();
			$pkg->package_type_id = 2;
			$pkg->name            = 'test';
			$pkg->path            = \Str::random('alnum', 32) . '.zip';
			$pkg->original_name   = \Str::random('alnum', 250);
			$pkg->version         = '1.00';
			$pkg->license_id      = 2;
			$pkg->url             = 'http://example.jp/';
			$pkg->description     = \Str::random('alnum', 1024);
			$pkg->save();

			\DB::commit_transaction();

			$id = $pkg->id;

			\DB::start_transaction();

			$pkg->path            = \Str::random('alnum', 32) . '.zip';
			$pkg->original_name   = \Str::random('alnum', 250);
			$pkg->version         = '2.00';
			$pkg->description     = \Str::random('alnum', 1024);
			$pkg->save();

			\DB::commit_transaction();

			\DB::start_transaction();

			$pkg->path            = \Str::random('alnum', 32) . '.zip';
			$pkg->original_name   = \Str::random('alnum', 250);
			$pkg->version         = '2.01';
			$pkg->description     = \Str::random('alnum', 1024);
			$pkg->overwrite();

			\DB::commit_transaction();

			\DB::start_transaction();

			$pkg = \Model_Package::find($id);
			$pkg->path            = \Str::random('alnum', 32) . '.zip';
			$pkg->original_name   = \Str::random('alnum', 250);
			$pkg->version         = '2.01';
			$pkg->description     = \Str::random('alnum', 1024);
			$pkg->overwrite();

			\DB::commit_transaction();

/*

		$pkg = \Model_Package::query()->where('id', $pkg->id)->get_one();

		$this->assertTrue(1 == \Model_Package::query()->where('id', $pkg->id)->count());

		$pkg = new \Model_Package($a, false);
	//	$pkg = \Model_Package::query()->where('id', $pkg->id)->get_one();
//		$pkg->revision++;
		$pkg->path            = \Str::random('alnum', 32) . '.zip';
		$pkg->original_name   = \Str::random('alnum', 250);
		$pkg->version         = '2.00';
		$pkg->description     = \Str::random('alnum', 1024);
var_dump($pkg->is_new());
//var_dump($pkg);
var_dump($pkg->save());
		;

	//	$this->assertTrue(2 == \Model_Package::query()->where('id', $pkg->id)->count());

		$this->assertNotNull($pkg);

		$pkg = new \Model_Package();
		$pkg->user_id         = 3;
		$pkg->package_type_id = 4;
		$pkg->name            = 'test2';
		$pkg->path            = \Str::random('alnum', 32) . '.rar';
		$pkg->original_name   = \Str::random('alnum', 250);
		$pkg->version         = '1.00';
		$pkg->license_id      = 5;
		$pkg->url             = 'http://example.jp/';
		$pkg->description     = \Str::random('alnum', 1024);
		$pkg->save();

		$this->assertNotNull($pkg);
*/

		}
		catch (Exception $e)
		{
			\DB::rollback_transaction();
			throw $e;
		}
	}
}

