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

require_once JS_PATH_MODELS.'model-jsport-team.php';
require_once JS_PATH_CLASSES.'class-jsport-matches.php';
require_once JS_PATH_OBJECTS.'class-jsport-match.php';
require_once JS_PATH_OBJECTS.'class-jsport-club.php';
require_once JS_PATH_OBJECTS.'class-jsport-venue.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-getplayers.php';

class classJsportTeam
{
    private $id = null;
    public $season_id = null;
    public $object = null;
    public $lists = null;
    public $matches_latest = 5;
    public $matches_next = 5;
    public $model = null;

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = (int) classJsportRequest::get('tid');
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Team ID not DEFINED');
        }

        $this->loadObject($loadLists);
    }

    private function loadObject($loadLists)
    {
        $obj = $this->model = new modelJsportTeam($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($loadLists) {
            $this->lists = $obj->loadLists();
            
        }
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getName($linkable = false, $itemid = 0)
    {
        global $jsConfig;

        if (!$linkable || ($jsConfig->get('enbl_teamlinks') == '0' && ($this->object->t_yteam != '1' || $jsConfig->get('enbl_teamhgllinks') != '1'))) {
            return $this->object->t_name;
        }
        $html = '';
        if ($this->id > 0 && isset($this->object->t_name)) {
            $html = classJsportLink::team($this->object->t_name, $this->id, $this->season_id, false, $itemid);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        if ($this->lists['def_img'] && is_file(JS_PATH_IMAGES.$this->lists['def_img'])) {
            return $this->lists['def_img'];
        }

        return JSCONF_TEAM_DEFAULT_IMG;
    }
    public function getEmblem($linkable = true, $type = 0, $class = 'emblInline', $width = 0, $itemid = 0)
    {
        global $jsConfig;
        $html = '';
        $html = jsHelperImages::getEmblem($this->object->t_emblem, 1, $class, $width);
        if ($linkable && $jsConfig->get('enbl_teamlogolinks') == '1') {
            $html = classJsportLink::team($html, $this->id, $this->season_id, '', $itemid);
        }

        return isset($this->object->t_emblem) ? $html : '';
    }
    public function getRow()
    {
        $this->setHeaderOptions();
        return $this;
    }
    public function getTabs()
    {
        global $jsConfig;
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_TEAM');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'flag';
        $this->getClub();
        $this->getVenue();
        //matches
        $this->getMatches();
        if (count($this->lists['matches'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_matches';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_MATCHES');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = jsHelper::getMatches($this->lists['matches'], $this->lists['match_pagination'], false);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }

        $this->getPlayers();
        //players
        $show_playertab = $jsConfig->get('show_playertab');
        if (count($this->lists['players']) || ($show_playertab == '1' && !count($this->lists['players']))) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_players';
            $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_PLAYER');
            $tabs[$intA]['body'] = $jsConfig->get('set_teampgplayertab') ? 'player-list-photo.php' : 'player-list.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'users';
        }
        //box score
        $this->getBoxScoreList();
        if (isset($this->lists['boxscore_home']) && ($this->lists['boxscore_home'] != '')) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_boxscore';
            $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_BOXSCORE');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = $this->lists['boxscore_home'];
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'boxscore';
        }
        if ($this->_displayOverviewTab() && count($this->lists['matches'])) {
            $obj = new modelJsportTeam($this->id, $this->season_id);
            $this->lists['curposition'] = $obj->getCurrentPosition();
            $this->getLatestMatches();
            $this->getNextMatches();
            ++$intA;
            $tabs[$intA]['id'] = 'stab_overview';
            $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_OVERVIEW_TAB');
            $tabs[$intA]['body'] = 'team-overview.php';
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

    public function getMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);

        $link = classJsportLink::team('', $this->id, $this->season_id, true);
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();
        $pagination->setAdditVar('jscurtab', 'stab_matches');
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();
        $pagination->setPages($rows['count']);
        $this->lists['match_pagination'] = $pagination;
        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->id, false);
                $matches[] = $match->getRowSimple();
            }
        }
        $this->lists['matches'] = $matches;
    }

    public function getPlayers()
    {
        global $jsConfig;
        $pl_list_order = $jsConfig->get('pllist_order');
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);
        if($pl_list_order){
            $pl = explode('_', $pl_list_order);
            if($pl[1] == '1'){
                $options['ordering'] = 'ef.fvalue';
                $efid = (int) str_replace('efields_', '', $pl[0]);
                $options['sortbyextra'] = $efid;
            }elseif($pl[1] == '2'){
                $options['ordering'] = 'eventid_'.$pl[0].' DESC';
            } 

        }
        $players = classJsportgetplayers::getPlayersFromTeam($options);
        $players_object = array();
        $players = $players['list'];
        if ($players) {
            $count_players = count($players);
            $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable(0, false);
            for ($intC = 0; $intC < $count_players; ++$intC) {
                $row = $players[$intC];
                $obj = new classJsportPlayer($row->id, $this->season_id);
                $obj->lists['tblevents'] = $row;
                $players_object[$intC] = $obj->getRowSimple();
                if ($jsConfig->get('played_matches')) {
                    $players_object[$intC]->played_matches = classJsportgetplayers::getPlayersPlayedMatches($row->id, $this->id, $this->season_id);
                }
                for ($intB = 0; $intB < count($ef); ++$intB) {
                    $players_object[$intC]->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB]->id, $row->id, 0, $this->season_id);
                }
            }
        }
        if ($jsConfig->get('played_matches')) {
            $this->lists['played_matches_col'] = classJsportLanguage::get('BLFA_MATCHPLAYED');
        }
                
        

        $this->lists['players'] = $players_object;

        //events
        $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($this->season_id);
    }
    public function getDescription()
    {
        return classJsportText::getFormatedText($this->object->t_descr);
    }

    private function _displayOverviewTab()
    {
        global $jsConfig;

        return $jsConfig->get('tlb_position') || $jsConfig->get('tlb_form') || $jsConfig->get('tlb_latest') || $jsConfig->get('tlb_next');
    }
    public function getLatestMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);

        $options['limit'] = $this->matches_latest;
        $options['played'] = '1';
        $options['ordering'] = 'm.m_date DESC, m.m_time DESC';
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();

        $matches = array();

        if ($rows['list']) {
            $rows['list'] = array_reverse($rows['list']);
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->id, false);
                $matches[] = $match->getRowSimple();
            }
        }
        $this->lists['matches_latest'] = $matches;
    }
    public function getNextMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);

        $options['limit'] = $this->matches_next;
        $options['played'] = '0';
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();

        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->id, false);
                $matches[] = $match->getRowSimple();
            }
        }
        $this->lists['matches_next'] = $matches;
    }
    public function setHeaderOptions()
    {
        global $jsConfig;
        if ($this->season_id > 0) {
            $this->lists['options']['calendar'] = $this->season_id;
            $this->lists['options']['standings'] = $this->season_id;
            if ($this->lists['enbl_join']) {
                $this->lists['options']['jointeam']['seasonid'] = $this->season_id;
                $this->lists['options']['jointeam']['teamid'] = $this->id;
            }
        }
        $this->lists['options']['tourn'] = $this->lists['tourn'];
        $img = $this->getEmblem(false);
        //social
        if ($jsConfig->get('jsbp_team') == '1') {
            $this->lists['options']['social'] = true;
            classJsportAddtag::addCustom('og:title', $this->getName(false));

            if ($img) {
                classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$this->object->t_emblem);
            }
            classJsportAddtag::addCustom('og:description', $this->getDescription());
        }
        $imgtitle = '';
        if ($img) {
            $imgtitle = $img.'&nbsp;';
        }
        $this->lists['options']['title'] = $imgtitle.$this->getName(false);
    }
    public function getYourTeam()
    {
        global $jsConfig;

        return ($this->object->t_yteam && $jsConfig->get('highlight_team')) ? $jsConfig->get('yteam_color') : '';
    }
    public function getClub($linkable = true){
        if ($this->object->club_id) {
            $venue = new classJsportClub($this->object->club_id);

            $this->lists['ef'][classJsportLanguage::get('BLFA_CLUB')] =  $venue->getName($linkable);
        }
        return false;
    }
    public function getVenue($linkable = true){
        if ($this->object->venue_id) {
            $venue = new classJsportVenue($this->object->venue_id);

            $this->lists['ef'][classJsportLanguage::get('BLFA_VENUE')] = $venue->getName($linkable);
        }
        return false;
    }
    public function getBoxScoreList(){
        $this->lists['boxscore_home'] = $this->model->getBoxScore();
        
    }
    public function getXML(){
        header('Content-Type: text/xml'); 
        //var_dump($this->lists);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><jsteam></jsteam>');

        $xml->addAttribute('version', '1.0');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $xml->addChild('seasonid', $this->season_id);
        $xml->addChild('teamid', $this->object->id);
        $xml->addChild('tname', $this->object->t_name);
        $xml->addChild('defimage', $this->object->def_img);
        if($this->object->t_emblem){
            $img = $this->getEmblem(false);

            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
        }else{
            $src = '';
        }
        
        
        $xml->addChild('emblem', $src);
        
        $sextras = $xml->addChild('teamextras');
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            foreach ($this->lists['ef'] as $key => $value) {
                $sextra = $sextras->addChild('teamextra');
                $sextra->addChild('extraname', $key);
                $sextra->addChild('extravalue', $value);
            }
        }
        $this->getPlayers();
        
        $playersC = $xml->addChild('players');
        
        $tfields = $playersC->addChild('tfields');
        $tfield1 = $tfields->addChild('tfield', '');
        $tfield1->addAttribute('uid','emblem');
        $tfield2 = $tfields->addChild('tfield', classJsportLanguage::get('BLFA_NAME'));
        $tfield2->addAttribute('uid','playername');
        if (isset($this->lists['played_matches_col']) && $this->lists['played_matches_col']) {
            $tfield3 = $tfields->addChild('tfield', $this->lists['played_matches_col']);
            $tfield3->addAttribute('uid','playedmatches');
        }
        if (count($this->lists['events_col'])) {
            foreach ($this->lists['events_col'] as $key => $value) {
                $fieldEV = $value->getEventName();
                $tfield4 = $tfields->addChild('tfield', $fieldEV);
                $tfield4->addAttribute('uid',$key);
                $img = $value->getEmblem();
                if($img){
                    $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
                    $src = $xpath->evaluate("string(//img/@src)");
                }else{
                    $src = '';
                }
                $tfield4->addAttribute('img',$src);
            }   
        }    
        if (count($this->lists['ef_table'])) {
            foreach ($this->lists['ef_table'] as $ef) {
                $key = 'efields_'.$ef->id;
                $value = $ef->name;
                $tfield5 = $tfields->addChild('tfield', $value);
                $tfield5->addAttribute('uid',$key);
            }
        }
        
        $players = $xml->addChild('players');
        
        for ($intA = 0; $intA < count($this->lists['players']); ++$intA) {
            $player = $this->lists['players'][$intA];
            $playerevents = $player->lists['tblevents'];
            $playerC = $players->addChild('player');
            $playerC->addChild('playerid',$player->object->id);
            $partcolumns = $playerC->addChild('partcolumns');
            
            $img = $player->getEmblem(false, 0, '');
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
            
            $tpartcolumn1 = $partcolumns->addChild('partcolumn',$src);
            $tpartcolumn1->addAttribute('uid','emblem');
            $tpartcolumn2 = $partcolumns->addChild('partcolumn',$player->getName(false));
            $tpartcolumn2->addAttribute('uid','playername');
            if (isset($this->lists['played_matches_col']) && $this->lists['played_matches_col']) {
                $tpartcolumn3 = $partcolumns->addChild('partcolumn',$player->played_matches);
                $tpartcolumn3->addAttribute('uid','playedmatches');
                
            }
            
                
            if (count($this->lists['events_col'])) {
                foreach ($this->lists['events_col'] as $key => $value) {
                    
                    if (isset($playerevents->{$key})) {

                        if (is_float(floatval($playerevents->{$key}))) {
                            $tpartcolumn4 = $partcolumns->addChild('partcolumn',round($playerevents->{$key}, 3));
                            $tpartcolumn4->addAttribute('uid',$key);
                            
                        } else {
                            $tpartcolumn4 = $partcolumns->addChild('partcolumn',floatval($playerevents->{$key}));
                            $tpartcolumn4->addAttribute('uid',$key);
                        }
                    }
                    
                }
            }
            
            if (count($this->lists['ef_table'])) {
                foreach ($this->lists['ef_table'] as $ef) {
                    $key = 'ef_'.$ef->id;
                    $value = $ef->name;
                    
                    if (isset($player->{$key})) {
                        $tpartcolumn5 = $partcolumns->addChild('partcolumn',$player->{$key});
                        $tpartcolumn5->addAttribute('uid',$key);
                    }else{
                        $tpartcolumn5 = $partcolumns->addChild('partcolumn','');
                        $tpartcolumn5->addAttribute('uid',$key);
                    }
                    

                }
            }
            

        }
        
        echo $xml->asXML();
    }  
    public function getJSON(){
        $json_array = array();
        $json_array['datetime'] = date('Y-m-d H:i:s');
        $json_array['seasonid'] = $this->season_id;
        $json_array['teamid'] = $this->object->id;
        $json_array['tname'] = $this->object->t_name;
        $json_array['defimage'] = $this->object->def_img;
        $json_array['tdescription'] = $this->getDescription();
        if($this->object->t_emblem){
            $img = $this->getEmblem(false);

            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
        }else{
            $src = '';
        }
        
        $json_array['emblem'] = $src;
        
        $json_array['teamextras'] = array();
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            $intA = 0;
            foreach ($this->lists['ef'] as $key => $value) {
                $json_array['teamextras'][$intA]['extraname'] = $key;
                $json_array['teamextras'][$intA]['extravalue'] = $value;
            }
            $intA++;
        }
        $this->getPlayers();
        
        
        $json_array['tfields'] = array();
        $json_array['tfields']['emblem'] = array('value'=>'');
        $json_array['tfields']['playername'] = array('value'=>classJsportLanguage::get('BLFA_NAME'));

        if (isset($this->lists['played_matches_col']) && $this->lists['played_matches_col']) {
            $json_array['tfields']['playedmatches'] = $this->lists['played_matches_col'];
        }
        if (count($this->lists['events_col'])) {
            foreach ($this->lists['events_col'] as $key => $value) {
                $fieldEV = $value->getEventName();

                $img = $value->getEmblem();
                if($img){
                    $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
                    $src = $xpath->evaluate("string(//img/@src)");
                }else{
                    $src = '';
                }
                
                $json_array['tfields'][$key] = array('value'=>$fieldEV,'image'=>$src);
            }   
        }    
        if (isset($this->lists['ef_table']) && count($this->lists['ef_table'])) {
            foreach ($this->lists['ef_table'] as $ef) {
                $key = 'efields_'.$ef->id;
                $value = $ef->name;

                $json_array['tfields'][$key] = array('value'=>$value);
            }
        }
        
        $json_array['players'] = array();
        
        for ($intA = 0; $intA < count($this->lists['players']); ++$intA) {
            $player = $this->lists['players'][$intA];
            $playerevents = $player->lists['tblevents'];

            $json_array['players'][$player->object->id] = array();

            
            $img = $player->getEmblem(false, 0, '');
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
            $json_array['players'][$player->object->id]['partcolumn']['emblem'] = $src;

            $json_array['players'][$player->object->id]['partcolumn']['playername'] = $player->getName(false);

            if (isset($this->lists['played_matches_col']) && $this->lists['played_matches_col']) {
                $json_array['players'][$player->object->id]['partcolumn']['playedmatches'] = $player->played_matches;

            }
            
                
            if (count($this->lists['events_col'])) {
                foreach ($this->lists['events_col'] as $key => $value) {
                    
                    if (isset($playerevents->{$key})) {

                        if (is_float(floatval($playerevents->{$key}))) {
                            
                            $json_array['players'][$player->object->id]['partcolumn'][$key] = round($playerevents->{$key}, 3);
                            
                        } else {
                            
                            $json_array['players'][$player->object->id]['partcolumn'][$key] = floatval($playerevents->{$key});
                            
                        }
                    }
                    
                }
            }
            
            if (count($this->lists['ef_table'])) {
                foreach ($this->lists['ef_table'] as $ef) {
                    $key = 'ef_'.$ef->id;
                    $value = $ef->name;
                    
                    if (isset($player->{$key})) {

                        $json_array['players'][$player->object->id]['partcolumn'][$key] = $player->{$key};
                         
                    }else{

                        $json_array['players'][$player->object->id]['partcolumn'][$key] = '';
                    }
                    

                }
            }
            

        }
        
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }  
}
