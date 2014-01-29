<?php
/**
 * Soup Importer
 *
 * @package     Soup
 * @subpackage  Import
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Can Erdogan <can@canerdogan.net>
 */
class Soup_Import
{
    protected $sql = array();

    public function __construct() {
    	$conn = Soup_Manager::getDefaultConnection();
    	$this->conn = $conn;
    }

    /**
     * lists all databases
     *
     * @return array
     */
    public function listDatabases()
    {
        if ( ! isset($this->sql['listDatabases'])) {
            throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
        }

        return $this->conn->fetchColumn($this->sql['listDatabases']);
    }

    /**
     * lists all availible database functions
     *
     * @return array
     */
    public function listFunctions()
    {
        if ( ! isset($this->sql['listFunctions'])) {
            throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
        }

        return $this->conn->fetchColumn($this->sql['listFunctions']);
    }

    /**
     * lists all database triggers
     *
     * @param string|null $database
     * @return array
     */
    public function listTriggers($database = null)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists all database sequences
     *
     * @param string|null $database
     * @return array
     */
    public function listSequences($database = null)
    {
        if ( ! isset($this->sql['listSequences'])) {
            throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
        }

        return $this->conn->fetchColumn($this->sql['listSequences']);
    }

    /**
     * lists table constraints
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableConstraints($table)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists table relations
     *
     * Expects an array of this format to be returned with all the relationships in it where the key is
     * the name of the foreign table, and the value is an array containing the local and foreign column
     * name
     *
     * Array
     * (
     *   [groups] => Array
     *     (
     *        [local] => group_id
     *        [foreign] => id
     *     )
     * )
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableRelations($table)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists table constraints
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableColumns($table)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists table constraints
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableIndexes($table)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists tables
     *
     * @param string|null $database
     * @return array
     */
    public function listTables($database = null)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists table triggers
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableTriggers($table)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists table views
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableViews($table)
    {
        throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
    }

    /**
     * lists database users
     *
     * @return array
     */
    public function listUsers()
    {
        if ( ! isset($this->sql['listUsers'])) {
            throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
        }

        return $this->conn->fetchColumn($this->sql['listUsers']);
    }

    /**
     * lists database views
     *
     * @param string|null $database
     * @return array
     */
    public function listViews($database = null)
    {
        if ( ! isset($this->sql['listViews'])) {
            throw new Soup_Import_Exception(__FUNCTION__ . ' not supported by this driver.');
        }

        return $this->conn->fetchColumn($this->sql['listViews']);
    }

    /**
     * checks if a database exists
     *
     * @param string $database
     * @return boolean
     */
    public function databaseExists($database)
    {
        return in_array($database, $this->listDatabases());
    }

    /**
     * checks if a function exists
     *
     * @param string $function
     * @return boolean
     */
    public function functionExists($function)
    {
        return in_array($function, $this->listFunctions());
    }

    /**
     * checks if a trigger exists
     *
     * @param string $trigger
     * @param string|null $database
     * @return boolean
     */
    public function triggerExists($trigger, $database = null)
    {
        return in_array($trigger, $this->listTriggers($database));
    }

    /**
     * checks if a sequence exists
     *
     * @param string $sequence
     * @param string|null $database
     * @return boolean
     */
    public function sequenceExists($sequence, $database = null)
    {
        return in_array($sequence, $this->listSequences($database));
    }

    /**
     * checks if a table constraint exists
     *
     * @param string $constraint
     * @param string $table     database table name
     * @return boolean
     */
    public function tableConstraintExists($constraint, $table)
    {
        return in_array($constraint, $this->listTableConstraints($table));
    }

    /**
     * checks if a table column exists
     *
     * @param string $column
     * @param string $table     database table name
     * @return boolean
     */
    public function tableColumnExists($column, $table)
    {
        return in_array($column, $this->listTableColumns($table));
    }

    /**
     * checks if a table index exists
     *
     * @param string $index
     * @param string $table     database table name
     * @return boolean
     */
    public function tableIndexExists($index, $table)
    {
        return in_array($index, $this->listTableIndexes($table));
    }

    /**
     * checks if a table exists
     *
     * @param string $table
     * @param string|null $database
     * @return boolean
     */
    public function tableExists($table, $database = null)
    {
        return in_array($table, $this->listTables($database));
    }

    /**
     * checks if a table trigger exists
     *
     * @param string $trigger
     * @param string $table     database table name
     * @return boolean
     */
    public function tableTriggerExists($trigger, $table)
    {
        return in_array($trigger, $this->listTableTriggers($table));
    }

    /**
     * checks if a table view exists
     *
     * @param string $view
     * @param string $table     database table name
     * @return boolean
     */
    public function tableViewExists($view, $table)
    {
        return in_array($view, $this->listTableViews($table));
    }

    /**
     * checks if a user exists
     *
     * @param string $user
     * @return boolean
     */
    public function userExists($user)
    {
        return in_array($user, $this->listUsers());
    }

    /**
     * checks if a view exists
     *
     * @param string $view
     * @param string|null $database
     * @return boolean
     */
    public function viewExists($view, $database = null)
    {
         return in_array($view, $this->listViews($database));
    }

    /**
     * importSchema
     *
     * method for importing existing schema to Soup_Record classes
     *
     * @param string $directory
     * @param array $connections Array of connection names to generate models for
     * @return array                the names of the imported classes
     */
    public function importSchema($directory, array $connections = array(), array $options = array())
    {
    	try {
        $classes = array();

        print 'start';

        $connection = Soup_Manager::getDefaultConnection();

          $builder = new Soup_Import_Builder();
          $builder->setTargetPath($directory);
          $builder->setOptions($options);

          $definitions = array();

          print_r($connection->getImport());

          foreach ($connection->getImport()->listTables() as $table) {
              $definition = array();
              $definition['tableName'] = $table;
              $definition['className'] = Soup_Inflector::classify(Soup_Inflector::tableize($table));
              $definition['columns'] = $connection->getImport()->listTableColumns($table);
              $definition['connection'] = $connection->getName();
              $definition['connectionClassName'] = $definition['className'];

              try {
                  $definition['relations'] = array();
                  $relations = $connection->getImport()->listTableRelations($table);
                  $relClasses = array();
                  foreach ($relations as $relation) {
                      $table = $relation['table'];
                      $class = Soup_Inflector::classify(Soup_Inflector::tableize($table));
                      if (in_array($class, $relClasses)) {
                          $alias = $class . '_' . (count($relClasses) + 1);
                      } else {
                          $alias = $class;
                      }
                      $relClasses[] = $class;
                      $definition['relations'][$alias] = array(
                          'alias'   => $alias,
                          'class'   => $class,
                          'local'   => $relation['local'],
                          'foreign' => $relation['foreign']
                      );
                  }
              } catch (Exception $e) {}

              $definitions[strtolower($definition['className'])] = $definition;
              $classes[] = $definition['className'];
          }

          // Build opposite end of relationships
          foreach ($definitions as $definition) {
              $className = $definition['className'];
              $relClasses = array();
              foreach ($definition['relations'] as $alias => $relation) {
                  if (in_array($relation['class'], $relClasses) || isset($definitions[$relation['class']]['relations'][$className])) {
                      $alias = $className . '_' . (count($relClasses) + 1);
                  } else {
                      $alias = $className;
                  }
                  $relClasses[] = $relation['class'];
                  $definitions[strtolower($relation['class'])]['relations'][$alias] = array(
                    'type' => Soup_Table::RELATION_MANY,
                    'alias' => $alias,
                    'class' => $className,
                    'local' => $relation['foreign'],
                    'foreign' => $relation['local']
                  );
              }
          }

          // Build records
          foreach ($definitions as $definition) {
              $builder->buildRecord($definition);
          }

        return $classes;

        } catch (Exception $e) {
        	throw new Soup_Import_Exception($e->getMessage());
        }
    }
}