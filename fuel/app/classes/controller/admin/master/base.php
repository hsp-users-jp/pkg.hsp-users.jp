<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Admin_Master_Base extends Controller_Base
{
	static protected $_model = '';
	static protected $_title = '';

	static protected function rows_count()
	{
		return call_user_func(array(static::$_model, 'count'));
	}

	static protected function row($id)
	{
		return
			call_user_func(array(static::$_model, 'query'))
				->where('id', $id)
				->get_one();
	}

	static protected function rows($per_page, $offset)
	{
		return
			call_user_func(array(static::$_model, 'query'))
				->rows_offset($offset)
				->rows_limit($per_page)
				->get();
	}

	static protected function cols()
	{
		$cols = call_user_func(array(static::$_model, 'properties'));
		unset($cols['deleted_at']);
		unset($cols['updated_at']);
		unset($cols['created_at']);
		return array_keys($cols);
	}

	public function action_index()
	{
		if (!static::$_model)
		{ // 基底クラスは表示できないので
			throw new HttpNotFoundException;
		}

		$pagination = Pagination::forge('page', array(
				'total_items' => self::rows_count(),
				'uri_segment' => 'page',
			));

		$data['title'] = static::$_title;
		$data['cols'] = self::cols();
		$data['rows'] = self::rows($pagination->per_page, $pagination->offset);
	//	$data['pagination'] = $pagination; // タグを出力するのでエスケープ処理させないため set_safe で追加

		$this->template->title = static::$_title;
		$this->template->content = View::forge('admin/master/_index', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	public function action_new()
	{
		if (!static::$_model)
		{ // 基底クラスは表示できないので
			throw new HttpNotFoundException;
		}

		$data['title'] = static::$_title;
		$data['cols'] = self::cols();
		$data['state'] = array();

		$val = Validation::forge('val');

		foreach ($data['cols'] as $col)
		{
			if (false === array_search($col, array('id')))
			{
				$val->add($col, $col)
					->add_rule('required');
			}
		}

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					$model = new static::$_model;
					foreach ($data['cols'] as $col)
					{
						$model[$col] = $val->validated($col);
					}
					$model->save();
	
					Messages::success('追加しました');
	
					Response::redirect(Uri::segment_replace('*/*/*'));
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage());
				}
			}
			else
			{
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Messages::error($errors);
			}
		}

		$this->template->title = static::$_title;
		$this->template->content = View::forge('admin/master/_new', $data);
	}

	public function action_edit($id)
	{
		if (!static::$_model)
		{ // 基底クラスは表示できないので
			throw new HttpNotFoundException;
		}

		$data['title'] = static::$_title;
		$data['cols'] = self::cols();
		$data['row'] = self::row($id);
		$data['state'] = array();

		if (!$data['row'])
		{
			throw new HttpNotFoundException;
		}

		$val = Validation::forge('val');

		foreach ($data['cols'] as $col)
		{
			if (false === array_search($col, array('id')))
			{
				$val->add($col, $col)
					->add_rule('required');
			}
		}

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					$model = $data['row'];
					foreach ($data['cols'] as $col)
					{
						$model[$col] = $val->validated($col);
					}
					$model->save();
	
					Messages::success('更新しました');
	
					Response::redirect(Uri::segment_replace('*/*/*'));
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage());
				}
			}
			else
			{
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Messages::error($errors);
			}
		}

		$this->template->title = static::$_title;
		$this->template->content = View::forge('admin/master/_edit', $data);
	}

	public function action_remove($id)
	{
		if (!static::$_model)
		{ // 基底クラスは表示できないので
			throw new HttpNotFoundException;
		}

		$row = self::row($id);

		if (!$row)
		{
			throw new HttpNotFoundException;
		}

		if (Input::post())
		{
			try
			{
				$model = $row;
				$model->delete();
	
				Messages::success('削除しました');
	
				Response::redirect(Uri::segment_replace('*/*/*'));
			}
			catch (\Exception $e)
			{
				Messages::error($e->getMessage());
			}
		}

		$data['title'] = static::$_title;
		$this->template->title = static::$_title;
		$this->template->content = View::forge('admin/master/_remove', $data);
	}

}
