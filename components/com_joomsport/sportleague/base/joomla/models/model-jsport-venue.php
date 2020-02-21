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
class modelJsportVenue
{
    public $season_id = null;
    public $venue_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->venue_id = (int) classJsportRequest::get('id');
        } else {
            $this->season_id = $season_id;
            $this->venue_id = $id;
        }
        if (!$this->venue_id) {
            die('ERROR! Venue ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_VENUE.''
                .' WHERE id = '.$this->venue_id);
        $this->row->v_name = classJsportTranslation::get('venue_'.$this->row->id, 'v_name',$this->row->v_name);
        $this->row->v_address = classJsportTranslation::get('venue_'.$this->row->id, 'v_address',$this->row->v_address);
        $this->row->v_descr = classJsportTranslation::get('venue_'.$this->row->id, 'v_descr',$this->row->v_descr);
        
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->venue_id, '5', 0);
        if($this->row->v_address){
            $this->lists['ef'][classJsportLanguage::get('BLFA_VADDRESS')] = $this->row->v_address;
        }
        $this->getPhotos();
        $this->getDefaultImage();

        $this->lists['options']['title'] = $this->row->v_name;

        return $this->lists;
    }

    public function getDefaultImage()
    {
        global $jsDatabase;
        $this->lists['def_img'] = null;
        if ($this->row->v_defimg) {
            $query = 'SELECT ph_filename FROM  '.DB_TBL_PHOTOS.' as p WHERE p.id = '.$this->row->v_defimg;

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
                .' WHERE ap.photo_id = p.id AND cat_type = 5 AND cat_id = '.$this->venue_id;

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
}
