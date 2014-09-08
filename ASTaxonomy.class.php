<?php

if (!interface_exists('IASToxonomy'))
{
    interface IASToxonomy
    {
        public static function GetLabel();
        public static function GetRegisterArguments($label);
        /*public static function RegisterSettings();
        public static function EnqueueStyles();
        public static function EnqueueScripts();
        public static function RegisterMenus();
        public static function AddMetaBoxes($post_type);
        public static function SaveMetaBoxes($post_id);*/
    }
}

if (!class_exists('ASToxonomy'))
{
    abstract class ASToxonomy implements IASToxonomy
    {
        
        public static $TAXONOMY = 'as_taxonomy';
        
        public static function RegisterTaxonomy($object_type=array(), $label='', $args=array())
        {
            $register_object_types = is_array($object_type) ? $object_type : array($object_type);
            $register_label = empty($label) ? static::GetLabel() : $label;
            $register_args = array_merge(static::GetRegisterArguments($register_label), $args);
            register_taxonomy(
                static::$TAXONOMY,
                $register_object_types,
                $register_args
            );
            if (!empty($register_object_types))
            {
                foreach ($register_object_types AS $register_object_type)
                {
                    register_taxonomy_for_object_type(static::$TAXONOMY, $register_object_type);
                }
            }
        }
        
        
    }
}