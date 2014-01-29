<?php

/**
 *
 * @package     Soup
 * @subpackage  Record
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Can Erdogan <can@canerdogan.net>
 *
 * @method mixed findBy*(mixed $value) magic finders; @see __call()
 * @method mixed findOneBy*(mixed $value) magic finders; @see __call()

 */
class Soup_Record extends Soup_Record_Abstract implements Countable, IteratorAggregate, Serializable {
	
	/**
	 * Save()'den önce verilerin saklanacağı depo.
	 * @var array $_data
	 */
	protected $_data = array ();
	
	/**
	 * Save'de kullanılacak olan verilerin saklanacağı array
	 * @var array $_values
	 */
	protected $_values = array ();
	
	/**
	 * Tanımlanmış relationların saklanacağı depo
	 * @var unknown_type
	 */
	protected $_relations = array ();
	
	/**
	 * @var integer $_index                  this index is used for creating object identifiers
	 */
	private static $_index = 1;
	
	/**
	 * @var integer $_oid                    object identifier, each Record object has a unique object identifier
	 */
	private $_oid;
	
	/**
	 * @var Soup_Record $_parent
	 */
	private $_parent;
	
	/**
	 * @var boolean
	 */
	private $_result = false;
	
	/**
	 * Construct
	 * 
	 * @param Soup_Table|null $table
	 * @param boolean $relationTable
	 */
	public function __construct($table = null, $relationTable = false) {
		if (isset ( $table ) && $table instanceof Soup_Table) {
			$this->_table = $table;
		} elseif (is_string($table)) {
			$newModel = new $table;
			$this->_table = $newModel->getTable();
			$exists = false;
		} else {
			// Bu nesneye ait tablo nesnesini alıyoruz.
			$class = get_class ( $this );
			$this->_table = new Soup_Table ( $class );
			$exists = false;
		}
		
		$this->_oid = self::$_index;
		
		self::$_index ++;
	}
	
	/**
	 * the current instance counter used to generate unique ids for php objects. Contains the next identifier.
	 *
	 * @return integer
	 */
	public static function _index() {
		return self::$_index;
	}
	
	/**
	 * @see $_oid;
	 *
	 * @return integer the object identifier
	 */
	public function getOid() {
		return $this->_oid;
	}
	
	/**
	 * Uniq class id
	 * 
	 * @return integer the object identifier
	 */
	public function oid() {
		return $this->_oid;
	}
	
	/**
	 * Parent class ayarlanıyor.
	 * @param Soup_Record|Soup_Result $parent
	 */
	public function setParent($parent) {
		$this->_parent = $parent;
	}
	
	/**
	 * Parent class geri döndürülüyor.
	 * @return Soup_Record $_parent
	 */
	public function getParent() {
		return $this->_parent;
	}
	
	/**
	 * Get
	 * 
	 * @param string $fieldName
	 * @see Soup_Access::get()
	 */
	public function get($fieldName) {
		if (! isset ( $this->_data [$fieldName] )) {
			if (isset ( $fieldName )) {
				if ($this->getTable ()->hasRelation ( $fieldName )) {
					$this->set ( $fieldName, $this->getTable ()->getRelation ( $fieldName ) );
				
				} else {
					throw new Soup_Record_Exception ( 'Not found relation with ' . $fieldName . '.' );
				}
			} else {
//				$this->set ( '', new Soup_Record() );
				throw new Soup_Record_Exception ( 'Can\'t use array.' );
			}
			
			return end ( $this->_data );
		} else {
			return $this->_data [$fieldName];
		}
	}
	
	/**
	 * Set data
	 *
	 * @param integer $fieldName
	 * @param Soup_Record $record
	 * @return void
	 * @see Soup_Access::set()
	 */
	public function set($fieldName, $record) {
		if (! $record instanceof Soup_Record) {
			$this->_values [$fieldName] = $record;
			
//			if ($this->_parent instanceof Soup_Record)
//				$this->_parent->setParentField(get_called_class(), $fieldName, $record);
		} else {
			$record->setParent ( $this );
			$this->_relations [$fieldName] = $record;
		}
		
		$this->_data [$fieldName] = $record;
	}
	

	/**
	 * Adds a record to collection
	 *
	 * @param Soup_Record $record		Record to be added
	 * @param string $key				Optional key for the record
	 * @return boolean
	 */
	public function add($record, $key = null) {
		print $key . ' ' . $record;
		
		foreach ( $this->_data as $val ) {
			if ($val === $record) {
				return false;
			}
		}
		
		if (isset ( $key )) {
			if (isset ( $this->_data [$key] )) {
				return false;
			}
			$this->_data [$key] = $record;
			return true;
		}
		
		$this->_data [] = $record;
		
		return true;
	}
	
	
	/**
	 * Save
	 * 
	 * @todo preSave, afterSave eventleri eklenecek
	 * @todo validationlar eklenecek 
	 * @return integer $primaryKey
	 */
	public function save() {
		try {
			$calledTable = Array();
			foreach ( $this->_data as $key => $value ) {
				if ($value instanceof Soup_Record) {
					$foreignKey = $this->getTable()->getForeignKey($value->getTable()->getName());
					
					if ($foreignKey['type'] == Soup_Table::RELATION_ONE) {
						$this->set($foreignKey['local'], $value->save());
					} else {
						$calledTable[$key] = $value;
					}
					
				}
			}
			
			Soup_Query::insert ( $this->getTable ()->getTableName (), $this->_values )->execute ();
			$lastId = Soup_Query::lastInsertId();
			
			$this->set($this->getTable()->getPrimaryKey(), $lastId);
			
			foreach ($calledTable as $key => $value) {
				$foreignKey = $this->getTable()->getForeignKey($value->getTable()->getName());
				
				$value->set($foreignKey['foreign'], $lastId);
				$value->save();
			}
			
			return $lastId;
		} catch (Soup_Query_Exception $e) {
			throw new Soup_Record_Exception($e->getMessage(), $e->getCode());
		}
	}
	
	/**
	 * serialize
	 * this method is automatically called when an instance of Doctrine_Record is serialized
	 *
	 * @return string
	 * @todo Bu kısım hatalı tekrar yazılacak
	 */
	public function serialize()
	{
		$str = serialize($this->_data);
		
		return $str;
	}
	
	/**
	 * this method is automatically called everytime an instance is unserialized
	 *
	 * @param string $serialized                Soup_Record as serialized string
	 * @return void
	 * @todo Bu kısım hatalı tekrar yazılacak
	 */
	public function unserialize($serialized)
	{
		$this->_data = unserialize($serialized);
	}
	
	/**
	 * @see Countable::count()
	 */
	public function count() {
		return count ( $this->_data );
	}
	
	/**
	 * @see Soup_Access::remove()
	 */
	public function remove($key) {
		$removed = $this->_data [$key];
		
		unset ( $this->_data [$key] );
		unset ( $this->_values [$key] );
		return $removed;
	}
	
	/**
	 * Whether or not this collection contains a specified element
	 *
	 * @param mixed $key                    the key of the element
	 * @return boolean
	 */
	public function contains($key) {
		return isset ( $this->_data [$key] );
	}
	
	/**
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new ArrayIterator ( $this->_data );
	}

	public function toArray() {
		return $this->_data;
	}
	
	public function __toString() {
		return 'Soup_Record';
	}
}

?>