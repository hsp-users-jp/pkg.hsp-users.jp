<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

/**
 * Model Package class tests
 * 
 * @group App
 * @group Model
 */
class Test_Model_Package extends Test_MyTestCase
{
	protected $tables = array(
		);

	static function random_fill($pkg_ = null)
	{
		$pkg = $pkg_ ?: new \Model_Package();

		$pkg->package_type_id = 2;
		$pkg->name            = \Str::random('alnum', 8);
		$pkg->path            = \Str::random('alnum', 32) . '.zip';
		$pkg->original_name   = \Str::random('alnum', 250);
		$pkg->version         = '1.00';
		$pkg->license_id      = 2;
		$pkg->url             = 'http://example.jp/';
		$pkg->description     = \Str::random('alnum', 1024);

		return $pkg;
	}

	/**
	 * @name パッケージ追加のテスト
	 */
	public function test_append()
	{
		$this->assertTrue(0 == \Model_Package::query()->count());

		$this->assertTrue(0 == \Model_Package_Base::query()->count());

		$pkg = self::random_fill();

		$this->assertTrue( $pkg->save() );

		$this->assertTrue( !is_null($pkg->id) );

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());
	}

	// パッケージ更新のテスト
	public function test_update()
	{
		$this->assertTrue(null == \Model_Package::find_revision(1));

		$pkg = self::random_fill();

		$this->assertTrue( $pkg->save() );

		$pkgs = \Model_Package::find_revision(1);
		reset($pkgs); $this->assertTrue(current($pkgs) && 1 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(!current($pkgs));

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());

		$pkg = self::random_fill($pkg);

		$this->assertTrue( $pkg->save() );

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());
		$this->assertTrue(2 == \Model_Package::count_of_revision(1));

//$q = \Model_Package::query_();
//foreach($q->get() as $p){print_r($p);}
//print_r(''.$q->get_query(true));
//fgets(STDIN,4096);

		$pkgs = \Model_Package::find_revision(1);
//print_r($pkgs);
//echo "::::::::";sleep(10);

		reset($pkgs); $this->assertTrue(current($pkgs) && 2 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(current($pkgs) && 1 == current($pkgs)->revision_id);

		$pkg = self::random_fill($pkg);

		$this->assertTrue( $pkg->save() );

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());
		$this->assertTrue(3 == \Model_Package::count_of_revision(1));

		$pkgs = \Model_Package::find_revision(1);//print_r($pkgs);
		reset($pkgs); $this->assertTrue(current($pkgs) && 3 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(current($pkgs) && 2 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(current($pkgs) && 1 == current($pkgs)->revision_id);
	}

	// パッケージ編集のテスト
	public function test_edit()
	{
		$this->assertTrue(null == \Model_Package::find_revision(1));

		$pkg = self::random_fill();

		$this->assertTrue( $pkg->save() );

		$pkgs = \Model_Package::find_revision(1);
		reset($pkgs); $this->assertTrue(current($pkgs) && 1 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(!current($pkgs));

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());

		$pkg = self::random_fill($pkg);

		$this->assertTrue( $pkg->overwrite() );

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());
		$this->assertTrue(1 == \Model_Package::count_of_revision(1));

		$pkgs = \Model_Package::find_revision(1);
		reset($pkgs); $this->assertTrue(current($pkgs) && 1 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(!current($pkgs));

		$pkg = self::random_fill($pkg);

		$this->assertTrue( $pkg->overwrite() );

		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());
		$this->assertTrue(1 == \Model_Package::count_of_revision(1));

		$pkgs = \Model_Package::find_revision(1);
		reset($pkgs); $this->assertTrue(current($pkgs) && 1 == current($pkgs)->revision_id);
		next($pkgs);  $this->assertTrue(!current($pkgs));
	}

	// パッケージ削除のテスト
	public function test_search()
	{
	}


	public function _test_save()
	{

		$this->assertTrue(0 == \Model_Package::query()->count());

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

		$id = $pkg->id;

		$this->assertTrue('1.00' == \Model_Package::query()->where('id', $id)->get_one()->version);
		$this->assertTrue(1 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());

		$pkg->path            = \Str::random('alnum', 32) . '.zip';
		$pkg->original_name   = \Str::random('alnum', 250);
		$pkg->version         = '2.00';
		$pkg->description     = \Str::random('alnum', 1024);
		$pkg->save();

		$this->assertTrue('2.00' == \Model_Package::query()->where('id', $id)->get_one()->version);
		$this->assertTrue(2 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());

		$pkg->path            = \Str::random('alnum', 32) . '.zip';
		$pkg->original_name   = \Str::random('alnum', 250);
		$pkg->version         = '2.01';
		$pkg->description     = \Str::random('alnum', 1024);
		$pkg->overwrite();

		$this->assertTrue('2.01' == \Model_Package::query()->where('id', $id)->get_one()->version);
		$this->assertTrue(2 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());

		$pkg = \Model_Package::query()->where('id', $id)->get_one();
		$this->assertTrue('2.01' == $pkg->version);
		$pkg->path            = \Str::random('alnum', 32) . '.zip';
		$pkg->original_name   = \Str::random('alnum', 250);
		$pkg->version         = '2.02';
		$pkg->description     = \Str::random('alnum', 1024);
		$pkg->overwrite();

		$this->assertTrue('2.02' == \Model_Package::query()->where('id', $id)->get_one()->version);
		$this->assertTrue(2 == \Model_Package::query()->count());
		$this->assertTrue(1 == \Model_Package_Base::query()->count());

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
}

