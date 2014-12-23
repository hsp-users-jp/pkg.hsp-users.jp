<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

namespace Fuel\Migrations;

class Seeds_hsp_specifications
{
	public function up()
	{
		$data = array(
					// http://usk.s16.xrea.com/hsp/history/
					array('hsp_category_id' => 'HSP 2',   'version' => '2.5'),
					array('hsp_category_id' => 'HSP 2',   'version' => '2.55'),
					array('hsp_category_id' => 'HSP 2',   'version' => '2.6'),
					// http://www.onionsoft.net/hsp/v33/doclib/history.txt
					array('hsp_category_id' => 'HSP 3',    'version' => '3.0'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.0a'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.1'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.2'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.2a'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.21'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.21a'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.21a2'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.22'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.3'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.3a'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.31'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.32'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.32a'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.32b'),
					array('hsp_category_id' => 'HSP 3',    'version' => '3.4'),
					//
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.3'),
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.3a'),
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.31'),
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.32'),
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.32a'),
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.32b'),
					array('hsp_category_id' => 'HSP3Dish', 'version' => '3.4'),
				);
		\DB::start_transaction();
		foreach ($data as $row)
		{
			$model = new \Model_Hsp_Specification();
			foreach ($row as $key => $val)
			{
				if ('hsp_category_id' == $key)
				{
					if (!($hsp_category = \Model_Hsp_category::query()->where('name', $val)->get_one()))
					{
						continue 2;
					}
					$val = $hsp_category->id;
				}
				$model[$key] = $val;
			}
			$model->save();
		}
		\DB::commit_transaction();
	}

	public function down()
	{
		\DBUtil::truncate_table(\Model_Hsp_Specification::table()); 
	}
}
