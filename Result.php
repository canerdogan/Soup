<?php

/**
 *
 * @package     Soup
 * @subpackage  Result
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Can Erdogan <can@canerdogan.net>
 */
class Soup_Result extends Soup_Result_Abstract implements Countable, IteratorAggregate
{

	/**
	 * Propertyler burada tutuluyor.
	 *
	 * @var array $_data
	 */
	protected $_data = array();

	/**
	 * Save'de kullanılacak olan verilerin saklanacağı array
	 *
	 * @var array $_values
	 */
	protected $_values = array();

	/**
	 * Bulunan toplam satır sayısı
	 *
	 * @var integer
	 */
	protected $_foundRows;

	/**
	 * Tanımlanmış relationların saklanacağı depo
	 *
	 * @var array
	 */
	protected $_relations = array();

	/**
	 * Gelen kayıt sayısı
	 *
	 * @var integer $_count
	 */
	protected $_count;

	/**
	 * @var integer $_index this index is used for creating object identifiers
	 */
	private static $_index = 1;

	/**
	 * @var integer $_oid object identifier, each Record object has a unique object identifier
	 */
	private $_oid;

	/**
	 * @var Soup_Record $_parent
	 */
	private $_parent;

	/**
	 * @var string
	 */
	private $_where;

	/**
	 * the current instance counter used to generate unique ids for php objects. Contains the next identifier.
	 *
	 * @return integer
	 */
	public static function _index()
	{
		return self::$_index;
	}

	/**
	 * @see $_oid;
	 *
	 * @return integer the object identifier
	 */
	public function getOid()
	{
		return $this->_oid;
	}

	/**
	 * Uniq class id
	 *
	 * @return integer the object identifier
	 */
	public function oid()
	{
		return $this->_oid;
	}

	/**
	 * Construct
	 */
	public function __construct($table = NULL)
	{
		if (isset ($table) && $table instanceof Soup_Table) {
			$this->_table = $table;
		} else {
			$class = Soup_Inflector::classify(Soup_Inflector::tableize($table));
			if (class_exists($class)) {
				$newClass = new $class ();
				$this->_table = $newClass->getTable();
				$exists = FALSE;
			}
		}
		$this->_oid = self::$_index;
		self::$_index++;
	}

	/**
	 * Parent class ayarlanıyor.
	 *
	 * @param Soup_Result $parent
	 */
	public function setParent(Soup_Result $parent)
	{
		$this->_parent = $parent;
	}

	/**
	 * Parent class geri döndürülüyor.
	 *
	 * @return Soup_Result $_parent
	 */
	public function getParent()
	{
		return $this->_parent;
	}

	/**
	 * Parent class var mı?
	 *
	 * @return boolean
	 */
	public function hasParent()
	{
		return isset($this->_parent);
	}


	/**
	 * @param string $cond
	 * @param string $args
	 */
	public function addWhere($cond, $rel)
	{
		$this->_where[$rel][] = $cond;
	}

	/**
	 * @return array
	 */
	public function getWhere($rel)
	{
		return $this->_where[$rel];
	}

	/**
	 * @param array $where
	 */
	public function setWhere($where, $rel)
	{
		$this->_where[$rel] = $where;
	}

	/**
	 * @param unknown_type $data
	 * @param Soup_Result  $parent
	 */
	public function populate($data, $parent = NULL)
	{
		//		print_r($data);
		$this->_count = 0;
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->_data [$key] = new Soup_Result ((is_numeric($key) ? $this->getTable() : $key));
				$this->_data [$key]->setParent($this);
				$this->_data [$key]->populate($value, $this);
				$this->_count++;
			} else {
				if ($key == 'foundRows') {
					$this->_foundRows = $value;
				} else {
					$this->_data [$key] = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Get
	 *
	 * @param string $fieldName
	 *
	 * @see Soup_Access::get()
	 */
	public function get($fieldName)
	{
		// 		print "\n Get: " . $fieldName . "\n";
		if (!isset ($this->_data [$fieldName]) AND !array_key_exists($fieldName, $this->_data)) {
			if ($this->getTable()->hasRelation($fieldName)) {
				$foreignKey = $this->getTable()->getForeignKey($fieldName);
				// 				print $fieldName . "\n";
				// 				print_r($foreignKey);
				// 				exit();
				$newResult = new Soup_Result ($foreignKey['class']);
				if (!$this->{$foreignKey['local']}) {
					throw new Soup_Result_Exception('This relation field is not set.');
				}
				if ($foreignKey['type'] == Soup_Table::RELATION_ONE) {
					$freshResult = $newResult->findOneBy($foreignKey['foreign'], $this->{$foreignKey['local']});
				} else {
					$freshResult = $newResult->findBy($foreignKey['foreign'], $this->{$foreignKey['local']});
				}
				$this->addWhere($foreignKey['foreign'] . ' = ?', $this->{$foreignKey['local']}, $foreignKey['alias']);
				// 				print $fieldName;
				// 				print $freshResult;
				if (!$freshResult) {
					// 					print 'pipi';
					$className = Soup_Inflector::classify($fieldName);
					$freshResult = new $className;
					// 					$freshResult->set($foreignKey['foreign'], $this->{$foreignKey['local']});
					// 					$freshResult->getTable()
					//                     return $freshResult;
				}
				// 					throw new Soup_Result_Exception('This relation field is not set.');
				$this->set($fieldName, $freshResult);

				return end($this->_data);

			} elseif ($this->getTable()->hasField($fieldName)) {

				//				return NULL;
			} elseif (is_numeric($fieldName) OR is_null($fieldName)) {

				//				$this->add ( new Soup_Result ( $this->getTable () ) );
				//				return end ( $this->_data );
			} else {
				//				Zend_Debug::dump($this->_data);
				throw new Soup_Result_Exception ('Can\'t found field or relation such "' . $fieldName . '"');

			}

			//			if (isset ( $fieldName )) {
			//				$this->set ( $fieldName, new Soup_Result () );
			//			} else {
			//				$this->add ( new Soup_Result () );
			//			}
			//			return end ( $this->_data );
		} else {
			return $this->_data [$fieldName];
		}
	}

	/**
	 *
	 * @param string $condition
	 * @param string $arg
	 *
	 * @return Soup_Result
	 */
	public function where($condition, $arg = NULL)
	{
		if ($this->hasParent()) {
			$foreignKey = $this->getTable()->getForeignKey($this->getParent()->getTable()->getName());
			// 			return $where = $this->getParent()->getWhere();
			if ($foreignKey['type'] == Soup_Table::RELATION_MANY) {
				return $this;
			} else {
				$query = Soup_Query::select()
								   ->from($this->getTable()->getTableName())
								   ->where($condition, $arg);
				// 				print_r($this->_where);
				$where = $this->getWhere($this->getTable()->getName());
				// 				print_r($where);
				if (!is_null($where)) {
					foreach ($where as $cond) {
						$query->andWhere($cond, $this->getParent()->{$foreignKey['foreign']});
					}
				}
				$freshResult = $query->execute();
			}

		} else {
			$query = Soup_Query::select()
							   ->from($this->getTable()->getTableName())
							   ->where($condition, $arg);
			$where = $this->getWhere($this->getTable()->getTableName());
			if (!is_null($where)) {
				foreach ($where as $cond) {
					$query->andWhere($cond);
				}
			}
			// 			print $query;
			$freshResult = $query->execute();
		}

		// 		if(is_null($freshResult))
		// 			$freshResult = new Soup_Result($this->getTable()->getTableName());
		return $freshResult;
	}

	/**
	 *
	 * @param string $order
	 *
	 * @return Soup_Result
	 */
	public function orderBy($order)
	{
		if ($this->hasParent()) {
			$foreignKey = $this->getTable()->getForeignKey($this->getParent()->getTable()->getName());
			if ($foreignKey['type'] == Soup_Table::RELATION_MANY) {
				return $this;
			} else {
				$where = $this->getParent()->getWhere($this->getTable()->getTableName());

				return Soup_Query::select()
								 ->from($this->getTable()->getTableName())
								 ->where($where[0], $this->getParent()->{$foreignKey['foreign']})
								 ->orderBy($order)
								 ->execute();
			}

		} else {
			$freshResult = Soup_Query::select()
									 ->from($this->getTable()->getTableName())
									 ->orderBy($order);
			$where = $this->getWhere($this->getTable()->getTableName());
			if (!is_null($where)) {
				$i = 0;
				foreach ($where as $cond) {
					if ($i == 0) {
						$freshResult->where($cond);
					} else {
						$freshResult->andWhere($cond);
					}
					$i++;
				}
			}

			return $freshResult->execute();
		}
	}

	/**
	 * @return Soup_Result
	 */
	public function last()
	{
		return end($this->_data);
	}

	/**
	 * @return Soup_Result
	 */
	public function first()
	{
		reset($this->_data);

		return current($this->_data);
	}

	/**
	 * Set data
	 *
	 * @param integer $fieldName
	 * @param string  $result
	 *
	 * @return void
	 * @see Soup_Access::set()
	 */
	public function set($fieldName, $result)
	{
		//		print "\n Set: " . $fieldName . " : " . $result . "\n";
		if (!is_object($result)) {
			$this->_values [$fieldName] = $result;
		} else {
			$result->setParent($this);
			$this->_relations [$fieldName] = $result;
		}
		// 		if (! $result instanceof Soup_Result || ! $result instanceof Soup_Record) {
		// 			$this->_values [$fieldName] = $result;
		// 		} else {
		// 			$result->setParent ( $this );
		// 			$this->_relations [$fieldName] = $result;
		// 		}
		$this->_data [$fieldName] = $result;
	}

	/**
	 * Adds a record to collection
	 *
	 * @param Soup_Record $record Record to be added
	 * @param string      $key    Optional key for the record
	 *
	 * @return boolean
	 */
	public function add($record, $key = NULL)
	{
		print $key . ' ' . $record;
		foreach ($this->_data as $val) {
			if ($val === $record) {
				return FALSE;
			}
		}
		if (isset ($key)) {
			if (isset ($this->_data [$key])) {
				return FALSE;
			}
			$this->_data [$key] = $record;

			return TRUE;
		}
		$this->_data [] = $record;

		return TRUE;
	}

	/**
	 * @see Soup_Access::remove()
	 */
	public function remove($key)
	{
		$removed = $this->_data [$key];
		unset ($this->_data [$key]);
		unset ($this->_values [$key]);

		return $removed;
	}

	/**
	 * Whether or not this collection contains a specified element
	 *
	 * @param mixed $key the key of the element
	 *
	 * @return boolean
	 */
	public function contains($key)
	{
		return isset ($this->_data [$key]);
	}

	/**
	 * Save
	 *
	 * @todo preSave, afterSave eventleri eklenecek
	 * @todo validationlar eklenecek
	 * @return integer $primaryKey
	 */
	public function save()
	{
		//		print_r($this->_data);
		try {
			$calledTable = Array();
			foreach ($this->_data as $key => $value) {
				//				print gettype($value);
				if ($value instanceof Soup_Result) {
					$value->save();

				} else if ($value instanceof Soup_Record) {
					$foreignKey = $this->getTable()->getForeignKey($value->getTable()->getName());
					$value->set($foreignKey['foreign'], $this->{$foreignKey['local']});
					$value->save();
				}
			}
			if (count($this->_values) > 0) {
				Soup_Query::update($this->getTable()->getTableName(), $this->_values)->where(
						  $this->getTable()->getPrimaryKey() . " = ?", $this->_data[$this->getTable()->getPrimaryKey()]
				)         ->execute();
			}

			//			Soup_Query::insert ( $this->getTable ()->getTableName (), $this->_values )->execute ();
			//			Soup_Query::update($this->getTable ()->getTableName (), $this->_values)->where(, )->execute();
			return TRUE;
		} catch (Soup_Query_Exception $e) {
			throw new Soup_Record_Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 *
	 * @todo bu kısımda tek bir data varsa o silinecek birden fazla data tutuluyorsa içerisinde hepsi birden silinecek. Relationlar eklendikten sonra test edilecek.
	 */
	public function delete()
	{
		//		if($this->count()>0)
		//			return 'Toplu siliş';
		//		else
		//			return 'Tekli Siliş';
		return Soup_Query::delete($this->getTable()->getTableName())->where(
						 $this->getTable()->getPrimaryKey() . ' = ?', $this->_data[$this->getTable()->getPrimaryKey()]
		)                ->execute();
	}

	/**
	 * serialize
	 * this method is automatically called when an instance of Doctrine_Record is serialized
	 *
	 * @return string
	 * @todo Bu kısım hatalı tekrar yazılacak
	 */
	//	public function serialize()
	//	{
	//		$str = serialize($this->_data);
	//
	//		return $str;
	//	}
	/**
	 * this method is automatically called everytime an instance is unserialized
	 *
	 * @param string $serialized Soup_Record as serialized string
	 *
	 * @return void
	 * @todo Bu kısım hatalı tekrar yazılacak
	 */
	//	public function unserialize($serialized)
	//	{
	//		$this->_data = unserialize($serialized);
	//	}
	//
	/**
	 * @see Countable::count()
	 */
	public function count()
	{
		return $this->_count;
	}

	/**
	 * Toplam satır sayısını geri döndürür
	 *
	 * @return integer
	 */
	public function foundRows()
	{
		return $this->_foundRows;
	}

	/**
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator()
	{
		return new ArrayIterator ($this->_data);
	}

	public function __toString()
	{
		return 'Soup_Result';
	}

	public function toArray()
	{
		if (is_array($this->_data)) {
			foreach ($this->_data as $key => $data) {
				if ($data instanceof Soup_Result) {
					$result[] = $data->toArray();
				} else {
					$result[$key] = $data;
				}
			}

			return $result;
		} else {
			return $this->_data;
		}
	}
}

?>