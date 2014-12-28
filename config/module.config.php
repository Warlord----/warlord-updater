<?php
return array(
	'service_manager' => array(
		'factories' => array(
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
		)
	),
	
	'controllers' => array(
		'invokables' => array(
			'WarlordUpdater\Controller\Updater' => 'WarlordUpdater\Controller\UpdaterController'
		)
	),
	
	'controller_plugins' => array(
		'invokables' => array(
			'updater' => 'WarlordUpdater\Controller\Plugin\UpdatePlugin',
			'loader' => 'WarlordUpdater\Controller\Plugin\LoadDirPlugin',
			'installer' => 'WarlordUpdater\Controller\Plugin\InstallPlugin',
		)
	),
	
	'router' => array(
		'routes' => array(
			'updater-install' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/install[/:env]',
					'constraints' => array(
						'env' => '[a-zA-Z][a-zA-Z0-9_-]*'
					),
					'defaults' => array(
						'controller' => 'WarlordUpdater\Controller\Updater',
						'action' => 'install'
					)
				)
			),
			'updater-update' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/update[/:env]',
					'constraints' => array(
						'env' => '[a-zA-Z][a-zA-Z0-9_-]*'
					),
					'defaults' => array(
						'controller' => 'WarlordUpdater\Controller\Updater',
						'action' => 'update'
					)
				)
			)
		)
	),
	
	'view_manager' => array(
		'template_path_stack' => array(
			'WarlordUpdater' => __DIR__ . '/../view'
		)
	),
	'WarlordUpdater' => array(
		'installDir' => array(
			__DIR__ . '/../install'
		),
		'updateDir' => array(
			'sys' => array()

			,
			'sql' => array()

		),
	)
);
