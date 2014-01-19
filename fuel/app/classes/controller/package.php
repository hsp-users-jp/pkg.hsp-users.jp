<?php

class Controller_Package extends Controller_Base
{

	public function action_list()
	{
		$pagination = Pagination::forge('page', array(
				'total_items' => Model_Package::count(),
				'uri_segment' => 'page',
			));

		switch (Input::get('sort'))
		{
		case 'recent': // 最近の更新順
			$query
				= Model_Package::order_by_recent_update();
			break;
		case 'recents': // 人気のダウンロード順
			$query
				= Model_Package::query();
			break;
		default:
			$query
				= Model_Package::query();
		}

		$query = $query
				->related('common')
				->related('version')
				->rows_offset($pagination->offset)
				->rows_limit($pagination->per_page);

		$data['rows'] = $query->get();
	//	$data['pagination'] = $pagination; // タグを出力するのでエスケープ処理させないため set_safe で追加

		$this->template->title = 'パッケージ一覧';
		$this->template->content = View::forge('package/list', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	public function action_detail($package_id)
	{
		$package
			= Model_Package::query()
				->related('common')
//				->related('version')
				->related('versions')
				->related('screenshots')
				->order_by('versions.id', 'desc')
				->where('id', $package_id)
				->get_one();
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$query
			= Model_Package_Version::query()
				->where('package_id', $package_id);
		if (Input::get('v'))
		{ // 指定したバージョンを取得
			$query = $query->where('version', Input::get('v'));
		}
		else
		{ // 最新を取得
			$query = $query->order_by('id', 'desc');
		}
		$package->version = $query->get_one();
		if (!$package->version)
		{
			throw new HttpNotFoundException;
		}

//		$package->versions
//			= Model_Package_Version::query()
//				->where('package_id', $package_id)
//				->order_by('id', 'desc')
//				->get();

		$data['package'] = $package;

		$hsp_categories
			= Model_Hsp_Category::query()
				->get();
		$data['hsp_categories'] = $hsp_categories;

		$working_requirements
			= Model_Working_Requirement::query()
				->related('hsp_specification')
				->where('package_version_id', $package->version->id)
				->get();
		$package_support = array();
		foreach ($hsp_categories as $hsp_category)
		{
			$package_support[$hsp_category->id][0] = 0;
			$package_support[$hsp_category->id][1] = 0;
		}
		foreach ($working_requirements as $working_requirement)
		{
			$package_support[$working_requirement->hsp_specification->hsp_category_id][0]++;
		}
		foreach ($hsp_categories as $hsp_category)
		{
			$package_support_ = & $package_support[$hsp_category->id][0];
			if (0 == $package_support_)
			{
				$package_support_ = Model_Working_Report::StatusUnknown;
			}
			else if (0 < $package_support_)
			{
				$package_support_ = Model_Working_Report::StatusSupported;
			}
			unset($package_support_);
		}
		$data['package_support'] = $package_support;

		$this->template->title = $package->common->name;
		$this->template->content = View::forge('package/detail', $data);
	}

	public function action_requirement($package_version_id)
	{
		$hsp_categories
			= Model_Hsp_Category::query()
				->get();
		$data['hsp_categories'] = $hsp_categories;

		$package_supports = array();
		$hsp_specifications_ = array();
		foreach ($hsp_categories as $hsp_category)
		{
			$package_supports[$hsp_category->id][0] = array();
			$package_supports[$hsp_category->id][1] = array();
			$hsp_specifications_[$hsp_category->id] = array();
		}

		$hsp_specifications
			= Model_Hsp_Specification::query()
				->get();
		foreach ($hsp_specifications as $hsp_specification)
		{
			$hsp_specifications_[$hsp_specification->hsp_category_id][] = $hsp_specification;
		}
		$data['hsp_specifications'] = $hsp_specifications_;

		$working_requirements
			= Model_Working_Requirement::query()
				->related('hsp_specification')
				->where('package_version_id', $package_version_id)
				->get();
		foreach ($working_requirements as $working_requirement)
		{
			$package_supports[$working_requirement->hsp_specification->hsp_category_id][0][$working_requirement->hsp_specification_id]
				= $working_requirement;
		}
		$data['package_supports'] = $package_supports;

		if (Input::is_ajax())
		{
			return View::forge('package/requirement.ajax', array('data' => $data));
		}

		$this->template->title = '';
		$this->template->content = View::forge('package/requirement', array('data' => $data));
	}

	public function get_new()
	{
		// 状態遷移
		//  1. パッケージのアップロード画面
		//     1.5. パッケージの解析画面(プログレス)
		//  2. パッケージの情報の入力画面
		
		$data = array();

		$this->template->title = '';
		$this->template->content = View::forge('package/upload', array('data' => $data));
		$this->template->js = View::forge('package/upload.js', $data);
	}

	public function action_new()
	{
		$data['state'] = array();

		$data['license_list'] = array('' => 'ライセンスを指定してください');
		foreach (Model_License::query()
					->get() as $row)
		{
			$data['license_list'][$row->id] = $row->name;
		}

		$data['package_type_list'] = array();
		foreach (Model_Package_Type::query()
					->get() as $row)
		{
			$data['package_type_list'][$row->id] = $row->name;
		}

		$data['hsp_category'] = array();
		$data['hsp_spec']     = array();
		$data['hsp_spec_max_row'] = 0;
		foreach (Model_Hsp_Category::query()
					->get()
				as $row)
		{
			$data['hsp_spec'][$row->id] = array();
			$data['hsp_category'][$row->id] = $row->name;
		}
		foreach (Model_Hsp_Specification::query()
					->get()
				as $row)
		{
			$data['hsp_spec'][$row->hsp_category_id][$row->id] = $row->version;
			$data['hsp_spec_max_row'] = max($data['hsp_spec_max_row'], count($data['hsp_spec'][$row->hsp_category_id]));
		}

		// バリデーション対象のフィールドを指定
		$val = Validation::forge('val');
		$val->add('title', '名称')
			->add_rule('required');
		$val->add('description', '説明')
			->add_rule('required');
		$val->add('url', 'url');
//		$val->add('package', 'package')
//			->add_rule('required');
		$val->add('version', 'バージョン')
			->add_rule('required');
		$val->add('package_type', 'パッケージ種別')
			->add_rule('required');
		$val->add('license', 'ライセンス')
			->add_rule('required');
//		$val->add('ss', 'ss');
		// HSPの対応バージョン
		$hsp_specs = array();
		for ($i = 0; $i < $data['hsp_spec_max_row']; ++$i)
		{
			foreach ($data['hsp_category'] as $hsp_category_id => $hsp_category_name)
			{
				if ($i < count($data['hsp_spec'][$hsp_category_id]))
				{
					$cell = array_slice($data['hsp_spec'][$hsp_category_id], $i, 1, true);
					list($hsp_spec_name) = array_values($cell);
					list($hsp_spec_id)   = array_keys($cell);
					$id = sprintf('hsp_spec.%d', $hsp_spec_id);
					$val->add($id, $id);
					$hsp_specs[$id] = $hsp_spec_id;
				}
			}
		}
Log::debug(print_r($hsp_specs,true));
Log::debug(print_r($_POST,true));
		if (Input::post())
		{
			if (Session::get('package.form'))
			{ // 取得済みのパッケージの情報をマージする
				$_POST = array_merge($_POST, Session::get('package.form'));
				Session::delete('package.form');
			}
			else if ($val->run())
			{
				$package_path = '';
				$ss_path      = array();

				try
				{
//					Upload::register('before', function (&$file) {
//Log::debug(print_r($file,true));
//						if (Upload::UPLOAD_ERR_OK == $file['error'])
//						{
//							switch ($file['element'])
//							{
//							case 'package':
//Log::debug($file['element'].' '.print_r($file['path'],true));
//								$file['file'] = DOCROOT.'files'.DS.'packages';
//Log::debug($file['element'].' '.print_r($file['path'],true));
//								break;
//							case 'ss':
//Log::debug($file['element'].' '.print_r($file['path'],true));
//								$file['file'] = DOCROOT.'files'.DS.'images';
//Log::debug($file['element'].' '.print_r($file['path'],true));
//								break;
//							}
//						}
//					});

//					Upload::process();

//					if (Upload::is_valid())
//					{
//						Upload::save();
//					}

//					$files = Upload::get_files();
//Log::info('a:'.print_r($files,true));

//					foreach ($files as $file)
//					{
//Log::debug('b:'.print_r($file,true));
//						switch ($file['field'])
//						{
//						case 'package':
//							$package_path = $file['saved_to'].$file['saved_as'];
//							break;
//						case 'ss':
//							$ss_path[] = $file['saved_to'].$file['saved_as'];
//							break;
//						}
//					}

					$package_path = Session::get('package.path');
					$ss_path = Session::get('package.ss');
					
					if (!empty($package_path))
					{
						try
						{
							DB::start_transaction();

							$tmp_dir     = Config::get('site.temp_dir');
							$package_dir = Config::get('site.package_dir');
							$ss_dir      = DOCROOT.Config::get('site.screenshot_dirname').'/';

							//File::create_dir(dirname(rtrim($package_dir, '/')), basename(rtrim($package_dir, '/')));

							// レコードの登録

							@ File::rename($tmp_dir.$package_path, $package_dir.$package_path);
							if (!file_exists($package_dir.$package_path))
							{
								Log::error(sprintf('rename %s -> %s', $tmp_dir.$package_path, $package_dir.$package_path));
								throw new \Exception('パッケージの保存が出来ませんでした');
							}

							$package = new Model_Package;
							$package->user_id            = 0;
							$package->package_common_id  = 0; // いったん仮で保存
							$package->package_version_id = 0;
							$package->save();

							$package_common = new Model_Package_Common;
							$package_common->package_id      = $package->id;
							$package_common->package_type_id = $val->validated('package_type');
							$package_common->name            = $val->validated('title');
							$package_common->description     = $val->validated('description');
							$package_common->url             = $val->validated('url');
							$package_common->save();
	
							$package_ver = new Model_Package_Version;
							$package_ver->package_id = $package->id;
							$package_ver->license_id = $val->validated('license');
							$package_ver->path       = basename($package_path);
							$package_ver->version    = $val->validated('version');
							$package_ver->save();

							$package->package_common_id  = $package_common->id;
							$package->package_version_id = $package_ver->id;
							$package->save();

							foreach ($ss_path as $path)
							{
								$ss = new Model_Package_Screenshot;
								$ss->package_version_id = $package_ver->id;
								$ss->path        = basename($path);
								$ss->title       = '';
								$ss->description = '';
								$ss->save();

								File::rename($tmp_dir.$path , DOCROOT.$ss_dir.$path);
							}
	
							foreach ($hsp_specs as $hsp_spec => $id)
							{
Log::debug(sprintf('$val->validated("%s")="%s","%s"',$hsp_spec,$val->validated($hsp_spec),Input::post($hsp_spec)));
								if (Input::post($hsp_spec))
								{
									$working_requirement = new Model_Working_Requirement;
									$working_requirement->package_version_id   = $package_ver->id;
									$working_requirement->hsp_specification_id = $id;
									$working_requirement->status  = Model_Working_Report::StatusSupported;
									$working_requirement->comment = '';
									$working_requirement->save();
								}
							}

							DB::commit_transaction();
	
							Session::set_flash('success', '追加しました');
		
							Response::redirect(sprintf('package/%d', $package->id));
						}
						catch (Exception $e)
						{
Log::debug(__FILE__.'('.__LINE__.')');
							$errors = array($e->getMessage());
							Log::error(implode("\n", $errors));
							Session::set_flash('error', $errors);

							// 未決のトランザクションクエリをロールバックする
							DB::rollback_transaction();
						}
					}
					else
					{
Log::debug(__FILE__.'('.__LINE__.')');
						$errors = array('入力項目を確認してください。');
						Log::error(implode("\n", $errors));
						Session::set_flash('error', $errors);
						$data['state']['package'] = 'has-error';
					}
					
					foreach (array_merge($ss_path, array($package_path)) as $path)
					{
						Log::error('remove upload file: ' . $path);
						@ unlink($path);
					}
				}
				catch (Exception $e)
				{
Log::debug(__FILE__.'('.__LINE__.')');
					$errors = array($e->getMessage());
					Log::error(implode("\n", $errors));
					Session::set_flash('error', $errors);
				}
			}
			else
			{
Log::debug(__FILE__.'('.__LINE__.')');
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Session::set_flash('error', $errors);
			}
		}

		$this->template->title = 'パッケージの追加';
		$this->template->content = View::forge('package/new', $data);
		$this->template->js = View::forge('package/new.js', $data);
	}

	public function action_update($package_id)
	{
		$package
			= Model_Package::query()
				->related('common')
				->related('version')
				->where('id', $package_id)
				->get_one();
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$data['package'] = $package;
		$data['state'] = array();

		$data['license_list'] = array('' => 'ライセンスを指定してください');
		foreach (Model_License::query()
					->get() as $row)
		{
			$data['license_list'][$row->id] = $row->name;
		}

		$data['package_type_list'] = array();
		foreach (Model_Package_Type::query()
					->get() as $row)
		{
			$data['package_type_list'][$row->id] = $row->name;
		}

		$data['hsp_category'] = array();
		$data['hsp_spec']     = array();
		$data['hsp_spec_max_row'] = 0;
		foreach (Model_Hsp_Category::query()
					->get()
				as $row)
		{
			$data['hsp_spec'][$row->id] = array();
			$data['hsp_category'][$row->id] = $row->name;
		}
		foreach (Model_Hsp_Specification::query()
					->get()
				as $row)
		{
			$data['hsp_spec'][$row->hsp_category_id][$row->id] = $row->version;
			$data['hsp_spec_max_row'] = max($data['hsp_spec_max_row'], count($data['hsp_spec'][$row->hsp_category_id]));
		}

		// バリデーション対象のフィールドを指定
		$val = Validation::forge('val');
		$val->add('title', 'title')
			->add_rule('required');
		$val->add('description', 'description')
			->add_rule('required');
		$val->add('url', 'url');
//		$val->add('package', 'package')
//			->add_rule('required');
		$val->add('version', 'version')
			->add_rule('required');
		$val->add('package_type', 'package_type')
			->add_rule('required');
		$val->add('license', 'license')
			->add_rule('required');
//		$val->add('ss', 'ss');
		// HSPの対応バージョン
		$hsp_specs = array();
		for ($i = 0; $i < $data['hsp_spec_max_row']; ++$i)
		{
			foreach ($data['hsp_category'] as $hsp_category_id => $hsp_category_name)
			{
				if ($i < count($data['hsp_spec'][$hsp_category_id]))
				{
					$cell = array_slice($data['hsp_spec'][$hsp_category_id], $i, 1, true);
					list($hsp_spec_name) = array_values($cell);
					list($hsp_spec_id)   = array_keys($cell);
					$id = sprintf('hsp_spec.%d', $hsp_spec_id);
					$val->add($id, $id);
					$hsp_specs[$id] = $hsp_spec_id;
				}
			}
		}
Log::debug(print_r($hsp_specs,true));
Log::debug(print_r($_POST,true));
		if (Input::post())
		{
			if ($val->run())
			{
				$package_path = '';
				$ss_path      = array();

				try
				{
					Upload::register('before', function (&$file) {
Log::debug(print_r($file,true));
						if (Upload::UPLOAD_ERR_OK == $file['error'])
						{
							switch ($file['element'])
							{
							case 'package':
Log::debug($file['element'].' '.print_r($file['path'],true));
								$file['file'] = DOCROOT.'files'.DS.'packages';
Log::debug($file['element'].' '.print_r($file['path'],true));
								break;
							case 'ss':
Log::debug($file['element'].' '.print_r($file['path'],true));
								$file['file'] = DOCROOT.'files'.DS.'images';
Log::debug($file['element'].' '.print_r($file['path'],true));
								break;
							}
						}
					});

					Upload::process();

					if (Upload::is_valid())
					{
						Upload::save();
					}

					$files = Upload::get_files();
Log::info('a:'.print_r($files,true));

					foreach ($files as $file)
					{
Log::debug('b:'.print_r($file,true));
						switch ($file['field'])
						{
						case 'package':
							$package_path = $file['saved_to'].$file['saved_as'];
							break;
						case 'ss':
							$ss_path[] = $file['saved_to'].$file['saved_as'];
							break;
						}
					}
					
					if (!empty($package_path))
					{
						try
						{
							DB::start_transaction();
		
							$package->user_id = 0;

							if ($val->validated('package_type') != $package->common->package_type_id ||
								$val->validated('title')        != $package->common->name            ||
								$val->validated('description')  != $package->common->description     )
							{
								$package_common = new Model_Package_Common;
								$package_common->package_id      = $package->id;
								$package_common->package_type_id = $val->validated('package_type');
								$package_common->name            = $val->validated('title');
								$package_common->description     = $val->validated('description');
								$package_common->url             = $val->validated('url');
								$package_common->save();
								$package->package_common_id  = $package_common->id;
							}
							else
							{
								$package_common = $package->common;
							}

							$package_ver = new Model_Package_Version;
							$package_ver->package_id = $package->id;
							$package_ver->license_id = $val->validated('license');
							$package_ver->path       = basename($package_path);
							$package_ver->version    = $val->validated('version');
							$package_ver->save();
							$package->package_version_id = $package_ver->id;

							$package->common  = null; // リレーションを解除
							$package->version = null;
							$package->save();

							$package->common  = $package_common; // リレーションを解除
							$package->version = $package_ver;

							foreach ($ss_path as $path)
							{
								$ss = new Model_Package_Screenshot;
								$ss->package_version_id = $package_ver->id;
								$ss->path        = basename($path);
								$ss->title       = '';
								$ss->description = '';
								$ss->save();
							}
	
							foreach ($hsp_specs as $hsp_spec => $id)
							{
Log::debug(sprintf('$val->validated("%s")="%s","%s"',$hsp_spec,$val->validated($hsp_spec),Input::post($hsp_spec)));
								if (Input::post($hsp_spec))
								{
									$working_requirement = new Model_Working_Requirement;
									$working_requirement->package_version_id   = $package_ver->id;
									$working_requirement->hsp_specification_id = $id;
									$working_requirement->status  = Model_Working_Report::StatusSupported;
									$working_requirement->comment = '';
									$working_requirement->save();
								}
							}
	
							DB::commit_transaction();
	
							Session::set_flash('success', '追加しました');
		
							Response::redirect(sprintf('package/%d', $package->id));
						}
						catch (Exception $e)
						{
Log::debug(__FILE__.'('.__LINE__.')');
							$errors = array($e->getMessage());
							Log::error(implode("\n", $errors));
							Session::set_flash('error', $errors);

							// 未決のトランザクションクエリをロールバックする
							DB::rollback_transaction();
						}
					}
					else
					{
Log::debug(__FILE__.'('.__LINE__.')');
						$errors = array('入力項目を確認してください。');
						Log::error(implode("\n", $errors));
						Session::set_flash('error', $errors);
						$data['state']['package'] = 'has-error';
					}
					
					foreach (array_merge($ss_path, array($package_path)) as $path)
					{
						Log::error('remove upload file: ' . $path);
						@ unlink($path);
					}
				}
				catch (Exception $e)
				{
Log::debug(__FILE__.'('.__LINE__.')');
					$errors = array($e->getMessage());
					Log::error(implode("\n", $errors));
					Session::set_flash('error', $errors);
				}
			}
			else
			{
Log::debug(__FILE__.'('.__LINE__.')');
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Session::set_flash('error', $errors);
			}
		}

		$this->template->title = 'パッケージの更新';
		$this->template->content = View::forge('package/update', $data);
	}

	public function action_edit($package_id)
	{
		$package
			= Model_Package::query()
				->related('common')
				->related('version')
				->where('id', $package_id)
				->get_one();
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$data['package'] = $package;
		$data['state'] = array();

		$data['license_list'] = array('' => 'ライセンスを指定してください');
		foreach (Model_License::query()
					->get() as $row)
		{
			$data['license_list'][$row->id] = $row->name;
		}

		$data['package_type_list'] = array();
		foreach (Model_Package_Type::query()
					->get() as $row)
		{
			$data['package_type_list'][$row->id] = $row->name;
		}

		$data['hsp_category'] = array();
		$data['hsp_spec']     = array();
		$data['hsp_spec_max_row'] = 0;
		foreach (Model_Hsp_Category::query()
					->get()
				as $row)
		{
			$data['hsp_spec'][$row->id] = array();
			$data['hsp_category'][$row->id] = $row->name;
		}
		foreach (Model_Hsp_Specification::query()
					->get()
				as $row)
		{
			$data['hsp_spec'][$row->hsp_category_id][$row->id] = $row->version;
			$data['hsp_spec_max_row'] = max($data['hsp_spec_max_row'], count($data['hsp_spec'][$row->hsp_category_id]));
		}

		// バリデーション対象のフィールドを指定
		$val = Validation::forge('val');
		$val->add('title', 'title')
			->add_rule('required');
		$val->add('description', 'description')
			->add_rule('required');
		$val->add('url', 'url');
//		$val->add('package', 'package')
//			->add_rule('required');
		$val->add('version', 'version')
			->add_rule('required');
		$val->add('package_type', 'package_type')
			->add_rule('required');
		$val->add('license', 'license')
			->add_rule('required');
//		$val->add('ss', 'ss');
		// HSPの対応バージョン
		$hsp_specs = array();
		for ($i = 0; $i < $data['hsp_spec_max_row']; ++$i)
		{
			foreach ($data['hsp_category'] as $hsp_category_id => $hsp_category_name)
			{
				if ($i < count($data['hsp_spec'][$hsp_category_id]))
				{
					$cell = array_slice($data['hsp_spec'][$hsp_category_id], $i, 1, true);
					list($hsp_spec_name) = array_values($cell);
					list($hsp_spec_id)   = array_keys($cell);
					$id = sprintf('hsp_spec.%d', $hsp_spec_id);
					$val->add($id, $id);
					$hsp_specs[$id] = $hsp_spec_id;
				}
			}
		}
Log::debug(print_r($hsp_specs,true));
Log::debug(print_r($_POST,true));
		if (Input::post())
		{
			if ($val->run())
			{
				$package_path = '';
				$ss_path      = array();

				try
				{
					Upload::register('before', function (&$file) {
Log::debug(print_r($file,true));
						if (Upload::UPLOAD_ERR_OK == $file['error'])
						{
							switch ($file['element'])
							{
							case 'package':
Log::debug($file['element'].' '.print_r($file['path'],true));
								$file['file'] = DOCROOT.'files'.DS.'packages';
Log::debug($file['element'].' '.print_r($file['path'],true));
								break;
							case 'ss':
Log::debug($file['element'].' '.print_r($file['path'],true));
								$file['file'] = DOCROOT.'files'.DS.'images';
Log::debug($file['element'].' '.print_r($file['path'],true));
								break;
							}
						}
					});

					Upload::process();

					if (Upload::is_valid())
					{
						Upload::save();
					}

					$files = Upload::get_files();
Log::info('a:'.print_r($files,true));

					foreach ($files as $file)
					{
Log::debug('b:'.print_r($file,true));
						switch ($file['field'])
						{
						case 'package':
							$package_path = $file['saved_to'].$file['saved_as'];
							break;
						case 'ss':
							$ss_path[] = $file['saved_to'].$file['saved_as'];
							break;
						}
					}
					
					if (!empty($package_path))
					{
						try
						{
							DB::start_transaction();
		
							$package->user_id = 0;

							if ($val->validated('package_type') != $package->common->package_type_id ||
								$val->validated('title')        != $package->common->name            ||
								$val->validated('description')  != $package->common->description     )
							{
								$package_common = new Model_Package_Common;
								$package_common->package_id      = $package->id;
								$package_common->package_type_id = $val->validated('package_type');
								$package_common->name            = $val->validated('title');
								$package_common->description     = $val->validated('description');
								$package_common->url             = $val->validated('url');
								$package_common->save();
								$package->package_common_id  = $package_common->id;
							}
							else
							{
								$package_common = $package->common;
							}

							$package_ver = new Model_Package_Version;
							$package_ver->package_id = $package->id;
							$package_ver->license_id = $val->validated('license');
							$package_ver->path       = basename($package_path);
							$package_ver->version    = $val->validated('version');
							$package_ver->save();
							$package->package_version_id = $package_ver->id;

							$package->common  = null; // リレーションを解除
							$package->version = null;
							$package->save();

							$package->common  = $package_common; // リレーションを解除
							$package->version = $package_ver;

							foreach ($ss_path as $path)
							{
								$ss = new Model_Package_Screenshot;
								$ss->package_version_id = $package_ver->id;
								$ss->path        = basename($path);
								$ss->title       = '';
								$ss->description = '';
								$ss->save();
							}
	
							foreach ($hsp_specs as $hsp_spec => $id)
							{
Log::debug(sprintf('$val->validated("%s")="%s","%s"',$hsp_spec,$val->validated($hsp_spec),Input::post($hsp_spec)));
								if (Input::post($hsp_spec))
								{
									$working_requirement = new Model_Working_Requirement;
									$working_requirement->package_version_id   = $package_ver->id;
									$working_requirement->hsp_specification_id = $id;
									$working_requirement->status  = Model_Working_Report::StatusSupported;
									$working_requirement->comment = '';
									$working_requirement->save();
								}
							}
	
							DB::commit_transaction();
	
							Session::set_flash('success', '追加しました');
		
							Response::redirect(sprintf('package/%d', $package->id));
						}
						catch (Exception $e)
						{
Log::debug(__FILE__.'('.__LINE__.')');
							$errors = array($e->getMessage());
							Log::error(implode("\n", $errors));
							Session::set_flash('error', $errors);

							// 未決のトランザクションクエリをロールバックする
							DB::rollback_transaction();
						}
					}
					else
					{
Log::debug(__FILE__.'('.__LINE__.')');
						$errors = array('入力項目を確認してください。');
						Log::error(implode("\n", $errors));
						Session::set_flash('error', $errors);
						$data['state']['package'] = 'has-error';
					}
					
					foreach (array_merge($ss_path, array($package_path)) as $path)
					{
						Log::error('remove upload file: ' . $path);
						@ unlink($path);
					}
				}
				catch (Exception $e)
				{
Log::debug(__FILE__.'('.__LINE__.')');
					$errors = array($e->getMessage());
					Log::error(implode("\n", $errors));
					Session::set_flash('error', $errors);
				}
			}
			else
			{
Log::debug(__FILE__.'('.__LINE__.')');
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Session::set_flash('error', $errors);
			}
		}

		$this->template->title = 'パッケージの更新';
		$this->template->content = View::forge('package/edit', $data);
	}

	public function action_remove($package_id)
	{
		$package
			= Model_Package::query()
				->related('common')
				->related('version')
				->where('id', $package_id)
				->get_one();
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$data['package'] = $package;

		$data["subnav"] = array('remove'=> 'active' );
		$this->template->title = 'Package &raquo; Remove';
		$this->template->content = View::forge('package/remove', $data);
	}

	// ファイルをアップロード
	public function post_upload()
	{
		$data = array('status' => '',
		              'success' => array(),
		              'message' => '',
		              'csrf_token' => Security::fetch_token()); // Ajax処理しているのでトークンを更新しないとうまく行かない

		Upload::process();

		if (Upload::is_valid())
		{
			@ Upload::save();

			// JSONで返答するために正常にアップロードきたファイルの一覧を作る
			foreach (Upload::get_files() as $file)
			{
				$data['success'][] = pathinfo($file['saved_as'], PATHINFO_FILENAME);
			}

			$data['status'] = 'success';
		}

		// アップロードでのエラーを、、、、とりあえず後でやる @todo
		foreach (Upload::get_errors() as $file)
		{
		// $file is an array with all file information,
		// $file['errors'] contains an array of all error occurred
		// each array element is an an array containing 'error' and 'message'
		}

		if (Input::is_ajax())
		{
			$json = Format::forge($data)->to_json();
			$headers = array (
				'Pragma'            => 'no-cache',
			);
			return Response::forge($json, 200, $headers);
		}
		else
		{
		}
	}

	// アップロード済みのファイルを検証
	public function post_validate()
	{
		$data = array('status' => '',
		              'message' => '',
		              'csrf_token' => Security::fetch_token()); // Ajax処理しているのでトークンを更新しないとうまく行かない

		// セッション削除
		Session::delete('package');

		// バリデーション対象のフィールドを指定
		$val = Validation::forge('val');
		$val->add('uploaded', '')
			->add_rule('min_length', 1)
			->add_rule('required');

		if ($val->run())
		{
			$package = array(
					'path' => '',
					'ss' => array(),
					'form' => array(), // 後でPOSTとマージするデータ
				);

			$uploaded = explode(',', $val->validated('uploaded'));

			$tmp_dir = Config::get('site.temp_dir');
			$tmp_files = array_filter(scandir($tmp_dir), function($filename){
								return preg_match('/^[0-9a-fA-F]{32,32}\..+$/', $filename);
							});
Log::debug(print_r($tmp_files,true));

			// パッケージらしい物を探す
			foreach ($uploaded as $hash)
			{
				$match_file = 
					array_filter(scandir($tmp_dir), function($filename) use ($hash){
									return preg_match('/^'.$hash.'\.(zip|lzh|as|hsp)$/', $filename);
								});
Log::debug(print_r($match_file,true));
				if (!empty($match_file))
				{
					$package['path'] = reset($match_file);
					break; // まあ、一番最初に見つかるでしょ
				}
			}

			// スクリーンショットらしい物を探す
			foreach ($uploaded as $hash)
			{
				$match_file = 
					array_filter(scandir($tmp_dir), function($filename) use ($hash){
									return preg_match('/^'.$hash.'\.(jpg|png|bmp|gif)$/', $filename);
								});
Log::debug(print_r($match_file,true));
				if (!empty($match_file))
				{
					$package['ss'][] = reset($match_file);
				}
			}

			if (!empty($package['path']))
			{
				// パッケージから情報を取得

				if (preg_match('/^.+?\.(as|hsp)$/', $package['path']))
				{
					// 仕様は
					//   http://www.onionsoft.net/hsp/v33/doclib/HSP%20Document%20Library/hdl_usage.htm
					//     の「ドキュメント付けされたヘッダファイル」
					//   http://www.onionsoft.net/hsp/v33/doclib/HSP%20Document%20Library/HS_BIBLE.txt
					// を参考
					// 
					// HS_BIBLE.txt
					// > フィールドタグには、下記の種類があります。
					// > 
					// >   (タグ)    (内容)
					// > ・%index    シンボル名, 見出し
					// > ・%prm      パラメータリスト, パラメータ説明文
					// > ・%inst     解説文
					// > ・%sample   サンプルスクリプト
					// > ・%href     関連項目
					// > ・%dll      使用プラグイン/モジュール
					// > ・%ver      バージョン
					// > ・%date     日付
					// > ・%author   著作者
					// > ・%url      関連 URL
					// > ・%note     備考 (補足情報等)
					// > ・%type     タイプ
					// > ・%group    グループ
					// > ・%port     対応環境
					// > ・%portinfo 移植のヒント

					// とりあえず、仕様を斜め読んだ感じで実装
					// あとで直そう @todo
					// 1. '/*' から '%*/' までを全て取得
					// 2. 行頭 '%' から 次の '%' までを取得し、タグと内容に分ける
					// 3. 内容をtrim

					$tmp = @ file_get_contents($tmp_dir.$package['path']) ?: '';
					$tmp = mb_convert_encoding($tmp, 'UTF-8', 'SJIS-win');
					$tmp = str_replace("\r", "\n", str_replace("\r\n", "\n", $tmp));
Log::debug($tmp_dir.$package['path']);

					$fields = array();
					if (preg_match_all('=/\*.+?\*/=ms', $tmp, $m))
					{
						foreach ($m[0] as $block_comments)
						{
							if (preg_match_all('=^(%[^%][^\s\n]+)\s*\n((?:(?!\n%).)*)=ms',
							                   $block_comments, $mm))
							{
								for ($i = 0; $i < count($mm[0]); ++$i)
								{
									if ('%rem' == strtolower($mm[1][$i])) {
										break;
									}
									$fields[] = array(
											'tag' => strtolower($mm[1][$i]),
											'text' => trim($mm[2][$i]),
										);
								}
							}
						}
					}
Log::debug(print_r($fields,true));

					foreach ($fields as $field)
					{
						switch ($field['tag'])
						{
						case '%index':
							break 2;
						case '%url';
							$package['form']['url'] = $field['text'];
							break;
						case '%ver';
							$package['form']['version'] = $field['text'];
							break;
						case '%inst';
							$package['form']['description'] = $field['text'];
							break;
						case '%dll';
							$package['form']['title'] = $field['text'];
							break;
						}
					}

					if ($package_type = Model_Package_Type::query()
											->where('name', 'モジュール')
											->get_one())
					{
						$package['form']['package_type'] = $package_type->id;
					}
				}
Log::debug(print_r($package,true));

				$data['status'] = 'success';
	
				// セッションに保存
				Session::set('package', $package);
			}
			else
			{
				$data['status'] = 'error';
				$data['message'] = "パッケージとして認識できるファイルが見当たりませんでした。\n"
				                 . "圧縮ファイルは zip と lzh 、ソースファイルは .as と .hsp がパッケージとして認識されます。";
			}
		}
		else
		{
			$data['status'] = 'error';

			foreach ($val->error() as $field => $error)
			{
				$data['message'] .= $error->get_message() . "\n";
			}
		}

		if (Input::is_ajax())
		{
			$json = Format::forge($data)->to_json();
			$headers = array (
				'Pragma'            => 'no-cache',
			);
			return Response::forge($json, 200, $headers);
		}
		else
		{
		}
	}
}
