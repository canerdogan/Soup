<?php

/**
 * 
 * Soup Adapters Abstract Class
 * Farklı veritabanlarını kullanmak için yapılmış soyutlama sınıfı
 * 
 * @author Semih TURNA
 * @version 1.0.0
 * @package Soup
 */

abstract class Soup_Adapters_Abstract extends PDO {
	
	protected $_requiredParams = array("server", "port", "database", "socket", "username", "password");
	
	protected $_serverName;
	
	protected $_port;
	
	protected $_databaseName;
	
	protected $_unixSocket;
	
	protected $_userName;
	
	protected $_password;
	
	protected $_charset = "utf8";
	
	protected $_isConnected;
	
	protected $_pdoDsn;
	
	protected $_pdoOptions = array(
		PDO::ATTR_CASE 				=> PDO::CASE_LOWER,
		PDO::ATTR_ERRMODE 			=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_ORACLE_NULLS 		=> PDO::NULL_NATURAL,
		PDO::ATTR_STRINGIFY_FETCHES => FALSE
	);
	
	public function getServer(){
		return $this->_serverName;
	}
	
	public function setServer($serverName){
		$this->_serverName = $serverName;
	}
	
	public function getPort(){
		return $this->_port;
	}
	
	public function setPort($port){
		$this->_port = $port;
	}
	
	public function getDatabase(){
		return $this->_databaseName;
	}
	
	public function setDatabase($databaseName){
		$this->_databaseName = $databaseName;
	}
	
	public function getSocket(){
		return $this->_unixSocket;
	}
	
	public function setSocket($socket){
		$this->_unixSocket = $socket;
	}
	
	public function getUsername(){
		return $this->_userName;
	}
	
	public function setUsername($userName){
		$this->_userName = $userName;
	}
	
	public function getPassword(){
		return $this->_password;
	}
	
	public function setPassword($password){
		$this->_password = $password;
	}
	
	public function getCharset(){
		return $this->_charset;
	}
	
	public function setCharset($charset){
		if($charset){
			$this->_charset = $charset;
		}
	}
	
	public function isConnected(){
		return (bool)$this->_isConnected;
	}
	
	public function getPdoDsn(){
		return $this->_pdoDsn;
	}
	
	protected function _initPdoDsn(array $options){
		$dsn = "mysql:";
		
		if(isset($options["server"])){
			$dsn .= "host=". $options["server"] .";";
		}
		
		if(isset($options["port"])){
			$dsn .= "port=". $options["port"] .";";
		}
		
		if(isset($options["database"])){
			$dsn .= "dbname=". $options["database"] .";";
		}
		
		if(isset($options["unix_socket"])){
            $dsn .= "unix_socket=". $options["unix_socket"] .";";
        }
        
        if(isset($options["charset"])){
            $dsn .= "charset=" . $options["charset"] . ";";
        }
		
		return $dsn;
	}

}