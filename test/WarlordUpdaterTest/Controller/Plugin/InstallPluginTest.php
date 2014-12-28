<?php
namespace WarlordUpdaterTest\Controller\Plugin;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use WarlordUpdater\Controller\UpdaterController;
use WarlordUpdaterTest\Bootstrap;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\ArrayUtils;
use WarlordUpdater\Model\Update;

class InstallPluginTest extends \PHPUnit_Framework_TestCase
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
					'controller' => 'install'
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

	public function testInstallRun()
	{
		$installerMock = $this->getMock(
				'\WarlordUpdater\Controller\Plugin\InstallPlugin', 
				array(
					'install'
				), array(), '', false);
		$installerMock->expects($this->once())
			->method('install')
			->with()
			->will($this->returnValue('string'));
		
		$this->controller->getPluginManager()->setService('installer', 
				$installerMock);
		
		$response = $this->controller->installer()->install();
	}

	public function testUseLoadDir()
	{
		$serviceManager = Bootstrap::getServiceManager();
		$serviceManager->setAllowOverride(true);
		$config = $serviceManager->get('config');
		$config['WarlordUpdater']['installDir'][0] = 'str1';
		$config['WarlordUpdater']['installDir'][1] = 'str2';
		$serviceManager->setService('config', $config);
		
		$this->controller->getPluginManager()->setService('installer', 
				new \WarlordUpdater\Controller\Plugin\InstallPlugin());
		
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
		
		$response = $this->controller->installer()->install();
	}

	public function testCallQuery()
	{
		$serviceManager = Bootstrap::getServiceManager();
		$serviceManager->setAllowOverride(true);
		$config = $serviceManager->get('config');
		$config['WarlordUpdater']['installDir'][0] = 'module/WarlordUpdater/test//WarlordUpdaterTest/mock';
		$serviceManager->setService('config', $config);
		
		$this->controller->getPluginManager()->setService('installer', 
				new \WarlordUpdater\Controller\Plugin\InstallPlugin());
		
		$loaderMock = $this->getMock(
				'\WarlordUpdater\Controller\Plugin\LoadDirPlugin', 
				array(
					'loadDirectory'
				), array(), '', false);
		
		$loaderMock->expects($this->once())
			->method('loadDirectory')
			->will(
				$this->returnValue(
						array(
							'module/WarlordUpdater/test//WarlordUpdaterTest/mock' => '0.sql'
						)));
		
		$this->controller->getPluginManager()->setService('loader', $loaderMock);
		
		$updateTableMock = $this->getMockBuilder(
				'\WarlordUpdater\Model\UpdateTable')
			->disableOriginalConstructor()
			->getMock();
		
		$updateTableMock->expects($this->once())
			->method('query');
		
		$serviceManager = $this->controller->getServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('\WarlordUpdater\Model\UpdateTable', 
				$updateTableMock);
		
		$response = $this->controller->installer()->install();
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
