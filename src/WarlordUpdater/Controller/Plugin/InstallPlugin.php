<?php
namespace WarlordUpdater\Controller\Plugin;


class InstallPlugin extends  BasePlugin
{
	public function install()
	{
		$config = $this->controller->getServiceLocator()->get('config');
		if(! isset($config['WarlordUpdater']['installDir'])) {
			return 'Nothing to install.';
		}
		$installDirs = $config['WarlordUpdater']['installDir'];
		$env = $this->controller->params('env');
		$env = $env ?  : 'Development';
		
		$message = '<h2>Install on ' . $env . ' ' . $config["db"]["database"] .
				 '.</h2>';
		foreach($installDirs as $dir) {
			try {
				$sql = $this->controller->loader()->loadDirectory($dir);
				$message .= $this->_runSql($dir, $sql);
			} catch(\Exception $e) {
				$message .= $e->getMessage() . '<br />';
				return $message;
			}
		}
		
		return $message;
	}
	
	private function _runSql($dir, $sql)
	{
		$success_patches = 0;
		$message = '';
		foreach($sql as $filename) {
			$patch_file = file_get_contents($dir . '/' . $filename);
			try {
				$this->getUpdateTable()->query($patch_file);
				$success_patches ++;
				$message .= "Patch <b>{$filename}</b> successfully installed. <br />";
			} catch(\Exception $e) {
				$message .= "<b>PATCH ERROR:</b><br />";
				$message .= "<b>Filename:</b> {$filename} <br />";
				$message .= "<b>SQL:</b> {$patch_file} <br />";
				$message .= "<b>ERROR:</b> {$e->getMessage()} <br />";
	
				return $message;
			}
		}
		return $message .
		" SQL install successfully complete. <b>{$success_patches}</b> patches installed<br />";
	}
}