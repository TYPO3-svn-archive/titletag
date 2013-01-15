<?php
/**
 * Copyright notice
 *
 * (c) 2012 Agentur am Wasser | Maeder & Partner AG
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * **************************************************************
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @copyright  Copyright (c) 2012 Agentur am Wasser | Maeder & Partner AG {@link http://www.agenturamwasser.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category   TYPO3
 * @package    titletag
 * @version    $Id$
 */

if (!defined ('TYPO3_MODE')) {
     die ('Access denied.');
}

/**
 * Titletag API: stack
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @package    TYPO3
 * @subpackage titletag
 */
class tx_titletag_stack implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * GetVars storage
     *
     * @var array
     */
    protected $_vars = array();

    /**
     *
     * @var tx_titletag_vars
     */
    private static $_instance = null;

    /**
     * Constructor is disabled, use static method
     * {@link tx_titletag_vars::getInstance()} instead
     *
     * @return void
     */
    protected function __construct() {}

    /**
     * Singleton implementation
     *
     * @return tx_titletag_vars
     */
    public static function getInstance()
    {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Implements {@link Countable::count()}
     *
     * @see Countable::count()
     * @return int
     */
    public function count()
    {
        return count($this->_vars);
    }

    /**
     * getIterator()
     *
     * @see IteratorAggregate::getIterator()
     * @return ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject($this->_vars);
    }

    /**
     * offsetExists()
     *
     * @see ArrayAccess::offsetExists()
     * @param int $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        if(!is_string($offset)) {
            throw new InvalidArgumentException('$offset must be string, "' . gettype($offset) . '" given');
        }
        return array_key_exists($offset, $this->_vars);
    }

    /**
     * offsetGet()
     *
     * @see ArrayAccess::offsetGet()
     * @param int $offset
     * @throws Exception
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new InvalidArgumentException('Offset "' . $offset . '" does not exist');
        }

        return $this->_vars[$offset];
    }

    /**
     * offsetUnset()
     *
     * @see ArrayAccess::offsetUnset()
     * @param int $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new InvalidArgumentException('Offset "' . $offset . '" does not exist');
        }
        unset($this->_vars[$offset]);
        return;
    }

    /**
     * offsetSet()
     *
     * @see ArrayAccess::offsetSet()
     * @param mixed $offset
     * @param mixed $getVar
     * @return void
     */
    public function offsetSet($offset, $getVar)
    {
        if ($this->offsetExists($offset)) {
            throw new InvalidArgumentException('Offset "' . $offset . '" already exists');
        }
        $this->_vars[$offset] = $getVar;
        return;
    }

    /**
     * Pushes a new getVar onto the stack
     *
     * @param string $offset
     * @param array $getVar
     * @return void
     */
    public function push($offset, array $getVar)
    {
        $this->offsetSet($offset, $getVar);
    }

    /**
     *
     * @param string $offset
     * @param array $getVar
     * @return void
     */
    public function prepend($offset, array $getVar)
    {
        $this->_vars = array_reverse($this->_vars, true);
        $this->offsetSet($offset, $getVar);
        $this->_vars = array_reverse($this->_vars, true);
    }

    /**
     * Clears the whole stack
     *
     * @return void
     */
    public function clear()
    {
        $this->_vars = array();
        return;
    }

}

?>