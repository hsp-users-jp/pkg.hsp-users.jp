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

}
