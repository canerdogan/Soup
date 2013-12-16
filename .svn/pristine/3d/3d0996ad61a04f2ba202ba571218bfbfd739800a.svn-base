<?php

/** 
 * Soup Table
 * 
 * @author canerdogan
 */
class Soup_Table {
	
	const RELATION_ONE = 1;
	const RELATION_MANY = 2;
	
	/**
	 * @var mixed $identifier		primary key
	 */
	protected $_identifier = array ();
	
	/**
	 * @var mixed $columns			Tablo sütunları
	 * 								Tablo sütunları şu şekilde tanımlanır:
	 * 								-- type			sütun tipi, 'integer' 'string' vs.
	 * 								-- length		sütun uzunluğu, 11 vs.
	 * 
	 * 								Ek olarak eklenebilir tanımlamalar ve Validationlar
	 * 								-- notnull		sütun notnull olarak tanımlanmış
	 * 								-- values		enum değerleri
	 * 								-- require		require validator
	 * 								... diğer validationlar
	 */
	protected $_columns = Array ();
	
	/**
	 * @var array $_relations				Relation bilgileri burada tutuluyor.
	 * 										--local		Lokal tablonun sütun adı
	 * 										--foreign	Relation'ı olan tablonun bağlı olduğu sütun adı
	 * 										--type		Soup_Table::RELATION_ONE veya Soup_Table::RELATION_MANY
	 */
	protected $_relations = Array ();
	
	/**
	 * Unique listesi Array olarak burada tutulur.
	 *
	 * @var array $_uniques
	 */
	protected $_uniques = array ();
	
	/**
	 * @var array $_fieldNames				Soup_Record'larda kullanılacak isimleri array olarak tutuluyor.
	 * 										keyler sütun isimleri valueler field isimleri
	 * 										Geri dönüş yapabilmek için sütun adından fieldName bulabilmek için tutuyoruz. 
	 */
	protected $_fieldNames = array ();
	
	/**
	 *
	 * @var array $_columnNames				Sütun isimleri. Array olarak tutuluyor
	 * 										keyler field isimleri valueler sütun isimleri
	 * 										Geri dönüş yapabilmek için fieldName'den sütun adını bulabilmek için tutuyoruz.
	 */
	protected $_columnNames = array ();
	
	/**
	 * @var integer $_columnCount			Keşlenmiş :) sütun sayısı
	 */
	protected $_columnCount = 0;
	
	/**
	 * @var boolean $hasDefaultValues		tablonun varsayılan olarak değerinin olup olmadığı kaydediliyor
	 */
	protected $hasDefaultValues;
	
	/**
	 * 
	 * @var array							Relation Instanceları burada saklanıyor.
	 */
	protected $_relationInstance = Array();
	
	/**
	 * @var array $options		tablo bilgileri
	 * 							--name			Model adı
	 * 							--tableName		Tablonun adı
	 * 							diğerlerini açıklamıyorum.
	 */
	protected $_options = array ('name' => null, 'tableName' => null, 'charset' => null, 'collate' => null, 'indexes' => array (), 'foreignKeys' => array (), 'orderBy' => null );
	
	/**
	 * 
	 * 
	 * @param string $name
	 * @param Soup_Connection $conn
	 */
	public function __construct($name, Soup_Connection $conn = null) {
		$this->_conn = $conn;
		$this->_options ['name'] = $name;
	}
	
	/**
	 * Alanın primary key olup olmadığını kontrol ediyoruz.
	 *
	 * @param string $fieldName  field adı
	 * @return boolean           Eğer identifier/primary key ise TRUE geri döner.
	 */
	public function isIdentifier($fieldName) {
		return ($fieldName === $this->getIdentifier () || in_array ( $fieldName, ( array ) $this->getIdentifier () ));
	}
	
	/**
	 * Primary keyleri döndürüyor.
	 * 
	 * @return array
	 */
	public function getIdentifier() {
		return $this->_identifier;
	}
	
	/**
	 * Primary key'i döndürüyor.
	 * 
	 * @return string
	 */
	public function getPrimaryKey() {
		return $this->_identifier[0];
	}
	
	/**
	 * Sütun adına göre field adını verir
	 * 
	 * @param string $column
	 */
	public function getFieldName($column) {
		return $this->_fieldNames[$column];
	}
	
	/**
     * Sütun adını verir fieldName e göre
     * FieldName = alias
     * Alias bulunamazsa alias yaratır. Tümünü küçük harf yaparak
     *
     * @param string $fieldName
     * @return string
     */
    public function getColumnName($fieldName)
    {
        $fieldName = is_array($fieldName) ? $fieldName[0]:$fieldName;

        if (isset($this->_columnNames[$fieldName])) {
            return $this->_columnNames[$fieldName];
        }

        return strtolower($fieldName);
    }
	
	/**
     * Fieldin tipini verir
     *
     * @param string $fieldName
     * @return string
     */
    public function getTypeOf($fieldName)
    {
        return $this->getTypeOfColumn($this->getColumnName($fieldName));
    }

    /**
     * Sütunun tipini verir
     *
     * @param string $columnName
     * @return string
     */
    public function getTypeOfColumn($columnName)
    {
        return isset($this->_columns[$columnName]) ? $this->_columns[$columnName]['type'] : false;
    }
    
	/**
	 * Tablo adını alıyoruz.
	 * @return string
	 */
	public function getName() {
		return $this->_options ['name'];
	}
    
	/**
	 * Tablo adını alıyoruz.
	 * @return string
	 */
	public function getTableName() {
		return $this->_options ['tableName'];
	}
	
	/**
	 * Model adını çekiyoruz.
	 * @return string
	 */
	public function getModelName() {
		return $this->_options ['name'];
	}
	
	/**
	 * Relation bulunan modeli geri döndürür.
	 * 
	 * @param string $relationName
	 * @return array
	 */
	public function getRelation($relationName) {
		if(! array_key_exists($relationName, $this->_relationInstance)) {
			$this->_relationInstance[$relationName] = new $relationName;
		}
		
		return $this->_relationInstance[$relationName];
	}
	
	public function getForeignKey($relationName) {
		if(!$this->hasRelation($relationName))
			return false;
		
		return $this->_relations[$relationName];
	}
	
	/**
	 * foreignKey ekleniyor.
	 *
	 * @param array $definition     foreignkey 
	 * @return void
	 */
	public function addForeignKey(array $definition) {
		$this->_options ['foreignKeys'] [] = $definition;
	}
	
	/**
	 * Tablo ayarlanıyor.
	 * @return void
	 */
	public function setTableName($tableName) {
		$this->_options ['tableName'] = $tableName;
	}
	
	public function bind($name, $options = array())
	{
		$e    = explode(' as ', $name);
		$e    = array_map('trim', $e);
		$name = $e[0];
		$alias = isset($e[1]) ? $e[1] : $name;
	
		if ( ! isset($options['type'])) {
			throw new Soup_Table_Exception('Relation type not set.');
		}
	
		if ($this->hasRelation($alias)) {
			unset($this->_relations[$alias]);
		}
	
		$this->_relations[$alias] = array_merge($options, array('class' => $name, 'alias' => $alias));
	
		return $this->_relations[$alias];
	}
	
	/**
	 * Tabloya relation ekliyoruz
	 *
	 * @param array $args       ilk argüman string gelir. Relation tablosunun ismi ikinci argüman array gelir, relation türü ve seçenekleri.
	 * @param integer $type     Soup_Table::RELATION_ONE veya Soup_Table::RELATION_MANY
	 * @return void
	 */
	public function addRelation($args, $type) {
		$options = (! isset ( $args [1] )) ? array () : $args [1];
		$options ['type'] = $type;
		
// 		$this->_relations[$args [0]] = $options;
		$this->bind($args[0], $options);
	}
	
	/**
     * Relation var mı yok mu kontrol ediliyor
     *
     * @param string $alias      relation kodu
     * @return boolean           relation var ise true yoksa false
     */
    public function hasRelation($alias)
    {
        if(array_key_exists($alias, $this->_relations)){
        	return true;
        }
        return false;
    }
    
	/**
     * Column var mı yok mu kontrol ediliyor.
     *
     * @param string $columnName  	Field adı
     * @return boolean          	Column tanımlı ise True yoksa False döner.
     */
    public function hasColumn($columnName)
    {
    	return isset($this->_columns[strtolower($columnName)]);
    }
    
	/**
     * Field var mı yok mu kontrol ediliyor
     *
     * Bu method fieldName varsa true döner 
	 * @param string $fieldName
     * @return boolean
     */
    public function hasField($fieldName)
    {
        return isset($this->_columnNames[$fieldName]);
    }
    
    /**
     * Column sayısını geri döndürür
     * 
     * @return integer 
     */
    public function columnCount()
    {
    	return $this->_columnCount;
    }
    
	/**
     * Resolves the passed find by field name inflecting the parameter.
     *
     * This method resolves the appropriate field name
     * regardless of whether the user passes a column name, field name, or a Doctrine_Inflector::classified()
     * version of their column name. It will be inflected with Doctrine_Inflector::tableize()
     * to get the column or field name.
     *
     * @param string $name
     * @return string $fieldName
     */
    public function resolveFindByFieldName($name)
    {
        $fieldName = Soup_Inflector::tableize($name);
        if ($this->hasColumn($name) || $this->hasField($name)) {
            return $this->getFieldName($this->getColumnName($name));
        } else if ($this->hasColumn($fieldName) || $this->hasField($fieldName)) {
            return $this->getFieldName($this->getColumnName($fieldName));
        } else {
            return false;
        }
    }
	
	/**
	 * Magic method propertiesleri düzenlemek için
	 *
	 * @param string $option
	 * @return mixed
	 */
	public function __get($option) {
		if (isset ( $this->_options [$option] )) {
			return $this->_options [$option];
		}
		return null;
	}
	
	/**
	 * Magic method propertiesleri kontrol etmek için
	 *
	 * @param string $option
	 */
	public function __isset($option) {
		return isset ( $this->_options [$option] );
	}
	
	/**
	 * Tabloya sütunları ekliyoruz
	 *
	 * @param string $name			sütunun adı
	 * @param string $type			sütun veri tipi
	 * @param integer $length		maksimum veri
	 * @param mixed $options
	 * @param boolean $prepend		sütunu önden mi arkadan mı ekleyeceği. Varsayılan olarak arkadan eklenir.
	 * @throws Soup_Table_Exception	hatalı parametre girilirse exception atılır.
	 * @return void
	 */
	public function setColumn($name, $type = null, $length = null, $options = array(), $prepend = false) {
		if (is_string ( $options )) {
			$options = explode ( '|', $options );
		}
		
		foreach ( $options as $k => $option ) {
			if (is_numeric ( $k )) {
				if (! empty ( $option )) {
					$options [$option] = true;
				}
				unset ( $options [$k] );
			}
		}
		
		$fieldName = $name;
		$name = strtolower ( $name );
		
		$fieldName = trim ( $fieldName );
		$name = trim ( $name );
		
		if ($prepend) {
			$this->_columnNames = array_merge ( array ($fieldName => $name ), $this->_columnNames );
			$this->_fieldNames = array_merge ( array ($name => $fieldName ), $this->_fieldNames );
		} else {
			$this->_columnNames [$fieldName] = $name;
			$this->_fieldNames [$name] = $fieldName;
		}
		
		if ($length == null) {
			switch ($type) {
				case 'integer' :
					$length = 8;
					break;
				case 'decimal' :
					$length = 18;
					break;
				case 'string' :
				case 'clob' :
				case 'float' :
				case 'integer' :
				case 'array' :
				case 'object' :
				case 'blob' :
				case 'gzip' :
					//$length = 2147483647;
					

					//All the DataDict driver classes have work-arounds to deal
					//with unset lengths.
					$length = null;
					break;
				case 'boolean' :
					$length = 1;
					break;
				case 'date' :
					// YYYY-MM-DD ISO 8601
					$length = 10;
					break;
				case 'time' :
					// HH:NN:SS+00:00 ISO 8601
					$length = 14;
					break;
				case 'timestamp' :
					// YYYY-MM-DDTHH:MM:SS+00:00 ISO 8601
					$length = 25;
					break;
			}
		}
		
		$options ['type'] = $type;
		$options ['length'] = $length;
		
		if ($prepend) {
			$this->_columns = array_merge ( array ($name => $options ), $this->_columns );
		} else {
			$this->_columns [$name] = $options;
		}
		
		if (isset ( $options ['primary'] ) && $options ['primary']) {
			if (isset ( $this->_identifier )) {
				$this->_identifier = ( array ) $this->_identifier;
			}
			if (! in_array ( $fieldName, $this->_identifier )) {
				$this->_identifier [] = $fieldName;
			}
		}
		if (isset ( $options ['default'] )) {
			$this->hasDefaultValues = true;
		}
		$this->_columnCount++;
	}
}

?>