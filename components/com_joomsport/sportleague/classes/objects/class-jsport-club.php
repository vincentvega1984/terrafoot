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
require_once JS_PATH_MODELS.'model-jsport-club.php';
require_once JS_PATH_OBJECTS.'class-jsport-team.php';

class classJsportClub
{
    private $id = null;
    public $object = null;
    public $lists = null;
    const VIEW = 'common';

    public function __construct($id = null)
    {
        if (!$id) {
            $this->id = (int) classJsportRequest::get('id');
        } else {
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Club ID not DEFINED');
        }

        $this->loadObject();
        $this->lists['options']['title'] = $this->object->c_name;
    }

    private function loadObject()
    {
        $obj = new modelJsportClub($this->id);
        $this->object = $obj->getRow();

        $this->lists = $obj->loadLists();
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getName($linkable = false)
    {
        $html = '';
        if ($this->id > 0) {
            $html = classJsportLink::club($this->object->c_name, $this->id, false, '', $linkable);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        return $this->lists['def_img'];
    }
    public function getEmblem()
    {
        return $this->object->c_emblem;
    }
    public function getRow()
    {
        return $this;
    }
    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_CLUB');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'flag';

        $this->getTeams();
        //teams
        if (count($this->lists['teamsObj'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_teams';
            $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_ADMIN_TEAM');
            $tabs[$intA]['body'] = 'team-list.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }

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

    public function getTeams()
    {
        $players_object = array();

        if (count($this->lists['teams'])) {
            foreach ($this->lists['teams'] as $row) {
                $obj = new classJsportTeam($row->id);
                $players_object[] = $obj->getRow();
            }
        }
        $this->lists['teamsObj'] = $players_object;
    }
    public function getDescription()
    {
        return classJsportText::getFormatedText($this->object->c_descr);
    }
    public function getView()
    {
        return self::VIEW;
    }
}
