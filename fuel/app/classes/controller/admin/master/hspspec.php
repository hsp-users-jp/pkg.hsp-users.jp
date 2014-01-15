<?php

class Controller_Admin_Master_HspSpec extends Controller_Admin_Master_Base
{
	static protected $_model = 'Model_Hsp_Specification';
	static protected $_title = 'HSP バージョン';

	static protected function rows($per_page, $offset)
	{
		return
			call_user_func(array(static::$_model, 'query'))
				->related(array(Model_Hsp_Category::table()))
				->rows_offset($per_page)
				->rows_limit($offset)
				->get();
	}
}
