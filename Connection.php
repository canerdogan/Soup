<?php
/**
 * 
 * Soup Connection Class
 * Veritabanı bağlantı sınıfı
 *
 * @package     Soup
 * @subpackage  Connection
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Semih TURNA <psychaos@gmail.com>
 */

class Soup_Connection {
	private $_allowedProperties = array ("server", "port", "database", "socket", "username", "password", "provider" );
	
	private $_provider = "Mysql";
	
	private $_serverName;
	
	private $_port;
	
	private $_databaseName;
	
	private $_unixSocket;
	
	private $_userName;
	
	private $_password;
	
	private $_charset = "utf8";
	
	private $_isConnected;
	
	private $_adapter;
	
	private $_import;
	
	private static $_instance;
	
	/**
	 * Aktif veritabanı adaptör adını döndürür
	 * @return string
	 */
	public function getProvider() {
		return $this->_provider;
	}
	
	/**
	 * Kullanılacak veritabanı adaptörünü belirler
	 * @param string $provider
	 * @return void
	 */
	public function setProvider($provider) {
		if ($provider) {
			$this->_provider = $provider;
		}
	}
	
	/**
	 * Veritabanı server adresi
	 * @param string $serverName
	 * @return void
	 */
	public function setServer($serverName) {
		$this->_serverName = $serverName;
	}
	
	public function setPort($port) {
		$this->_port = $port;
	}
	
	/**
	 * Veritabanı adı
	 * @param string $databaseName
	 * @return void
	 */
	public function setDatabase($databaseName) {
		$this->_databaseName = $databaseName;
	}
	
	public function getDatabase() {
		if ($this->_databaseName)
			return $this->_databaseName;
		else
			return false;
	}
	
	/**
	 * TODO: Yorum satırındaki açıklama satırına daha uygun bir şey bulmak lazım
	 * Veritabanı soketi
	 * @param string $socket
	 * @return void
	 */
	public function setSocket($socket) {
		$this->_unixSocket = $socket;
	}
	
	/**
	 * Veritabanı kullanıcı adı
	 * @param string $userName
	 * @return void
	 */
	public function setUsername($userName) {
		$this->_userName = $userName;
	}
	
	/**
	 * Veritabanı şifresi
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password) {
		$this->_password = $password;
	}
	
	/**
	 * Veritabanı karakter seti
	 * @param string $charset
	 * @return void
	 */
	public function setCharset($charset) {
		if ($charset) {
			$this->_charset = $charset;
		}
	}
	
	/**
	 * Bağlantı kontrolü yapar
	 * @return void
	 */
	public function isConnected() {
		return ( bool ) $this->_isConnected;
	}
	
	/**
	 * Adapters'ları döndürür
	 * @return Mysql|Oracle|Pgsql|Sqlite|Firebird
	 */
	public function getAdapter() {
		return $this->_adapter;
	}
	
	public function __construct(array $options = array()) {
		if (sizeof ( $options ) > 0) {
			foreach ( $this->_allowedProperties as $key ) {
				if (array_key_exists ( $key, $options ) && $options [$key]) {
					call_user_func_array ( array (&$this, "set" . ucwords ( $key ) ), ( array ) $options [$key] );
				}
			}
		}
	}
	
	/**
	 * Bağlantı nesnesini döndürür
	 * @param array $options
	 * @return Soup_Connection
	 */
	public static function getInstance(array $options = array()) {
		if (! isset ( self::$_instance )) {
			self::$_instance = new Soup_Connection ( $options );
		}
		
		return self::$_instance;
	}
	
	/**
	 * Veritabanı sağlayıcısını yükler
	 * @param string $adapter
	 * @throws Soup_Database_Exception
	 * @return Soup_Adapter
	 */
	private function _loadAdapter($adapter, $options = array()) {
		$class = ucwords ( $adapter );
		$path = dirname ( __FILE__ ) . "/Adapters/" . ucwords ( $class ) . ".php";
		
		if (! file_exists ( $path )) {
			throw new Soup_Database_Exception ( $class . " not found." );
		}
		
		require_once ($path);
		
		return new $class ();
	}
	
	/**
	 * Veritabanı bağlantısını açar
	 * @return void
	 */
	public function connect() {
		$this->_adapter = $this->_loadAdapter ( $this->_provider );
		$this->_adapter->connect ( array ("server" => $this->_serverName, "port" => $this->_port, "database" => $this->_databaseName, "username" => $this->_userName, "password" => $this->_password, "socket" => $this->_unixSocket, "charset" => $this->_charset ) );
		
		$this->_isConnected = $this->_adapter->isConnected ();
		
		return $this->getInstance ();
	}
	
	/**
	 * Veritabanı bağlantısını kapatır.
	 * @return void
	 */
	public function close() {
		if ($this->_isConnected) {
			$this->_adapter = NULL;
		}
	}
	
	public function getImport() {
		if(!$this->_import) {
			$importAdapter = 'Soup_Import_' . $this->_provider;
			$this->_import = new $importAdapter;
		}
		
		return $this->_import;
	}
	
	public function __destruct() {
		$this->close ();
	}

}