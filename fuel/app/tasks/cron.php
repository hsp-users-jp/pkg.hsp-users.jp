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
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
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
		echo "0  * * * * oil r cron:clean\n";
		echo "0  * * * * oil r cron:scan\n";
		echo "30 0 * * * oil r cron:report\n";
		echo "-- 8< ------------------\n";
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r cron:clean "arguments"
	 *
	 * @return string
	 */
	public function clean($args = NULL)
	{
	//	echo "\n===========================================";
	//	echo "\nRunning task [Cron:Clean]";
	//	echo "\n-------------------------------------------\n\n";

		// 一定期間たっても一時フォルダに置かれたままのアップロードされたファイルを削除

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
		}

		// など
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r cron:scan "arguments"
	 *
	 * @return string
	 */
	public function scan($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Cron:Scan]";
		echo "\n-------------------------------------------\n\n";

		 // アップロード済みのファイルのウイルススキャン
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r cron:report "arguments"
	 *
	 * @return string
	 */
	public function report($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning task [Cron:Report]";
		echo "\n-------------------------------------------\n\n";

		// 何かのレポートをメール送信
	}

}
/* End of file tasks/cron.php */
