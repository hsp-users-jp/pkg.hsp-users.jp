<?php

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
		echo "0  * * * * oil cron r cron:clean\n";
		echo "0  * * * * oil cron r cron:scan\n";
		echo "30 0 * * * oil cron r cron:report\n";
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
		echo "\n===========================================";
		echo "\nRunning task [Cron:Clean]";
		echo "\n-------------------------------------------\n\n";

		 // 一定期間たっても一時フォルダに置かれたままのアップロードされたファイルを削除
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
