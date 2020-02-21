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


class modelJsportPerson
{
    public $season_id = null;
    public $person_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        $this->season_id = $season_id;
        $this->person_id = $id;

        if (!$this->person_id) {
            die('ERROR! Person ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT p.*,c.name '
                .'FROM '.DB_TBL_PERSONS.' as p'
                .' LEFT JOIN '.DB_TBL_PERSONS_CATEGORY.' as c ON c.id=p.category_id'
                .' WHERE p.id = '.$this->person_id);
        $this->row->first_name = classJsportTranslation::get('person_'.$this->row->id, 'first_name',$this->row->first_name);
        $this->row->last_name = classJsportTranslation::get('person_'.$this->row->id, 'last_name',$this->row->last_name);
        
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        global $jsDatabase;
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->person_id, '6', $this->season_id);
        $this->lists['ef'][classJsportLanguage::get('BL_NAME')] = $this->row->first_name.' '.$this->row->last_name;
        
        
        $this->getPhotos();
        $this->getDefaultImage();


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
                .' WHERE ap.photo_id = p.id AND cat_type = 6 AND cat_id = '.$this->person_id;

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
