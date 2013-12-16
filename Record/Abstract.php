<?php

/** 
 * @author canerdogan
 * 
 * 
 */
abstract class Soup_Record_Abstract extends Soup_Access {
	
	/**
	 * @param Soup_Table $_table
	 */
	protected $_table;
	
	/**
	 * 
	 * Sql özellikleri orderBy vs gibi burada tutuluyor.
	 * @var array
	 */
	private $_sqlOptions = array();
	
	/**
	 * Tablo ayarlanıyor
	 * @return void
	 */
	public function setTableName($tableName)
    {
        $this->_table->setTableName($tableName);
    }
	
	/**
	 * One-to-one relation kuruluyor
	 *
	 * @param string $componentName		relation adı
	 * @param string $options			relation seçenekleri
	 * 
	 * @return Soup_Record				this object
	 */
	public function hasOne() {
		$this->_table->addRelation ( func_get_args (), Soup_Table::RELATION_ONE );
		
		return $this;
	}
	
	/**
	 * One-to-Many / Many-to-Many relation kuruluyor
	 *
	 * @param string $componentName		relation adı
	 * @param string $options			relation seçenekleri
	 * 
	 * @return Soup_Record				this object
	 */
	public function hasMany() {
		$this->_table->addRelation ( func_get_args (), Soup_Table::RELATION_MANY );
		
		return $this;
	}
	
	/**
	 * Sütun tanımlamaları yapılıyor 
	 *
	 * @param string $name
	 * @param string $type
	 * @param integer $length
	 * @param mixed $options
	 * @return void
	 */
	public function hasColumn($name, $type = null, $length = null, $options = array()) {
		$this->_table->setColumn ( $name, $type, $length, $options );
	}
	
	/**
	 * Table nesnesi geri döner
	 *
	 * @return Soup_Table		Soup_Table nesnesi 
	 */
	public function getTable() {
		return $this->_table;
	}
	
	/**
	 * Detaylı select sorgusu yazabilirsiniz
	 * 
	 * @param mixed $field
	 * @return Soup_Record
	 */
	public function select($field = NULL) {
		return $this;
	}
	
	public function orderBy($field) {
		if(!$field)
			throw new Soup_Record_Exception('This field is required.');
			
		$this->_sqlOptions['orderBy'] = $field;
		
		return $this;
	}
	
	/**
	 * Arama methodu
	 * Kullanım:
	 * ->find(1);											PrimaryKey'e göre sonuç döndürür
	 * ->find( Array('where' => 'id = 1') ); 				İçerisine sorgu elemanları verilebilir array olarak.
	 * ->find( Array('where' => Array('id = ?', 1) ) );		Şeklinde de kullanılabilir bu kullanım sayesinde filter kullanılabilir.
	 * ->find( Array('where' =>
	 * 			Array('id = ? And name = ?',
	 * 				Array(1, 'Can')
	 * 			)
	 * 		 )
	 * );													Şeklinde de kullanılabilir bu kullanım sayesinde filter kullanılabilir.
	 * 
	 * @throws Soup_Record_Exception
	 * @return Soup_Result|array
	 * @todo: Statik methodlar eklenecek
	 */
	public function find() {
		$num_args = func_num_args();
		
		// Herhangi bir argüman verilmediyse
		if ($num_args<=0)
			return $this->findAll();

		$arg = func_get_arg(0);
		
		if (is_numeric($arg)) {
			$result = Soup_Query::select()
						 ->from($this->getTable()->getTableName())
						 ->where($this->getTable()->getPrimaryKey() . ' = ?', $arg)
						 ->execute();
						 
			return $result;
		}
		
		else if (is_array($arg)) {
			$query = Soup_Query::select();
			$query->from($this->getTable()->getTableName());
			
			if (array_key_exists('where', $arg)) {
				if (is_array($arg['where'])) {
					$query->where($arg['where'][0], $arg['where'][1]);
				}
				
				else {
					$query->where($arg['where']);
				}
			}
			
			if (array_key_exists('orderBy', $arg)) {
				$query->orderBy($arg['orderBy']);
			}
			
			if (array_key_exists('orderBy', $this->_sqlOptions)) {
				$query->orderBy($this->_sqlOptions['orderBy']);
			}
			
			if (array_key_exists('groupBy', $arg)) {
				$query->groupBy($arg['groupBy']);
			}
			
			if (array_key_exists('having', $arg)) {
				$query->having($arg['having']);
			}
			
			if (array_key_exists('offset', $arg)) {
				$query->offset($arg['offset']);
			}
			
			if (array_key_exists('limit', $arg)) {
				$query->limit($arg['limit']);
			}
			
			$result = $query->execute();
			
			return $result;
		}

		else {
			throw new Soup_Record_Exception('Wrong usage find() method.');
		}
			
	}
	
	
	/**
	 * @param integer limit
	 * @return Soup_Result|array
	 */
	public function findAll($limit=NULL) {
		$query = Soup_Query::select()
						 	->from( $this->getTable()->getTableName() );
						 	
		if (array_key_exists('orderBy', $this->_sqlOptions)) {
				$query->orderBy($this->_sqlOptions['orderBy']);
		}
		
		if ($limit)
			$query->limit($limit);
		
		$result = $query->execute();
		
		return $result;
	}
	
	
	/**
	 * 
	 * 
	 * @param integer $primaryKey
	 * @return Soup_Result
	 */
	public function findPK($primaryKey) {
		$result = Soup_Query::select()
						 	->from($this->getTable()->getTableName())
						 	->where($this->getTable()->getPrimaryKey() . ' = ?', $primaryKey)
						 	->limit(1)
						 	->fetchOne();
		
		return $result;
	}
	
	
	/**
	 * 
	 * @param string $fieldName
	 * @param string $params
	 * @return Soup_Result
	 */
	public function findOneBy($fieldName, $params) {
		if(is_null($params)){
			$result = Soup_Query::select()
							->from($this->getTable()->getTableName())
							->where($fieldName . ' IS NULL')
							->limit(1)
							->fetchOne();
			
		} else {
			$result = Soup_Query::select()
						 	->from($this->getTable()->getTableName())
						 	->where($fieldName . ' = ?', $params)
						 	->limit(1)
						 	->fetchOne();
		}
		
		return $result;
	}
	
	/**
	 * Fieldname'e göre arama yapıyor.
	 *
	 * @param string $column
	 * @param string $value
	 * @return Soup_Result|array
	 */
	public function findBy($fieldName, $params) {
		if(is_null($params)){
			$result = Soup_Query::select()
							->from($this->getTable()->getTableName())
							->where($fieldName . ' IS NULL')
							->fetchOne();
		} else {
			$result = Soup_Query::select()
						 	->from($this->getTable()->getTableName())
						 	->where($fieldName . ' = ?', $params)
						 	->execute();
		}
		return $result;
	}
	
	/**
	 *
	 * @param string $column
	 * @param string $value
	 * @return Soup_Result|array
	 */
	public function __call($method, $arguments) {
		$lcMethod = strtolower ( $method );
		
		if (substr ( $lcMethod, 0, 6 ) == 'findby') {
			$by = substr ( $method, 6, strlen ( $method ) );
			$method = 'findBy';
		} else if (substr ( $lcMethod, 0, 9 ) == 'findoneby') {
			$by = substr ( $method, 9, strlen ( $method ) );
			$method = 'findOneBy';
		}
		
		if (isset ( $by )) {
// 			if (! isset ( $arguments [0] )) {
// 				throw new Soup_Record_Exception ( 'You must specify the value to ' . $method );
// 			}
			if (! isset ( $arguments [0] ))
				$arguments[0] = NULL;
			
			$fieldName = $this->getTable()->resolveFindByFieldName ( $by );
			
			if ($this->getTable()->hasField ( $fieldName )) {
				
				return $this->$method ( $fieldName, $arguments [0] );
			} else if ($this->getTable ()->hasRelation ( $by )) {
				throw new Soup_Record_Exception ( 'Cannot findBy many relationship.' );
			
		//                $relation = $this->getTable()->getR($by);
			//
			//                if ($relation['type'] === Doctrine_Relation::MANY) {
			//                    throw new Doctrine_Table_Exception('Cannot findBy many relationship.');
			//                }
			//
			//                return $this->$method($relation['local'], $arguments[0]);
			} else {
				return $this->$method ( $by, $arguments );
			}
		}
		
		throw new Soup_Table_Exception( sprintf ( 'Unknown method %s::%s', get_class ( $this ), $method ) );
	}
	
}

?>