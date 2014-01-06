<?php

/**
 * 
 * Soup Query Class
 * Sorgu Oluşturma Sihirbazı
 * 
 * @author Semih TURNA
 * @version 1.0.0
 * @package Soup
 */

class Soup_Query {
	CONST SELECT 			= "SELECT ";
	CONST DELETE 			= "DELETE";
	CONST INSERT 			= "INSERT ";
	CONST UPDATE 			= "UPDATE ";
	CONST REPLACE 			= "REPLACE";
	CONST VALUES 			= " VALUES ";
	CONST INTO 				= " INTO ";
	CONST SET 				= " SET ";
	CONST DISTINCT 			= " DISTINCT" ;
    CONST FROM 				= " FROM ";
    CONST UNION 			= " UNION ";
    CONST WHERE 			= " WHERE ";
    CONST GROUP 			= " GROUP BY ";
    CONST HAVING 			= " HAVING ";
    CONST ORDER 			= " ORDER BY ";
    CONST LIMIT 			= " LIMIT ";
    CONST OFFSET 			= " OFFSET ";
    CONST INNER_JOIN     	= " INNER JOIN ";
    CONST LEFT_JOIN      	= " LEFT JOIN ";
    CONST RIGHT_JOIN		= " RIGHT JOIN ";
    CONST FULL_JOIN      	= " FULL JOIN ";
    CONST CROSS_JOIN     	= " CROSS JOIN ";
    CONST NATURAL_JOIN   	= " NATURAL JOIN ";
    CONST OUTER 			= " OUTER JOIN ";
    CONST LEFT_OUTER 		= " LEFT OUTER JOIN ";
    CONST RIGHT_OUTER 		= " RIGHT OUTER JOIN ";
    
	CONST SQL_WILDCARD   			= " * ";
	CONST SQL_AND        			= " AND ";
    CONST SQL_AS         			= " AS ";
    CONST SQL_OR         			= " OR ";
    CONST SQL_ON         			= " ON ";
    CONST SQL_PARENTHESES_LEFT 		= " ( ";
    CONST SQL_PARENTHESES_RIGHT 	= " ) ";
    CONST SQL_EQUALS 				= " = ";
	CONST SQL_CALC_FOUND_ROWS		= " SQL_CALC_FOUND_ROWS ";
	CONST SQL_NULL                  = "NULL";
	
    /**
     * Soup_Query Instance
     * @var Soup_Query 
     */
	private static $_instance;
	
	/**
	 * Yapılacak sorgu çeşidini tanımlar
	 * @var string
	 * @use SELECT|INSERT|UPDATE|DELETE|REPLACE
	 */
	private $_operation = "SELECT";
    
    /**
     * Tabloların alias bilgilerini tutar
     * @var array
     */
    private $_alias = array();
    
    /**
     * Birleştirme (Join) işlemlerinde tablolar arasındaki ilişkiyi tutar
     * @var array
     */
    private $_relations = array();
	
    /**
     * En son çalıştırılan SQL kod bilgisini tutar
     * @var string
     */
	protected $_sql;
	
	protected $_table;
	
    /**
     * SQL kod bilgisinin parçalanmış halini tutar
     * @var array 
     */
	protected $_sqlParts = array();
    
    /**
     * Table ve Sütun bilgilerini tutar
     * @var array
     */
    protected $_tableInfos = array();
    
	public function __construct(){ }
	
	private function __clone(){ }
	
    /**
     * En son çalıştırılan SQL koduna ait parçaları döndürür
     * @return array 
     */
	public static function getSqlParts(){
		return self::$_instance->_sqlParts;
	}
	
	/**
	 * Verilen string tablo ismi Soup_Table nesnesi olarak geri döner
	 * @param Soup_Table|string $table
	 * @return Soup_Record
	 */
	public static function getTable($table){
		if(!isset(self::$_instance)){
			self::$_instance = new Soup_Query();
		}
		
		if($table instanceof Soup_Table){
			self::$_instance->_table = $table;
		} 
		else{
			self::$_instance->_table = new $table();
		}
		
		return self::$_instance->_table->select();
	}
	
    /**
     * SELECT SQL kodunu oluşturur
     * @param string|array $columns
     * @return Soup_Query 
     */
	public static function select($columns = self::SQL_WILDCARD){
		if(!isset(self::$_instance)){
			self::$_instance = new Soup_Query();
		}

		self::$_instance->_operation 			= "SELECT";
		self::$_instance->_sqlParts 			= array();
        self::$_instance->_tableInfos           = array();
		self::$_instance->_sqlParts["columns"] 	= $columns;
			
		return self::$_instance;
	}
	
    /**
     * DELETE SQL kodunu oluşturur
     * @param string $table
     * @return Soup_Query 
     */
	public static function delete($table = NULL){
		if(!isset(self::$_instance)){
			self::$_instance = new Soup_Query();
		}
		
		self::$_instance->_operation 	= "DELETE";
		self::$_instance->_sqlParts 	= array();
		
		if(!is_null($table)){
			self::$_instance->_sqlParts["from"] = $table;
		}
		
		return self::$_instance;
	}
	
    /**
     * INSERT SQL kodunu oluşturur
     * @param string $table
     * @param array $values
     * @return Soup_Query 
     */
	public static function insert($table, $values = array()){
		if(!isset(self::$_instance)){
			self::$_instance = new Soup_Query();
		}
		
		self::$_instance->_operation 			= "INSERT";
		self::$_instance->_sqlParts 			= array();
		self::$_instance->_sqlParts["from"] 	= $table;
		self::$_instance->_sqlParts["values"] 	= $values;

		return self::$_instance;
	}
	
    /**
     * UPDATE SQL kodunu oluşturur
     * @param string $table
     * @param array $values
     * @return Soup_Query 
     */
	public static function update($table, $values = array()){
		if(!isset(self::$_instance)){
			self::$_instance = new Soup_Query();
		}
		
		self::$_instance->_operation 			= "UPDATE";
		self::$_instance->_sqlParts 			= array();
		self::$_instance->_sqlParts["from"] 	= $table;
		self::$_instance->_sqlParts["values"] 	= $values;
		
		return self::$_instance;
	}
	
    /**
     * REPLACE SQL kodunu oluşturur
     * @param string $table
     * @param array $values
     * @return Soup_Query 
     */
	public static function replace($table, $values = array()){
		if(!isset(self::$_instance)){
			self::$_instance = new Soup_Query();
		}
		
		self::$_instance->_operation 			= "REPLACE";
		self::$_instance->_sqlParts 			= array();
		self::$_instance->_sqlParts["from"] 	= $table;
		self::$_instance->_sqlParts["values"] 	= $values;

		return self::$_instance;
	}
	
	public static function getLastSqlString(){
		return (string)self::$_instance->_builSqlString()->_sql;
	}
    
    /**
     * FROM SQL kodunu oluşturur
     * @param string $table
     * @param string $alias
     * @return Soup_Query 
     */
	public function from($table, $alias = NULL){
		$table = Soup_Inflector::tableize($table);
		
        $this->_alias[$alias]       = $table;
//		$this->_sqlParts["from"]    = $table . (!is_null($alias) ? self::SQL_AS . $alias : NULL);
		$this->_sqlParts["from"]    = $table;
        $this->_setTableInfo($table);
	
		return $this;
	}
	
    /**
     * JOIN SQL cümlesini oluşturur
     * @param string $table
     * @param string $values
     * @param string $alias
     * @return Soup_Query 
     */
	public function join($table, $values, $alias = NULL){
		$this->_join($table, $values, self::INNER_JOIN, $alias);
		
		return $this;
	}
    
    /**
     * LEFT JOIN SQL cümlesini oluşturur
     * @param string $table
     * @param string $values
     * @param string $alias
     * @return Soup_Query 
     */
	public function leftJoin($table, $values, $alias = NULL){
		$this->_join($table, $values, self::LEFT_JOIN, $alias);
		
		return $this;
	}
	
    /**
     * RIGHT JOIN SQL cümlesini oluşturur
     * @param string $table
     * @param string $values
     * @param string $alias
     * @return Soup_Query 
     */
	public function rightJoin($table, $values, $alias = NULL){
		$this->_join($table, $values, self::RIGHT_JOIN, $alias);
		
		return $this;
	}
	
    /**
     * OUTER JOIN SQL cümlesini oluşturur
     * @param string $table
     * @param string $values
     * @param string $alias
     * @return Soup_Query 
     */
	public function outer($table, $values, $alias = NULL){
		$this->_join($table, $values, self::OUTER, $alias);
		
		return $this;
	}
	
    /**
     * LEFT OUTER JOIN SQL cümlesini oluşturur
     * @param string $table
     * @param string $values
     * @param string $alias
     * @return Soup_Query 
     */
	public function leftOuter($table, $values, $alias = NULL){
		$this->_join($table, $values, self::LEFT_OUTER, $alias);
		
		return $this;
	}
	
    /**
     * RIGHT OUTER JOIN SQL cümlesini oluşturur
     * @param string $table
     * @param string $values
     * @param string $alias
     * @return Soup_Query 
     */
	public function rightOuter($table, $values, $alias = NULL){
		$this->_join($table, $values, self::RIGHT_OUTER, $alias);
		
		return $this;
	}
	
	/**
	 * NATURAL JOIN SQL cümlesini oluşturur
	 * @param string $table
	 * @param string $alias
	 * @return Soup_Query
     * 
	 * @todo MySQL ile nasıl çalıştığı kontrol edilecek
	 */
	public function naturalJoin($table, $alias = NULL){
		$this->_join($table, NULL, self::NATURAL_JOIN, $alias);
		
		return $this;
	}
	
    /**
     * Tüm JOIN SQL cümlelerini oluşturur. (join, left|right join, outer, outer left|right join, natural join)
     * @param string $table
     * @param string $values
     * @param string $type
     * @param string $alias 
     * @return void
     */
	private function _join($table, $values = NULL, $type = NULL, $alias = NULL){
        $this->_alias[$alias] = $table;
        
        if(!is_null($values)){
            $this->_relations[$table] = $values;
        }
        
//		$this->_sqlParts["join"][]  = $type . $table . (!is_null($alias) ? self::SQL_AS . $alias : NULL) .
//										(!is_null($values) ? self::SQL_ON . self::SQL_PARENTHESES_LEFT . $values . self::SQL_PARENTHESES_RIGHT : "");
		$this->_sqlParts["join"][]  = $type . $table .
								(!is_null($values) ? self::SQL_ON . self::SQL_PARENTHESES_LEFT . $values . self::SQL_PARENTHESES_RIGHT : "");
        
        $this->_setTableInfo($table);
	}
	
    /**
     * WHERE SQL cümlesini oluşturur
     * @param string $condition
     * @param string|array $args
     * @return Soup_Query 
     */
	public function where($condition, $args = NULL){
		$this->_where($condition, $args, NULL);
		
		return $this;
	}
	
    /**
     * WHERE SQL cümlesini AND işlevi ile kullanır
     * @param string $condition
     * @param string|array $args
     * @return Soup_Query 
     */
	public function andWhere($condition, $args = NULL){
		$this->_where($condition, $args, self::SQL_AND);
		
		return $this;
	}
	
    /**
     * WHERE SQL cümlesini OR işlevi ile kullanır
     * @param string $condition
     * @param string|array $args
     * @return Soup_Query 
     */
	public function orWhere($condition, $args = NULL){
		$this->_where($condition, $args, self::SQL_OR);
		
		return $this;
	}
	
    /**
     * WHERE SQL cümlesini oluşturur
     * @param string $condition
     * @param string|array $args
     * @param string $type
     * @return void
     */
	private function _where($condition, $args = NULL, $type = ""){
		$this->_sqlParts["where"][] = $type . self::SQL_PARENTHESES_LEFT . $this->_format($condition, $args) . self::SQL_PARENTHESES_RIGHT;
	}
    
    /**
     * GROUP BY SQL cümlesini oluşturur
     * @param string $columns
     * @return Soup_Query 
     */
	public function groupBy($columns){
		$this->_sqlParts["group"] = $columns;
		
		return $this;
	}
	
    /**
     * HAVING SQL cümlesini oluşturur
     * @param string $condition
     * @param string|array $args
     * @return Soup_Query 
     */
	public function having($condition, $args = NULL){
		$this->_having($condition, $args, NULL);
		
		return $this;
	}
	
    /**
     * HAVING SQL cümlesini AND işlevi ile kullanır
     * @param string $condition
     * @param string|array $args
     * @return Soup_Query 
     */
	public function andHaving($condition, $args = NULL){
		$this->_having($condition, $args, self::SQL_AND);
		
		return $this;
	}
    
    /**
     * HAVING SQL cümlesini OR işlevi ile kullanır
     * @param string $condition
     * @param string|array $args
     * @return Soup_Query 
     */
	public function orHaving($condition, $args = NULL){
		$this->_having($condition, $args, self::SQL_OR);
		
		return $this;
	}
	
    /**
     * HAVING SQL cümlesini oluşturur
     * @param string $condition
     * @param string|array $args
     * @param string $type
     * @return void
     */
	private function _having($condition, $args = NULL, $type = ""){
		$this->_sqlParts["having"][] = $type . self::SQL_PARENTHESES_LEFT . $this->_format($condition, $args) . self::SQL_PARENTHESES_RIGHT;
	}
    
    /**
     * ORDER BY SQL cümlesini oluşturur
     * @param string|array $orders
     * @return Soup_Query 
     */
	public function orderBy($orders){
		if(is_array($orders)){
			$literal = array(); foreach($orders as $column => $direction){
				$literal[] = $column ." ". $direction;
			}
			
			$this->_sqlParts["order"] = implode(", ", $literal);
		}
		else{
			$this->_sqlParts["order"] = $orders;
		}
		
		return $this;
	}
	
    /**
     * LIMIT SQL cümlesini oluşturur
     * @return Soup_Query 
     */
	public function limit(){
		$args = func_get_args();
		
		if(func_num_args() == 2){
			$this->_sqlParts["limit"] = $args;
		}
		else{
			$this->_sqlParts["limit"] = intval($args[0]);
		}
		
		return $this;
	}
	
    /**
     * OFFSET SQL cümlesini oluşturur
     * @param string $offset
     * @return Soup_Query 
     */
	public function offset($offset){
		$this->_sqlParts["offset"] = intval($offset);
		
		return $this;
	}
	
    /**
     * Tablo(lar) içinde bulunan bilgi ve bilgi gruplarını seçmek için SQL cümlesi oluşturur
     * @return void
     */
	private function _buidSelect(){
		$sql = self::SELECT . $this->_renameColumnName($this->_sqlParts["columns"]) . self::FROM . $this->_sqlParts["from"];
		
		// Join Calls
		if(isset($this->_sqlParts["join"])){
			foreach($this->_sqlParts["join"] as $join){
				$sql .= $join;
			}
		}
		
		// Where Calls
		if(isset($this->_sqlParts["where"])){
			$sql .= self::WHERE; foreach($this->_sqlParts["where"] as $condition){
				$sql .= $condition;
			}
		}	
		
		// GroupBy Calls
		if(isset($this->_sqlParts["group"])){
			$sql .= self::GROUP . $this->_sqlParts["group"];
		}
		
		// Having Calls
		if(isset($this->_sqlParts["having"])){
			$sql .= self::HAVING; foreach($this->_sqlParts["having"] as $condition){
				$sql .= $condition;
			}
		}
		
		// Having Calls
		if(isset($this->_sqlParts["order"])){
			$sql .= self::ORDER . $this->_sqlParts["order"];
		}
		
		// LIMIT Calls
		if(isset($this->_sqlParts["limit"])){
			$sql .= self::LIMIT . (is_array($this->_sqlParts["limit"]) ? implode(", ", $this->_sqlParts["limit"]) : $this->_sqlParts["limit"]);
		}
		
		// Offset Calls
		if(isset($this->_sqlParts["offset"])){
			$sql .= self::OFFSET . $this->_sqlParts["offset"];
		}
		
		$this->_sql = $sql;
	}
	
    /**
     * Tablo içinde bulunan herhangi bir kaydı silmek için kullanılacak SQL cümlesini oluşturur
     * @return void
     */
	private function _buildDelete(){
		$sql = self::DELETE . self::FROM . $this->_sqlParts["from"];
		
		// Where Calls
		if(isset($this->_sqlParts["where"])){
			$sql .= self::WHERE; foreach($this->_sqlParts["where"] as $condition){
				$sql .= $condition;
			}
		}	
		
		// LIMIT Calls
		if(isset($this->_sqlParts["limit"])){
			$sql .= self::LIMIT . (is_array($this->_sqlParts["limit"]) ? implode(", ", $this->_sqlParts["limit"]) : $this->_sqlParts["limit"]);
		}
		
		$this->_sql = $sql;
	}
	
    /**
     * Herhangi bir tabloya kayıt eklemek için kullanılacak SQL cümlesini oluşturur
     * @return void
     */
	private function _buildInsert(){
		//$keys 	= array_keys($this->_sqlParts["values"]);
		$keys 	= array_map(array(&$this, "_quoteSimpleColumnName"), array_keys($this->_sqlParts["values"]));
		$values = array_map(array(&$this, "_quoteValues"), array_values($this->_sqlParts["values"]));
        
        array_walk( $values, function(&$v, $k){
            is_null( $v ) AND $v = Soup_Query::SQL_NULL;
        } );
		
		$sql 	 = self::INSERT . self::INTO . $this->_sqlParts["from"];
		$sql 	.= self::SQL_PARENTHESES_LEFT . implode(", ", $keys) . self::SQL_PARENTHESES_RIGHT 
					. self::VALUES . self::SQL_PARENTHESES_LEFT . implode(", ", $values) . self::SQL_PARENTHESES_RIGHT;
		
		$this->_sql = $sql;
	}
	
    /**
     * Tablo içinde bulunan herhangi bir kaydı güncellemek için kullanılacak SQL cümlesini oluşturur
     * @return void
     */
	private function _buildUpdate(){
// 		$keys 	= array_keys($this->_sqlParts["values"]);
		$keys 	= array_map(array(&$this, "_quoteSimpleColumnName"), array_keys($this->_sqlParts["values"]));
		$values = array_map(array(&$this, "_quoteValues"), array_values($this->_sqlParts["values"]));
        
		$sql 	 = self::UPDATE . $this->_sqlParts["from"] . self::SET;
		
		$cols = array(); foreach($keys as $index => $key){
			$cols[] = $key . self::SQL_EQUALS . ( is_null( $values[$index] ) ? self::SQL_NULL : $values[$index] );
		}
		
		$sql .= implode(", ", $cols);
		
		// Where Calls
		if(isset($this->_sqlParts["where"])){
			$sql .= self::WHERE; foreach($this->_sqlParts["where"] as $condition){
				$sql .= $condition;
			}
		}	
		
		// LIMIT Calls
		if(isset($this->_sqlParts["limit"])){
			$sql .= self::LIMIT . (is_array($this->_sqlParts["limit"]) ? implode(", ", $this->_sqlParts["limit"]) : $this->_sqlParts["limit"]);
		}
		
		$this->_sql = $sql;
	}
	
    /**
     * Bir tabloya veri eklerken ilgili kayıt bu tablo içinde bulunuyorca güncellemek,
     * eğer ilgili veri tablo içinde bulunmuyorsa eklemek için kullanılacak SQL cümlesini oluşturur
     * @return void
     */
	private function _buildReplace(){
// 		$keys 	= array_keys($this->_sqlParts["values"]);
		$keys 	= array_map(array(&$this, "_quoteSimpleColumnName"), array_keys($this->_sqlParts["values"]));
		$values = array_map(array(&$this, "_quoteValues"), array_values($this->_sqlParts["values"]));
		
        array_walk( $values, function(&$v, $k){
            is_null( $v ) AND $v = Soup_Query::SQL_NULL;
        } );
        
		$sql 	 = self::REPLACE . self::INTO . $this->_sqlParts["from"];
		$sql 	.= self::SQL_PARENTHESES_LEFT . implode(", ", $keys) . self::SQL_PARENTHESES_RIGHT 
					. self::VALUES . self::SQL_PARENTHESES_LEFT . implode(", ", $values) . self::SQL_PARENTHESES_RIGHT;
		
		$this->_sql = $sql;
	}
	
    /**
     * Girilen sorgu çeşidine göre SQL cümlesi oluşturur
     * @return Soup_Query 
     */
    private function _builSqlString(){
        switch($this->_operation){
			case "SELECT" :
				$this->_buidSelect();
			break;
			case "DELETE" :
				$this->_buildDelete();
			break;
			case "INSERT" :
				$this->_buildInsert();
			break;
			case "REPLACE" :
				$this->_buildReplace();
			break;
			case "UPDATE" :
				$this->_buildUpdate();
			break;
		}
        
        return $this;
    }
    
    /**
     * En son eklenen kaydın unique id numarasını döndürür
     * @return integer 
     */
	public static function lastInsertId(){
		return Soup_Manager::getDefaultConnection()->getAdapter()->lastInsertId();
	}
	
    /**
     * Oluşturulan SQL cümlesini çalıştırır
     * @return Null|Integer|Soup_Result
     */
	public function execute(){
		$this->_builSqlString();

		try{
		
			if(strcmp($this->_operation, "SELECT") == 0){
				$smt = Soup_Manager::getDefaultConnection()->getAdapter()->query($this->_sql);
			
				return $this->_reFormat($smt->fetchAll(Mysql::FETCH_ASSOC));
			}
			else{
				$smt = Soup_Manager::getDefaultConnection()->getAdapter()->exec($this->_sql);
			
				return $smt;
			}
		}
		catch(Exception $e){
            throw new Soup_Query_Exception($e->getMessage() ."<br /><br /><pre>". $this->_sql ."</pre>");
		}
	}
    
    /**
     * Sadece tek bir kaydı seçmek için kullanılır
     * @return Null|Soup_Result 
     */
    public function fetchOne(){
        $result = $this->limit(1)->execute();

        return !is_null($result) ? $result[0] : NULL;
    }
	
	public static function foundRows(){		
		return Soup_Manager::getDefaultConnection()->getAdapter()->query("SELECT FOUND_ROWS() AS rowCount")->fetchColumn(0);
	}
    
    /**
     * WHERE SQL cümlesine girilen parameteleri formatlar
     * @param string $string
     * @param string|array $args
     * @return string 
     */
    private function _format($string, $args){
    	!is_array($args) AND $args = (array)$args;
    	
    	$size = sizeof($args);
		
		if($size > 0){			
			for($i = 0; $i < $size; $i++){
				if(!is_array($args[$i])){
					$string = preg_replace('/\?/', $this->_quoteValues($args[$i]), $string, 1);
				}
				else{
					foreach($args[$i] as $key => $arg){
						$args[$i][$key] = $this->_quoteValues($arg);
					}

					$values = implode(", ", $args[$i]);
					$string = preg_replace('/\?/', $values, $string, 1);
				}
			}
		}
		
		return $string;
    }
    
    /**
     * Gönderilen paramtere değerleri için escape işlemi uygular
     * @param string $value
     * @return string
     */
    private function _quoteValues($value){
        return !is_null( $value ) ? "'". addcslashes($value, "\000\n\r\\'\"\032") ."'" : $value;
    }
    
    private function _quoteTableName($name){
		if(strpos($name, ".") === FALSE){
			return $this->_quoteSimpleTableName($name);
		}
		
		$parts = explode(".", $name);
		
		foreach($parts as $key => $part){
			$part[$key] = $this->_quoteSimpleTableName($part);
		}
		
		return implode(".", $parts);
	}
	
	private function _quoteSimpleTableName($name){
		return "`". $name ."`";
	}
	
	private function _quoteColumnName($name){
		if(($pos = strrpos($name, ".")) !== FALSE){
			$prefix = $this->_quoteTableName(substr($name, 0, $pos)) .".";
			$name	= substr($name, ($pos + 1));
		}
		else{
			$prefix = "";
		}
		
		return $prefix .(($name == "*") ? $name : $this->_quoteSimpleColumnName($name));
	}
	
	private function _quoteSimpleColumnName($name){
		return "`". $name ."`";
	}
    
	private function _isQuoted($name){
		if(strpos($name, "`") !== FALSE){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
    /**
     * Bir tablo içinde bulunan sütun bilgilerini döndürür
     * @param string $table
     * @return array 
     */
    private function _getColumnsInfo($table){
        return Soup_Manager::getDefaultConnection()->getAdapter()->getColumnsInfo($table);
    }
    
    /**
     * Bir tablo içinde bulunan sütunları döndürür
     * @param string $table 
     * @return void
     */
    private function _setTableInfo($table){
        $columns = $this->_getColumnsInfo($table);
        
        foreach($columns as $column){
            $this->_tableInfos[$table][] = $column["field"];
        }
    }
	
	private function _rename($name){
		if(empty($name) || is_null($name)){
			return $name;
		}
		
		foreach($this->_alias as $k => $table){
			if(in_array($name, $this->_tableInfos[$table])){
				return $this->_quoteTableName($table) .".". $this->_quoteColumnName($name) .
							self::SQL_AS .
							 $this->_quoteColumnName(strtoupper($table) ."__". $name);
			}
		}
	}
    
    /**
     * SELECT SQL cümlesi içinde geçen sütun isimlerini yeniden adlandırır.
     * @param string $columns
     * @return string 
     */
    private function _renameColumnName($columns){
		$asPattern			= "/^(.*?)(?i:\s+as\s+|\s+)(.*)$/";
		$stringColPattern	= "/\s*,\s*/";
		$isSqlCalc			= FALSE; 
		
		if(is_string($columns)){
			if(strpos($columns, trim(self::SQL_CALC_FOUND_ROWS)) !== FALSE){
				$columns	= substr($columns, strlen(trim(self::SQL_CALC_FOUND_ROWS)) + 1);
				$isSqlCalc	= TRUE;
			}
			
			if(trim($columns) == trim(self::SQL_WILDCARD)){
				foreach($this->_tableInfos as $table => $columns){
					foreach($columns as $key => $column){
						$columns[$key] = $table .".". $column;
					}
				}
			}
			else{
				$columns = preg_split($stringColPattern, trim($columns), -1, PREG_SPLIT_NO_EMPTY);
			}
		}
		
	// Alias ile belirtilmiş "Sütun" isimlerini "Tablo" isimlerine çeviriyoruz.
		foreach($columns as $key => $column){
			if(preg_match($asPattern, $column, $matches)){
				if(strpos($matches[1], "(") === FALSE){
					$columns[$key] = $matches[1] . self::SQL_AS . $this->_quoteColumnName($matches[2]);
				}
				else{
					$columns[$key] = $matches[1] . self::SQL_AS . $this->_quoteColumnName("__". $matches[2]);
				}
			}				
			
			if(strpos($columns[$key], ".") !== FALSE){
				list($alias, $col) = explode(".", $columns[$key]);

				if(array_key_exists($alias, $this->_alias)){
					if(!$this->_isQuoted($col)){
						$columns[$key] = $this->_quoteColumnName($this->_alias[$alias]) .".". 
											$this->_quoteColumnName($col) . self::SQL_AS . 
											$this->_quoteColumnName(strtoupper($this->_alias[$alias]) ."__". $col);
					}
					else{
						if(preg_match($asPattern, $col, $matches)){
							$col = $this->_quoteColumnName($matches[1]) . self::SQL_AS . $this->_quoteColumnName(strtoupper($this->_alias[$alias]) ."__". trim($matches[2], "`"));
						}

						$columns[$key] = $this->_quoteColumnName($this->_alias[$alias]) .".". $col;
					}
				}
				else{
					$columns[$key] = $this->_quoteColumnName($table) .".". 
										$this->_quoteColumnName($col) . self::SQL_AS . 
										$this->_quoteColumnName(strtoupper($table) ."__". $col);
				}
			}
		}
		
		$compiled = ""; if($isSqlCalc){
			$compiled = self::SQL_CALC_FOUND_ROWS;
		}
		
		$compiled .= implode(", ", $columns);

		return $compiled;
    }
    
    /**
     * Sorgu sonucu dönen kayıtları formatlar
     * @param array $result
     * @return Null|Soup_Result 
     */
    private function _reFormat($result){     
        $relations  = array();
        $aliases    = $this->_alias;
        
        array_walk(array_values($this->_relations), function(&$value) use(&$aliases, &$relations){
            preg_match("/(.+)\.(.+)\s=\s(.+)\.(.+)/", $value, $matches);

            $left   = $aliases[$matches[3]];
            $right  = $aliases[$matches[1]];
            
            if(sizeof(($key = array_keys($relations, $left, TRUE))) > 0){
                $relations[$key[0] ."/". $right] = $right;
            }
            else{
                $relations[$left ."/". $right] = $right;
            }
        });
        
        $nestify = function($array, $delimiter = "/"){
            if(!is_array($array))
                return FALSE;
            
            $nested     = array();
            $pattern    = "/" . preg_quote($delimiter, "/") . "/";
            
            foreach($array as $key => $node){
                $parts          = preg_split($pattern, $key, -1, PREG_SPLIT_NO_EMPTY);
                $leafPart       = array_pop($parts);
                $parentArray    =& $nested;
                
                foreach($parts as $part){
                    if(!isset($parentArray[$part]) || !is_array($parentArray[$part])){
                        $parentArray[$part] = array();
                    }
                    
                    $parentArray =& $parentArray[$part];
                }
                
                if(empty($parentArray[$leafPart])){
                    $parentArray[$leafPart] = array();
                }
            }
            
            return $nested;
        };
        
        if(sizeof($result) > 0){
            $data = array();
            $skeleton = $nestify($relations);
            
            foreach($result as $row){
                $stack = array();
                
                foreach($row as $key => $value){
                    list($table, $field) = explode("__", $key);
                    
                    $stack[$table][$field] = $value;
                }
                
                $data[] = sizeof($skeleton) > 0 ? $this->_setDataRows($stack, $skeleton) : $stack;
            }
            
            foreach($data as $key => $value){
            	$nValue         = array_values( $value);
            	$newData[$key]  = $nValue[0];
            }
			
			
			if(strpos($this->_sql, trim(self::SQL_CALC_FOUND_ROWS)) !== FALSE){
				$newData["foundRows"] = Soup_Manager::getDefaultConnection()->getAdapter()->query("SELECT FOUND_ROWS() AS foundRows")->fetchColumn();
			}
            
            $originalTable  = Soup_Inflector::classify(current(explode(" ", $this->_sqlParts["from"])));
            $resultObject   = new Soup_Result($originalTable);
            
//             print $originalTable;
//             print_r($this->_sqlParts["where"]);
            if(isset($this->_sqlParts["where"]))
            	$resultObject->setWhere($this->_sqlParts['where'], $originalTable);
            
            return $resultObject->populate($newData);
        }
        else{
            return NULL;
        }
    }
    
    /**
     * Gelen değerleri ilgili tablo ile ilişkilendirir.
     * @param array $node
     * @param array $skeleton
     * @return array 
     */
    private function _setDataRows($node, $skeleton){
        $stack = array();
        
        foreach($skeleton as $key => $bone){
            if(is_array($bone) && sizeof($bone) > 0){
                $stack[Soup_Inflector::classify($key)] = $node[$key];
                $stack[Soup_Inflector::classify($key)] = array_merge($stack[Soup_Inflector::classify($key)], $this->_setDataRows($node, $bone));
            }
            else{
                $stack[Soup_Inflector::classify($key)] = $node[$key];
            }
        }
        
        return $stack;
    }
    
    /**
     * Oluşturulan SQL cümlesini çalıştırır
     * @return Null|Integer|Soup_Result
     */
    public static function exec($sql){
    	try{
    		return Soup_Manager::getDefaultConnection()->getAdapter()->exec($sql);
    	}
    	catch(Exception $e){
    		throw new Soup_Query_Exception($e->getMessage() ."<br /><br /><pre>". $sql ."</pre>");
    	}
    }
    
    /**
     * Oluşturulan SQL cümlesini çalıştırır
     * @return Null|Integer|Soup_Result
	 * @todo PDO FetchColumn methodu ile değiştirilip kontrol edilecek.
     */
    public static function fetchColumn($sql){
    	try{
    		$smt = Soup_Manager::getDefaultConnection()->getAdapter()->query($sql);
    		return $smt->fetchAll(PDO::FETCH_COLUMN);
    	}
    	catch(Exception $e){
    		throw new Soup_Query_Exception($e->getMessage() ."<br /><br /><pre>". $sql ."</pre>");
    	}
    }
    
    /**
     * Oluşturulan SQL cümlesini çalıştırır
     * @return Null|Integer|Soup_Result
     */
    public static function query($sql){
    	try{
    		$smt = Soup_Manager::getDefaultConnection()->getAdapter()->query($sql);
    		return $smt->fetchAll(Mysql::FETCH_ASSOC);
    	}
    	catch(Exception $e){
    		throw new Soup_Query_Exception($e->getMessage() ."<br /><br /><pre>". $sql ."</pre>");
    	}
    }
	
	/**
	 * Sql sorgusu geri döndürülür.
	 * @return string
	 */
	public function __toString(){		
		return (string)$this->_builSqlString()->_sql;
	}

}

?>