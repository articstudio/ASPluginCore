<?php

if (!interface_exists('IASPostType'))
{
    interface IASPostType
    {
        public static function GetLabels();
        public static function GetRegisterArguments($labels=array());
        /*public static function RegisterSettings();
        public static function EnqueueStyles();
        public static function EnqueueScripts();
        public static function RegisterMenus();
        public static function AddMetaBoxes($post_type);
        public static function SaveMetaBoxes($post_id);*/
    }
}

if (!class_exists('ASPostType'))
{
    abstract class ASPostType implements IASPostType
    {
        
        public static $POST_TYPE = 'as_post_type';
        
        public static function RegisterPostType($labels=array(), $args=array())
        {
            $register_labels = array_merge(static::GetLabels(), $labels);
            $register_args = array_merge(static::GetRegisterArguments($register_labels), $args);
            register_post_type(
                static::$POST_TYPE,
                $register_args
            );
        }
        
        
    }
}