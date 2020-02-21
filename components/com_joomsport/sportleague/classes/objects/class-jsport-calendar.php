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

class classJsportCalendar
{
    private $season_id = null;
    private $object = null;
    public $lists = null;
    public $view = null;

    public function __construct($season_id = null)
    {
        global $jsConfig;
        $this->season_id = $season_id;
        if (!$this->season_id) {
            $this->season_id = classJsportRequest::get('sid');
        }
        if (!$this->season_id) {
            die('ERROR! SEASON ID not DEFINED');
        }

        $seasonObj = new classJsportSeason($this->season_id);

        $childObj = $seasonObj->getChild();
        $this->object = $childObj->getCalendar();
        $this->lists = $childObj->lists;
        $this->lists['options']['title'] = ($seasonObj->lists['optionsT']['title']);
        $this->lists['t_single'] = $seasonObj->getSingle();
        $this->lists['pagination'] = $childObj->pagination;
        $this->view = $childObj->getCalendarView();

        $this->setHeaderOptions();

        /*var_dump($childObj);
        $obj = new modelJsportCalendar($this->season_id);
        $this->object = $obj->row;*/
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getRow()
    {

        //$this->loadObject($this->object);
        return $this->getObject();
    }
    public function getView()
    {
        return $this->view;
    }

    public function setHeaderOptions()
    {
        global $jsConfig;
        $this->lists['options']['standings'] = $this->season_id;
        if (!$this->lists['t_single'] && $jsConfig->get('enbl_linktoplayerlistcal') == '1') {
            $this->lists['options']['playerlist'] = $this->season_id;
        }
        $this->lists['options']['print'] = '<a href="javascript:void(0);" onclick="componentPopup();"><span class="glyphicon glyphicon-print"></span></a>';
    }
    
    public function getXML(){
        header('Content-Type: text/xml'); 
        //var_dump($this->object);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><jscalendar></jscalendar>');

        $xml->addAttribute('version', '1.0');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $xml->addChild('seasonid', $this->season_id);
        
        $matches = $xml->addChild('matches');
        
        if(isset($this->object) && count($this->object)){
            foreach ($this->object as $match) {
                $matchC = $matches->addChild('match');
                $matchC->addChild('matchid', $match->id);

                $matchC->addChild('mstatus', $match->object->m_played);
                $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                $match_dateCustom= classJsportDate::getDate($match->object->m_date, $match->object->m_time,'m-d-Y H:i p');
                
                $matchC->addChild('mdate', $match_dateCustom);
                $matchC->addChild('mdatecustom', $match_date);
                $matchC->addChild('mlocation', $match->getLocation());
                $matchC->addChild('mdayname', $match->object->m_name);
               
                
                
                $partic_home = $match->getParticipantHome();
                $partic_away = $match->getParticipantAway();
                $hometeam = $matchC->addChild('home');
                $hometeam->addChild('score', $match->object->score1);
                $hometeam->addChild('teamid', $match->object->team1_id);
                $hometeam->addChild('teamname', $partic_home->getName(false));
                $emblem = $partic_home->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
                $src = $xpath->evaluate("string(//img/@src)");
                $hometeam->addChild('teamlogo', $src);
                
                $awayteam = $matchC->addChild('away');
                $awayteam->addChild('score', $match->object->score2);
                $awayteam->addChild('teamid', $match->object->team2_id);
                $awayteam->addChild('teamname', $partic_away->getName(false));
                $emblem = $partic_away->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
                $src = $xpath->evaluate("string(//img/@src)");
                $awayteam->addChild('teamlogo', $src);
                
            }
        }
        
        echo $xml->asXML();
    }
    public function getJSON(){
        $json_array = array();

        $json_array['datetime'] = date('Y-m-d H:i:s');
        $json_array['seasonid'] = $this->season_id;
        $json_array['tsingle'] = $this->lists['t_single'];
        $json_array['matches'] = array();
        
        if(isset($this->object) && count($this->object)){
            foreach ($this->object as $match) {
                $json_array['matches'][$match->id] = array();
                $json_array['matches'][$match->id]['mstatus'] = $match->object->m_played;
                $match_date = classJsportDate::getDate($match->object->m_date, $match->object->m_time);
                $match_dateCustom= classJsportDate::getDate($match->object->m_date, $match->object->m_time,'%m-%d-%Y %I:%M %p');
                
                $json_array['matches'][$match->id]['mdate'] = $match_dateCustom;
                $json_array['matches'][$match->id]['mdateCustom'] = $match_date;
                $json_array['matches'][$match->id]['mlocation'] = $match->getLocation();
                $json_array['matches'][$match->id]['mdayname'] = $match->object->m_name;
               
                
                
                $partic_home = $match->getParticipantHome();
                $partic_away = $match->getParticipantAway();
                $json_array['matches'][$match->id]['home'] = array();
                $json_array['matches'][$match->id]['home']['score'] = $match->object->score1;
                $json_array['matches'][$match->id]['home']['teamid'] = $match->object->team1_id;
                $json_array['matches'][$match->id]['home']['teamname'] = $partic_home->getName(false);
                $emblem = $partic_home->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
                $src = $xpath->evaluate("string(//img/@src)");
                $json_array['matches'][$match->id]['home']['teamlogo'] = $src;
                
                $json_array['matches'][$match->id]['away'] = array();
                $json_array['matches'][$match->id]['away']['score'] = $match->object->score2;
                $json_array['matches'][$match->id]['away']['teamid'] = $match->object->team2_id;
                $json_array['matches'][$match->id]['away']['teamname'] = $partic_away->getName(false);
                $emblem = $partic_away->getEmblem(false);
                $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
                $src = $xpath->evaluate("string(//img/@src)");
                $json_array['matches'][$match->id]['away']['teamlogo'] = $src;
                
            }
        }
        
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }
}
