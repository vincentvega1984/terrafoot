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
class modelJsportClub
{
    public $club_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id)
    {
        $this->club_id = $id;

        if (!$this->club_id) {
            die('ERROR! Club ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_CLUB.''
                .' WHERE id = '.$this->club_id);
        $this->row->c_name = classJsportTranslation::get('club_'.$this->row->id, 'c_name',$this->row->c_name);
        $this->row->c_descr = classJsportTranslation::get('club_'.$this->row->id, 'c_descr',$this->row->c_descr);
        
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->club_id, '4', 0);
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getTeams();

        return $this->lists;
    }

    public function getDefaultImage()
    {
        global $jsDatabase;
        $this->lists['def_img'] = null;
        if ($this->row->def_img) {
            $query = 'SELECT ph_filename FROM  '.DB_TBL_PHOTOS.' as p WHERE p.id = '.$this->row->def_img;

            $this->lists['def_img'] = $jsDatabase->selectValue($query);
        } elseif (isset($this->lists['photos'][0])) {
            $this->lists['def_img'] = $this->lists['photos'][0]->filename;
        }
    }
    public function getPhotos()
    {
        global $jsDatabase;
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                .' FROM '.DB_TBL_ASSIGN_PHOTOS.' as ap, '.DB_TBL_PHOTOS.' as p'
                .' WHERE ap.photo_id = p.id AND cat_type = 6 AND cat_id = '.$this->club_id;

        $photos = $jsDatabase->select($query);
        $this->lists['photos'] = array();
        if (count($photos)) {
            foreach ($photos as $photo) {
                if (is_file(JS_PATH_IMAGES.$photo->filename)) {
                    $this->lists['photos'][] = $photo;
                }
            }
        }
    }

    public function getTeams()
    {
        global $jsDatabase;
        $query = 'SELECT * FROM  '.DB_TBL_TEAMS.' as t WHERE club_id = '.$this->club_id;

        $this->lists['teams'] = $jsDatabase->select($query);
    }
}
