<?php
// ASAdminPage

if (!interface_exists('IASAdminPage'))
{
    interface IASAdminPage
    {
        public static function GetMenuLabel();
        public static function GetPageLabel();
        public static function LoadPageHook();
        public static function PageHook();
    }
}

if (!class_exists('ASAdminPage'))
{
    abstract class ASAdminPage implements IASAdminPage
    {

        protected static $SETTINGS = array();
        
        public static $ADMIN_ACTIONS = array();
        
        public static $AJAX_ACTIONS = array();
        
        protected static $STYLES = array();
        
        protected static $SCRIPTS = array();
        
        protected static $FILE = '';
        
        public static $_slug = '';
        public static $_parent_slug = '';
        public static $_capability = '';
        public static $_icon = '';
        public static $_position = null;
        
        
        
        public static function Init()
        {
            add_action('admin_init', array(get_called_class(), 'RegisterSettings'), 10);
            add_action('admin_init', array(get_called_class(), 'RegisterPageHooks'), 10);
            add_action('admin_menu', array(get_called_class(), 'RegisterMenus'), 10);
            MsSocialBackend::AddFilter(array('load', 'admin_actions'), array(get_called_class(), 'AdminActionsLoaderFilter'));
            MsSocialAjax::AddFilter(array('load', 'ajax_actions'), array(get_called_class(), 'AjaxActionsLoaderFilter'));
        }
    
        public static function AdminActionsLoaderFilter($actions)
        {
            return array_merge($actions, static::$ADMIN_ACTIONS);
        }
    
        public static function AjaxActionsLoaderFilter($actions)
        {
            return array_merge($actions, static::$AJAX_ACTIONS);
        }
        
        public static function GetPageHook()
        {
            return get_plugin_page_hookname(static::$_slug, static::$_parent_slug);
        }
        
        public static function RegisterSettings()
        {
            $settings = static::$SETTINGS;
            foreach ($settings AS $group => $group_options)
            {
                foreach ($group_options AS $option)
                {
                    register_setting($group, $option);
                }
            }
        }
        
        public static function RegisterPageHooks()
        {
            add_action('admin_enqueue_scripts', array(get_called_class(), 'EnqueueStyles'), 10);
            add_action('admin_enqueue_scripts', array(get_called_class(), 'EnqueueScripts'), 10);
            
            add_action('load-' . static::GetPageHook(), array(get_called_class(), 'LoadPageHook'), 10);
            add_action(static::GetPageHook(), array(get_called_class(), 'PageHook'), 10);
        }
        
        public static function RegisterMenus()
        {
            if (!empty(static::$_slug))
            {
                $function = array(get_called_class(), 'ShowPage');
                if (empty(static::$_parent_slug))
                {
                    add_menu_page(static::GetPageLabel(), static::GetMenuLabel(), static::$_capability, static::$_slug, $function, static::$_icon, static::$_position);
                }
                else
                {
                    add_submenu_page(static::$_parent_slug, static::GetPageLabel(), static::GetMenuLabel(), static::$_capability, static::$_slug, $function);
                }
            }
        }
        
        public static function EnqueueStyles($hook)
        {
            if ($hook == static::GetPageHook())
            {
                $styles = static::$STYLES;
                foreach ($styles AS $style_handle)
                {
                    wp_enqueue_style($style_handle);
                }
            }
        }
        
        public static function EnqueueScripts($hook)
        {
            if ($hook == static::GetPageHook())
            {
                $scripts = static::$SCRIPTS;
                foreach ($scripts AS $script_handle)
                {
                    wp_enqueue_script($script_handle);
                }
            }
        }
        
        public static function ShowPage()
        {
            include MSSOCIAL_DIR . static::$FILE;
        }
    }
}
