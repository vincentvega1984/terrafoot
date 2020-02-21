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

class classJsportConfig
{
    private $_config = array();
    private static $_instance = null;

    private function __construct()
    {
        global $jsDatabase;

        $query = 'SELECT cfg_name as name, cfg_value as value'
                .' FROM '.DB_TBL_CONFIG;
        $this->_config = $jsDatabase->selectKeyPair($query);
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get($val)
    {
        return (isset($this->_config[$val]))?$this->_config[$val]:'';
    }
}
