<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

namespace Fuel\Tasks;

class Cron
{

	/**
	 * ヘルプを表示
	 *
	 * 使い方 (コマンドラインから):
	 *
	 * php oil r cron:help "arguments"
	 *
	 * @return string
	 */
	public function help($args = NULL)
	{
		echo "\nrun \"crontab -e\" and paste here!\n";
		echo "-- >8 ------------------\n";
		echo "# pkg.hsp-users.jp cron tasks\n";
		echo "0  * * * * oil r cron:account_expired\n";
		echo "0  * * * * oil r cron:clean\n";
		echo "0  * * * * oil r cron:scan\n";
		echo "30 0 * * * oil r cron:report\n";
		echo "-- 8< ------------------\n";
	}

	/**
	 * 仮登録状態のアカウントの期限切れ確認
	 *
	 * 使い方 (コマンドラインから):
	 *
	 * php oil r cron:account_expired
	 *
	 * @return string
	 */
	public function account_expired()
	{
		// 仮登録状態のアカウントの期限切れ確認
		\Log::info('running task "cron:account_expired"');

		try
		{
			\DB::start_transaction();

			foreach (\Model_User::find_account_expired() as $user)
			{
				\Model_User::ban($user->id);

				\Log::info('id:%d was account expired', $user->id);
			}

			\DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			\DB::rollback_transaction();
		
			\Log::error($e->getMessage());
		}
	}

	/**
	 * 一定期間たっても一時フォルダに置かれたままのアップロードされたファイルを削除
	 *
	 * 使い方 (コマンドラインから):
	 *
	 * php oil r cron:clean
	 *
	 * @return string
	 */
	public function clean()
	{
		// 一定期間たっても一時フォルダに置かれたままのアップロードされたファイルを削除
		\Log::info('running task "cron:clean"');

		$remove_limit = time() - 1 * 60 * 60; // 一時間以前のファイルを削除対象とする

		$area = \File::forge(array(
						'basedir'	=> \Config::get('app.temp_dir'),
						'use_locks'	=> true,
					));
		try
		{
			$dir = \File::read_dir('.', 0, array(
						'^[0-9a-fA-F]{32}\.*' => 'file',
					), $area);
			foreach ($dir as $file)
			{
				if ($area->get_time($file, 'modified') < $remove_limit)
				{
					if ($area->delete($file))
					{
						\Log::info(sprintf('delete "%s" from temporary.', $file));
					}
					else
					{
						\Log::error(sprintf('delete "%s"', $file));
					}
				}
			}
		}
		catch (\FileAccessException $e)
		{
			// 失敗したときの処理
			\Log::error($e->getMessage());
		}

		// など
	}

	/**
	 * アップロード済みのファイルのウイルススキャン
	 *
	 * 使い方 (コマンドラインから):
	 *
	 * php oil r cron:scan
	 *
	 * @return string
	 */
	public function scan($args = NULL)
	{
		// アップロード済みのファイルのウイルススキャン
		\Log::info('running task "cron:scan"');

		echo "\n===========================================";
		echo "\nRunning task [Cron:Scan]";
		echo "\n-------------------------------------------\n\n";

	}

	/**
	 * 何かのレポートをメール送信
	 *
	 * 使い方 (コマンドラインから):
	 *
	 * php oil r cron:report
	 *
	 * @return string
	 */
	public function report()
	{
		// 何かのレポートをメール送信
		\Log::info('running task "cron:report"');

		echo "\n===========================================";
		echo "\nRunning task [Cron:Report]";
		echo "\n-------------------------------------------\n\n";

	}

}
/* End of file tasks/cron.php */
