<?php

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
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->getPhotos();
        $this->getDefaultImage();
        $this->lists['ef'] = null;
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

        $this->lists['photos'] = $jsDatabase->select($query);
    }
}
