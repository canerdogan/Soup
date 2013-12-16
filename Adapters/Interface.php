<?php

/**
 * 
 * Soup Adapters Interface Class
 * 
 * @author Semih TURNA
 * @version 1.0.0
 * @package Soup
 */

interface Soup_Adapters_Interface {
	
	public function getServer();
	
	public function setServer($serverName);
	
	public function getPort();
	
	public function setPort($port);
	
	public function getDatabase();
	
	public function setDatabase($databaseName);
	
	public function getSocket();
	
	public function setSocket($socket);
	
	public function getUsername();
	
	public function setUsername($userName);
	
	public function getPassword();
	
	public function setPassword($password);
	
	public function getCharset();
	
	public function setCharset($charset);
	
	public function isConnected();
	
	public function getPdoDsn();
	
	public function connect(array $options = array());

}