<?php

/**
 * 
 * Soup Manager Class
 * Veritabanı bağlantı kontrol sınıfı
 *
 * @package     Soup
 * @subpackage  Manager
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Semih TURNA <psychaos@gmail.com>
 */

final class Soup_Manager {
	
	/**
	 * Bağlantı nesnelerini tutar
	 * @var array
	 */
	private static $_connections = array ();
	
	/**
	 * Varsayılan bağlantı nesnesi
	 * @var Soup_Connection
	 */
	private static $_defaultConnection;
	
	private function __construct() { }
	
	private function __clone() { }
	
	/**
	 * Havuza yeni bir bağlantı nesnesi ekler
	 * @param string $key
	 * @param Soup_Connection $connection
	 * @return void
	 */
	public static function addConnection($key, Soup_Connection $connection) {
		if (! array_key_exists ( $key, self::$_connections )) {
			self::$_connections [$key] = & $connection;
		}
	}
	
	/**
	 * Bağlantı nesnelerini oluşturup kullanılmak üzere havuza ekler
	 * @param mixed $connections
	 * @throws Soup_Database_Exception
	 * @return void
	 */
	public static function connect($connections) {
		if (is_array ( $connections )) {
			foreach ( $connections as $key => $connection ) {
				if ($connection instanceof Soup_Connection && ! array_key_exists ( $key, self::$_connections )) {
					self::$_connections [$key] = $connection;
				}
			}
			
			reset(self::$_connections);
			
			self::$_defaultConnection = current ( self::$_connections );
		} 
		else {
			if ($connections instanceof Soup_Connection) {
				self::$_connections [] = $connections;
				self::$_defaultConnection = & self::$_connections [0];
			}
		}
		

		self::$_defaultConnection->connect();
	}
	
	/**
	 * Varsayılan bağlantı nesnesini döndürür
	 * @return Soup_Connection
	 */
	public static function getDefaultConnection(){
		return self::$_defaultConnection;
	}
	
	/**
	 * Varsayılan bağlantı nesnesini değiştirir ve bağlantı nesnesini döndürür
	 * @param string $key
	 * @throws Soup_Database_Exception
	 * @return Soup_Connection
	 */
	public static function setDefaultConnection($key) {
		if (array_key_exists ( $key, self::$_connections )) {
			self::$_defaultConnection = & self::$_connections [$key];
			self::$_defaultConnection->connect();
			
			return self::$_defaultConnection;
		}
		
		throw new Soup_Database_Exception ( "Database connection not found." );
	}
	
	/**
	 * Havuzdan verilen anahtara göre bağlantı nesnesini döndürür
	 * @param string $key
	 * @throws Soup_Database_Exception
	 * @return Soup_Connection
	 */
	public static function getConnection($key) {
		if (array_key_exists ( $key, self::$_connections )) {
			return self::$_connections [$key];
		}
		
		throw new Soup_Database_Exception ( "Database connection not found." );
	}
	
	/**
	 * Verilen anahtar ile ilişkilendirilmiş bağlantıyı kapatır
	 * @param string $key
	 * @param bolean $clear
	 * @throws Soup_Database_Exception
	 * @return void
	 */
	public static function close($key = NULL, $clear = FALSE) {
		if ($key && array_key_exists ( $key, self::$_connections )) {
			self::$_connections [$key]->close ();
			
			if ($clear) {
				if (self::$_connections [$key] instanceof self::$_defaultConnection) {
					self::$_defaultConnection = NULL;
				}
				
				unset ( self::$_connections [$key] );
			}
		}
		else{
			throw new Soup_Database_Exception ( "Database connection not found." );
		}
	}
	
	/**
	 * Açık olan bütün bağlantıları kapatır.
	 * @param bolean $clear
	 * @return void
	 */
	public static function closeAll($clear = FALSE) {
		if (sizeof ( self::$_connections ) > 0) {
			foreach ( self::$_connections as $connection ) {
				$connection->close ();
			}
			
			if ($clear) {
				self::$_connections = array ();
				self::$_defaultConnection = NULL;
			}
		}
	}
	
	/**
	 * Bağlantı kontrolü yapar
	 * @param string $connKey
	 * @return boolean
	 */
	public static function isConnected($connKey = Null){
		if(is_null($connKey)) {
			return self::$_defaultConnection->isConnected();
		}
		else{
			return self::getConnection($connKey)->isConnected();
		}
		
	}

}