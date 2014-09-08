<?php

if (!class_exists('ASSingleton'))
{

    abstract class ASSingleton
    {
        /**
         * @var cached reference to singleton instance 
         */
        protected static $Instances;

        /**
         * gets the instance via lazy initialization (created on first usage)
         * @return Self
         */
        public static function getInstance()
        {
            if (null === static::$Instances)
            {
                static::$Instances = array();
            }
            $class = get_called_class();
            if (!isset(static::$Instances[$class]))
            {
                static::$Instances[$class] = new $class;
            }
            return static::$Instances[$class];
        }

        /**
         * is not allowed to call from outside: private!
         */
        private function __construct()
        {

        }

        /**
         * prevent the instance from being cloned
         * @return void
         */
        private function __clone()
        {

        }

        /**
         * prevent from being unserialized
         * @return void
         */
        private function __wakeup()
        {

        }
    }
    
}