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
require_once JS_PATH_ENV_CLASSES.'class-jsport-user.php';
class modelJsportTeam
{
    public $season_id = null;
    public $team_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->team_id = (int) classJsportRequest::get('tid');
        } else {
            $this->season_id = $season_id;
            $this->team_id = $id;
        }
        if (!$this->team_id) {
            die('ERROR! Team ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_TEAMS.''
                .' WHERE id = '.$this->team_id);
        $this->row->t_name = classJsportTranslation::get('team_'.$this->row->id, 't_name',$this->row->t_name);
        $this->row->t_descr = classJsportTranslation::get('team_'.$this->row->id, 't_descr',$this->row->t_descr);
        $this->row->t_city = classJsportTranslation::get('team_'.$this->row->id, 't_city',$this->row->t_city);
        
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->team_id, '1', $this->season_id);
        if($this->row->t_city){
            $this->lists['ef'][classJsportLanguage::get('BLFA_CITY')] = $this->row->t_city;
        }
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getHeaderSelect();
        $this->lists['enbl_join'] = $this->canJoinTeam();
        //$this->getCurrentPosition();
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
                .' WHERE ap.photo_id = p.id AND cat_type = 2 AND cat_id = '.$this->team_id;

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
    public function getHeaderSelect()
    {
        global $jsDatabase;
        /*$query = "SELECT s.s_id as id,s.s_name as s_name, t.id as tourn_id, t.name"
                . " FROM ".DB_TBL_SEASONS." as s"
                . " JOIN ".DB_TBL_TOURNAMENT." as t ON t.id = s.t_id"
                . " JOIN ".DB_TBL_SEASON_TEAMS." as st ON s.s_id=st.season_id"
                . " WHERE s.published='1' AND t.published='1' AND st.team_id=" . $this->team_id . ""
                . " ORDER BY t.name, t.id, s.s_name";
        $this->lists["header"]["season"] = $jsDatabase->select($query);*/

        $query = 'SELECT * FROM '.DB_TBL_TOURNAMENT." WHERE published = '1' AND t_single = '0' ORDER BY name";

        $tourn = $jsDatabase->select($query);

        $javascript = " onchange='fSubmitwTab(this);'";

        $jqre = '<select class="selectpicker" name="sid" id="sid"  size="1" '.$javascript.'>';
        $jqre .= '<option value="0">'.classJsportLanguage::get('BLFA_ALL').'</option>';

        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m WHERE m.m_id=md.id AND md.s_id= -1'
                ." AND m.m_single='0' "
                .' AND (m.team1_id='.$this->team_id.' OR m.team2_id='.$this->team_id.')';

        $frm = $jsDatabase->selectValue($query);

        if ($frm) {
            $jqre .= '<option value="-1" '.((-1 == $this->season_id) ? 'selected' : '').'>'.classJsportLanguage::get('BLFA_FRIENDLY_MATCHES').'</option>';
        }
        $this->_lists['tour_name_s'] = array();
        /////////!!!!!
        $ind_s = 0;
        for ($i = 0; $i < count($tourn); ++$i) {
            $is_tourn2 = array();
            $query = 'SELECT s.s_id as id,s.s_name as s_name'
                    .' FROM '.DB_TBL_SEASONS.' as s'
                    .' LEFT JOIN '.DB_TBL_TOURNAMENT.' as t ON t.id = s.t_id,'
                    .' '.DB_TBL_SEASON_TEAMS.' as st'
                    ." WHERE s.published='1' AND st.team_id=".$this->team_id.''
                    .' AND s.s_id=st.season_id AND t.id='.$tourn[$i]->id.' '
                    .' ORDER BY s.s_name';

            $rows = $jsDatabase->select($query);

            if (count($rows)) {
                $ind_s = 1;
                $tourn[$i]->name = classJsportTranslation::get('tournament_'.$tourn[$i]->id, 'name',$tourn[$i]->name);
        
                $jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">'; ///this
                array_push($this->_lists['tour_name_s'], $tourn[$i]->name);
                for ($g = 0; $g < count($rows); ++$g) {
                    $rows[$g]->s_name = classJsportTranslation::get('season_'.$rows[$g]->id, 's_name',$rows[$g]->s_name);
        
                    $jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $this->season_id) ? 'selected' : '').'>'.$rows[$g]->s_name.'</option>';
                    $seasplayed[] = $rows[$g]->id;
                }
                $jqre .= '</optgroup>';
            }
        }
        $jqre .= '</select>';

        $this->lists['tourn'] = $jqre;
    }

    public function getCurrentPosition()
    {
        global $jsDatabase;

        if ($this->season_id) {
            $query = 'SELECT * FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->season_id
                .' AND participant_id = '.$this->team_id
                .' ORDER BY ordering';

            return $jsDatabase->selectObject($query);
        }

        return '';
    }

    public function canJoinTeam()
    {
        global $jsDatabase;
        global $jsConfig;
        $tr = false;
        $user_id = classJsportUser::getUserId();
        $query = 'Select * FROM '.DB_TBL_PLAYERS.' WHERE usr_id='.intval($user_id);

        $usr = $jsDatabase->selectObject($query);

        if (!$jsConfig->get('player_reg') && $usr && $user_id) {
            $tr = true;
        }
        if ($jsConfig->get('player_reg')) {
            $tr = true;
        }
        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MODERS.' WHERE tid= '.$this->team_id;
        $is_moder = $jsDatabase->selectValue($query);

        return $tr && $is_moder && $jsConfig->get('esport_join_team');
    }
    
    public function getBoxScore(){
        global $jsDatabase;
            
        $team_id = $this->team_id; 
        $query = "SELECT * FROM #__bl_box_fields"
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);
        
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        if($checkfornull){
            $query = "SELECT player_id FROM #__bl_box_matches"
                    ." WHERE team_id = {$team_id}"
                    .($this->season_id?" AND season_id=".$this->season_id:"")
                    . " AND (".$checkfornull.")"
                    ." GROUP BY player_id";
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
        $season_id = $this->season_id;
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
			            AND s.team_id = ".$home_team
                                    .($season_id?' AND s.season_id='.$season_id:'')
                                    ." AND ev.uid=p.id AND f_id={$efbox} AND ev.fvalue={$simpleBox[$intS]->id}"
                                    .' GROUP BY p.id' 
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
                                            $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL.""
                                                    . " FROM #__bl_box_matches"
                                                    . " WHERE team_id={$home_team} AND player_id={$player->object->id}"
                                                    . ($this->season_id?" AND season_id=".$this->season_id:""));

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
                    
                    if(count($playersIN) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= JText::_('BLFA_BOXSCORE_TOTAL');
                        $html .= '</td>';
                        $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE team_id={$home_team} AND player_id IN (".  implode(',', $playersIN).")"
                                . ($this->season_id?" AND season_id=".$this->season_id:""));
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
                        AND s.team_id = ".$home_team
                    .($season_id?' AND s.season_id='.$season_id:'')
                        .' GROUP BY p.id'            
                        .' ORDER BY p.first_name,p.last_name';
            
            $players = $jsDatabase->select($query);
            for($intA=0;$intA<count($parentB);$intA++){
                $box = $parentB[$intA];
                $intChld = 0;
                for($intB=0;$intB<count($box['childs']); $intB++){
                    $intChld++;
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
                                            $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE team_id={$home_team} AND player_id={$player->object->id}"
                                            . ($this->season_id?" AND season_id=".$this->season_id:""));

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
                    
                    if(count($playersIN) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= JText::_('BLFA_BOXSCORE_TOTAL');
                        $html .= '</td>';
                        $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE team_id={$home_team}"
                        . ($this->season_id?" AND season_id=".$this->season_id:""));

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
        return $html;
        
    }
}
