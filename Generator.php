<?php

/**
 * 
 * Soup Generator
 * 
 * @author canerdogan
 * @version 1.0.0
 * @package Soup
 */

class Soup_Generator {
	
	private static $sql = array ('listDatabases' => 'SHOW DATABASES', 'listTableFields' => 'DESCRIBE %s', 'listSequences' => 'SHOW TABLES', 'listTables' => 'SHOW TABLES', 'listUsers' => 'SELECT DISTINCT USER FROM USER', 'listViews' => "SHOW FULL TABLES %s WHERE Table_type = 'VIEW'" );
	
	private static $_tableInfo;
	
	public static function generateModel($modelDir) {
		
		self::importSchema($modelDir);
		
// 		self::$_tableInfo = self::listTables ();
// 		print_r(self::$_tableInfo);
// 		foreach ( self::$_tableInfo as $index => $table ) {
			// 			print 'Tablo: ' . $table['name'] . "<br>\n";
		// 			print_r ( self::listTableIndexes( $table ['name'] ) );
		// 			print "\n";
		// 			self::$_tableInfo [$index] ['columnInfo'] = self::getColumnList ( $table ['name'] );
		// 			self::$_tableInfo [$index] ['foreign'] = self::getForeignKeys ( $table ['name'] );
// 		}
	
		// 		print_r ( self::$_tableInfo );
	}
	
	public static function getTableList() {
		$_sql = 'SHOW TABLE STATUS FROM ' . Soup_Manager::getDefaultConnection()->getDatabase ();
		
		return Soup_Query::query ( $_sql );
	}
	
	public static function listTables($database = null)
	{
		return Soup_Query::fetchColumn(self::$sql['listTables']);
	}
	
	public static function getColumnList($table) {
		$_sql = 'SHOW FULL FIELDS FROM `' . $table . '`';
		
		return Soup_Query::query ( $_sql );
	}
	
	public static function getForeignKeys($table) {
		$_sql = 'SHOW CREATE TABLE `' . $table . '`';
		
		return Soup_Query::query ( $_sql );
	}
	
	public static function listTableColumns($table) {
		$sql = 'DESCRIBE ' . Soup_Inflector::quoteIdentifier ( $table );
		$result = Soup_Query::query ( $sql );
		
		$description = array ();
		$columns = array ();
		foreach ( $result as $key => $val ) {
			
			$val = array_change_key_case ( $val, CASE_LOWER );
			
			$decl = self::getPortableDeclaration ( $val );
			
			$values = isset ( $decl ['values'] ) ? $decl ['values'] : array ();
			$val ['default'] = $val ['default'] == 'CURRENT_TIMESTAMP' ? null : $val ['default'];
			
			$description = array ('name' => $val ['field'], 'type' => $decl ['type'] [0], 'alltypes' => $decl ['type'], 'ntype' => $val ['type'], 'length' => $decl ['length'], 'fixed' => ( bool ) $decl ['fixed'], 'unsigned' => ( bool ) $decl ['unsigned'], 'values' => $values, 'primary' => (strtolower ( $val ['key'] ) == 'pri'), 'default' => $val ['default'], 'notnull' => ( bool ) ($val ['null'] != 'YES'), 'autoincrement' => ( bool ) (strpos ( $val ['extra'], 'auto_increment' ) !== false) );
			if (isset ( $decl ['scale'] )) {
				$description ['scale'] = $decl ['scale'];
			}
			$columns [$val ['field']] = $description;
		}
		
		return $columns;
	}
	
	public static function getPortableDeclaration(array $field) {
		$dbType = strtolower ( $field ['type'] );
		$dbType = strtok ( $dbType, '(), ' );
		if ($dbType == 'national') {
			$dbType = strtok ( '(), ' );
		}
		if (isset ( $field ['length'] )) {
			$length = $field ['length'];
			$decimal = '';
		} else {
			$length = strtok ( '(), ' );
			$decimal = strtok ( '(), ' );
			if (! $decimal) {
				$decimal = null;
			}
		}
		$type = array ();
		$unsigned = $fixed = null;
		
		if (! isset ( $field ['name'] )) {
			$field ['name'] = '';
		}
		
		$values = null;
		$scale = null;
		
		switch ($dbType) {
			case 'tinyint' :
				$type [] = 'integer';
				$type [] = 'boolean';
				if (preg_match ( '/^(is|has)/', $field ['name'] )) {
					$type = array_reverse ( $type );
				}
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				$length = 1;
				break;
			case 'smallint' :
				$type [] = 'integer';
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				$length = 2;
				break;
			case 'mediumint' :
				$type [] = 'integer';
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				$length = 3;
				break;
			case 'int' :
			case 'integer' :
				$type [] = 'integer';
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				$length = 4;
				break;
			case 'bigint' :
				$type [] = 'integer';
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				$length = 8;
				break;
			case 'tinytext' :
			case 'mediumtext' :
			case 'longtext' :
			case 'text' :
			case 'text' :
			case 'varchar' :
				$fixed = false;
			case 'string' :
			case 'char' :
				$type [] = 'string';
				if ($length == '1') {
					$type [] = 'boolean';
					if (preg_match ( '/^(is|has)/', $field ['name'] )) {
						$type = array_reverse ( $type );
					}
				} elseif (strstr ( $dbType, 'text' )) {
					$type [] = 'clob';
					if ($decimal == 'binary') {
						$type [] = 'blob';
					}
				}
				if ($fixed !== false) {
					$fixed = true;
				}
				break;
			case 'enum' :
				$type [] = 'enum';
				preg_match_all ( '/\'((?:\'\'|[^\'])*)\'/', $field ['type'], $matches );
				$length = 0;
				$fixed = false;
				if (is_array ( $matches )) {
					foreach ( $matches [1] as &$value ) {
						$value = str_replace ( '\'\'', '\'', $value );
						$length = max ( $length, strlen ( $value ) );
					}
					if ($length == '1' && count ( $matches [1] ) == 2) {
						$type [] = 'boolean';
						if (preg_match ( '/^(is|has)/', $field ['name'] )) {
							$type = array_reverse ( $type );
						}
					}
					
					$values = $matches [1];
				}
				$type [] = 'integer';
				break;
			case 'set' :
				$fixed = false;
				$type [] = 'text';
				$type [] = 'integer';
				break;
			case 'date' :
				$type [] = 'date';
				$length = null;
				break;
			case 'datetime' :
			case 'timestamp' :
				$type [] = 'timestamp';
				$length = null;
				break;
			case 'time' :
				$type [] = 'time';
				$length = null;
				break;
			case 'float' :
			case 'double' :
			case 'real' :
				$type [] = 'float';
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				break;
			case 'unknown' :
			case 'decimal' :
				if ($decimal !== null) {
					$scale = $decimal;
				}
			case 'numeric' :
				$type [] = 'decimal';
				$unsigned = preg_match ( '/ unsigned/i', $field ['type'] );
				break;
			case 'tinyblob' :
			case 'mediumblob' :
			case 'longblob' :
			case 'blob' :
			case 'binary' :
			case 'varbinary' :
				$type [] = 'blob';
				$length = null;
				break;
			case 'year' :
				$type [] = 'integer';
				$type [] = 'date';
				$length = null;
				break;
			case 'bit' :
				$type [] = 'bit';
				break;
			case 'geometry' :
			case 'geometrycollection' :
			case 'point' :
			case 'multipoint' :
			case 'linestring' :
			case 'multilinestring' :
			case 'polygon' :
			case 'multipolygon' :
				$type [] = 'blob';
				$length = null;
				break;
			default :
				$type [] = $field ['type'];
				$length = isset ( $field ['length'] ) ? $field ['length'] : null;
		}
		
		$length = (( int ) $length == 0) ? null : ( int ) $length;
		$def = array ('type' => $type, 'length' => $length, 'unsigned' => $unsigned, 'fixed' => $fixed );
		if ($values !== null) {
			$def ['values'] = $values;
		}
		if ($scale !== null) {
			$def ['scale'] = $scale;
		}
		return $def;
	}
	
	public static function listTableRelations($tableName) {
		$relations = array ();
		$sql = "SELECT column_name, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.key_column_usage WHERE table_name = '" . $tableName . "' AND table_schema = '" . Soup_Manager::getDefaultConnection()->getDatabase () . "' and REFERENCED_COLUMN_NAME is not NULL ORDER BY CONSTRAINT_NAME ASC";
		$results = Soup_Query::query ( $sql );
		foreach ( $results as $result ) {
			$result = array_change_key_case ( $result, CASE_LOWER );
			$relations [] = array ('table' => $result ['referenced_table_name'], 'local' => $result ['column_name'], 'foreign' => $result ['referenced_column_name'] );
		}
		return $relations;
	}
	
	public static function listTableIndexes($table) {
		$keyName = 'Key_name';
		$nonUnique = 'Non_unique';
		
		$keyName = strtolower ( $keyName );
		$nonUnique = strtolower ( $nonUnique );
		
		$table = Soup_Inflector::quoteIdentifier ( $table );
		$query = 'SHOW INDEX FROM ' . $table;
		$indexes = Soup_Query::query ( $query );
		
		$result = array ();
		foreach ( $indexes as $indexData ) {
			if ($indexData [$nonUnique] && ($index = Soup_Inflector::fixIndexName ( $indexData [$keyName] ))) {
				$result [] = $index;
			}
		}
		return $result;
	}
	
	public static function importSchema($directory, array $connections = array(), array $options = array()) {
		$classes = array ();
		
		$builder = new Soup_Import_Builder ();
		$builder->setTargetPath ( $directory );
		$builder->setOptions ( $options );
		
		$definitions = array ();
		
		foreach ( self::listTables() as $table ) {
			$definition = array ();
			$definition ['tableName'] = $table;
			$definition ['className'] = Soup_Inflector::classify ( Soup_Inflector::tableize ( $table ) );
			$definition ['columns'] = self::listTableColumns( $table );
			$definition ['connection'] = null;
			$definition ['connectionClassName'] = $definition ['className'];
			
			try {
				$definition ['relations'] = array ();
				$relations = self::listTableRelations ( $table );
				$relClasses = array ();
				$counter = array();
				foreach ( $relations as $relation ) {
					$table = $relation ['table'];
					$class = Soup_Inflector::classify ( Soup_Inflector::tableize ( $table ) );
					$counter[$table] = (isset($counter[$table])? $counter[$table] + 1: 1);
					
					if (in_array ( $class, $relClasses )) {
						$alias = $class . '_' . $counter[$table];
					} else {
						$alias = $class;
					}
					$relClasses [] = $class;
					$definition ['relations'] [$alias] = array ('alias' => $alias, 'class' => $class, 'local' => $relation ['local'], 'foreign' => $relation ['foreign'] );
				}
			} catch ( Exception $e ) {
			}
			
			$definitions [strtolower ( $definition ['className'] )] = $definition;
			$classes [] = $definition ['className'];
		}
		
		// Build opposite end of relationships
		foreach ( $definitions as $definition ) {
			$className = $definition ['className'];
			$relClasses = array ();
			$counter = array();
			foreach ( $definition ['relations'] as $alias => $relation ) {
				$counter[$relation['class']] = (isset($counter[$relation['class']])? $counter[$relation['class']] + 1: 1);
				if (in_array ( $relation ['class'], $relClasses ) || isset ( $definitions [$relation ['class']] ['relations'] [$className] )) {
					$alias = $className . '_' . (count ( $relClasses ) + 1);
// 					$alias = $className . '_' . $counter[$className];
				} else {
					$alias = $className;
				}
				$relClasses [] = $relation ['class'];
				$definitions [strtolower ( $relation ['class'] )] ['relations'] [$alias] = array ('type' => Soup_Table::RELATION_MANY, 'alias' => $alias, 'class' => $className, 'local' => $relation ['foreign'], 'foreign' => $relation ['local'] );
			}
		}
		
		// Build records
		foreach ( $definitions as $definition ) {
			$builder->buildRecord ( $definition );
		}
		
		return $classes;
	}
}

?>