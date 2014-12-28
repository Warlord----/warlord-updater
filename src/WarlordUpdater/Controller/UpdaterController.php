<?php

/**
 *
 * @category  WarlordUpdater
 * @package   WarlordUpdater_Controller
 * @author    Warlord
 */
namespace WarlordUpdater\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\TableGateway\TableGateway;
use WarlordUpdater\Model\Update;
use Zend\View\Model\ViewModel;
use Application\Model\User;

class UpdaterController extends AbstractActionController
{

	public function installAction()
	{
		if($this->params('env') == 'test')
			return $this->_doInstall();
		$request = $this->getRequest();
		if ($request->isPost()) {
			if($request->getPost()->get('del') == 'Yes')
			{
				return $this->_doInstall();
			}
			else if($request->getPost()->get('del') == 'No')
			{
				return $this->redirect()->toUrl('/');
			}
		}
		$viewModel = new ViewModel();
		$viewModel->setTemplate('warlord-updater/updater/confirm-install.phtml');
		$viewModel->message = "All data will be deleted. Are you sure?";
		return $viewModel;
	}

	public function updateAction()
	{
		$updater = $this->updater();
		return array(
			'message' => $updater->update()
		);
		
	}
	
	private function _doInstall()
	{
		$installer = $this->installer();
		$updater = $this->updater();
		return array(
			'message' => $installer->install() . '<br /><br />' . $updater->update()
		);
	}

}
