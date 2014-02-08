<?php

class Model_Working_Report extends \Orm\Model_Soft
{
	const StatusSupported     = "Supported";
	const StatusNotSupported  = "NotSupported";
	const StatusUnknown       = "Unknown";
	const StatusPartedSupport = "PartedSupport";

	protected static $_properties = array(
		'id',
		'user_id',
		'package_id',
		'hsp_specification_id',
		'status',
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
		'Observer_UserId' => array(
			'events' => array('before_save')
		),
	);

	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'working_reports';

}
