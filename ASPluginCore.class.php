<?php

if (!interface_exists('IASPluginCore'))
{
    interface IASPluginCore
    {
        public static function Init();
        public static function Setup();

        public static function Activation();
        public static function Deactivation();
        public static function Uninstall();
    }
}

if (!class_exists('ASPluginCore'))
{

    abstract class ASPluginCore extends ASBase implements IASPluginCore
    {
        protected static $POST_TYPES = array();
        protected static $TAXONOMIES = array();
        protected static $WIDGETS = array();
        
        public static function GetAjaxClassName()
        {
            return get_called_class() . 'Ajax';
        }
        
        public static function GetAdminClassName()
        {
            return get_called_class() . 'Backend';
        }
        
        public static function GetFrontendClassName()
        {
            return get_called_class() . 'Frontend';
        }
        
        protected static function GetEnvClassName()
        {
            return (defined('DOING_AJAX') && DOING_AJAX) ? static::GetAjaxClassName() : (is_admin() ? static::GetAdminClassName() : static::GetFrontendClassName());
        }


        public static function Loader()
        {
            add_action('init', array(get_called_class(), 'ActionInit'));
            add_action('plugins_loaded', array(get_called_class(), 'ActionSetup'));
            add_action('widgets_init', array(get_called_class(), 'RegisterWidgets'));
            
            //static::AddFilters();
            //static::AddActions();
            
            return true;
        }

        

        public static function ActionInit()
        {
            if (static::Init())
            {
                static::AddFilters();
                static::AddActions();
                static::RegisterPostTypes();
                static::RegisterTaxonomies();
                $_this = static::getInstance();
                $class = static::GetEnvClassName();
                $file = 'core/' . $class . '.class.php';
                static::RequireClassFileToExec($file, $class, 'Load', array($_this->_plugin_basename, $_this->_dir, $_this->_url));
            }
        }

        public static function ActionSetup()
        {
            //load_plugin_textdomain(MRCOOKIES_I18N, false, MRCOOKIES_DIR_LANGUAGES);
            //load_textdomain(MRCOOKIES_I18N, MRCOOKIES_DIR_LANGUAGES . '/mrcookies-' . get_locale() . '.mo');
            static::Setup();
        }
        
        public static function RegisterWidgets()
        {
            $widgets = static::$WIDGETS;
            static::ApplyFilters(array('load','widgets'), $widgets);
            foreach ($widgets AS $widget)
            {
                require_once $widget['dir'] . $widget['file'];
                register_widget($widget['class']);
            }
        }


        
        
        public static function PluginActivation()
        {
            //static::AddFilters();
            //static::RegisterPostTypes();
            //static::RegisterTaxonomies();
            static::ExecMethod('Activation', array(), true);
        }

        public static function PluginDeactivation()
        {
            static::ExecMethod('Deactivation', array(), true);
        }

        public static function PluginUninstall()
        {
            static::ExecMethod('Uninstall', array(), true);
        }
        
        protected static function RegisterPostTypes()
        {
            $post_types = static::$POST_TYPES;
            static::ApplyFilters(array('register', 'posttypes'), $post_types);
            if (is_array($post_types) && !empty($post_types))
            {
                foreach ($post_types AS $post_type => $post_type_args)
                {
                    register_post_type($post_type, $post_type_args);
                }
            }
        }
        
        protected static function RegisterTaxonomies()
        {
            $taxonomies = static::$TAXONOMIES;
            static::ApplyFilters(array('register', 'taxonomies'), $taxonomies);
            if (is_array($taxonomies) && !empty($taxonomies))
            {
                foreach ($taxonomies AS $taxonomy => $taxonomy_args)
                {
                    register_taxonomy($taxonomy, $taxonomy_args['post_type'], $taxonomy_args['arguments']);
                }
            }
        }

    }
}
