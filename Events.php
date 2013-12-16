<?php

/**
 * Soup Events Class
 * 
 * @author Semih TURNA
 * @version 1.0.0
 * @package Soup
 */
class Soup_Events{
    private static $_instance;
    
    private $_events = array();
    private $_class;
    
    private $_validCallbacks = array(
        "beforeSave",
        "beforeInsert",
        "beforeUpdate",
        "beforeValidation",
        "beforeFilter",
        "afterSave",
        "afterInsert",
        "afterUpdate",
        "afterValidation",
        "afterFilter"
    );
    
    private function __construct(){ }
    
    private function __clone(){ }
    
    public static function getInstance(Soup_Record &$class){        
        if(!isset(self::$_instance)){
            self::$_instance = new Soup_Events();
        }
        
        $reflection = new ReflectionClass($class);
        $methods    = $reflection->getMethods();
        
        foreach($methods as $method){
            if(in_array($method->name, self::$_instance->_validCallbacks)){
                self::$_instance->_bind(array(&$class, $method->name));
            }
        }
        
        self::$_instance->_class =& $class;
        
        return self::$_instance;
    }
    
    private function _bind($callback){             
        if(!self::$_instance->_has($callback)){
            $className = get_class(self::$_instance->_class);
            
            self::$_instance->_events[$className][] = $callback;
        }
    }
    
    private function _has($method){
        $className = get_class(self::$_instance->_class);
        
        return (isset(self::$_instance->_events[$className]) 
                    && in_array($method, self::$_instance->_events[$className]));
    }
    
    public function trigger($method, $args = NULL){
        echo "sada";
        if(self::$_instance->_has($method)){
            if($args){
                call_user_func_array(array(&self::$_instance->_class, $method), (array)$args);
            }
            else{
                call_user_func(array(&self::$_instance->_class, $method));
            }
        }
    }
}

?>
