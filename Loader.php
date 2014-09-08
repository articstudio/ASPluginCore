<?php

if (!defined('ASPluginDir'))
{
    define('ASPluginDir', dirname( __FILE__ ));
}

/**** PATTERNS ****/
require ASPluginDir . '/patterns/ASSingleton.class.php';

/**** OPTIONS ****/
require ASPluginDir . '/ASOptions.class.php';

/**** TAXONOMIES ****/
require ASPluginDir . '/ASTaxonomy.class.php';

/**** POST TYPES ****/
require ASPluginDir . '/ASPostType.class.php';

/**** WIDGET ****/
require ASPluginDir . '/ASWidget.class.php';

/**** BASE ****/
require ASPluginDir . '/ASBase.class.php';

/**** CORE ****/
require ASPluginDir . '/ASPluginCore.class.php';
