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
require_once JS_PATH_OBJECTS.'class-jsport-season.php';
require_once JS_PATH_MODELS.'model-jsport-season.php';
class modelJsportMatch
{
    public $match_id = null;
    public $lists = null;
    public $row = null;
    public $season = null;

    public function __construct($match_id)
    {
        $this->match_id = $match_id;

        if (!$this->match_id) {
            die('ERROR! Match ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT m.*,md.m_name '
                .'FROM '.DB_TBL_MATCH.' as m'
                .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                .' WHERE m.id = '.$this->match_id);
        $this->row->match_descr = classJsportTranslation::get('match_'.$this->row->id, 'match_descr',$this->row->match_descr);
    }
    public function getSeasonID()
    {
        global $jsDatabase;

        $season_id = $jsDatabase->selectValue('SELECT s_id '
                .'FROM '.DB_TBL_MATCHDAY
                .' WHERE id = '.$this->row->m_id);

        return $season_id;
    }

    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->match_id, '2', 0);
        $this->getPhotos();
        $this->getPlayerEvents();
        $this->getTeamEvents();
        $this->getLineUps();
        $this->getMaps();
    }
    public function getPhotos()
    {
        global $jsDatabase;
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                .' FROM '.DB_TBL_ASSIGN_PHOTOS.' as ap, '.DB_TBL_PHOTOS.' as p'
                .' WHERE ap.photo_id = p.id AND cat_type = 3 AND cat_id = '.$this->match_id;

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

    public function getPlayerEvents()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        if ($season_id > 0) {
            $sObj = new modelJsportSeason($season_id);
            $single = $sObj->getSingle();
        } else {
            $single = $this->row->m_single;
        }
        $query = 'SELECT me.*,ev.*,p.id as playerid'
                        .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_PLAYERS.' as p'
                        ." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
                        .' AND me.match_id = '.$this->match_id.' AND '.($single ? 'me.player_id='.intval($this->row->team1_id) : 'me.t_id='.intval($this->row->team1_id))
                        .' ORDER BY me.eordering,CAST(me.minutes AS UNSIGNED)';

        $this->lists['m_events_home'] = $jsDatabase->select($query);

        $query = 'SELECT me.*,ev.*,p.id as playerid'
                        .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_PLAYERS.' as p'
                        ." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
                        .' AND me.match_id = '.$this->match_id.' AND '.($single ? 'me.player_id='.intval($this->row->team2_id) : 'me.t_id='.intval($this->row->team2_id))
                        .' ORDER BY me.eordering, CAST(me.minutes AS UNSIGNED)';

        $this->lists['m_events_away'] = $jsDatabase->select($query);
        
        $query = 'SELECT me.*,ev.*,me.player_id as playerid'
                        .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev'

                        ." WHERE ev.player_event = '1' AND me.e_id = ev.id"
                        .' AND me.match_id = '.$this->match_id
                        .' AND me.minutes != "" AND me.minutes!= "0"'
                        .' ORDER BY CAST(me.minutes AS UNSIGNED),me.eordering';

        $this->lists['m_events_all'] = $jsDatabase->select($query);
        $this->lists['m_events_display'] = 1;
        if(count($this->lists['m_events_all']) == count($this->lists['m_events_home'])+count($this->lists['m_events_away'])){
            $this->lists['m_events_display'] = 0;
        }
    }
    public function getTeamEvents()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        if ($season_id > 0) {
            $sObj = new modelJsportSeason($season_id);
            $single = $sObj->getSingle();
        } else {
            $single = $this->row->m_single;
        }

        $query = 'SELECT DISTINCT ev.e_name as e_name, ev.*'
                .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_TEAMS.' as p'
                        .' WHERE me.t_id = p.id AND me.t_id IN ( '.intval($this->row->team1_id).', '.intval($this->row->team2_id).')'
                        ." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->match_id
                        .' ORDER BY ev.ordering,ev.e_name';

        $this->lists['team_events'] = $jsDatabase->select($query);

        for ($intA = 0; $intA < count($this->lists['team_events']); ++$intA) {
            $query = 'SELECT me.ecount'
                .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_TEAMS.' as p'
                        .' WHERE me.t_id = p.id AND me.t_id = '.intval($this->row->team1_id).''
                        ." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->match_id
                        .' AND ev.id = '.$this->lists['team_events'][$intA]->id
                        .' ORDER BY ev.ordering,ev.e_name';

            $this->lists['team_events'][$intA]->home_value = $jsDatabase->selectValue($query);

            $query = 'SELECT me.ecount'
                .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_TEAMS.' as p'
                        .' WHERE me.t_id = p.id AND me.t_id = '.intval($this->row->team2_id).''
                        ." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->match_id
                        .' AND ev.id = '.$this->lists['team_events'][$intA]->id
                        .' ORDER BY ev.ordering,ev.e_name';

            $this->lists['team_events'][$intA]->away_value = $jsDatabase->selectValue($query);
        }
    }
    public function getLineUps()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        $ef = classJsportExtrafields::getExtraFieldListSQ(0);

        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_out,sb.player_in,sb.minutes"
                .' FROM '.DB_TBL_PLAYERS.' as p'
                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_out AND sb.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team1_id)
                .' WHERE s.team_id='.intval($this->row->team1_id)." AND s.mainsquard = '1'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard1'] = $this->sortLineUps($jsDatabase->select($query), $ef, $season_id);


        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_out,sb.player_in,sb.minutes"
                .' FROM '.DB_TBL_PLAYERS.' as p'
                                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_out AND sb.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team2_id)
                .' WHERE s.team_id='.intval($this->row->team2_id)." AND s.mainsquard = '1'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard2'] = $this->sortLineUps($jsDatabase->select($query), $ef, $season_id);

        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_in,sb.minutes,sb2.minutes as sb2m,sb2.player_in as sb2in"
                .' FROM '.DB_TBL_PLAYERS.' as p'
                                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_in AND sb.match_id=s.match_id'
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb2 ON p.id=sb2.player_out AND sb2.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team1_id)
                .' WHERE s.team_id='.intval($this->row->team1_id)." AND s.mainsquard = '0'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard1_res'] = $this->sortLineUps($jsDatabase->select($query), $ef, $season_id);

        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_in,sb.minutes,sb2.minutes as sb2m,sb2.player_in as sb2in"
                .' FROM '.DB_TBL_PLAYERS.' as p'
                                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_in AND sb.match_id=s.match_id'
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb2 ON p.id=sb2.player_out AND sb2.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team2_id)
                .' WHERE s.team_id='.intval($this->row->team2_id)." AND s.mainsquard = '0'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard2_res'] = $this->sortLineUps($jsDatabase->select($query), $ef, $season_id);
    }
    
    
    private function sortLineUps($list, $ef, $season_id) 
    {
        global $jsConfig;   
        global $jsDatabase;
        $count_players = count($list);
        $pl_list_order = $jsConfig->get('pllist_order_se');
        for ($intC = 0; $intC < $count_players; ++$intC) {
            for ($intB = 0; $intB < count($ef); ++$intB) {
                if('ef_'.$ef[$intB]->id.'_1' == 'ef_'.$pl_list_order){
                    $val = classJsportExtrafields::getExtraFieldValue($ef[$intB]->id, $list[$intC]->id, 0, $season_id);
                    if($val == null){
                        $list[$intC]->{'ef_'.$ef[$intB]->id.'_1'} = -1;
                    }else{
                        $query = 'SELECT DISTINCT(ef.id),ef.*,'
                                .'ev.fvalue as fvalue,ev.fvalue_text'
                                .' FROM '.DB_TBL_EXTRA_FILDS.' as ef'
                                .'  JOIN '.DB_TBL_EXTRA_VALUES.' as ev'
                                .' ON ef.id=ev.f_id'
                                .' AND ef.id = '.$ef[$intB]->id
                                .' AND ev.uid='.($list[$intC]->id ? intval($list[$intC]->id) : -1).''
                                .' AND ((ev.season_id='.($season_id > 0 ? $season_id : -100)." AND ef.season_related = '1') OR (ev.season_id=0 AND ef.season_related = '0'))"
                                ." WHERE ef.published=1 AND ef.type='0' ".(classJsportUser::getUserId() ? '' : " AND ef.faccess='0'").'';
                        $efObj = $jsDatabase->selectObject($query);
                        
                        $ordering = $jsDatabase->selectValue('SELECT eordering FROM '.DB_TBL_EXTRA_SELECT." WHERE id='".(int) $efObj->fvalue."'");
                        $list[$intC]->{'ef_'.$ef[$intB]->id.'_1'} = $ordering+1;
                    }
                }
                
            }
        }
        
        
        if ($pl_list_order > 0){            
                      
            usort($list, array('modelJsportMatch' ,'eventSortSQ'));
        }
        
        return $list;
    }
    private function eventSortSQ($f1,$f2) {
                global $jsConfig;
                $pl_list_order = $jsConfig->get('pllist_order_se');
                try{
                    if (!$f1->{'ef_'.$pl_list_order} && $f2->{'ef_'.$pl_list_order}) {
                        return -1;
                    }
                    if ($f1->{'ef_'.$pl_list_order} && !$f2->{'ef_'.$pl_list_order}) {
                        return 1;
                    }

                    return strnatcmp($f1->{'ef_'.$pl_list_order}, $f2->{'ef_'.$pl_list_order});
                }catch(Exception $ex){
                    
                }
            }  
    
    public function getMaps()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();

        $query = 'SELECT m.*,mp.m_score1,mp.m_score2'
                .' FROM '.DB_TBL_SEAS_MAPS.' as sm'
                .' JOIN '.DB_TBL_MAPS.' as m ON m.id=sm.map_id'
                .' LEFT JOIN '.DB_TBL_MAPSCORE.' as mp ON m.id=mp.map_id AND mp.m_id='.intval($this->match_id)
                .' WHERE m.id=sm.map_id AND sm.season_id='.intval($season_id).''
                .' ORDER BY m.id';
        $this->lists['maps'] = $jsDatabase->select($query);
    }
    public function getSeasonOptions()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();

        $query = 'SELECT *'
                .' FROM '.DB_TBL_SEASONS.' as s'
                .' WHERE s.s_id='.intval($season_id);

        return $jsDatabase->selectObject($query);
    }

    public function getCustomMatch()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();

        $query = 'SELECT *'
                .' FROM '.DB_TBL_MATCH_STATUSES
                .' WHERE id='.intval($this->row->m_played);

        return $jsDatabase->selectObject($query);
    }
    public function getBoxScore($home = true){
        global $jsDatabase,$jsConfig;

        
            $home_team = (int) $this->row->team1_id;
            $away_team = (int) $this->row->team2_id;
          
        $team_id = $home?$home_team:$away_team; 
        $query = "SELECT * FROM #__bl_box_fields"
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);

        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            
            if($boxf[$intA]->ftype == '1'){
                $options = json_decode($boxf[$intA]->options,true);
                if($options['depend1'] && $options['depend2']){
                    $checkfornull .= ' ( boxfield_'.$options['depend1'].' IS NOT NULL ';
                    $checkfornull .= ' AND boxfield_'.$options['depend2'].' IS NOT NULL ) ';
                }
            }else{
                $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
            }
            
            
            
        }
        if($checkfornull){
            $query = "SELECT player_id FROM #__bl_box_matches"
                    ." WHERE match_id={$this->match_id} AND team_id = {$team_id}"
                    . " AND (".$checkfornull.")";
            $players = $jsDatabase->selectColumn($query);
            $html = '';
            if(count($players)){
                $html = $this->getBoxHtml($team_id, $players);
            }
            return $html;
        }
        return null;
    }
    
    public function getBoxHtml($home_team, $playersNotNull){
        global $jsConfig,$jsDatabase;
        $season_id = $this->getSeasonID();
        $efbox = (int) $jsConfig->get('boxExtraField','0');
        
        $html = '';
        $totalSQL = '';
        $bfields = $jsDatabase->select('SHOW COLUMNS FROM #__bl_box_matches LIKE  "boxfield_%"');
        
        for($intA=0;$intA<count($bfields);$intA++){
            $totalSQL .= 'SUM('.$bfields[$intA]->Field .') as '.$bfields[$intA]->Field.',';
        }
        if(!$totalSQL){
            $totalSQL = '*';
        }else{
            $totalSQL .= '1';
        }                
        
        $parentB = array();
        $complexBox = $jsDatabase->select('SELECT * FROM #__bl_box_fields WHERE parent_id="0" AND published="1"  AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $jsDatabase->select('SELECT * FROM #__bl_box_fields WHERE parent_id="'.$complexBox[$intA]->id.'" AND published="1" AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
                for($intB=0;$intB<count($childBox); $intB++){
                    $options = json_decode($childBox[$intB]->options,true);
                    $extras = isset($options['extraVals'])?$options['extraVals']:array();
                    $childBox[$intB]->extras = $extras;
                    if(count($extras)){
                        foreach($extras as $extr){
                            array_push($complexBox[$intA]->extras, $extr);
                        }
                    }
                }
            }else{
                $options = json_decode($complexBox[$intA]->options,true);
                $extras = isset($options['extraVals'])?$options['extraVals']:array();
                $complexBox[$intA]->extras =  $extras;
            }
            $parentB[$intA]['object'] = $complexBox[$intA];
            $parentB[$intA]['childs'] = $childBox;
        }
        
        $th1 = '';
        $th2 = '';
        
        if($efbox){
            $simpleBox = $jsDatabase->select('SELECT id, sel_value as name FROM #__bl_extra_select WHERE fid="'.$efbox.'" ORDER BY eordering,sel_value') ;
            for($intS=0;$intS<count($simpleBox);$intS++){  
                $query = "SELECT p.id as id,CONCAT(p.first_name,' ',p.last_name) as p_name
			            FROM #__bl_players as p, #__bl_players_team as s
                                    , #__bl_extra_values as ev 
			            WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
			            AND s.team_id = ".$home_team.' AND s.season_id='.$season_id
                                    ." AND ev.uid=p.id AND f_id={$efbox} AND ev.fvalue={$simpleBox[$intS]->id}"
                        .' ORDER BY p.first_name,p.last_name';
                $players = $jsDatabase->select($query);
                //$html .= $simpleBox[$intS]->name;
                $th1=$th2='';
                $boxtd = array();
                for($intA=0;$intA<count($parentB);$intA++){
                    $box = $parentB[$intA];
                    $intChld = 0;
                    
                    for($intB=0;$intB<count($box['childs']); $intB++){
                        if(!count($box['childs'][$intB]->extras) || in_array($simpleBox[$intS]->id, $box['childs'][$intB]->extras)){
                            $intChld++;
                            $box['childs'][$intB]->name = classJsportTranslation::get('boxfields_'.$box['childs'][$intB]->id, 'name',$box['childs'][$intB]->name);
        
                            $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                            $boxtd[] =  $box['childs'][$intB]->id;
                            
                        }
                    }

                    if(!count($box['object']->extras) || in_array($simpleBox[$intS]->id, $box['object']->extras)){
                        $box['object']->name = classJsportTranslation::get('boxfields_'.$box['object']->id, 'name',$box['object']->name);
        
                        if($intChld){
                            $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                        }else{
                            $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                            $boxtd[] =  $box['object']->id;
                        }
                    }elseif($intChld){
                        $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                    }
                }
                $html_head = $html_body = '';
                $html_head .= '<div class="table-responsive">
                    <table class="table jsBoxStatDIvFE">
                                <thead>
                                    <tr>
                                        <th rowspan="2">'.$simpleBox[$intS]->name.'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                $playersIN = array();
                                
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        if(in_array($players[$intPP]->id, $playersNotNull)){
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            
                                            $player = new classJsportPlayer($players[$intPP]->id,$season_id);
                                            $html_body .= $player->getName(true);
                                            $html_body .= '</td>';
                                            $player_stat = $jsDatabase->selectObject("SELECT * FROM #__bl_box_matches WHERE match_id={$this->match_id} AND team_id={$home_team} AND player_id={$player->object->id}");

                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                                            }
                                            $playersIN[] = $players[$intPP]->id;
                                            $html_body .= '</tr>';
                                        }
                                    }
                            if($html_body){
                                $html .= $html_head.$html_body.'</tbody>';
                            }        
                    
                    if(count($playersIN) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= JText::_('BLFA_BOXSCORE_TOTAL');
                        $html .= '</td>';
                        $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE match_id={$this->match_id} AND team_id={$home_team} AND player_id IN (".  implode(',', $playersIN).")");
                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                            
                            $html .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tfoot>';
                    }
                    if($html_body){
                        $html .=  '</table></div>';
                    }

            }
        }else{
            $th1=$th2='';
            $boxtd = array();

            $query = "SELECT p.id 
                        FROM #__bl_players as p, #__bl_players_team as s

                        WHERE s.confirmed='0' AND s.player_join='0' AND s.player_id = p.id
                        AND s.team_id = ".$home_team.' AND s.season_id='.$season_id
                                    
                        .' ORDER BY p.first_name,p.last_name';
            
            $players = $jsDatabase->select($query);
            
            for($intA=0;$intA<count($parentB);$intA++){
                $box = $parentB[$intA];
                $intChld = 0;
                for($intB=0;$intB<count($box['childs']); $intB++){
                    $intChld++;
                    $box['childs'][$intB]->name = classJsportTranslation::get('boxfields_'.$box['childs'][$intB]->id, 'name',$box['childs'][$intB]->name);
        
                    $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                    $boxtd[] =  $box['childs'][$intB]->id;
                    
                }
                $box['object']->name = classJsportTranslation::get('boxfields_'.$box['object']->id, 'name',$box['object']->name);
        
                if($intChld){
                    $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                }else{
                    $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                    $boxtd[] =  $box['object']->id;
                }
                
            }
            $html_head = $html_body = '';
            $html_head .= '<div class="table-responsive"><table class="table jsBoxStatDIvFE">
                                <thead>
                                    <tr>
                                        <th rowspan="2">'.JText::_('BLFA_PLAYERR').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                    $playersIN = array();
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        if(in_array($players[$intPP]->id, $playersNotNull)){
                                        
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = new classJsportPlayer($players[$intPP]->id,$season_id);
                                            $html_body .= $player->getName(true);
                                            $html_body .= '</td>';
                                            $player_stat = $jsDatabase->selectObject("SELECT * FROM #__bl_box_matches WHERE match_id={$this->match_id} AND team_id={$home_team} AND player_id={$player->object->id}");

                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                                            }

                                            $playersIN[] = $players[$intPP]->id;
                                            $html_body .= '</tr>';
                                        }
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    }
                    
                    if(count($playersIN) && $html){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= JText::_('BLFA_BOXSCORE_TOTAL');
                        $html .= '</td>';
                        $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE match_id={$this->match_id} AND team_id={$home_team}");

                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                            $html .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tfoot>';
                    }
                    if($html){
                        $html .=  '</table></div>';
                    }
        }
        return $html;
        
    }
}
