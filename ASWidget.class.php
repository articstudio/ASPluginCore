<?php

if (!interface_exists('IASWidget'))
{
    interface IASWidget
    {
        public static function GetLabel();
        public static function GetOptions();
        public function showWidget($args, $instance);
        public function showForm($instance);
        public function updateWidget($new_instance, $old_instance);
    }
}

if (!class_exists('ASWidget'))
{
    abstract class ASWidget extends WP_Widget implements IASWidget
    {
        public static $ID_base = 'as_widget';

        public function __construct() {
            parent::__construct(
                static::$ID_base,
                static::GetLabel(),
                static::GetOptions(),
                array()
            );
        }
        
        public function widget($args, $instance)
        {
            $this->showWidget($args, $instance);
        }
        
        public function form($instance)
        {
            $this->showForm($instance);
        }
        
        public function update($new_instance, $old_instance)
        {
            $instance = $this->updateWidget($new_instance, $old_instance);
            return $instance;
        }

    }
}