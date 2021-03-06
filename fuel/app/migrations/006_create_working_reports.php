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

class Create_working_reports
{
	public function up()
	{
		\DBUtil::create_table('working_reports', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'package_id' => array('constraint' => 11, 'type' => 'int'),
			'hsp_specification_id' => array('constraint' => 11, 'type' => 'int'),
			'status' => array('constraint' => '"Supported","NotSupported","PartedSupport","Unknown"', 'type' => 'enum'),
			'comment' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('working_reports');
	}
}