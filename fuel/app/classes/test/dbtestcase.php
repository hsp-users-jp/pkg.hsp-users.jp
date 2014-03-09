<?php

class Test_DbTestCase extends TestCase
{
	protected $tables = array();

	public function setup()
	{
		parent::setup();

//		@ unlink(implode(DS, array(APPPATH,'config','test','migrations.php')));
		\Migrate::version(0, 'default', 'app');
		\Migrate::latest('default', 'app');
		
		\Migrate::version(0, '*', 'package');
		\Migrate::latest('*', 'package');

		if (!empty($this->tables))
		{
			foreach ($this->tables as $table)
			{
				$this->fixit($table);
			}
		}
	}
	
	public function teardown()
	{
		\Migrate::version(0, 'default', 'app');
		\Migrate::version(0, '*', 'package');

		parent::teardown();
	}

	protected function fixit($table)
	{
	}
}
