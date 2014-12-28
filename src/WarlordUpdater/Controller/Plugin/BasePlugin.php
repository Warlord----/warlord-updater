<?php
namespace WarlordUpdater\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use WarlordUpdater\Model\Update;

class BasePlugin extends  AbstractPlugin
{
	protected $_updateTable;
	
	/**
	 *
	 * @return UpdateTable
	 */
	public function getUpdateTable()
	{
		if(! $this->_updateTable) {
			$sm = $this->controller->getServiceLocator();
			$this->_updateTable = $sm->get('\WarlordUpdater\Model\UpdateTable');
		}
		return $this->_updateTable;
	}
}