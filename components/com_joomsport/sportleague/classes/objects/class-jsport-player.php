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

require_once JS_PATH_MODELS.'model-jsport-player.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-getplayers.php';
require_once JS_PATH_CLASSES.'class-jsport-matches.php';
require_once JS_PATH_OBJECTS.'class-jsport-match.php';
require_once JS_PATH_OBJECTS.'class-jsport-season.php';

class classJsportPlayer
{
    private $id = null;
    public $season_id = null;
    public $object = null;
    public $lists = null;
    public $model = null;

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = (int) classJsportRequest::get('id');
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Player ID not DEFINED');
        }
        $this->loadObject($loadLists);
    }

    private function loadObject($loadLists)
    {
        $obj = $this->model = new modelJsportPlayer($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($loadLists) {
            $this->lists = $obj->loadLists();
            $this->lists['options']['tourn'] = $this->lists['tourn'];
        }
        $this->lists['options']['title'] = $this->getName(false);
    }

    public function getName($linkable = false, $itemid = 0)
    {
        global $jsConfig;
        $pname = $jsConfig->get('player_name')
                ?
                ($this->object->nick ? $this->object->nick : $this->object->first_name.' '.$this->object->last_name)
                :
                ($this->object->first_name.' '.$this->object->last_name);
        if (!$linkable || $jsConfig->get('enbl_playerlinks') == '0') {
            return $pname;
        }
        $html = '';
        if ($this->id > 0 && isset($this->object->first_name)) {
            $html = classJsportLink::player($pname, $this->id, $this->season_id,false, $itemid);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        return $this->lists['def_img'];
    }
    public function getEmblem($linkable = true, $type = 0, $class = 'emblInline', $width = 0, $light = true, $itemid = 0)
    {
        global $jsConfig;
        $html = '';
        if (!isset($this->lists['def_img'])) {
            $this->loadObject(true);
        }
        $html = jsHelperImages::getEmblem($this->lists['def_img'], 0, $class, $width, $light);
        if ($linkable && $jsConfig->get('enbl_playerlogolinks') == '1') {
            $html = classJsportLink::player($html, $this->id, $this->season_id, $itemid, $linkable);
        }

        return $html;
    }

    public function getRow()
    {
        $this->setHeaderOptions();

        return $this;
    }
    public function getRowSimple()
    {
        return $this;
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_PLAYERR');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'users';
        //matches
        $this->getMatches();
        if (count($this->lists['matches'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_matches';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_MATCHES');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = jsHelper::getMatches($this->lists['matches'], $this->lists['match_pagination']);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }
        
        $this->getMatchPlayed();
        $this->getStatBlock();
        $this->getMatchesBlock();
        $this->getBoxScoreList();
        if(count($this->lists['career_matches']) || count($this->lists['career'])){
            ++$intA;
            $tabs[$intA]['id'] = 'stab_statistic';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_STAT');
            $tabs[$intA]['body'] = 'player-stat.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'chart';
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
    public function getDescription()
    {
        return classJsportText::getFormatedText($this->object->about);
    }

    public function getEvents()
    {

        
        $players = classJsportgetplayers::getPlayersFromTeam(array('season_id' => $this->season_id), $this->id, false);
        if (isset($players['list'][0])) {
            $this->lists['players'] = $players['list'];
        }
        //events
        $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($this->season_id);
    }
    public function getMatchPlayed()
    {
        global $jsConfig;
        $this->lists['played_matches'] = null;
        if ($jsConfig->get('played_matches')) {
            $this->lists['played_matches'] = classJsportgetplayers::getPlayersPlayedMatches($this->id, 0, $this->season_id);
        }
    }
    
    public function getStatBlock(){
        global $jsConfig, $jsDatabase;
        $this->getEvents();
        //var_dump($this->lists['players']);
        $jsblock_career = $jsConfig->get('jsblock_career');
        if($jsblock_career){
            $jsB = json_decode($jsblock_career,true);
            if($jsB["enable"]){
                $this->_lists['jsblock_career_enable'] = $jsB["enable"];
                $jsblock_career_fields_selected = $jsB["options"];
            }else{
                $this->lists['career'] = $this->lists['career_head'] = array();
                return;
            }
            
        }


        $available_options = array(
            'op_mplayed' => array(
                'field' => 'played',
                'text' => JText::_('BLBE_CAREER_PLAYED')
            ),
            'op_mlineup' => array(
                'field' => 'career_lineup',
                'text' => JText::_('BLBE_CAREER_LINEUP')
            ),
            'op_minutes' => array(
                'field' => 'career_minutes',
                'text' => JText::_('BLBE_CAREER_PLAYEDMINUTES'),
                'img' => '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/stopwatch.png" width="24" class="sub-player-ico" title="'.JText::_('BLBE_CAREER_PLAYEDMINUTES').'" alt="'.JText::_('BLBE_CAREER_PLAYEDMINUTES').'" />'
            ),
            'op_subsin' => array(
                'field' => 'career_subsin',
                'text' => JText::_('BLBE_CAREER_SUBSIN'),
                'img' => '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/ico/in-new.png" width="24" class="sub-player-ico" title="'.JText::_('BLBE_CAREER_SUBSIN').'" alt="'.JText::_('BLBE_CAREER_SUBSIN').'" />'
            ),
            'op_subsout' => array(
                'field' => 'career_subsout',
                'text' => JText::_('BLBE_CAREER_SUBSOUT'),
                'img' => '<img src="'.JS_LIVE_URL.'components/com_joomsport/img/ico/out-new.png" width="24" class="sub-player-ico" title="'.JText::_('BLBE_CAREER_SUBSOUT').'" alt="'.JText::_('BLBE_CAREER_SUBSOUT').'" />'
            )
        );
        $resultoptions = array();
        if($jsblock_career_fields_selected && count($jsblock_career_fields_selected)){
            
            foreach($jsblock_career_fields_selected as $block){
                if(isset($available_options[$block])){
                    $resultoptions[] = $available_options[$block];
                }else{
                    $block = str_replace('ev_', 'eventid_', $block);
                    if(isset($this->lists['events_col'][$block])){
                        $resultoptions[] = array(
                            'field' => $block,
                            'text' => $this->lists['events_col'][$block]->getEventName(),
                            'img' => $this->lists['events_col'][$block]->getEmblem(),
                        );
                    }
                }
            }
            
        }

        $output = $outpuhead = array();
        if(count($resultoptions)){
            if(!$this->season_id){
                $outputhead[] = JText::_('BLFA_SEASON');
            }
            $outputhead[] = JText::_('BLFA_TEAM');
            foreach($resultoptions as $ro){
                if (isset($ro['img']) && $ro['img']) {
                    $outputhead[] = $ro['img'];
                }else
                if (isset($ro['text'])) {
                    $outputhead[] = $ro['text'];
                }
            }
            for($intA=0;$intA<count($this->lists['players']);$intA++){
                $pl = $this->lists['players'][$intA];
                if(!$this->season_id){
                    $oseas = new classJsportSeason($pl->season_id);
                    $output[$intA][] = $oseas->object->tsname;
                }
                if($pl->team_id){
                    
                    $teamObj = new classJsportTeam($pl->team_id,$pl->season_id);
                    $output[$intA][] = $teamObj->getEmblem().$teamObj->getName(true);
                }else{
                    $output[$intA][] = '';
                }  
                foreach($resultoptions as $ro){
                    if (isset($pl->{$ro['field']})) {
                        if (is_float(floatval($pl->{$ro['field']}))) {
                            $output[$intA][] = round($pl->{$ro['field']}, 3);
                        } else {
                            $output[$intA][] = floatval($pl->{$ro['field']});
                        }
                    }
                }
                
            }
        }
        $this->lists['career_head'] = $outputhead;
        $this->lists['career'] = $output;
        
    }
    public function getMatchesBlock(){
        global $jsConfig, $jsDatabase;
        if(!$jsConfig->get('jsblock_matchstat')){
            $this->lists['career_matches'] = array();
            return;
        }
        $kick_events = $jsConfig->get('kick_events');
        if($kick_events){
            $kick_events = json_decode($kick_events, true);
        }
        $query = 'SELECT m.*,s.mainsquard'
                .' FROM '.DB_TBL_MATCH.' as m '
                .' JOIN '.DB_TBL_MATCHDAY.' as md ON md.id=m.m_id'
                .' JOIN '.DB_TBL_SQUARD.' as s ON m.id=s.match_id'
                ." WHERE m.m_played='1' "
                ." AND s.player_id=".$this->id
                .($this->season_id ? ' AND md.s_id='.$this->season_id : '')
                ." GROUP BY m.id"
                .' ORDER BY m.m_date,m.m_time';
        $matches = $jsDatabase->select($query);
        
        $timeline = $jsConfig->get('jstimeline');
        $timeline = json_decode($timeline,true);
        $duration = (isset($timeline['duration']))?intval($timeline['duration']):0;
        $html = '';
       for($intA = 0; $intA < count($matches); $intA ++){
           $minutes_played = 0;
           $match = new classJsportMatch($matches[$intA]->id);
           $partic_home = $match->getParticipantHome();
           $partic_away = $match->getParticipantAway();
           $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
           $match_duration = $duration;
           $moptions = json_decode($matches[$intA]->options, true);

            if(isset($moptions['duration']) && $moptions['duration']){
                $match_duration = $moptions['duration'];
            } 
            if($matches[$intA]->mainsquard == 1){
                $query = 'SELECT minutes'
                        .' FROM '.DB_TBL_SUBSIN.' as s,'
                        .' '.DB_TBL_MATCH.' as m,'
                        .' '.DB_TBL_MATCHDAY.' as md'
                        .' WHERE s.match_id = '.intval($matches[$intA]->id)
                        .' AND s.player_out='.$this->id;
                $min = (int) $jsDatabase->selectValue($query);
                if(!$min){
                    $min = $match_duration;
                    if(is_array($kick_events) && count($kick_events)){
                        $query = "SELECT minutes"
                                . " FROM ".DB_TBL_MATCH_EVENTS
                                . " WHERE match_id = ".(intval($matches[$intA]->id))
                                . " AND player_id = ".intval($this->id)
                                . " AND e_id IN (".implode(',', $kick_events).")"
                                . " ORDER BY minutes asc"
                                . " LIMIT 1";
                        $kickOut = (int) $jsDatabase->selectValue($query);
                        if($kickOut){
                            $min = $kickOut;
                        }
                    }
                }
                $minutes_played = $min;
            }else{
                $query = 'SELECT minutes'
                        .' FROM '.DB_TBL_SUBSIN.' as s'
                        
                        .' WHERE s.match_id = '.intval($matches[$intA]->id)
                        .' AND s.player_in='.$this->id;
                $min = (int) $jsDatabase->selectValue($query);
                if($min){
                    $query = 'SELECT minutes'
                        .' FROM '.DB_TBL_SUBSIN.' as s'
                        
                        .' WHERE s.match_id = '.intval($matches[$intA]->id)
                        .' AND s.player_out='.$this->id;
                    $min2 = (int) $jsDatabase->selectValue($query);
                    if($min2){
                        $minutes_played = $min2 - $min;
                    }else{
                        $kickOut = 0;
                        if(is_array($kick_events) && count($kick_events)){
                            $query = "SELECT minutes"
                                . " FROM ".DB_TBL_MATCH_EVENTS
                                . " WHERE match_id = ".(intval($matches[$intA]->id))
                                . " AND player_id = ".intval($this->id)
                                . " AND e_id IN (".implode(',', $kick_events).")"
                                . " ORDER BY minutes asc"
                                . " LIMIT 1";
                            $kickOut = (int) $jsDatabase->selectValue($query);
                            
                        }
                        if($kickOut){
                            $minutes_played = $kickOut - $min;
                        }else{
                            $minutes_played = $match_duration - $min;
                        }
                        
                    }
                    
                    
                }else{
                    $minutes_played = 0;
                }
            } 
           
           
           $match_events = '';
           
           $query = 'SELECT e_id,minutes'
                        .' FROM '.DB_TBL_MATCH_EVENTS
                        .' WHERE match_id = '.$matches[$intA]->id
                        .' AND player_id='.$this->id
                        .' ORDER BY eordering,minutes';
            $ev = $jsDatabase->select($query);
            for($intG=0;$intG<count($ev);$intG++){
                $evObj = new classJsportEvent($ev[$intG]->e_id);
                $title = $evObj->getEventName();
                if($ev[$intG]->minutes){
                    $title .= ' '.$ev[$intG]->minutes.'\'';
                }
                $match_events .= $evObj->getEmblem(false, $title);
            }
           if($minutes_played || $match_events){
           $html .= '<div class="jstable-row">
                            <div class="jstable-cell jsMatchDivTime">
                                <div class="jsDivLineEmbl">'

                                    .$match_date
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHome">
                                <div class="jsDivLineEmbl">'

                                    .  jsHelper::nameHTML($partic_home->getName(true))
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHomeEmbl">'
                                .'<div class="jsDivLineEmbl" style="float:right;">'
                                    .($partic_home->getEmblem())

                                .'</div>

                            </div>
                            <div class="jstable-cell jsMatchDivScore">
                                '.jsHelper::getScore($match).'
                            </div>
                            <div class="jstable-cell jsMatchDivAwayEmbl">
                                <div class="jsDivLineEmbl">'

                                        .($partic_away->getEmblem())
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivAway">'
                                .'<div class="jsDivLineEmbl">'

                                        .jsHelper::nameHTML($partic_away->getName(true), 0).'

                                </div>    
                            </div>'
                            .'<div class="jstable-cell">'
                                .$match_events
                            .'</div>'
                            .'<div class="jstable-cell">'
                                .$minutes_played.'\''
                            .'</div>
                        </div>    ';
           }
       }
       $this->lists['career_matches'] = $html;
        
    }    
    public function getBoxScoreList(){
        $this->lists['boxscore'] = $this->model->getBoxScore();
        $this->lists['boxscore_matches'] = $this->model->getBoxScoreMatches();
    }
    public function getMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);
        //$link = 'index.php?task=player&id='.$this->id.'&sid='.$this->season_id.'#stab_matches';
        $link = classJsportLink::player('', $this->id, $this->season_id, true);
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();
        $pagination->setAdditVar('jscurtab', 'stab_matches');
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList(1);
        $pagination->setPages($rows['count']);
        $this->lists['match_pagination'] = $pagination;
        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->id, false);
                $matches[] = $match->getRow();
            }
        }
        $this->lists['matches'] = $matches;
    }
    public function setHeaderOptions()
    {
        global $jsConfig;
        if ($this->season_id > 0) {
            $this->lists['options']['calendar'] = $this->season_id;
            $this->lists['options']['standings'] = $this->season_id;
        }

        //social
        if ($jsConfig->get('jsbp_player') == '1') {
            $this->lists['options']['social'] = true;
            classJsportAddtag::addCustom('og:title', $this->getName(false));
            $img = $this->getDefaultPhoto();
            if (is_file(JS_PATH_IMAGES.$img)) {
                classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$img);
            }

            classJsportAddtag::addCustom('og:description', $this->getDescription());
        }
    }
    public function getYourTeam()
    {
        return '';
    }
    
    public function getXML(){
        header('Content-Type: text/xml'); 
        //var_dump($this);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><jsplayer></jsplayer>');

        $xml->addAttribute('version', '1.0');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $xml->addChild('seasonid', $this->season_id);
        $xml->addChild('playerid', $this->object->id);
        $xml->addChild('pname', $this->object->first_name.' '.$this->object->last_name);
        $xml->addChild('pnick', $this->object->nick);
        $xml->addChild('pdescription', $this->object->about);
        $xml->addChild('pcountry', $this->object->country);
        
        $img = jsHelperImages::getEmblemBig($this->getDefaultPhoto());
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
        
        $xml->addChild('defimg', $src);
        $sextras = $xml->addChild('playerextras');
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            foreach ($this->lists['ef'] as $key => $value) {
                $sextra = $sextras->addChild('playerextra');
                $sextra->addChild('extraname', $key);
                $sextra->addChild('extravalue', $value);
            }
        }
        $this->getEvents();
        $this->getMatchPlayed();
        
        $playerstats = $xml->addChild('playerstats');

        if ($this->lists['played_matches'] !== null) {
            $playerstat = $playerstats->addChild('playerstat');
            $playerstat->addChild('statname',classJsportLanguage::get('BLFA_MATCHPLAYED'));
            $playerstat->addChild('statvalue',$this->lists['played_matches']);
            

        }
        if (count($this->lists['events_col'])) {
            foreach ($this->lists['events_col'] as $key => $value) {
                if (isset($this->lists['players']->{$key})) {
                    
                    $playerstat = $playerstats->addChild('playerstat');
                    $playerstat->addChild('statname',$value->getEventName());

                    if (is_float(floatval($this->lists['players']->{$key}))) {

                        $playerstat->addChild('statvalue',round($this->lists['players']->{$key}, 3));
                    } else {

                        $playerstat->addChild('statvalue',floatval($this->lists['players']->{$key}));
                    }


                }
            }
        }

            

        
        
        echo $xml->asXML();
    } 
    
    public function getJSON(){
        $json_array = array();

        $json_array['datetime'] =  date('Y-m-d H:i:s');
        $json_array['seasonid'] = $this->season_id;
        $json_array['playerid'] = $this->object->id;
        $json_array['pname'] =  $this->object->first_name.' '.$this->object->last_name;
        $json_array['pnick'] = $this->object->nick;
        $json_array['pdescription'] =  $this->object->about;
        $json_array['pcountry'] = $this->object->country;
        
        $img = jsHelperImages::getEmblemBig($this->getDefaultPhoto());
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");

        $json_array['defimg'] = $src;
        
        $json_array['playerextras'] = array();

        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            $intA = 0;
            foreach ($this->lists['ef'] as $key => $value) {
                $json_array['playerextras'][$intA]['extraname'] = $key;
                $json_array['playerextras'][$intA]['extratype'] = 'text';
                if($key == 'Country'){
                    $xpath = new DOMXPath(@DOMDocument::loadHTML($value));
                    $src = $xpath->evaluate("string(//img/@src)");
                    if($src){
                        $value = $src;
                    }
                    $json_array['playerextras'][$intA]['extratype'] = 'image';
                }
                if(substr($value,0,10) == '<a target='){
                    $xpath = new DOMXPath(@DOMDocument::loadHTML($value));
                    $src = $xpath->evaluate("string(//a/@href)");
                    if($src){
                        $value = $src;
                    }
                    $json_array['playerextras'][$intA]['extratype'] = 'link';
                }
                
                $json_array['playerextras'][$intA]['extravalue'] = $value;
                $intA++;
            }
        }
        $this->getEvents();
        $this->getMatchPlayed();
        $this->getStatBlock();
        
        $json_array['playerstats'] = array();
        if ($this->lists['played_matches'] !== null) {
            $json_array['playerstats']['playedmatches'] = array('name'=>classJsportLanguage::get('BLFA_MATCHPLAYED'),'value'=>$this->lists['played_matches'], 'img'=>'');
        }
        
        if (count($this->lists['events_col'])) {
            foreach ($this->lists['events_col'] as $key => $value) {
                if(count($this->lists['players'])){
                    $statvalue = 0;
                    foreach ($this->lists['players'] as $pl) {
                        
                    
                        if (isset($pl->{$key})) {

                            if (is_float(floatval($pl->{$key}))) {

                                $statvalue += round($pl->{$key}, 3);
                            } else {

                                $statvalue += floatval($pl->{$key});
                            }
                            

                        }
                    }
                    $img = $value->getEmblem();
                    $img_src = '';
                    if($img){
                        $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
                        $src = $xpath->evaluate("string(//img/@src)");
                        if($src){
                            $img_src = $src;
                        }
                    }
                    $json_array['playerstats'][$key] = array('name'=>$value->getEventName(),'value'=>$statvalue,'img'=>$img_src);

                }
            }
        }

        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }
    
}
