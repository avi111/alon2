<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/09/2017
 * Time: 22:51
 */

namespace log;


class log
{
	static public function log($msg, $caller = null, $blog = false){
		//
	}

	public function __invoke($msg, $caller = null)
	{
		if(is_array($msg) || is_object($msg)){
			$msg=var_export($msg,true);
		}

		$request=isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';

		$template = implode(' ; ', array(
			date('m/d/Y h:i:s',strtotime('now')),
			$request,
			$msg
		));

		$caller = sanitize_title($caller) ?? false;

		if ($caller) {
			$upload_dir = wp_upload_dir();

			$baseDir = $upload_dir['basedir'];
			$baseDir = explode('sites', $baseDir)[0];

			$logsFolder = sprintf("%s/logs", $baseDir);
			if (!file_exists($logsFolder)) {
				mkdir(sprintf($logsFolder));
			}

			$path = sprintf("%s/%s%s", $logsFolder, $caller, '.log');

			$myfile = fopen($path, "a") or die("Unable to open file!");
			$txt = $template;
			fwrite($myfile, $txt . "\n");
			fclose($myfile);
		} else {
			error_log($template);
		}
	}

}