<?php

if (!interface_exists('IASPluginFrontendCore'))
{
    interface IASPluginFrontendCore
    {
        public static function Init();
        public static function RegisterStyles($hook);
        public static function RegisterScripts($hook);
        
    }
}

if (!class_exists('ASPluginFrontendCore'))
{
    abstract class ASPluginFrontendCore extends ASBase implements IASPluginFrontendCore
    {
        
        public static function Loader()
        {
            add_action('init', array(get_called_class(), 'Init'), 10);
            add_action('wp_enqueue_scripts', array(get_called_class(), 'RegisterStyles'), 10);
            add_action('wp_enqueue_scripts', array(get_called_class(), 'RegisterScripts'), 10);
            
            
            static::AddFilters();
            static::AddActions();
            
        }
        
    }
}
