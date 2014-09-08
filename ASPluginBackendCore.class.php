<?php

require ASPluginDir . '/ASAdminPage.class.php';

if (!interface_exists('IASPluginBackendCore'))
{
    interface IASPluginBackendCore
    {
        public static function Init();
        public static function RegisterStyles($hook);
        public static function RegisterScripts($hook);
        
        //public static function RegisterMenus();
        //public static function AddMetaBoxes($post_type);
        //public static function SaveMetaBoxes($post_id);
    }
}

if (!class_exists('ASPluginBackendCore'))
{
    abstract class ASPluginBackendCore extends ASBase implements IASPluginBackendCore
    {
    
        public static $PAGES = array();
        public static $ADMIN_ACTIONS = array();
        
        public static function Loader()
        {
            add_action('admin_init', array(get_called_class(), 'Init'), 10);
            add_action('admin_enqueue_scripts', array(get_called_class(), 'RegisterStyles'), 10);
            add_action('admin_enqueue_scripts', array(get_called_class(), 'RegisterScripts'), 10);
            
            
            static::AddFilters();
            static::AddActions();
            static::PagesLoader();
            static::AdminActionsLoader();
            
            //add_action('admin_menu', array(get_called_class(), 'RegisterMenus'), 10);
            //add_action('add_meta_boxes', array(get_called_class(), 'AddMetaBoxes'), 10);
            //add_action('save_post', array(get_called_class(), 'SaveMetaBoxes'), 10);
        }
        
        
        private static function PagesLoader()
        {
            $pages = static::$PAGES;
            static::ApplyFilters(array('load','pages'), $pages);
            foreach ($pages AS $page_class)
            {
                call_user_func(array($page_class, 'Init'));
            }
        }
        
        
        private static function AdminActionsLoader()
        {
            $admin_actions = static::$ADMIN_ACTIONS;
            static::ApplyFilters(array('load','admin_actions'), $admin_actions);
            foreach ($admin_actions AS $admin_action_tag => $admin_action_function)
            {
                add_action('admin_action_'. $admin_action_tag, $admin_action_function);
            }
        }
        
        
        
        
        
        
        
        public static function SavePostMeta($post_id, $meta_key)
        {
            $post = get_post($post_id);
            $post_type = get_post_type_object( $post->post_type );
            if (current_user_can($post_type->cap->edit_post, $post_id))
            {
                $post_key = $meta_key . '_' . $post_id;
                $new_meta_value = (isset( $_POST[$post_key]) ? sanitize_html_class($_POST[$post_key]) : '');
                $meta_value = get_post_meta($post_id, $meta_key, true);
                if ($new_meta_value && '' == $meta_value)
                {
                    return add_post_meta($post_id, $meta_key, $new_meta_value, true);
                }
                elseif ($new_meta_value && $new_meta_value != $meta_value)
                {
                    return update_post_meta($post_id, $meta_key, $new_meta_value);
                }
                elseif ( '' == $new_meta_value && $meta_value )
                {
                    return delete_post_meta( $post_id, $meta_key, $meta_value );
                }
            }
            return false;
        }
        
        public static function DisplayPostMeta_Select($post_id, $meta_key, $options, $title='', $help='')
        {
            $value = get_post_meta($post_id, $meta_key, true);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo $title; ?></th>
                    <td>
                        <select name="<?php echo $meta_key . '_' . $post_id; ?>" id="<?php echo $meta_key . '_' . $post_id; ?>" class="regular-text">
                            <?php foreach ($options AS $options_value => $option_name): ?>
                            <option value="<?php echo $options_value; ?>" <?php selected($options_value, $value); ?>><?php echo $option_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($help)): ?>
                        <p class="description"><?php echo $help; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        public static function DisplayPostMeta_Input($post_id, $meta_key, $title='', $help='')
        {
            $value = get_post_meta($post_id, $meta_key, true);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo $title; ?></th>
                    <td>
                        <input type="text" name="<?php echo $meta_key . '_' . $post_id; ?>" id="<?php echo $meta_key . '_' . $post_id; ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
                        <?php if (!empty($help)): ?>
                        <p class="description"><?php echo $help; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        public static function DisplayPostMeta_Number($post_id, $meta_key, $title='', $help='')
        {
            $value = get_post_meta($post_id, $meta_key, true);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo $title; ?></th>
                    <td>
                        <input type="number" name="<?php echo $meta_key . '_' . $post_id; ?>" id="<?php echo $meta_key . '_' . $post_id; ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
                        <?php if (!empty($help)): ?>
                        <p class="description"><?php echo $help; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        public static function DisplayPostMeta_Textarea($post_id, $meta_key, $title='', $help='')
        {
            $value = get_post_meta($post_id, $meta_key, true);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo $title; ?></th>
                    <td>
                        <textarea name="<?php echo $meta_key . '_' . $post_id; ?>" rows="10" cols="50" id="<?php echo $meta_key . '_' . $post_id; ?>" class="large-text"><?php echo $value; ?></textarea>
                        <?php if (!empty($help)): ?>
                        <p class="description"><?php echo $help; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        
    }
}
