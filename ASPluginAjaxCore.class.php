<?php

if (!interface_exists('IASPluginAjaxCore'))
{
    interface IASPluginAjaxCore
    {
        /*public static function RegisterSettings();
        public static function EnqueueStyles();
        public static function EnqueueScripts();
        public static function RegisterMenus();
        public static function AddMetaBoxes($post_type);
        public static function SaveMetaBoxes($post_id);*/
    }
}

if (!class_exists('ASPluginAjaxCore'))
{
    abstract class ASPluginAjaxCore extends ASBase implements IASPluginAjaxCore
    {
        protected static $AJAX_ACTIONS = array();
        protected $_notices = array();
        
        public static function Loader()
        {
            static::AddFilters();
            static::AddActions();
            static::AjaxActionsLoader();
        }
        
        
        private static function AjaxActionsLoader()
        {
            $ajax_actions = static::$AJAX_ACTIONS;
            static::ApplyFilters(array('load','ajax_actions'), $ajax_actions);
            foreach ($ajax_actions AS $action_key => $action_config)
            {
                if ($action_config['public'])
                {
                    add_action('wp_ajax_nopriv_' . $action_key, $action_config['callback']);
                }
                if ($action_config['private'])
                {
                    add_action('wp_ajax_' . $action_key, $action_config['callback']);
                }
            }
        }
        
        public static function AddNotice($message, $error = false)
        {
            $_this = static::getInstance();
            return array_push($_this->_notices, array('message' => $message, 'error' => $error));
        }
        
        public static function GetNotices($json=false)
        {
            $_this = static::getInstance();
            return $json ? json_encode($_this->_notices) : $_this->_notices;
        }
    }
}
