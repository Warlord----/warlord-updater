<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Delme for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace WarlordUpdaterTest\Framework;

use PHPUnit_Framework_TestCase;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class TestCase extends AbstractHttpControllerTestCase
{

    public static $locator;

    public static function setLocator($locator)
    {
        self::$locator = $locator;
    }

    public function getLocator()
    {
        return self::$locator;
    }
}
