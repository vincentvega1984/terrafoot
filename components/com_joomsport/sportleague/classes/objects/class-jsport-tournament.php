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

require_once JS_PATH_MODELS.'model-jsport-tournament.php';
class classJsportTournament
{
    private $id = null;
    private $object = null;
    public $lists = null;

    public function __construct($id = null)
    {
        $this->id = $id;
        if (!$this->id) {
            $this->id = classJsportRequest::get('id');
        }
        if (!$this->id) {
            die('ERROR! TOURNAMENT ID not DEFINED');
        }

        $obj = new modelJsportTournament($this->id);
        $this->object = $obj->getRow();
        $this->lists = $obj->lists;

        $title = isset($this->object->name) ? $this->object->name : '';
        $this->lists['options']['title'] = $title;
    }

    public function getRow()
    {
        return $this->object;
    }
}
