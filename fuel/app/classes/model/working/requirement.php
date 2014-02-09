<?php

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
}
