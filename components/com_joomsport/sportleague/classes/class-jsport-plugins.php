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

class classJsportPlugins
{
    public static function get($name, $arguments = array())
    {
        $return = '';
        if ($name) {
            if ($arguments == null) {
                $arguments = $_GET;
            }
            foreach (glob(JS_PATH_PLUGINS.'plugin-joomsport-*.php') as $filename) {
                include_once $filename;

                $dir_array = explode(DIRECTORY_SEPARATOR, $filename);
                if (count($dir_array)) {
                    $classname = $dir_array[count($dir_array) - 1];
                    $classname = str_replace('.php', '', $classname);
                    $classname = str_replace('-', '', $classname);
                    if (class_exists($classname)) {

                        //$classname::$name($arguments);
                        if (method_exists($classname, $name)) {
                            $return .= $classname::$name($arguments);
                        }
                    }
                }
            }
        }
        //load all plugin classes
        //check all plugins for current task
        //execute plugin function 
        return $return;
    }
}
