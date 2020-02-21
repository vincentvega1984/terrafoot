<?php
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

?>
<?php
//header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
//header("Pragma: no-cache"); // HTTP 1.0.
//header("Expires: 0"); // Proxies.
//$time_start = microtime(TRUE);
//ini_set('memory_limit', '64M'); //tmp value remove it!!!!!!!
//load defines
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'defines.php';

//load DB class (joomla in this case)
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-database-base.php';
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-addtag.php';
// get database object
global $jsDatabase;
$jsDatabase = new classJsportDatabaseBase();
//load config class

require_once JS_PATH_CLASSES.'class-jsport-config.php';
global $jsConfig;
$jsConfig = classJsportConfig::getInstance();
//load request class classJsportRequest
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-request.php';
//load session class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-session.php';
//load date class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-date.php';
//load language class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-language.php';
//load translation class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-translation.php';
//load link class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-link.php';
//load user class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-user.php';
//load text class
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-text.php';
//load plugin class
require_once JS_PATH_CLASSES.'class-jsport-plugins.php';
//load extra fields class
require_once JS_PATH_CLASSES.'class-jsport-extrafields.php';
//load pagination class
require_once JS_PATH_CLASSES.'class-jsport-pagination.php';
//load helper
require_once JS_PATH_HELPERS.'js-helper-images.php';
require_once JS_PATH_HELPERS.'js-helper-tabs.php';
require_once JS_PATH_HELPERS.'js-helper.php';

//execute task
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-controller.php';
$controllerSportLeague = new classJsportController();
// add css

//echo memory_get_usage()/1024.0 . " kb <br />";
//echo microtime(TRUE)-$time_start;
?>