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

require_once JS_PATH_MODELS.'model-jsport-tournamentlist.php';

class classJsportTournamentlist
{
    private $object = null;
    public $lists = null;

    public function __construct()
    {
        $obj = new modelJsportTournamentlist();
        $this->object = $obj->getRow();
        $this->lists['options']['title'] = classJsportLanguage::get('BLFA_TOURNLIST');
    }

    public function getRow()
    {
        return $this->object;
    }
}
