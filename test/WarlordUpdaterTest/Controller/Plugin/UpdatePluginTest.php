<?php
namespace WarlordUpdaterTest\Controller\Plugin;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use WarlordUpdater\Controller\UpdaterController;
use WarlordUpdaterTest\Bootstrap;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\ArrayUtils;
use WarlordUpdater\Model\Update;

class UpdatePluginTest extends \PHPUnit_Framework_TestCase
{
	protected $traceError = true;
	protected $controller;
	protected $initialConfig;

	public function setUp()
	{
		chdir(dirname(__DIR__) . '/../../../../../');
		
		$this->initialConfig = $config = $this->addToConfig(
				array(
					'db' => array(
						'database' => 'test'
					)
				));
		$controller = new UpdaterController();
		$controller->getPluginManager()->setInvokableClass('updater', 
				'\WarlordUpdater\Controller\Plugin\UpdatePlugin');
		$serviceManager = Bootstrap::getServiceManager();
		$controller->setServiceLocator($serviceManager);
		
		$this->event = new MvcEvent();
		$routerConfig = isset($config['router']) ? $config['router'] : array();
		$router = \Zend\Mvc\Router\Http\TreeRouteStack::factory($routerConfig);
		$this->event->setRouter($router);
		$this->event->setRouteMatch(
				new RouteMatch(array(
					'controller' => 'update'
				)));
		$controller->setEvent($this->event);
		
		$this->controller = $controller;
	}
	
	public function tearDown()
	{
		$serviceManager = Bootstrap::getServiceManager();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('config', $this->initialConfig);
	}

	public function testUpdateRun()
	{
		$updaterMock = $this->getMock(
				'\WarlordUpdater\Controller\Plugin\UpdatePlugin', 
				array(
					'update'
				), array(), '', false);
		$updaterMock->expects($this->once())
			->method('update')
			->with()
			->will($this->returnValue('string'));
		
		$this->controller->getPluginManager()->setService('updater', 
				$updaterMock);
		
		$response = $this->controller->updateAction();
	}

	public function testUseUpdateTable()
	{
		$updateTableMock = $this->getMockBuilder(
				'\WarlordUpdater\Model\UpdateTable')
			->disableOriginalConstructor()
			->getMock();
		
		$updateTableMock->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue(array()));
		
		$serviceManager = $this->controller->getServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('\WarlordUpdater\Model\UpdateTable', 
				$updateTableMock);
		
		$response = $this->controller->updateAction();
	}

	public function testUseLoadDir()
	{
		$updateTableMock = $this->getMockBuilder(
				'\WarlordUpdater\Model\UpdateTable')
			->disableOriginalConstructor()
			->getMock();
		
		$updateTableMock->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue(array()));
		
		$serviceManager = $this->controller->getServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('\WarlordUpdater\Model\UpdateTable', 
				$updateTableMock);
		
		$this->addToConfig(
				array(
					'WarlordUpdater' => array(
						'updateDir' => array(
							'sql' => array(
								'str1'
							),
							'sys' => array(
								'str2'
							)
						)
					)
				));
		
		$loaderMock = $this->getMock(
				'\WarlordUpdater\Controller\Plugin\LoadDirPlugin', 
				array(
					'loadDirectory'
				), array(), '', false);
		
		$loaderMock->expects($this->at(0))
			->method('loadDirectory')
			->with('str1')
			->will($this->returnValue(array()));
		
		$loaderMock->expects($this->at(1))
			->method('loadDirectory')
			->with('str2')
			->will($this->returnValue(array()));
		
		$this->controller->getPluginManager()->setService('loader', $loaderMock);
		
		$response = $this->controller->updateAction();
	}

	public function testUseSaveToTable()
	{
		$this->addToConfig(
				array(
					'WarlordUpdater' => array(
						'updateDir' => array(
							'sql' => array(
								'module/WarlordUpdater/test//WarlordUpdaterTest/mock'
							),
							'sys' => array(
								'str2'
							)
						)
					)
				));
		
		$loaderMock = $this->getMock(
				'\WarlordUpdater\Controller\Plugin\LoadDirPlugin', 
				array(
					'loadDirectory'
				), array(), '', false);
		$loaderMock->expects($this->exactly(2))
		->method('loadDirectory')
		->will($this->returnValue(array('module/WarlordUpdater/test//WarlordUpdaterTest/mock' => '0.sql')));
		
		$this->controller->getPluginManager()->setService('loader', $loaderMock);
		
		$updateTableMock = $this->getMockBuilder(
				'\WarlordUpdater\Model\UpdateTable')
			->disableOriginalConstructor()
			->getMock();
		$updateTableMock->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue(array()));
		
		$updateTableMock->expects($this->once())
			->method('saveUpdate')
			->will($this->returnValue(''));
		
		$updateTableMock->expects($this->once())
			->method('query')
			->will($this->returnValue(''));
		
		
		$serviceManager = $this->controller->getServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('\WarlordUpdater\Model\UpdateTable', 
				$updateTableMock);
		
		
		$response = $this->controller->updateAction();
	}

	public function testSkipSave()
	{
		$this->addToConfig(
				array(
					'WarlordUpdater' => array(
						'updateDir' => array(
							'sql' => array(
								'module/WarlordUpdater/test//WarlordUpdaterTest/mock'
							),
							'sys' => array(
								'str2'
							)
						)
					)
				));
		
		$loaderMock = $this->getMock(
				'\WarlordUpdater\Controller\Plugin\LoadDirPlugin', 
				array(
					'loadDirectory'
				), array(), '', false);
		$loaderMock->expects($this->exactly(2))
		->method('loadDirectory')
		->will($this->returnValue(array('module/WarlordUpdater/test//WarlordUpdaterTest/mock' => '0.sql')));
		
		$this->controller->getPluginManager()->setService('loader', $loaderMock);
		
		$updateTableMock = $this->getMockBuilder(
				'\WarlordUpdater\Model\UpdateTable')
			->disableOriginalConstructor()
			->getMock();
		
		$update = new Update();
		$update->patch_file = 'module/WarlordUpdater/test//WarlordUpdaterTest/mock/0.sql';
		$updateTableMock->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue(array($update)));
		
		$updateTableMock->expects($this->never())
			->method('saveUpdate');
		
		$updateTableMock->expects($this->never())
			->method('query');
		
		
		$serviceManager = $this->controller->getServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('\WarlordUpdater\Model\UpdateTable', 
				$updateTableMock);
		
		
		$response = $this->controller->updateAction();
	}

	private function addToConfig($cfg)
	{
		$serviceManager = Bootstrap::getServiceManager();
		$serviceManager->setAllowOverride(true);
		$config = $serviceManager->get('config');
		$config = ArrayUtils::merge($config, $cfg);
		$serviceManager->setService('config', $config);
		
		return $config;
	}
}
