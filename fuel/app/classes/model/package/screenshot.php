<?php

class Model_Package_Screenshot extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'name',
		'width',
		'height',
		'title',
		'description',
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
	protected static $_table_name = 'package_screenshots';

	public function setImageFromTemp($path)
	{
		$tmp_dir = Config::get('app.temp_dir');
		$ss_dir  = DOCROOT.Config::get('app.screenshot_dirname').'/';

		$path = basename($path);

		// スクリーンショットを一時ディレクトリから移動
		@ File::rename($tmp_dir.$path , $ss_dir.$path);
		if (!file_exists($ss_dir.$path))
		{
			Log::error(sprintf('rename %s -> %s', $tmp_dir.$path , $ss_dir.$path));
			throw new \Exception('スクリーンショットの保存が出来ませんでした');
		}
	//	Log::info(sprintf('rename %s -> %s', $tmp_dir.$path , $ss_dir.$path));

		$size = Image::sizes($ss_dir.$path);

		$this->name        = $path;
		$this->width       = $size->width;
		$this->height      = $size->height;
		$this->title       = '';
		$this->description = '';
	}
}
