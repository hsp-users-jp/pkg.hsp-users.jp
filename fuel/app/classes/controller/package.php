<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014-2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Package extends Controller_Base
{

	public function action_list()
	{
		switch (Input::get('sort'))
		{
		case 'recent': // 最近の更新順
			$query
				= Model_Package::order_by_recent_update();
			break;
		case 'popular': // 人気のダウンロード順
			$query
				= Model_Package::order_by_popular();
			break;
		default:
			$query
				= Model_Package::query();
		}

		$query = $query->related('user');

		$pagination = Pagination::forge('page', array(
				'total_items' => $query->count(),
				'uri_segment' => 'page',
			));

		$query = $query
				->rows_offset($pagination->offset)
				->rows_limit($pagination->per_page);

		$data['rows'] = $query->get();
	//	$data['pagination'] = $pagination; // タグを出力するのでエスケープ処理させないため set_safe で追加

		$this->template->title = 'パッケージ一覧';
		$this->template->breadcrumb = array( '/' => 'トップ', '' => $this->template->title );
		$this->template->content = View::forge('package/list', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	public function action_detail($package_id)
	{
		$package_revisions
			= Model_Package::find_revision($package_id);
		if (!$package_revisions)
		{
			throw new HttpNotFoundException;
		}

		Asset::add_path('images/', 'img');

		$package = null;
		$lastest_version = null;
		$revisions = array();
		foreach ($package_revisions as $revision) // 新しい履歴から列挙される
		{
			if (!$lastest_version)
			{
				$lastest_version = $revision;
			}
			if (!$package &&
				(!Input::get('v') ||
				 $revision->version == Input::get('v')))
			{ // 指定したバージョン or 最新を取得
				$package = $revision;
			}
			array_push($revisions, array(
					'version'     => $revision->version,
					'revision_id' => $revision->revision_id,
					'date'        => $revision->updated_at ?: $revision->created_at,
					'deleted'     => !is_null($revision->deleted_at),
				));
		}
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

//		$package->screenshots;

		$data['package'] = Prop::forge(array(
								'current'  => $package,
								'lastest'  => $lastest_version,
								'versions' => $revisions,
							));

		$hsp_categories
			= Model_Hsp_Category::query()
				->get();
		$data['hsp_categories'] = $hsp_categories;

		$dummy = array('summary'=>Model_Working_Report::StatusUnknown,'detail'=>array());
		$working_requirements
			= Model_Working_Requirement::get_requirements($package->revision_id);
		$package_supports = array();
		foreach ($hsp_categories as $hsp_category)
		{
			$package_supports[$hsp_category->id] = array(
					Arr::get($working_requirements, $hsp_category->id, $dummy), // 要求環境
					$dummy // ユーザーの動作報告
				);
		}
		$data['package_supports'] = $package_supports;

		$data['is_editable']     = $lastest_version->revision_id == $package->revision_id;
		$data['is_super_admin']  = Auth::is_super_admin();
		$data['is_author']       = Auth::is_login_user($package->user_id) ||
		                           $data['is_super_admin'];

		$this->template->title = $package->name;
		$this->template->breadcrumb = array( '/' => 'トップ', 'package' => 'パッケージ一覧', '' => $this->template->title );
		$this->template->content = View::forge('package/detail', $data);
		$this->template->js = View::forge('package/detail.js', $data);
	}

	public function action_requirement($package_revision_id)
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

		$dummy = array('summary'=>Model_Working_Report::StatusUnknown,'detail'=>array());
		$working_requirements
			= Model_Working_Requirement::get_requirements($package_revision_id);
		$package_supports = array();
		foreach ($hsp_categories as $hsp_category)
		{
			$package_supports[$hsp_category->id] = array(
					Arr::get($working_requirements, $hsp_category->id, $dummy), // 要求環境
					$dummy // ユーザーの動作報告
				);
		}
		$data['package_supports'] = $package_supports;

		if (Input::is_ajax())
		{
			return View::forge('package/requirement.ajax', array('data' => $data));
		}

		$package = Model_Package::find($package_revision_id);
		$package_id = $package ? 'package/' . strval($package->id) : '';
		$package_name = $package ? $package->name : '';

		$this->template->title = $package_name . ' の動作環境';
		$this->template->breadcrumb = array( '/' => 'トップ', 'package' => 'パッケージ一覧',
		                                    $package_id => $package_name,
		                                    '' => $this->template->title );
		$this->template->content = View::forge('package/requirement', array('data' => $data));
	}

	public function action_download($package_revision_id)
	{
		$package
			= Model_Package::find($package_revision_id);
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$path = Config::get('app.package_dir') . $package->path;
		$original_name = $package->original_name;

		// ファイルの存在チェック
		if (!file_exists($path))
		{
			Log::warning(sprintf('package verson #%d(%s) "%s" not found', $package->id, $package->revision, $path));
			throw new HttpNotFoundException;
		}

		// ダウンロード数をpiwikでカウント
		if ('tracked' != Input::server('QUERY_STRING') &&
			Config::get('piwik.enable'))
		{
			include_once(implode(DS, array(APPPATH, 'vendor', 'PiwikTracker.php')));
			$t = new PiwikTracker(Config::get('piwik.siteid'), Config::get('piwik.url'));
			$t->setTokenAuth(Config::get('piwik.token'));
			$t->setUrl(Uri::current());
		//	$t->enableCookies(Input::server('HTTP_HOST'));
			$t->doTrackAction(Uri::current(), 'download');
		}

		File::download($path, $original_name);
	}

	private function setup_new_or_update_form($package_id = false)
	{
		$is_update = false !== $package_id;

		$data['is_update'] = $is_update;

		$package = null;

		// パッケージIDが指定されていたら(＝更新時)取得
		if ($is_update)
		{
			$package = Model_Package::find_by_id($package_id);
			if (!$package)
			{
				throw new HttpNotFoundException;
			}
		}

		$data['is_update'] = $is_update;
		$data['package']   = $package;
		$data['uploaded']  = array();

		// エラー内容など
		$data['state'] = array();

		// ライセンスの一覧
		$data['license_list'] = array('' => 'ライセンスを指定してください');
		foreach (Model_License::query()
					->get() as $row)
		{
			$data['license_list'][$row->id] = $row->name;
		}

		// パッケージ種別の一覧
		$data['package_type_list'] = array();
		foreach (Model_Package_Type::query()
					->get() as $row)
		{
			$data['package_type_list'][$row->id] = $row->name;
		}

		// 対応状況
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
//Log::debug(print_r($hsp_specs,true));
//Log::debug(print_r($_POST,true));
		if (Input::post())
		{
			if ($val->run())
			{
				$package_path = '';
				$ss_path      = array();

				try
				{
					$package_path = Session::get('package.path');
					$ss_path = Session::get('package.ss', array());
					
					if (!empty($package_path))
					{
						try
						{
							DB::start_transaction();

							$tmp_dir     = Config::get('app.temp_dir');
							$package_dir = Config::get('app.package_dir');
							$ss_dir      = DOCROOT.Config::get('app.screenshot_dirname').'/';

							//File::create_dir(dirname(rtrim($package_dir, '/')), basename(rtrim($package_dir, '/')));

Log::debug(__FILE__.'('.__LINE__.')');
							// パッケージを一時ディレクトリから移動
							@ File::rename($tmp_dir.$package_path, $package_dir.$package_path);
							if (!file_exists($package_dir.$package_path))
							{
								Log::error(sprintf('rename %s -> %s', $tmp_dir.$package_path, $package_dir.$package_path));
								throw new \Exception('パッケージの保存が出来ませんでした');
							}

							// レコードの登録

Log::debug(__FILE__.'('.__LINE__.')');
							if (!$package)
							{
Log::debug(__FILE__.'('.__LINE__.')');
								$package = new Model_Package;
							}

							$package->name            = $val->validated('title');
							$package->path            = basename($package_path);
							$package->original_name   = Session::get('upload.'.pathinfo($package_path, PATHINFO_FILENAME).'.name',
							                                         basename($package_path));
							$package->version         = $val->validated('version');
							$package->url             = $val->validated('url');
							$package->description     = $val->validated('description');
							$package->license_id      = $val->validated('license');
							$package->package_type_id = $val->validated('package_type');

Log::debug(__FILE__.'('.__LINE__.')');
Log::debug(print_r($ss_path,true));
//Log::debug(print_r($package->screenshots,true));

							// 登録済みのスクリーンショットを削除
							$package_screenshots = $package->screenshots;
							foreach ($package_screenshots as $i => $ss)
							{
								if (Session::get('cancel.'.pathinfo($ss->name, PATHINFO_FILENAME)))
								{
									unset($package->screenshots[$i]);
								}
							}
Log::debug(__FILE__.'('.__LINE__.')');
//Log::debug(print_r($package->screenshots,true));
						//	$package->screenshots = Arr::reindex($package->screenshots);
							// スクリーンショットを保存
							foreach ($ss_path as $path)
							{
								$ss = new Model_Package_Screenshot;
								$ss->setImageFromTemp($path);
								$package->screenshots[] = $ss;
							}
Log::debug(__FILE__.'('.__LINE__.')');
//Log::debug(print_r($package->screenshots,true));

							$package->save();

Log::debug(__FILE__.'('.__LINE__.')');
							// サポート状況を保存
							foreach ($hsp_specs as $hsp_spec => $id)
							{
Log::debug(sprintf('$val->validated("%s")="%s","%s"',$hsp_spec,$val->validated($hsp_spec),Input::post($hsp_spec)));
								if (Input::post($hsp_spec))
								{
									$working_requirement = new Model_Working_Requirement;
									$working_requirement->package_revision_id   = $package->revision_id;
									$working_requirement->hsp_specification_id = $id;
									$working_requirement->status       = Model_Working_Report::StatusSupported;
									$working_requirement->comment      = '';
									$working_requirement->save();

								//	$package->working_requirements[] = $working_requirement;
								}
							}

							DB::commit_transaction();

							// セッションを削除
							Session::delete('upload');
							Session::delete('package');
							Session::delete('cancel');

							if ($is_update)
							{
								Messages::success('パッケージを更新しました');
							}
							else
							{
								Messages::success('パッケージを追加しました');
							}
		
							return Response::redirect(sprintf('package/%d', $package->id));
						}
						catch (\Exception $e)
						{
Log::debug(__FILE__.'('.__LINE__.')');
							Messages::error($e->getMessage(), 'エラーが発生しました');

							// 移動したファイルを戻す
							if (file_exists($package_dir.$package_path))
							{
								Log::info(sprintf('rollback rename %s -> %s', $package_dir.$package_path, $tmp_dir.$package_path));
								@ File::rename($package_dir.$package_path, $tmp_dir.$package_path);
							}
							foreach ($ss_path as $path)
							{
								if (file_exists($ss_dir.$path))
								{
									Log::info(sprintf('rollback rename %s -> %s', $ss_dir.$path, $tmp_dir.$path));
									@ File::rename($ss_dir.$path, $tmp_dir.$path);
								}
							}

							if (isset($package_screenshots))
							{
								$package->screenshots = $package_screenshots;
							}

							// 未決のトランザクションクエリをロールバックする
							DB::rollback_transaction();
						}
					}
					else
					{
Log::debug(__FILE__.'('.__LINE__.')');
						Messages::error('入力項目を確認してください。');
						$data['state']['package'] = 'has-error';
					}
				}
				catch (Exception $e)
				{
Log::debug(__FILE__.'('.__LINE__.')');
					Messages::error($e->getMessage());
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
	
				Messages::error($errors);
			}
		}

		// 登録済みの情報をマージ
		if ($package && $package->screenshots)
		{
Log::debug(print_r($package->screenshots,true));
			$ss_dir = Config::get('app.screenshot_dirname').'/';
			$ss_path = DOCROOT.$ss_dir;

			foreach ($package->screenshots as $ss)
			{
				if (!Session::get('cancel.'.pathinfo($ss->name, PATHINFO_FILENAME)))
				{
					$data['uploaded'][] =
							array(
								'field'=> 'ss',
								'name' => $ss->name,
								'size' => filesize($ss_path.$ss->name),
								'url' => Uri::create($ss_dir.$ss->name),
							);
				}
			}
		}
		if (Input::post())
		{
			// 送信済みのファイルの情報
			$tmp_dir = Config::get('app.temp_dir');
			$package_uploaded = false;
			foreach (Session::get('upload', array()) as $hash => $file)
			{
				$fname = sprintf('%s.%s', $hash, pathinfo($file['name'], PATHINFO_EXTENSION));
				$path  = $tmp_dir . $fname;

				switch ($file['field'])
				{
				case 'package':
					if (!$package_uploaded &&
						Session::get('package.path') == $fname &&
						file_exists($path))
					{
						$data['uploaded'][] =
								array(
									'field'=> $file['field'],
									'name' => $file['name'],
									'size' => filesize($path),
									'url'  => '',
								);
						$package_uploaded = true;
					}
					break;
				case 'ss':
					if (file_exists($path))
					{
						$data['uploaded'][] =
								array(
									'field'=> $file['field'],
									'name' => $file['name'],
									'size' => filesize($path),
									'url'  => '',
								);
					}
					break;
				}
			}
			if (!$package_uploaded)
			{ // パッケージが指定されていないのでエラーとする
				$data['state']['package'] = 'has-error';
			}
		}
Log::debug(print_r($data['uploaded'],true));

		return $data;
	}

	public function action_new()
	{
		if (!Input::post())
		{
			// セッションを削除というかリセット
			Session::delete('upload'); // あとで全部まとめて package.hoge にする @todo
			Session::delete('package');
			Session::delete('cancel');
		}

		$data = $this->setup_new_or_update_form();

		$data['title'] = 'パッケージの追加';

		$this->template->title = $data['title'];
		$this->template->breadcrumb = array( '/' => 'トップ', 'package' => 'パッケージ一覧', '' => $this->template->title );
		$this->template->content = View::forge('package/upload', $data);
		$this->template->js = View::forge('package/upload.js', $data);
	}

	public function action_update($package_id)
	{
		if (!Input::post())
		{
			// セッションを削除というかリセット
			Session::delete('upload');
			Session::delete('package');
			Session::delete('cancel');
		}

		$data = $this->setup_new_or_update_form($package_id);

		$data['title'] = 'パッケージの更新';

		$this->template->title = $data['title'];
		$this->template->breadcrumb = array( '/' => 'トップ', 'package' => 'パッケージ一覧', '' => $this->template->title );
		$this->template->content = View::forge('package/upload', $data);
		$this->template->js = View::forge('package/upload.js', $data);
	}

	public function post_edit($package_id)
	{
		$data['csrf_token'] = Security::fetch_token(); // Ajax処理しているのでトークンを更新しないとうまく行かない

		$val = Validation::forge('val');
		$val->add('name', '')
			->add_rule('required');
		$val->add('value', '')
			->add_rule('required');

		if (!$val->run())
		{
			$data['message'] = '不正な処理が行われました。';
			Log::error($data['message']);
		}
		else
		{
			$package
				= Model_Package::find_by_id($package_id);
			if (!$package)
			{
				// パラメータが無ければ何もしない
				$data['message'] = 'パッケージが見つかりませんでした。';
				Log::error($data['message']);
			}
			else
			{
				try
				{
					DB::start_transaction();

					switch ($val->validated('name'))
					{
					case 'name':
						$package->name = $val->validated('value');
						$package->overwrite();
						break;
					case 'description':
						$package->description = $val->validated('value');
						$package->overwrite();
						break;
					case 'version':
						$package->version = $val->validated('value');
						$package->overwrite();
						break;
					case 'type':
						$package_type
							= Model_Package_Type::query()
								->where('id', (int)$val->validated('value'))
								->get_one();
						if ($package_type)
						{
							$package->package_type_id = $package_type->id;
							$package->overwrite();
							$data['icon'] = $package_type->icon;
						}
						else
						{
							$data['message'] = 'データーが見つかりませんでした。';
							Log::error($data['message']);
						}
						break;
					case 'license':
						$license
							= Model_License::query()
								->where('id', (int)$val->validated('value'))
								->get_one();
						if ($license)
						{
							$package->license_id = $license->id;
							$package->overwrite();
							$data['license'] = array(
									'url' => $license->url,
									'description' => $license->description
								);
						}
						else
						{
							$data['message'] = 'データーが見つかりませんでした。';
							Log::error($data['message']);
						}
						break;
					}

					DB::commit_transaction();

					if (!isset($data['message']))
					{
						$data['success'] = 'success';
					}
				}
				catch (\Exception $e)
				{
					$data['message'] = 'データーベース処理でエラーが発生しました。';

					$errors = array($e->getMessage());
					Log::error($data['message']."\n".implode("\n", $errors));

					// 未決のトランザクションクエリをロールバックする
					DB::rollback_transaction();
				}
			}
		}

		$json = Format::forge($data)->to_json();
		$headers = array (
			'Pragma'            => 'no-cache',
		);
		return Response::forge($json, 200, $headers);
	}

	public function action_remove($package_revision_id, $type = null)
	{
		$package
			= Model_Package::find($package_revision_id);
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$data['package'] = $package;

		$val = Validation::forge('val');
		$val->add('id', '')
			->add_rule('required')
			->add_rule('match_value', $package_revision_id);

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					DB::start_transaction();

					// リビジョン数を調べ、１だったらallと同じ扱いにする
					if (Model_Package::count_of_revision($package->id) <= 1)
					{
						$type = 'all';
					}

					switch ($type)
					{
					case 'all':
						if (null === ($package_base = Model_Package_Base::find($package->id)))
						{
							throw new \Exception(sprintf('package base not found %d', $package->id));
						}
						$package_base->delete();
						break;
					case null:
						$package->delete();
						break;
					default:
						throw new \Exception(sprintf('invalid argument %s', $type));
					}

					DB::commit_transaction();
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生しました');

					// 未決のトランザクションクエリをロールバックする
					DB::rollback_transaction();
				}
			}

			return Response::redirect('package/' . $package->id);
		}

		if (Input::is_ajax())
		{
			return View::forge('package/remove.ajax', array('data' => $data));
		}

		$package_id = $package ? 'package/' . strval($package->id) : '';
		$package_name = $package ? $package->name : '';

		$this->template->title = $package_name . ' の削除';
		$this->template->breadcrumb = array( '/' => 'トップ', 'package' => 'パッケージ一覧',
		                                    $package_id => $package_name,
		                                    '' => $this->template->title );
		$this->template->content = View::forge('package/remove', array('data' => $data));
	}

	public function action_cure($package_revision_id)
	{
		$package
			= Model_Package::find($package_revision_id);
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$data['package'] = $package;

		$val = Validation::forge('val');
		$val->add('id', '')
			->add_rule('required')
			->add_rule('match_value', $package_revision_id);

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					if ($package->deleted_at)
					{
						DB::start_transaction();
						$package->restore();
						DB::commit_transaction();
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生しました');

					// 未決のトランザクションクエリをロールバックする
					DB::rollback_transaction();
				}
			}

			return Response::redirect('package/' . $package->id);
		}

		if (Input::is_ajax())
		{
			return View::forge('package/cure.ajax', array('data' => $data));
		}

		$this->template->title = 'Package &raquo; Cure';
		$this->template->content = View::forge('package/cure', array('data' => $data));
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

			$ss = Session::get('package.ss', array());

			// JSONで返答するために正常にアップロードきたファイルの一覧を作る
			foreach (Upload::get_files() as $file)
			{
				$data['success'][] = pathinfo($file['saved_as'], PATHINFO_FILENAME);
				// あとで使うのでセッションに保存
				Session::set('upload.'.pathinfo($file['saved_as'], PATHINFO_FILENAME),
				             array('name'  => $file['name'],
				                   'field' => $file['field']));
				// スクリーンショットの場合のみ保存
				if ('ss' == $file['field'])
				{
					$ss[] = $file['saved_as'];
				}
			}

			// スクリーンショットの場合のみ保存
			if (!empty($ss))
			{
				Session::set('package.ss', $ss);
			}

			$data['status'] = 'success';
		}

Log::debug(print_r(Session::get('upload'),true));
Log::debug(print_r(Session::get('package'),true));

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

	// アップロード済みファイルの取り消し
	public function post_cancel()
	{
		$data = array('status' => '',
		              'message' => '',
		              'csrf_token' => Security::fetch_token()); // Ajax処理しているのでトークンを更新しないとうまく行かない

		// バリデーション対象のフィールドを指定
		$val = Validation::forge('val');
		$val->add('cancel', '')
			->add_rule('min_length', 1)
			->add_rule('required');

		if ($val->run())
		{
			$cancel_file = $val->validated('cancel');
			foreach (Session::get('upload', array()) as $hash => $fname)
			{
				if ($cancel_file == $fname)
				{
					$rm_file = sprintf('%s%s.%s', Config::get('app.temp_dir'),
					                   $hash, pathinfo($fname, PATHINFO_EXTENSION));
					Log::info('remove ' . $rm_file . ' by user');
					File::delete($rm_file);
					$data['status'] = 'success';
					$data['canceled'] = $hash;
					break;
				}
			}
			// パッケージ更新時にスクリーンショットを削除するために対象をセッションに保存する
			if (empty($data['status']))
			{
				Session::set('cancel.'.pathinfo($cancel_file, PATHINFO_FILENAME), true);
			}
		}

		$json = Format::forge($data)->to_json();
		$headers = array (
			'Pragma'            => 'no-cache',
		);
		return Response::forge($json, 200, $headers);
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
				//	'ss' => array(),
					'form' => array(), // 後でPOSTとマージするデータ
				);

			$uploaded = explode(',', $val->validated('uploaded'));

			$tmp_dir = Config::get('app.temp_dir');
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
//			foreach ($uploaded as $hash)
//			{
//				$match_file = 
//					array_filter(scandir($tmp_dir), function($filename) use ($hash){
//									return preg_match('/^'.$hash.'\.(jpg|png|bmp|gif)$/', $filename);
//								});
//Log::debug(print_r($match_file,true));
//				if (!empty($match_file))
//				{
//					$package['ss'][] = reset($match_file);
//				}
//			}

			if (!empty($package['path']))
			{
				// ファイルからタイトルなどを解析
				$pkg = Model_Package_Upload::forge($tmp_dir.$package['path']);
				$package['form'] = $pkg->analyze();

Log::debug(print_r($package,true));

				$data['form'] = $package['form'];
				$data['status'] = 'success';
	
				// セッションに保存
				Session::set('package.path', $package['path']);
				Session::set('package.form', $package['form']);
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
