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

require_once JS_PATH_MODELS.'model-jsport-person.php';


class classJsportPerson
{
    private $id = null;
    public $season_id = null;
    public $object = null;
    public $lists = null;
    public $model = null;

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = (int) classJsportRequest::get('id');
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Person ID not DEFINED');
        }
        $this->loadObject($loadLists);
    }

    private function loadObject($loadLists)
    {
        $obj = $this->model = new modelJsportPerson($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($loadLists) {
            $this->lists = $obj->loadLists();
            
        }
        $this->lists['options']['title'] = $this->getName(false);
    }

    public function getName($linkable = false, $itemid = 0)
    {
        global $jsConfig;
        $pname = ($this->object->first_name.' '.$this->object->last_name);
        if (!$linkable || $jsConfig->get('enbl_playerlinks') == '0') {
            return $pname;
        }
        $html = '';
        if ($this->id > 0 && isset($this->object->first_name)) {
            $html = classJsportLink::person($pname, $this->id, $this->season_id,false, $itemid);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        return $this->lists['def_img'];
    }
    public function getEmblem($linkable = true, $type = 0, $class = 'emblInline', $width = 0, $light = true, $itemid = 0)
    {
        global $jsConfig;
        $html = '';
        if (!isset($this->lists['def_img'])) {
            $this->loadObject(true);
        }
        $html = jsHelperImages::getEmblem($this->lists['def_img'], 0, $class, $width, $light);
        if ($linkable && $jsConfig->get('enbl_playerlogolinks') == '1') {
            $html = classJsportLink::player($html, $this->id, $this->season_id, $itemid, $linkable);
        }

        return $html;
    }

    public function getRow()
    {
        //$this->setHeaderOptions();

        return $this;
    }
    public function getRowSimple()
    {
        return $this;
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_PLAYERR');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'users';
        //photos
        if (count($this->lists['photos'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_photos';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_PHOTOS');
            $tabs[$intA]['body'] = 'gallery.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'photos';
        }

        return $tabs;
    }
    public function getDescription()
    {
        return classJsportText::getFormatedText($this->object->about);
    }

    
    
}
