<?php

/**
 * @package     Soup
 * @subpackage  Access
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Can Erdogan <can@canerdogan.net>
 */
abstract class Soup_Access implements ArrayAccess {
	/**
	 * Set key and value to data
	 *
	 * @see     set, offsetSet
	 * @param   $name
	 * @param   $value
	 * @return  void
	 */
	public function __set($name, $value) {
		if (! $name)
			throw new Exception ( 'Boş bırakmamalı! O güzel günleriiii... via Tarkan!' );
		
		$this->set ( $name, $value );
	}
	
	/**
	 * Get key from data
	 *
	 * @see     get, offsetGet
	 * @param   mixed $name
	 * @return  mixed
	 */
	public function __get($name) {
		return $this->get ( $name );
	}
	
	/**
	 * Check if key exists in data
	 *
	 * @param   string $name
	 * @return  boolean whether or not this object contains $name
	 */
	public function __isset($name) {
		return $this->contains ( $name );
	}
	
	/**
	 * Remove key from data
	 *
	 * @param   string $name
	 * @return  void
	 */
	public function __unset($name) {
		return $this->remove ( $name );
	}
	
	/**
	 * Check if an offset axists
	 *
	 * @param   mixed $offset
	 * @return  boolean Whether or not this object contains $offset
	 */
	public function offsetExists($offset) {
		return $this->contains ( $offset );
	}
	
	/**
	 * An alias of get()
	 *
	 * @see     get, __get
	 * @param   mixed $offset
	 * @return  mixed
	 */
	public function offsetGet($offset) {
		return $this->get ( $offset );
	}
	
	/**
	 * Sets $offset to $value
	 *
	 * @see     set, __set
	 * @param   mixed $offset
	 * @param   mixed $value
	 * @return  void
	 */
	public function offsetSet($offset, $value) {
		if (! isset ( $offset )) {
			$this->add ( $value );
		} else {
			$this->set ( $offset, $value );
		}
	}
	
	/**
	 * Unset a given offset
	 *
	 * @see   set, offsetSet, __set
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		return $this->remove ( $offset );
	}
	
	/**
	 * Remove the element with the specified offset
	 *
	 * @param mixed $offset The offset to remove
	 * @return boolean True if removed otherwise false
	 */
	public function remove($offset) {
		throw new Soup_Exception ( 'Remove is not supported for ' . get_class ( $this ) );
	}
	
	/**
	 * Return the element with the specified offset
	 *
	 * @param mixed $offset     The offset to return
	 * @return mixed
	 */
	public function get($offset) {
		throw new Soup_Exception ( 'Get is not supported for ' . get_class ( $this ) );
	}
	
	/**
	 * Set the offset to the value
	 *
	 * @param mixed $offset The offset to set
	 * @param mixed $value The value to set the offset to
	 */
	public function set($offset, $value) {
		throw new Soup_Exception ( 'Set is not supported for ' . get_class ( $this ) );
	}
	
	/**
	 * Check if the specified offset exists 
	 * 
	 * @param mixed $offset The offset to check
	 * @return boolean True if exists otherwise false
	 */
	public function contains($offset) {
		throw new Soup_Exception ( 'Contains is not supported for ' . get_class ( $this ) );
	}
	
	/**
	 * Add the value  
	 * 
	 * @param mixed $value The value to add 
	 * @return void
	 */
	public function add($value) {
		throw new Soup_Exception ( 'Add is not supported for ' . get_class ( $this ) );
	}
}