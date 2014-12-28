<?php
namespace WarlordUpdater\Controller\Plugin;

use WarlordUpdater\Model\Update;

class UpdatePlugin extends  BasePlugin
{
	private $_sql = array();
	private $_sys = array();
	private $_skips = array();
	private $_sm;
	
	public function update()
	{
		$this->_sm = $this->controller->getServiceLocator();
		$config = $this->_sm->get('config');
		
		if(! isset($config['WarlordUpdater']['updateDir'])) {
			return 'Nothing to update.';
		}
		$updateDirsSql = $config['WarlordUpdater']['updateDir']['sql'];
		$updateDirsSys = $config['WarlordUpdater']['updateDir']['sys'];
		$env = $this->controller->params('env');
		$env = $env ?  : 'Development';
	
		$message = '<h2>Update on ' . $env . ' ' . $config["db"]["database"] .
		'.</h2>';
	
		try {
			foreach($updateDirsSql as $sqlDir) {
				$this->_sql[$sqlDir] = $this->controller->loader()->loadDirectory($sqlDir);
			}
			foreach($updateDirsSys as $sysDir) {
				$this->_sys[$sysDir] = $this->controller->loader()->loadDirectory($sysDir);
			}
		} catch(Zend\Db\Adapter\Exception $e) {
			$message .= $e->getMessage() . '<br />';
			return $message;
		}
	
		$updates = $this->getUpdateTable()->fetchAll();
		foreach($updates as $update) {
			$this->_skips[] = $update->patch_file;
		}
	
		$message .= $this->_runUpdateSql();
		$message .= $this->_runUpdateSys();
	
		return $message;
	}
	
	private function _runUpdateSql()
	{
		$success_patches = 0;
		$message = '';
		foreach($this->_sql as $dir => $files) {
			foreach($files as $filename) {
				$patch_file = $dir . '/' . $filename;
				if(in_array($patch_file, $this->_skips) === false) {
					try {
						$sql = file_get_contents($patch_file);
						$this->getUpdateTable()->query($sql);
	
						$update = new Update();
						$update->patch_file = $patch_file;
						$update->created_at = date('Y-m-d H:i:s');
						$this->getUpdateTable()->saveUpdate($update);
						$success_patches ++;
						$message .= "Patch <b>{$filename}</b> success installed. <br />";
					} catch(\Zend\Db\Sql\Exception $e) {
						$message .= "<b>PATCH ERROR:</b><br />";
						$message .= "<b>Filename:</b> {$e->filename} (on line {$e->line})<br />";
						$message .= "<b>SQL:</b> {$e->sql} <br />";
						$message .= "<b>ERROR:</b> {$e->getMessage()} <br />";
	
						return $message;
					}
				}
			}
		}
	
		$message .= "SQL update success complete. <b>{$success_patches}</b> patches installed <br /><br />";
		return $message;
	}
	
	private function _runUpdateSys()
	{
		$success_patches = 0;
		$message = '';
		foreach($this->_sys as $dir => $files) {
			foreach($files as $filename) {
				$patch_file = $dir . '/' . $filename;
				if(in_array($patch_file, $this->_skips) === false) {
					if(! file_exists($patch_file)) {
						$message .=	"File doesn't exist " . $patch_file;
						return $message;
					}
						
					try {
						include $patch_file;
	
						$update = new Update();
						$update->patch_file = $patch_file;
						$update->created_at = date('Y-m-d H:i:s');
						$this->getUpdateTable()->saveUpdate($update);
						$success_patches ++;
						$message .= "Patch <b>{$filename}</b> success installed. <br />";
					} catch(\Exception $e) {
						$message .= "<b>PATCH ERROR:</b><br />";
						$message .= "<b>Filename:</b> {$patch_file} <br />";
						$message .= "<b>ERROR:</b> {$e->getMessage()} <br />";
	
						return $message;
					}
				}
			}
		}
	
		$message .= "System update success complete. <b>{$success_patches}</b> patches installed<br />";
		return $message;
	}
	
}

?>