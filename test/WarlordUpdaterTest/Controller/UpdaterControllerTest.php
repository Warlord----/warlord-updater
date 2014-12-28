<?php
namespace WarlordUpdaterTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Db\ResultSet\ResultSet;
use Zend\Stdlib\Parameters;
use WarlordUpdater\Controller\UpdaterController;
use WarlordUpdaterTest\Bootstrap;

class UpdaterControllerTest extends AbstractHttpControllerTestCase
{
	protected $traceError = true;
	protected $controller;
	
	public function setUp()
	{
		$this->setApplicationConfig(Bootstrap::getServiceManager()->get('ApplicationConfig'));
		chdir(dirname(__DIR__) . '/../../../../');
		
// 		$config = include 'config\application.config.php';
// 		$this->setApplicationConfig($config);
				
		parent::setUp();
	}

	public function testInstallActionCanBeAccessed()
	{
		$this->dispatch('/install');
		$this->assertResponseStatusCode(200);
		
		$this->assertModuleName('WarlordUpdater');
		$this->assertControllerName('WarlordUpdater\Controller\Updater');
		$this->assertControllerClass('UpdaterController');
		$this->assertMatchedRouteName('updater-install');
	}

	public function testInstallModalDialog()
	{
		$this->dispatch('/install');
		
		$this->assertQueryCount('div[id="war_upd"] form', 1);
	}

	public function testInstallYes()
	{
		$this->getRequest()
			->setMethod('POST')
			->setPost(new Parameters(array(
			'del' => 'Yes'
		)));
		
// 	    $this->controller = new UpdaterController();
// 		$installerMock = $this->getMock(
// 				'\WarlordUpdater\Controller\Plugin\InstallPlugin', 
// 				array(
// 					'install'
// 				), array(), '', false);
// 		$installerMock->expects($this->once())
// 			->method('install')
// 			->will($this->returnValue('string'));
// 		$this->controller->getPluginManager()->setService('installer', 
// 				$installerMock);
		
		$this->dispatch('/install');
		
		$this->assertQueryCount('div[id="war_upd"] form', 0);
	}

	public function testInstallNo()
	{
		$this->getRequest()
			->setMethod('POST')
			->setPost(new Parameters(array(
			'del' => 'No'
		)));
		
		$this->dispatch('/install');
		
		$this->assertRedirect();
		$this->assertRedirectTo('/');
	}
	
	public function testInstallTestEnv()
	{
// 		$this->getRequest()
// 			->setMethod('GET')
// 			->setGet(new Parameters(array(
// 			'del' => 'No'
// 		)));
		
		$this->dispatch('/install/test');
		
		$this->assertNotRedirect();
		$this->assertQueryCount('div[id="war_upd"] form', 0);
	}

	public function testUpdateActionCanBeAccessed()
	{
		$this->dispatch('/update');
		$this->assertResponseStatusCode(200);
		
		$this->assertModuleName('WarlordUpdater');
		$this->assertControllerName('WarlordUpdater\Controller\Updater');
		$this->assertControllerClass('UpdaterController');
		$this->assertMatchedRouteName('updater-update');
	}
}
