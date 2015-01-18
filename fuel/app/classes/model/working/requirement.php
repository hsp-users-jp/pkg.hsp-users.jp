<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014-2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Model_Working_Requirement extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'package_revision_id',
		'hsp_specification_id',
		'status', // Model_Working_Report::Status*
		'comment',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => true,
		),
	);

	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'working_requirements';

	protected static $_has_one = array(
		'hsp_specification' => array(
			'key_from' => 'hsp_specification_id',
			'model_to' => 'Model_Hsp_Specification',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	// ステータスを更新
	public static function update_status($package_revision_id, $hsp_specification_id, $status)
	{
		$result
			= self::query()
				->where('package_revision_id', $package_revision_id)
				->where('hsp_specification_id', $hsp_specification_id)
				->get_one();
		if (!$result)
		{
			$result = new self;
			$result->package_revision_id  = $package_revision_id;
			$result->hsp_specification_id = $hsp_specification_id;
			$result->comment = '';
		}
		$result->status       = $status;
		return $result->save();
	}

	// パッケージのIDを指定して要求環境の情報を取得
	public static function get_requirements($package_revision_id)
	{
		$working_requirements
			= self::query()
				->related('hsp_specification')
				->where('package_revision_id', $package_revision_id)
				->get();
		$result = array();

		foreach ($working_requirements as $working_requirement)
		{
			$category_id = $working_requirement->hsp_specification->hsp_category_id;

			if (!isset($result[$category_id]))
			{ // カテゴリ用の領域を初期化
				$result[$category_id] = array(
						'summary' => Model_Working_Report::StatusUnknown,
						'detail'  => array()
					);
			}

			// カテゴリ内の集計結果を更新
			$support = & $result[$category_id];
			switch ($support['summary'])
			{
			case Model_Working_Report::StatusUnknown:
				$support['summary'] = $working_requirement->status;
				break;
			case Model_Working_Report::StatusSupported:
			case Model_Working_Report::StatusNotSupported:
				if ($support['summary'] != $working_requirement->status)
					$support['summary'] = Model_Working_Report::StatusPartedSupport;
				break;
			}

			// 詳細を追加
			$support['detail'][$working_requirement->hsp_specification_id]
				= $working_requirement->status;
		}

		return $result;
	}
}
