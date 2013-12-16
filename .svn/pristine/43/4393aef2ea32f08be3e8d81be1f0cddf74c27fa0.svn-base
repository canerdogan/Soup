<?php

/**
 * 
 * Sqlite Class
 * Sqlite bağlantı sınıfı
 * 
 * @author Semih TURNA
 * @version 1.0.0
 * @package Soup
 */

class Sqlite extends Soup_Adapters_Abstract {

	/**
	 * Connection String oluşturuluyor
	 * @param array $options
	 */
	public function __construct($options = array()){
		$this->_connectionString = "sqlite:". $options["database"];
	}

}