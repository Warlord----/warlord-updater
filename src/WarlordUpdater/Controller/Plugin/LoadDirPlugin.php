<?php
namespace WarlordUpdater\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class LoadDirPlugin extends  AbstractPlugin
{
	/**
	 * Get direcory content
	 *
	 * @param string $dir
	 * @return array
	 */
	public function loadDirectory($dir)
	{
		if(! file_exists($dir)) {
			throw new \Exception("file not found " . $dir);
		}
	
		if(! is_dir($dir)) {
			throw new \Exception("is not a dir " . $dir);
		}
	
		$content = array();
		$handle = opendir($dir);
		while(($file = readdir($handle)) !== false) {
			if($file != "." && $file != ".." && $file != ".svn") {
				$content[$file] = $file;
			}
		}
		return $content;
	}
}