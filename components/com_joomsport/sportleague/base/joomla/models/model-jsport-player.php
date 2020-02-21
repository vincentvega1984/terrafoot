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
require_once JS_PATH_OBJECTS.'class-jsport-team.php';
require_once JS_PATH_OBJECTS.'class-jsport-season.php';
require_once JS_PATH_OBJECTS.'class-jsport-match.php';

class modelJsportPlayer
{
    public $season_id = null;
    public $player_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        $this->season_id = $season_id;
        $this->player_id = $id;

        if (!$this->player_id) {
            die('ERROR! Player ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT p.*,c.country,c.ccode '
                .'FROM '.DB_TBL_PLAYERS.' as p'
                .' LEFT JOIN '.DB_TBL_COUNTRIES.' as c ON c.id=p.country_id'
                .' WHERE p.id = '.$this->player_id);
        $this->row->first_name = classJsportTranslation::get('player_'.$this->row->id, 'first_name',$this->row->first_name);
        $this->row->last_name = classJsportTranslation::get('player_'.$this->row->id, 'last_name',$this->row->last_name);
        $this->row->about = classJsportTranslation::get('player_'.$this->row->id, 'about',$this->row->about);
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        global $jsDatabase;
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->player_id, '0', $this->season_id);
        $this->lists['ef'][classJsportLanguage::get('BL_NAME')] = $this->row->first_name.' '.$this->row->last_name;
        if($this->row->nick){
            $this->lists['ef'][classJsportLanguage::get('BL_NICK')] = $this->row->nick;
        }
        $teams = $jsDatabase->select('SELECT t.t_name'
                . ' FROM '.DB_TBL_TEAMS. ' as t'
                . ' JOIN '.DB_TBL_PLAYERS_TEAM .' as p ON t.id=p.team_id'
                . ' WHERE p.player_id = '.$this->player_id
                . ($this->season_id?" AND p.season_id = ".$this->season_id:"")
                . ' GROUP BY t.id');
        if(count($teams)){
            $tt = '';
            for($intA = 0; $intA < count($teams); $intA++){
                if($intA != 0){
                    $tt .= ', ';
                }
                $tt .= $teams[$intA]->t_name;
            }
            $this->lists['ef'][classJsportLanguage::get('BLFA_TEAM')] = $tt;
        }
        if ($this->row->country_id) {
            $url = 'components/com_joomsport/img/flags/' . strtolower($this->row->ccode) . '.png';
            if (file_exists($url)) {
                $this->lists['ef'][classJsportLanguage::get('BL_COUNTRY')] =  '<img src="' . JURI::base() . $url . '" alt="' . $this->row->country . '"/> ' . $this->row->country . ' </span>';
            }
        }
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getHeaderSelect();

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
                .' WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '.$this->player_id;

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

        $query = 'SELECT * FROM '.DB_TBL_TOURNAMENT." WHERE published = '1' ORDER BY name";

        $tourn = $jsDatabase->select($query);

        $javascript = " onchange='fSubmitwTab(this);'";

        $jqre = '<select class="selectpicker" name="sid" id="sid"  size="1" '.$javascript.'>';
        $jqre .= '<option value="0">'.classJsportLanguage::get('BLFA_ALL').'</option>';
        
        
        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m WHERE m.m_id=md.id AND md.s_id= -1'
                ." AND m.m_single='1' "
                .' AND (m.team1_id='.$this->player_id.' OR m.team2_id='.$this->player_id.')';

        $frm = $jsDatabase->selectValue($query);

        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m,'
                .' '.DB_TBL_SQUARD.' as sc'
                .' WHERE sc.match_id=m.id AND sc.player_id='.$this->player_id.''
                .' AND m.m_id=md.id AND md.s_id= -1 ';
        $frm2 = $jsDatabase->selectValue($query);

        if ($frm || $frm2) {
            $jqre .= '<option value="-1" '.((-1 == $this->season_id) ? 'selected' : '').'>'.classJsportLanguage::get('BLFA_FRIENDLY_MATCHES').'</option>';
        }
        $this->_lists['tour_name_s'] = array();
        /////////!!!!!
        $ind_s = 0;
        for ($i = 0; $i < count($tourn); ++$i) {
            $is_tourn2 = array();
            $tsingl = $tourn[$i]->t_single;
            //print_r($tsingl);
            if ($tsingl) {
                $query = 'SELECT s.s_id as id,s.s_name as s_name'
                        .' FROM '.DB_TBL_SEASON_PLAYERS.' as sp,'
                        .' '.DB_TBL_SEASONS.' as s'
                        ." WHERE s.published = '1' AND s.t_id=".$tourn[$i]->id.''
                        .' AND s.s_id=sp.season_id AND sp.player_id='.$this->player_id;
            } else {
                $query = 'SELECT DISTINCT(s.s_id) as id,s.s_name as s_name'
                        .' FROM '.DB_TBL_SEASONS.' as s,'
                        .' '.DB_TBL_SEASON_TEAMS.' st,'
                        .' '.DB_TBL_PLAYERS_TEAM.' as pt'
                        ." WHERE pt.confirmed='0' AND s.published = '1'"
                        .' AND s.t_id='.$tourn[$i]->id.' AND st.season_id=s.s_id'
                        .' AND st.team_id=pt.team_id'
                        .' AND pt.season_id=s.s_id AND pt.player_id='.$this->player_id
                        .'  ORDER BY s.s_name';
            }

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
    public function getBoxScore(){
        global $jsDatabase;
        
        $query = "SELECT * FROM #__bl_box_fields"
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);
        
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        
        $html = $this->getBoxHtml();
            
        return $html;
        
    }
    
    public function getBoxHtml(){
        global $jsConfig, $jsDatabase;
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
        
        $query = "SELECT ev.fvalue
			            FROM #__bl_players as p
                                    , #__bl_extra_values as ev 
			            WHERE ev.uid=p.id AND ev.f_id={$efbox} AND ev.uid={$this->player_id}"
                        .' ORDER BY p.first_name,p.last_name';
        $efid = $jsDatabase->selectValue($query);                            
        $query = "SELECT * FROM #__bl_box_fields"
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);
        
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        if(!$checkfornull){
            return '';
        }
        
        $parentB = array();
        $complexBox = $jsDatabase->select('SELECT * FROM #__bl_box_fields WHERE parent_id="0" AND published="1"  AND displayonfe="1" ORDER BY ordering,name') ;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $jsDatabase->select('SELECT * FROM #__bl_box_fields WHERE parent_id="'.$complexBox[$intA]->id.'" AND published="1" AND displayonfe="1" ORDER BY ordering,name') ;
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
            
            $simpleBox = $jsDatabase->select('SELECT id, sel_value as name FROM #__bl_extra_select WHERE fid="'.$efbox.'" AND id="'.$efid.'" ORDER BY eordering,sel_value') ;
            for($intS=0;$intS<count($simpleBox);$intS++){    
                $players = array($this->player_id);
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
                                        <th rowspan="2">'
                                        .($this->season_id?  JText::_('BLFA_TEAM'):JText::_('BLFA_SEASON'))
                                        .'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                
                                $player_stat = $jsDatabase->select("SELECT ".$totalSQL.",team_id,season_id FROM #__bl_box_matches WHERE player_id={$this->player_id} AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".$this->season_id." GROUP BY team_id":" GROUP BY season_id"));
                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            if($season_id){
                                                $post_id = $player_stat[$intPP]->team_id;
                                                $team = new classJsportTeam($post_id,$season_id);
                                                $html_body .= $team->getName();
                                            }else{
                                                $post_id = $player_stat[$intPP]->season_id;
                                                $season = new classJsportSeason($post_id);
                                                $html_body .= $season->object->tsname;
                                            }
                                            
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    }  
                    if(count($player_stat) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= JText::_('BLFA_BOXSCORE_TOTAL');
                        $html .= '</td>';
                        $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE player_id={$this->player_id}"
                        .($this->season_id?" AND season_id = ".$this->season_id:""));
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
            $players = array($this->player_id);
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
                                        <th rowspan="2">'
                                        .($this->season_id?  JText::_('BLFA_TEAM'):JText::_('BLFA_SEASON'))
                                        .'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                
                                    $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL.",team_id,season_id FROM #__bl_box_matches WHERE player_id={$this->player_id} AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".$this->season_id." GROUP BY team_id":" GROUP BY season_id"));


                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            if($season_id){
                                                $post_id = $player_stat[$intPP]->team_id;
                                                $team = new classJsportTeam($post_id,$season_id);
                                                $html_body .= $team->getName();
                                            }else{
                                                $post_id = $player_stat[$intPP]->season_id;
                                                $season = new classJsportSeason($post_id);
                                                $html_body .= $season->object->tsname;
                                            }
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    } 
                    if(count($player_stat) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= JText::_('BLFA_BOXSCORE_TOTAL');
                        $html .= '</td>';
                        $player_stat = $jsDatabase->selectObject("SELECT ".$totalSQL." FROM #__bl_box_matches WHERE player_id={$this->player_id}"
                        .($this->season_id?" AND season_id = ".$this->season_id:""));
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
    
    public function getBoxScoreMatches(){
        global $jsDatabase;
        
        
        $html = $this->getBoxHtmlMatches();
            
        return $html;
        
    }
    
    public function getBoxHtmlMatches(){
        global $jsConfig,$wpdb,$jsDatabase;
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
        $query = "SELECT ev.fvalue
			            FROM #__bl_players as p
                                    , #__bl_extra_values as ev 
			            WHERE ev.uid=p.id AND ev.f_id={$efbox} AND ev.uid={$this->player_id}"
                        .' ORDER BY p.first_name,p.last_name';
        $efid = $jsDatabase->selectValue($query);  
        $query = "SELECT * FROM #__bl_box_fields"
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        if(!$checkfornull){
            return '';
        }
        
        $parentB = array();
        $complexBox = $jsDatabase->select('SELECT * FROM #__bl_box_fields WHERE parent_id="0" AND published="1"  AND displayonfe="1" ORDER BY ordering,name') ;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $jsDatabase->select('SELECT * FROM #__bl_box_fields WHERE parent_id="'.$complexBox[$intA]->id.'" AND published="1" AND displayonfe="1" ORDER BY ordering,name') ;
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
            
            $simpleBox = $jsDatabase->select('SELECT id, sel_value as name FROM #__bl_extra_select WHERE fid="'.$efbox.'" AND id="'.$efid.'" ORDER BY eordering,sel_value') ;
            for($intS=0;$intS<count($simpleBox);$intS++){    
                $players = array($this->player_id);
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
                                        <th rowspan="2">'.  JText::_('BL_TAB_MATCH').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                
                                $player_stat = $jsDatabase->select("SELECT * FROM #__bl_box_matches WHERE player_id={$this->player_id}  AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".$this->season_id:""));

                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            
                                            $match = new classJsportMatch($player_stat[$intPP]->match_id);
                                            $partic_home = $match->getParticipantHome();
                                            $partic_away = $match->getParticipantAway();

                                            $title = '';
                                            if ($partic_home) {
                                                $title .= $partic_home->getName().' ';
                                            }
                                            $title .= $match->object->score1.':'.$match->object->score2;
                                            if ($partic_away) {
                                                $title .= ' '.$partic_away->getName();
                                            }
                                            
                                            $html_body .= $title;
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody></table></div>';
                    } 
                    
            }
        }else{
            $th1=$th2='';
            $boxtd = array();
            $players = array($this->player_id);
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
                                        <th rowspan="2">'.JText::_('BL_TAB_MATCH').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
            
                                    $player_stat = $jsDatabase->select("SELECT * FROM #__bl_box_matches WHERE player_id={$this->player_id}  AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".$this->season_id:"")
                                    );

                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = get_post($player_stat[$intPP]->match_id);
                                            $html_body .= $player->post_title;
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody></table></div>';
                    } 
        }
        return $html;
        
    }
}
