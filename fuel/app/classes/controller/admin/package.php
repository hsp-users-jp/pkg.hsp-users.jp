<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Admin_Package extends Controller_Base
{
	public function action_index()
	{
		$pagination = Pagination::forge('page', array(
				'total_items' => Model_Package::count(),
				'uri_segment' => 'page',
			));

		$data['packages'] = array();
		foreach (Model_Package::query()
				->related(array('type', 'user', 'base'))
				->offset($pagination->offset)
				->limit($pagination->per_page)
			//	->as_object('\\Auth\\Model\\Auth_User')
				->get() as $package)
		{
			$tmp = array(
					'id'         => $package->id,
					'name'       => $package->name,
					'type'       => $package->type,
					'version'    => $package->version,
					'updated_at' => $package->updated_at,
					'user'       => $package->user,
					'deleted'    => is_null($package->base) ||
					                !is_null($package->base->deleted_at),
				);
			$data['packages'][] = $tmp;
		}

		$data['packages'] = Prop::forge($data['packages']);

		$data["subnav"] = array('user'=> 'active' );
		$this->template->title = 'Admin &raquo; Package';
		$this->template->content = View::forge('admin/package/index', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	private function destroy_or_cure($package_id, $view, $work)
	{
		$package
			= Model_Package::query()
				->where('id', $package_id)
				->get_one();
		if (!$package)
		{
			throw new HttpNotFoundException;
		}

		$val = Validation::forge('val');
		$val->add('id', '')
			->add_rule('required')
			->add_rule('match_value', $package_id);

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					DB::start_transaction();

					if (call_user_func($work, $package))
					{
						DB::commit_transaction();
					}
					else
					{
						Messages::error('パッケージ状態の更新に失敗しました');

						// 未決のトランザクションクエリをロールバックする
						DB::rollback_transaction();
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage());

					// 未決のトランザクションクエリをロールバックする
					DB::rollback_transaction();
				}
			}

			Response::redirect('admin/package');
		}

		$data['id'] = $package->id;
		$data['name'] = $package->name;

		if (Input::is_ajax())
		{
			return View::forge('admin/package/confirm.ajax', array('view' => $view, 'data' => $data));
		}

		$this->template->title = '';
		$this->template->content = View::forge('admin/package/confirm', array('view' => $view, 'data' => $data));
	}

	public function action_destroy($package_id)
	{
		return $this->destroy_or_cure(
			$package_id,
			'admin/package/destroy',
			function($package){
				if ($package->destroy())
				{
					Messages::success(
						sprintf('%d:"%s" は削除されました',
							$package->id, $package->name));
					return true;
				}
				else
				{
					return false;
				}
			});
	}

	public function action_cure($package_id)
	{
		return $this->destroy_or_cure(
			$package_id,
			'admin/package/cure',
			function($package){
				if ($package->cure())
				{
					Messages::success(
						sprintf('%d:"%s" は復元されました',
							$package->id, $package->name));
					return true;
				}
				else
				{
					return false;
				}
			});
	}
}
