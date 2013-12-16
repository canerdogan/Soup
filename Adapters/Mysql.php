<?php
/**
 * 
 * MySql Class
 * MySql bağlantı sınıfı
 * 
 * @author Semih TURNA
 * @version 1.0.0
 * @package Soup
 */

class Mysql extends Soup_Adapters_Abstract implements Soup_Adapters_Interface{

	public $properties;
	
	public function __construct(){
		$this->properties['string_quoting'] = array('start' => "'",
				'end' => "'",
				'escape' => '\\',
				'escape_pattern' => '\\');
		
		$this->properties['identifier_quoting'] = array('start' => '`',
				'end' => '`',
				'escape' => '`');
	}
	
	public function connect(array $options = array()){
		if(sizeof($options) > 0){
			foreach($this->_requiredParams as $key ){
				if(isset($options[$key]) && $options[$key]){ // array_key_exists($key, $options)
					call_user_func_array(array(&$this, "set" . ucwords($key)), (array)$options[$key]);
				}
			}
		}

		// Bağlantı kodunu oluşturuyoruz.
		$this->_pdoDsn = $this->_initPdoDsn(array(
			"server" 		=> $this->_serverName,
			"port" 			=> $this->_port,
			"database" 		=> $this->_databaseName,
			"unix_socket" 	=> $this->_unixSocket,
			"charset" 		=> $this->_charset
		));
		
		try{
			parent::__construct($this->_pdoDsn, $this->_userName, $this->_password, $this->_pdoOptions);
            
            $this->query("SET NAMES ". $this->_charset);
			
			$this->_isConnected = TRUE;
		}
		catch(PDOException $e){
			$this->_isConnected = FALSE;
			
			throw new Soup_Database_Exception($e);
		}
		
		return $this;
	}
	
	public function getTables(){
		$tables = array();
		$query 	= $this->query("SHOW TABLES");
		
		while($row = $query->fetch(PDO::FETCH_NUM)){
			$tables[] = $row[0];
		}
		
		return $tables;
	}
	
	public function getColumnsInfo($table){
		$columns 	= array();
		$query 		= $this->query("SHOW COLUMNS FROM ". $table);
		$cols 		= $query->fetchAll();
		
		foreach($cols as $k1 => $arr){
            foreach($arr as $k2 => $value){
                if(!preg_match("/\d/", $k2)){
                    $columns[$cols[$k1]["field"]][$k2] = $value;
                }
            }
        }
		 
		return $columns;
	}

}