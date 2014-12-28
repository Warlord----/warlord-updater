<?php
/**
 * File for Module Class
 *
 * @category  WarlordUpdater
 * @package   WarlordUpdater
 * @author    Warlord
 * @copyright Copyright (c) 2013, Warlord
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */

/**
 * @namespace
 */
namespace WarlordUpdater;

/**
 * @uses Zend\Module\Consumer\AutoloaderProvider
 * @uses Zend\EventManager\StaticEventManager
 */

use WarlordUpdater\Model\Update;
use WarlordUpdater\Model\UpdateTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;


/**
 * Module Class
 *
 * Handles Module Initialization
 *
 */
class Module
{
    
    public function getServiceConfig()
    {
    	return array(
    		'factories' => array(
    			'WarlordUpdater\Model\UpdateTable' =>  function($sm) {
    				$tableGateway = $sm->get('UpdateTableGateway');
    				$table = new UpdateTable($tableGateway);
    				return $table;
    			},
    			'UpdateTableGateway' => function ($sm) {
    				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    				$resultSetPrototype = new ResultSet();
    				$resultSetPrototype->setArrayObjectPrototype(new Update());
    				return new TableGateway('updates', $dbAdapter, null, $resultSetPrototype);
    			},
    		),
    	);
    }
    

    /**
     * Get Autoloader Configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
//             'Zend\Loader\StandardAutoloader' => array(
//                 'namespaces' => array(
//                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
//                 ))
	        	'Zend\Loader\ClassMapAutoloader' => array(
	        					__DIR__ . '/autoload_classmap.php',
	        	)
            );
    }

    /**
     * Get Module Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    
}
