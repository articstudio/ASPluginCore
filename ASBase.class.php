<?php

if (!interface_exists('IASBase'))
{
    interface IASBase
    {
        public static function Loader();
    }
}

if (!class_exists('ASBase'))
{

    abstract class ASBase extends ASOptions implements IASBase
    {
        protected static $FILTERS = array();
        protected static $ACTIONS = array();

        protected $_plugin_basename = 'ASPluginCore';
        protected $_dir = '';
        protected $_url = '';
        protected $_loaded = false;
        
        

        public function GetBasename()
        {
            return $this->_plugin_basename;
        }

        public function GetDir()
        {
            return $this->_dir;
        }

        public function GetUrl()
        {
            return $this->_url;
        }
        
        

        /**
         * Check WP uses multisite/network
         * @return Boolean
         */
        public static function UseNetwork()
        {
            return is_multisite();
        }
        
        
        /**** ACTIONS ****/
        
        public static function MakeActionTag($breadcrumb)
        {
            array_unshift($breadcrumb, get_called_class());
            $tag = strtolower(implode('_', $breadcrumb));
            //var_dump('---------------------- ' . $tag . '----------------------------------------');
            return $tag;
        }
        
        public static function DoAction($breadcrumb, $args=array())
        {
            $tag = static::MakeFilterTag($breadcrumb);
            //var_dump('---------------------- ' . $tag . '----------------------------------------' . "<br>\n");
            do_action_ref_array($tag, $args);
        }
        
        public static function AddAction($breadcrumb, $function_to_add, $priority = 10, $accepted_args = 1)
        {
            $tag = static::MakeFilterTag($breadcrumb);
            //var_dump('---------------------- ' . $tag . '----------------------------------------' . "<br>\n");
            return add_action($tag, $function_to_add, $priority, $accepted_args);
        }
        
        protected static function AddActions()
        {
            $actions = static::$ACTIONS;
            static::ApplyFilters(array('add', 'actions'), $actions);
            if (is_array($actions) && !empty($actions))
            {
                foreach ($actions AS $action_tag => $action_function)
                {
                    if (is_string($action_function))
                    {
                        add_filter($action_tag, $action_function);
                    }
                    else if (is_array($action_function))
                    {
                        if (isset($action_function['function']))
                        {
                            add_filter($action_tag, $action_function['function'], (isset($action_function['priority'])?$action_function['priority']:10), (isset($action_function['accepted_args'])?$action_function['accepted_args']:1));
                        }
                        else
                        {
                            add_filter($action_tag, $action_function);
                        }
                    }
                }
            }
        }
        
        
        /**** FILTERS ****/
        
        public static function MakeFilterTag($breadcrumb)
        {
            array_unshift($breadcrumb, get_called_class());
            $tag = strtolower(implode('_', $breadcrumb));
            //var_dump('---------------------- ' . $tag . '----------------------------------------');
            return $tag;
        }
        
        public static function ApplyFilters($breadcrumb, &$value, $args=array())
        {
            $tag = static::MakeFilterTag($breadcrumb);
            //var_dump('---------------------- ' . $tag . '----------------------------------------' . "<br>\n");
            return apply_filters_ref_array($tag, array(&$value, $args));
        }
        
        public static function AddFilter($breadcrumb, $function_to_add, $priority = 10, $accepted_args = 1)
        {
            $tag = static::MakeFilterTag($breadcrumb);
            //var_dump('---------------------- ' . $tag . '----------------------------------------' . "<br>\n");
            add_filter($tag, $function_to_add, $priority, $accepted_args);
        }
        
        protected static function AddFilters()
        {
            $filters = static::$FILTERS;
            static::ApplyFilters(array('add', 'filters'), $filters);
            if (is_array($filters) && !empty($filters))
            {
                foreach ($filters AS $filter_tag => $filter_function)
                {
                    if (is_string($filter_function))
                    {
                        add_filter($filter_tag, $filter_function);
                    }
                    else if (is_array($filter_function))
                    {
                        if (isset($filter_function['function']))
                        {
                            add_filter($filter_tag, $filter_function['function'], (isset($filter_function['priority'])?$filter_function['priority']:10), (isset($filter_function['accepted_args'])?$filter_function['accepted_args']:1));
                        }
                        else
                        {
                            add_filter($filter_tag, $filter_function);
                        }
                    }
                }
            }
        }

        
        
        

        public static function FilePath($file, $dir=false)
        {
            $dir = is_dir($dir) ? $dir : static::getInstance()->GetDir();
            $dir .= substr($dir, -strlen(DIRECTORY_SEPARATOR)) !== DIRECTORY_SEPARATOR ? DIRECTORY_SEPARATOR : '';
            $path = $dir . $file;
            return (is_dir($path) || is_file($path)) ? $path : false;
        }

        public static function IncludeFile($file, $dir=false, $require=true)
        {
            $path = static::FilePath($file, $dir);
            if ($path)
            {
                return $require ? (require_once $path) : (include $path);
            }
            return false;
        }

        public static function RequireFile($file, $dir=false)
        {
            return static::IncludeFile($file, $dir, true);
        }
        
        public static function RequireClassFileToExec($file, $class, $method, $args=array(), $dir=false)
        {
            /*var_dump('----------------------');
            var_dump($file);
            var_dump($dir);
            var_dump($class);*/
            if (static::RequireFile($file, $dir) && class_exists($class))
            {
                /*var_dump($method);
                var_dump($args);
                var_dump('++++----------------------');*/
                call_user_func_array(array($class, $method), $args);
            }
        }

        
        
        public static function Load($basename='', $dir='', $url='')
        {
            $plugin = static::getInstance();
            if ($plugin->LoadPlugin($basename, $dir, $url))
            {
                static::DoAction(array('loaded'));
                return true;
            }
            return false;
        }

        protected function LoadPlugin($basename='', $dir='', $url='')
        {
            /*var_dump($basename);
            var_dump($dir);
            var_dump($url);
            var_dump($this->_loaded);*/
            if (!$this->_loaded)
            {
                $this->_plugin_basename = !empty($basename) ? $basename : $this->_plugin_basename;
                $this->_dir = !empty($dir) ? $dir : $this->_dir;
                $this->_url = !empty($url) ? $url : $this->_url;
                $this->_loaded = static::Loader();
            }
            return $this->_loaded;
        }
        
        
        
        protected static function GetBlogID()
        {
            global $wpdb;
            return $wpdb->blogid;
        }

        protected static function ExecMethodAllBlogs($method, $args = array())
        {
            $blogid = static::GetBlogID();
            $blog_list = get_blog_list(0, 'all');
            foreach ($blog_list as $blog)
            {
                switch_to_blog($blog['blog_id']);
                static::ExecMethod($method, $args, false);
            }
            switch_to_blog($blogid);
        }
        
        protected static function ExecMethod($method, $args = array(), $allblogs = false)
        {
            (static::UseNetwork() && $allblogs) ? static::ExecMethodAllBlogs($method) : call_user_func_array(array(get_called_class(), $method), $args);
        }

    }
    
}
