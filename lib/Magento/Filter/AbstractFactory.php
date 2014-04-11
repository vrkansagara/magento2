<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Filter;

/**
 * Magento filter factory abstract
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * Set of filters
     *
     * @var array
     */
    protected $invokableClasses = array();

    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = true;

    /**
     * Shared instances, by default is shared
     *
     * @var array
     */
    protected $shared = array();

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Zend_Filter_Interface[]
     */
    protected $sharedInstances = array();

    /**
     * @param \Magento\ObjectManager $objectManger
     */
    public function __construct(\Magento\ObjectManager $objectManger)
    {
        $this->objectManager = $objectManger;
    }

    /**
     * Check is it possible to create a filter by given name
     *
     * @param string $alias
     * @return bool
     */
    public function canCreateFilter($alias)
    {
        return array_key_exists($alias, $this->invokableClasses);
    }

    /**
     * Check is shared filter
     *
     * @param string $class
     * @return bool
     */
    public function isShared($class)
    {
        return isset($this->shared[$class]) ? $this->shared[$class] : $this->shareByDefault;
    }

    /**
     * Create a filter by given name
     *
     * @param string $alias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     */
    public function createFilter($alias, array $arguments = array())
    {
        $addToShared = !$arguments || isset(
            $this->sharedInstances[$alias]
        ) xor $this->isShared(
            $this->invokableClasses[$alias]
        );

        if (!isset($this->sharedInstances[$alias])) {
            $filter = $this->objectManager->create($this->invokableClasses[$alias], $arguments);
        } else {
            $filter = $this->sharedInstances[$alias];
        }

        if ($addToShared) {
            $this->sharedInstances[$alias] = $filter;
        }

        return $filter;
    }
}