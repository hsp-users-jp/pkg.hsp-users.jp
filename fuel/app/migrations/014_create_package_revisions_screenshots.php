<?php

namespace Fuel\Migrations;

class Create_package_revisions_screenshots
{
	public function up()
	{
		\DBUtil::create_table('package_revisions_screenshots', array(
			'package_revision_id' => array('constraint' => 11, 'type' => 'int', 'null' => false),
			'package_screenshot_id' => array('constraint' => 11, 'type' => 'int', 'null' => false),
		), array('package_revision_id', 'package_screenshot_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('package_revisions_screenshots');
	}
}