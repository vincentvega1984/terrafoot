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
class modelJsportSeason
{
    public $season_id = null;
    public $lists = null;
    public $object = null;

    public function __construct($id)
    {
        $this->season_id = $id;

        if (!$this->season_id) {
            die('ERROR! SEASON ID not DEFINED');
        }
        global $jsDatabase;
        $this->object = $jsDatabase->selectObject('SELECT s.*, t.name,t.t_single,'
                ."t.tournament_type, CONCAT(t.name, ' ', s.s_name) as tsname,t.name as tourn_name,"
                .'t.descr as tourn_descr, t.logo as tourn_logo '
                .'FROM '.DB_TBL_SEASONS.' as s'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t  ON s.t_id = t.id'
                .' WHERE s.published="1" AND t.published="1" AND s.s_id = '.intval($this->season_id));
        //translation
        if($this->object){
            $this->object->tourn_descr = classJsportTranslation::get('tournament_'.$this->object->t_id, 'descr',$this->object->tourn_descr);
            $this->object->tourn_name = classJsportTranslation::get('tournament_'.$this->object->t_id, 'name',$this->object->tourn_name);
            $this->object->s_name = classJsportTranslation::get('season_'.$this->object->s_id, 's_name',$this->object->s_name);
            $this->object->tsname = $this->object->tourn_name.' '.$this->object->s_name;   
            $this->object->s_descr = classJsportTranslation::get('season_'.$this->object->s_id, 's_descr',$this->object->s_descr);
            $this->object->s_rules = classJsportTranslation::get('season_'.$this->object->s_id, 's_rules',$this->object->s_rules);
        }
        
    }
    public function getRow()
    {
        return $this->object;
    }
    public function getName()
    {
        if($this->object){
            return $this->object->tsname;
        }
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->season_id, '3', $this->season_id);

        return $this->lists;
    }
    public function getType()
    {
        return $this->object->tournament_type;
    }

    public function getSingle()
    {
        if($this->season_id>0){
        return $this->object->t_single;
        }
    }

    public function getColors()
    {
        global $jsDatabase;
        $query = 'SELECT * FROM '.DB_TBL_TBLCOLORS
                .' WHERE s_id='.$this->season_id
                .' ORDER BY place';
        $colors = $this->lists['colors'] = $jsDatabase->select($query);

        $color_mass = array();
        for ($j = 0;$j < count($colors);++$j) {
            $tmp_pl = $colors[$j]->place;
            $color_mass[intval($colors[$j]->place)] = $colors[$j]->color;
            $tmp_arr = explode(',', $tmp_pl);
            $tmp_arr2 = explode('-', $tmp_pl);
            if (count($tmp_arr) > 1) {
                foreach ($tmp_arr as $arr) {
                    if (intval($arr)) {
                        $color_mass[intval($arr)] = $colors[$j]->color;
                    }
                }
            }
            if (count($tmp_arr2) > 1) {
                for ($zzz = $tmp_arr2[0];$zzz < $tmp_arr2[1] + 1;++$zzz) {
                    $color_mass[$zzz] = $colors[$j]->color;
                }
            }
        }

        return $color_mass;
    }
}
