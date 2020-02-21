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

require_once JS_PATH_MODELS.'model-jsport-seasonlist.php';
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-dlists.php';

class classJsportSeasonlist
{
    private $id = null;
    private $object = null;
    public $lists = null;
    public $tournid = null;

    public function __construct()
    {
        $this->tournid = classJsportRequest::get('filtr_tourn');
        
        $obj = new modelJsportSeasonlist($this->tournid);
        $this->object = $obj->getRow();
        $this->lists['options']['title'] = classJsportLanguage::get('BLFA_SEAS_LIST');
        $this->lists['options']['tourn'] = classJsportDlists::getSeasonsTournList($this->tournid);
    }

    public function getRow()
    {
        return $this->object;
    }

    public function canJoin($season)
    {
        $reg_start = mktime(substr($season->reg_start, 11, 2), substr($season->reg_start, 14, 2), 0, substr($season->reg_start, 5, 2), substr($season->reg_start, 8, 2), substr($season->reg_start, 0, 4));
        $reg_end = mktime(substr($season->reg_end, 11, 2), substr($season->reg_end, 14, 2), 0, substr($season->reg_end, 5, 2), substr($season->reg_end, 8, 2), substr($season->reg_end, 0, 4));

        $part_count = $this->partCount($season);

        if ($season->s_reg && ($part_count < $season->s_participant || $season->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season->reg_end == '0000-00-00 00:00:00'))) {
            return true;
        }

        return false;
    }
    public function partCount($season)
    {
        global $jsDatabase;
        if ($season->t_single) {
            $query = 'SELECT COUNT(*)'
                        .' FROM '.DB_TBL_PLAYERS.' as t ,'
                        .' '.DB_TBL_SEASON_PLAYERS.' as st'
                        .' WHERE st.player_id = t.id AND st.season_id = '.$season->s_id;
        } else {
            $query = 'SELECT COUNT(*)'
                        .' FROM '.DB_TBL_TEAMS.' as t ,'
                        .' '.DB_TBL_SEASON_TEAMS.' as st'
                        .' WHERE st.team_id = t.id AND st.season_id = '.$season->s_id;
        }

        return $part_count = $jsDatabase->selectValue($query);
    }
    public function getJSON(){
        global $jsConfig;
        $json_array = array();

        $json_array['datetime'] = date('Y-m-d H:i:s');
        
        if(count($this->object)){
            for($intA=0;$intA<count($this->object);$intA++){
                $season = $this->object[$intA];
                $json_array['season'][$season->s_id]['season_name'] = $season->s_name;
                $json_array['season'][$season->s_id]['tourn_name'] = $season->name;
                $json_array['season'][$season->s_id]['tourn_id'] = $season->t_id;
                $json_array['season'][$season->s_id]['tsingle'] = $season->t_single;
            }
        }
        
        $conf = json_decode($jsConfig->get('mobile_options',''),true);
        $json_array['defoptions'] = array(
            'team' => (isset($conf['default_team'])?$conf['default_team']:0),
            'season' => (isset($conf['default_season'])?$conf['default_season']:0),
        );
                
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }
}
