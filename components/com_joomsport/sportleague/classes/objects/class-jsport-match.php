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

require_once JS_PATH_MODELS.'model-jsport-match.php';
require_once JS_PATH_MODELS.'model-jsport-comments.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-participant.php';
require_once JS_PATH_OBJECTS.'class-jsport-venue.php';
require_once JS_PATH_OBJECTS.'class-jsport-event.php';
class classJsportMatch
{
    public $id = null;
    public $object = null;
    public $season_id = null;
    public $lists = null;
    public $model = null;

    public function __construct($id = 0, $calcLists = true)
    {
        $this->id = $id;
        if (!$this->id) {
            $this->id = (int) classJsportRequest::get('id');
        }
        if (!$this->id) {
            die('ERROR! Match ID not DEFINED');
        }

        $obj = $this->model = new modelJsportMatch($this->id);

        $this->loadObject($obj->row);
        $this->loadSeasonID($obj->getSeasonID());

        if ($calcLists) {
            $obj->loadLists();
        }
        $this->lists = $obj->lists;

        $this->lists['seasObj'] = $obj->getSeasonOptions();
        $this->lists['mStatuses'] = $obj->getCustomMatch();
    }

    public function loadObject($row)
    {
        $this->object = $row;
    }

    public function loadSeasonID($season_id)
    {
        $this->season_id = $season_id;
    }

    public function getObject()
    {
        return $this->object;
    }

    /*public function getResultString(){
        $html = '';
        if($this->object->m_played == '1'){
            
        }else{
            
        }
    }*/

    public function getTips()
    {
    }

    public function getParticipantHome()
    {
        $obj = new classJsportParticipant($this->season_id, $this->object->m_single);
        if ($this->object->team1_id > 0) {
            $part = $obj->getParticipiantObj($this->object->team1_id);

            return $part;
        } else {
            return;
        }
    }
    public function getParticipantAway()
    {
        $obj = new classJsportParticipant($this->season_id, $this->object->m_single);
        if ($this->object->team2_id > 0) {
            $part = $obj->getParticipiantObj($this->object->team2_id);

            return $part;
        } else {
            return;
        }
    }

    public function getRow()
    {
        $this->getTitle($this);
        $this->setHeaderOptions();
        $this->getComments();

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

        if($this->lists['m_events_display'] == 1){
            $this->getPlayerObj($this->lists['m_events_home']);
            $this->getPlayerObj($this->lists['m_events_away']);
        }else{
            $this->getPlayerObj($this->lists['m_events_all']);
            $this->getPlayerObj($this->lists['m_events_home']);
            $this->getPlayerObj($this->lists['m_events_away']);
        }
        $this->getTeamEvenetObj($this->lists['team_events']);
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_MATCH');
        $tabs[$intA]['body'] = 'match-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'flag';
        //about
        if ($this->object->match_descr) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_about';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_ABOUT');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = classJsportText::getFormatedText($this->object->match_descr);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }
        //box score
        $this->getBoxScoreList();
        if (($this->lists['boxscore_home'] != '') || ($this->lists['boxscore_away'] != '')) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_boxscore';
            $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_BOXSCORE');
            $tabs[$intA]['body'] = 'boxscore.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'boxscore';
        }
        //squad
        if (count($this->lists['squard1']) || count($this->lists['squard2'])) {
            $this->getPlayerObj($this->lists['squard1']);
            $this->getPlayerObj($this->lists['squard2']);
            $this->getPlayerObj($this->lists['squard1_res']);
            $this->getPlayerObj($this->lists['squard2_res']);
            ++$intA;
            $tabs[$intA]['id'] = 'stab_squad';
            $tabs[$intA]['title'] = classJsportLanguage::get('BLFA_SQUARD');
            $tabs[$intA]['body'] = 'squad-list.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'users';
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

    public function getPlayerObj(&$players)
    {
        $players_object = array();
        $intU = 0;
        
        if ($players) {
            foreach ($players as $row) {
                if (($row->playerid)) {
                    $obj = new classJsportPlayer($row->playerid, $this->season_id);
                    $objEvent = new classJsportEvent($row->id);
                    $players[$intU]->objEvent = $objEvent;
                    $players[$intU]->obj = $obj->getRowSimple();
                    ++$intU;
                }
            }
        }
        //$this->lists['players'] = $players_object;
    }
    public function getTeamEvenetObj(&$events)
    {
        $events_object = array();
        $intU = 0;
        if ($events) {
            foreach ($events as $row) {
                $objEvent = new classJsportEvent($row->id);
                $events[$intU]->objEvent = $objEvent;

                ++$intU;
            }
        }
        //$this->lists['players'] = $players_object;
    }

    public function getTitle($match)
    {
        $partic_home = $this->getParticipantHome();
        $partic_away = $this->getParticipantAway();

        $title = '';
        if ($partic_home) {
            $title .= $partic_home->getName().' ';
        }
        $title .= jsHelper::getScore($match);
        if ($partic_away) {
            $title .= ' '.$partic_away->getName();
        }
        $this->lists['options']['title'] = '';//$title;
        $this->lists['options']['titleSocial'] = $title;//$title;
        $this->lists['options']['calendar'] = $this->season_id;
        $this->lists['options']['standings'] = $this->season_id;
    }

    public function getLocation($linkable = true)
    {
        if ($this->object->venue_id) {
            $venue = new classJsportVenue($this->object->venue_id);

            return $venue->getName($linkable);
        } elseif ($this->object->m_location) {
            return $this->object->m_location;
        }
    }
    public function getETLabel($et = true)
    {
        $match = $this->object;
        if ($match->m_played != '1') {
            return '';
        }

        $etclass = ($match->score1 > $match->score2) ? 'extra-time-h' : 'extra-time-g';
        if ($match->score1 == $match->score2) {
            $etclass = ($match->aet1 > $match->aet2) ? 'extra-time-h' : 'extra-time-g';
            if ($match->aet1 == $match->aet2 && $match->p_winner) {
                $etclass = ($match->p_winner == $match->team1_id) ? 'extra-time-h' : 'extra-time-g';
            }
        }
        if($et){
            if (($this->lists['seasObj']->s_enbl_extra || $this->season_id == -1) && $match->is_extra) {
                if ($match->p_winner || $match->aet1 != $match->aet2 || $match->score1 != $match->score2) {
                    return $match->p_winner ? '' : '<div class="'.$etclass.'" title="'.classJsportLanguage::get('BLFA_TEAM_WON_ET').'">'.classJsportLanguage::get('BLFA_ET').'</div>';
                }
            }
        }
        if ($match->p_winner) {
            return "<div class='".$etclass."' title='".classJsportLanguage::get('W_TT')."'>".classJsportLanguage::get('W').'</div>';
        }
    }

    public function getBonusLabel()
    {
        $match = $this->object;
        if ($match->m_played != '1') {
            return '';
        }
        if (($match->bonus1 != '0.00' || $match->bonus2 != '0.00')) {
            $html = '<div style="text-align:center;" title="'.classJsportLanguage::get('BLFA_BONUS').'">';
            $html .= '<span style="font-size:75%;">'.floatval($match->bonus1).':</span>';
            $html .= '<span style="font-size:75%;">'.floatval($match->bonus2).'</span>';
            $html .= '</div>';

            return $html;
        }
    }

    public function getComments()
    {
        global $jsConfig;
        $this->lists['enbl_comments'] = $jsConfig->get('mcomments');
        if ($this->lists['enbl_comments']) {
            $commentObj = new modelJsportComments($this->id);

            $this->lists['usr_comments'] = $commentObj->getComments();
            $this->lists['canDeleteComments'] = $commentObj->canDelComment($this->season_id);
        }
    }
    public function setHeaderOptions()
    {
        global $jsConfig;
        //social
        if ($jsConfig->get('jsbp_match') == '1') {
            $this->lists['options']['social'] = true;
            classJsportAddtag::addCustom('og:title', $this->lists['options']['titleSocial']);
            if (isset($this->lists['photos'][0])) {
                $img = $this->lists['photos'][0];
                if (is_file(JS_PATH_IMAGES.$img->filename)) {
                    classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$img->filename);
                }
            }
            classJsportAddtag::addCustom('og:description', $this->object->match_descr);
        }
    }
    public function getBoxScoreList(){
        $this->lists['boxscore_home'] = $this->model->getBoxScore();
        $this->lists['boxscore_away'] = $this->model->getBoxScore(false);
    }
    public function getXML(){
        header('Content-Type: text/xml'); 
        //var_dump($this);
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><jsmatch></jsmatch>');

        $xml->addAttribute('version', '1.0');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $xml->addChild('matchid', $this->id);
        $xml->addChild('matchdayid', $this->object->m_id);
        $xml->addChild('seasonid', $this->season_id);
        $xml->addChild('matchdescription', $this->object->match_descr);
        $xml->addChild('isextra', $this->object->is_extra);
        $xml->addChild('mplayed', $this->object->m_played);
        $xml->addChild('matchday', $this->object->m_name);
        $xml->addChild('bonus1', $this->object->bonus1);
        $xml->addChild('bonus2', $this->object->bonus2);
        $xml->addChild('venue', $this->getLocation(false));
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $this->object->m_date)) {
            $mdate =  classJsportDate::getDate($this->object->m_date, $this->object->m_time);
        } else {
            $mdate =  $this->object->m_date;
        }
        $xml->addChild('mdate', $mdate);
        
        $stages = $xml->addChild('stages');
        
        if(isset($this->lists->maps) && count($this->lists->maps)){
            foreach($this->lists->maps as $map){
                $stage = $stages->addChild('stage');
                $stage->addChild('stagename', $map->m_name);
                $stage->addChild('homescore', $map->m_score1);
                $stage->addChild('awascore', $map->m_score2);
            }
        }
        
        $sextras = $xml->addChild('teamextras');
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            foreach ($this->lists['ef'] as $key => $value) {
                $sextra = $sextras->addChild('teamextra');
                $sextra->addChild('extraname', $key);
                $sextra->addChild('extravalue', $value);
            }
        }
        $partic_home = $this->getParticipantHome();
        $partic_away = $this->getParticipantAway();
            
        $this->getPlayerObj($this->lists['m_events_home']);
        $this->getPlayerObj($this->lists['m_events_away']);
        $this->getTeamEvenetObj($this->lists['team_events']);
        
        $home = $xml->addChild('home');
        $home->addChild('particid', $this->object->team1_id);
        $home->addChild('score', $this->object->score1);
        $home->addChild('particname', $partic_home->getName(false));
        $emblem = $partic_home->getEmblem(false);
        $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
        $src = $xpath->evaluate("string(//img/@src)");
        $home->addChild('emblem', $src);
        $playerevents = $home->addChild('playerevents');
        if(isset($this->lists['m_events_home']) && count($this->lists['m_events_home'])){
            foreach ($this->lists['m_events_home'] as $ev) {
                $playerevent = $playerevents->addChild('playerevent');
                $playerevent->addChild('eventid',$ev->e_id);
                $playerevent->addChild('playerid',$ev->player_id);
                $playerevent->addChild('ecount',$ev->ecount);
                $playerevent->addChild('eventname',$ev->e_name);
                $eimg = $ev->objEvent->getEmblem(false);

                $xpath = new DOMXPath(@DOMDocument::loadHTML($eimg));
                $src = $xpath->evaluate("string(//img/@src)");
                $playerevent->addChild('eventimg',$src);
                $playerevent->addChild('eventminute',$ev->minutes);
                $playerevent->addChild('playername',$ev->obj->getName(false));
            }
        }
        
        $away = $xml->addChild('away');
        $away->addChild('particid', $this->object->team2_id);
        $away->addChild('score', $this->object->score2);
        $away->addChild('particname', $partic_away->getName(false));
        $emblem = $partic_away->getEmblem(false);
        $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
        $src = $xpath->evaluate("string(//img/@src)");
        $away->addChild('emblem', $src);
        $playerevents = $away->addChild('playerevents');
        if(isset($this->lists['m_events_away']) && count($this->lists['m_events_away'])){
            foreach ($this->lists['m_events_away'] as $ev) {
                $playerevent = $playerevents->addChild('playerevent');
                $playerevent->addChild('eventid',$ev->e_id);
                $playerevent->addChild('playerid',$ev->player_id);
                $playerevent->addChild('ecount',$ev->ecount);
                $playerevent->addChild('eventname',$ev->e_name);
                $eimg = $ev->objEvent->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($eimg));
                $src = $xpath->evaluate("string(//img/@src)");
                $playerevent->addChild('eventimg',$src);
                $playerevent->addChild('eventminute',$ev->minutes);
                $playerevent->addChild('playername',$ev->obj->getName(false));
            }
        }
        
        $teamevents = $xml->addChild('teamevents');
        if(isset($this->lists['team_events']) && count($this->lists['team_events'])){
            foreach ($this->lists['team_events'] as $ev) {
                $playerevent = $teamevents->addChild('teamevent');
                $playerevent->addChild('eventid',$ev->id);
                $playerevent->addChild('eventname',$ev->e_name);
                $eimg = $ev->objEvent->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($eimg));
                $src = $xpath->evaluate("string(//img/@src)");
                $playerevent->addChild('eventimg',$src);

                $playerevent->addChild('home',$ev->home_value);
                $playerevent->addChild('away',$ev->away_value);
            }
        }
        
        echo $xml->asXML();
    }
    public function getJSON(){
       
        $json_array = array();

        $json_array['datetime'] = date('Y-m-d H:i:s');
        $json_array['matchid'] = $this->id;
        $json_array['matchdayid'] = $this->object->m_id;
        $json_array['seasonid'] = $this->season_id;
        $json_array['matchdescription'] = $this->object->match_descr;
        $json_array['isextra'] = $this->object->is_extra;
        $json_array['mplayed'] = $this->object->m_played;
        $json_array['matchday'] = $this->object->m_name;
        $json_array['bonus1'] = $this->object->bonus1;
        $json_array['bonus2'] = $this->object->bonus2;
        $json_array['venue'] = $this->getLocation(false);
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $this->object->m_date)) {
            $mdate =  classJsportDate::getDate($this->object->m_date, $this->object->m_time);
        } else {
            $mdate =  $this->object->m_date;
        }
        $json_array['mdate'] = $mdate;
        $json_array['stages'] = array();
        if(isset($this->lists['maps']) && count($this->lists['maps'])){
            foreach($this->lists['maps'] as $map){
                $json_array['stages'][$map->id] = array(
                    'stagename' => $map->m_name,
                    'homescore' => $map->m_score1,
                    'awayscore' => $map->m_score2
                );

            }
        }
        $json_array['matchextras'] = array();
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            $intA = 0;
            foreach ($this->lists['ef'] as $key => $value) {
                $json_array['matchextras'][$intA]['extraname'] = $key;
                $json_array['matchextras'][$intA]['extravalue'] = $value;
                $intA++;
            }
        }
        $partic_home = $this->getParticipantHome();
        $partic_away = $this->getParticipantAway();
            
        $this->getPlayerObj($this->lists['m_events_home']);
        $this->getPlayerObj($this->lists['m_events_away']);
        $this->getTeamEvenetObj($this->lists['team_events']);
        
        $json_array['home'] = array();
        $json_array['home']['particid'] = $this->object->team1_id;
        $json_array['home']['score'] = $this->object->score1;
        $json_array['home']['particname'] = $partic_home->getName(false);
        $emblem = $partic_home->getEmblem(false);
        $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
        $src = $xpath->evaluate("string(//img/@src)");
        $json_array['home']['emblem'] = $src;
        $json_array['home']['playerevents'] = array();
        if(isset($this->lists['m_events_home']) && count($this->lists['m_events_home'])){
            $intA=0;
            foreach ($this->lists['m_events_home'] as $ev) {
               
                $json_array['home']['playerevents'][$intA]['eventid'] = $ev->e_id;
                $json_array['home']['playerevents'][$intA]['playerid'] = $ev->player_id;
                $json_array['home']['playerevents'][$intA]['ecount'] = $ev->ecount;
                $json_array['home']['playerevents'][$intA]['eventname'] = $ev->e_name;
                $eimg = $ev->objEvent->getEmblem(false);

                $xpath = new DOMXPath(@DOMDocument::loadHTML($eimg));
                $src = $xpath->evaluate("string(//img/@src)");
                $json_array['home']['playerevents'][$intA]['eventimg'] = $src;
                $json_array['home']['playerevents'][$intA]['eventminute'] = $ev->minutes;
                $json_array['home']['playerevents'][$intA]['playername'] = $ev->obj->getName(false);
                $intA++;
            }
            
        }
        $json_array['away'] = array();

        $json_array['away']['particid'] =  $this->object->team2_id;
        $json_array['away']['score'] = $this->object->score2;
        $json_array['away']['particname'] = $partic_away->getName(false);
        $emblem = $partic_away->getEmblem(false);
        $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
        $src = $xpath->evaluate("string(//img/@src)");
        $json_array['away']['emblem'] = $src;
        $json_array['away']['playerevents'] = array();
        if(isset($this->lists['m_events_away']) && count($this->lists['m_events_away'])){
            $intA=0;
            foreach ($this->lists['m_events_away'] as $ev) {
                $json_array['away']['playerevents'][$intA]['eventid'] = $ev->e_id;
                $json_array['away']['playerevents'][$intA]['playerid'] = $ev->player_id;
                $json_array['away']['playerevents'][$intA]['ecount'] = $ev->ecount;
                $json_array['away']['playerevents'][$intA]['eventname'] = $ev->e_name;
                $eimg = $ev->objEvent->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($eimg));
                $src = $xpath->evaluate("string(//img/@src)");
                $json_array['away']['playerevents'][$intA]['eventimg'] = $src;
                $json_array['away']['playerevents'][$intA]['eventminute'] = $ev->minutes;
                $json_array['away']['playerevents'][$intA]['playername'] = $ev->obj->getName(false);
                $intA++;
            }
        }
        $json_array['teamevents'] = array();

        if(isset($this->lists['team_events']) && count($this->lists['team_events'])){
            foreach ($this->lists['team_events'] as $ev) {
                
                $json_array['teamevents'][$ev->id]['eventname'] = $ev->e_name;
                $eimg = $ev->objEvent->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($eimg));
                $src = $xpath->evaluate("string(//img/@src)");
                $json_array['teamevents'][$ev->id]['eventimg'] = $src;

                $json_array['teamevents'][$ev->id]['home'] = $ev->home_value;
                $json_array['teamevents'][$ev->id]['away'] = $ev->away_value;
            }
        }
        
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }
    
}
