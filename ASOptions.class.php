<?php

if (!class_exists('ASOptions'))
{

    abstract class ASOptions extends ASSingleton
    {
        protected static $DEFAULT_OPTIONS = array();

        /**
         * Retrieve WP option
         * @param String $option Option name
         * @param Mixed $default Default value if this options not exists
         * @param Boolean $use_cache Usage of the WP cache (Only in multisite)
         * @return Mixed
         */
        public static function GetOption($option, $default=false, $use_cache=true)
        {
            return static::UseNetwork() ? get_site_option($option, $default, $use_cache) : get_option($option, $default);
        }

        /**
         * Save (Insert / Update) WP option
         * @param String $option Option name
         * @param Mixed $value Option value
         * @return Boolean
         */
        public static function SaveOption($option, $value)
        {
            if (static::UseNetwork())
            {
                return add_site_option($option, $value) ? true : update_site_option($option, $value);
            }
            return add_option($option, $value) ? true : update_option($option, $value);
        }

        /**
         * Delete WP option
         * @param String $option Option name
         * @return Boolean
         */
        public static function DeleteOption($option)
        {
            return static::UseNetwork() ? delete_option($option) : delete_site_option($option);
        }

        /**
         * Retrieve default plugin option
         * @param String $option Option name
         * @param Mixed $default Default value if this options not exists
         * @return Mixed
         */
        public static function GetDefaultOption($option, $default=false)
        {
            return isset(static::$DEFAULT_OPTIONS[$option]) ? static::$DEFAULT_OPTIONS[$option] : $default;
        }

        /**
         * Retrieve default plugin options
         * @return Array
         */
        public static function GetDefaultOptions()
        {
            return static::$DEFAULT_OPTIONS;
        }

        protected static function SanitizeOptions($options)
        {
            $defaults = static::GetDefaultOptions();
            return array_merge($defaults, $options);
        }

        /**
         * Retrieve plugin options
         * @param Boolean $use_cache Usage of the WP cache (Only in multisite)
         * @return Array
         */
        public static function GetOptions($use_cache=false)
        {
            $options = static::GetDefaultOptions();
            foreach($options AS $option_name => $option_value)
            {
                $options[$option_name] = static::GetOption($option_name, $option_value, $use_cache);
            }
            return $options;
        }

        /**
         * Save (Insert / Update) plugin options
         * @param Array $options Plugin options
         * @param Boolean $force Force all plugin options. Get default options for not assigned options.
         * @return Boolean
         */
        public static function SaveOptions($options, $force=false)
        {
            $options = $force ? static::SanitizeOptions($options) : $options;
            foreach($options AS $option_name => $option_value)
            {
                static::SaveOption($option_name, $option_value);
            }
            return true;
        }

        /**
         * Reset plugin options
         * @return Boolean
         */
        public static function ResetOptions()
        {
            return static::SaveOptions(array(), true);
        }

        /**
         * Delete plugin options
         * @return Boolean
         */
        public static function DeleteOptions()
        {
            $options_keys = array_keys(static::GetDefaultOptions());
            foreach($options_keys AS $option_name)
            {
                static::DeleteOption($option_name);
            }
            return true;
        }

        /**
         * Retrieve WP option
         * @param String $option Option name
         * @param Mixed $default Default value if this options not exists
         * @param Boolean $use_cache Usage of the WP cache (Only in multisite)
         * @return Mixed
         */
        public static function GetSecureOption($option, $default=false, $use_cache=true)
        {
            $value = static::UseNetwork() ? get_site_option($option, false, $use_cache) : get_option($option, false);
            if ($value === false)
            {
                $value = static::GetDefaultOption($option, $default);
            }
            return $value;
        }
    }
}