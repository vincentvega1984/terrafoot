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

require_once JS_PATH_ENV_CLASSES.'class-jsport-getplayers.php';
require_once JS_PATH_OBJECTS.'class-jsport-player.php';
require_once JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-dlists.php';
class classJsportPlayerlist
{
    public $season_id = null;
    public $team_id = null;
    public $playersort = null;
    public $playerevents = null;
    public $lists = null;

    public function __construct($season_id = null)
    {
        $this->season_id = $season_id;
        if (!$this->season_id) {
            $this->season_id = classJsportRequest::get('sid');            
            $this->team_id = (int) classJsportRequest::get('team_id');
            $this->playersort = classJsportRequest::get('playersort');
            $this->playerevents = (int) classJsportRequest::get('playerevents');
        }
        $this->loadObject();
        $this->lists['options']['title'] = classJsportLanguage::get('BLFA_PLAYERSLIST');
        $this->setHeaderOptions();
    }

    private function loadObject()
    {
        $options['season_id'] = $this->season_id;
$this->lists['played_matches_col'] = classJsportLanguage::get('BLFA_MATCHPLAYED');
        $link = classJsportLink::playerlist($this->season_id);
        if (classJsportRequest::get('sortf')) {
            $link .= '&sortf='.classJsportRequest::get('sortf');
            $link .= '&sortd='.classJsportRequest::get('sortd');
        }
        if ($this->team_id) {
	    $options['team_id'] = $this->team_id;
            $link .= '&team_id='.$this->team_id;
	}
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();
	
        
        if (classJsportRequest::get('sortf')) {
            
            $options['ordering'] = classJsportRequest::get('sortf').' '.classJsportRequest::get('sortd');
            if(substr(classJsportRequest::get('sortf'),0, 7) == 'efields'){
                $efid = (int) str_replace('efields_', '', classJsportRequest::get('sortf'));
                $options['sortbyextra'] = $efid;
                $options['ordering'] = 'ef.fvalue '.classJsportRequest::get('sortd');
            }
        } else {
            if(substr($this->playersort,0, 7) == 'efields'){
                $options['ordering'] = 'ef.fvalue';
                $efid = (int) str_replace('efields_', '', $this->playersort);
                $options['sortbyextra'] = $efid;
            }else
            if ($this->playersort == 0) {
                $options['ordering'] = 'p.first_name, p.last_name';
            } else {
                $options['ordering'] = 'eventid_'.$this->playersort.' DESC';
            }            
        }

        $players = classJsportgetplayers::getPlayersFromTeam($options);
        $pagination->setPages($players['count']);
        $this->lists['pagination'] = $pagination;

        $players = $players['list'];
        $players_object = array();

        if ($players) {
            $count_players = count($players);
            $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable(0,false);
            for ($intC = 0; $intC < $count_players; ++$intC) {
                $row = $players[$intC];
                $obj = new classJsportPlayer($row->id, $this->season_id);
                $obj->lists['tblevents'] = $row;

                $players_object[$intC] = $obj->getRowSimple();
                
                for ($intB = 0; $intB < count($ef); ++$intB) {
                    $players_object[$intC]->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB]->id, $row->id, 0, $this->season_id);
                }
            }
            
        }
        $this->lists['players'] = $players_object;
        
        //events
        if(!isset($_REQUEST['playerevents'])){
            $this->playerevents = 1;
        }
        
        $this->lists['events_col'] = $this->playerevents? classJsportgetplayers::getPlayersEvents($this->season_id): array();
    }

    public function getRow()
    {
        return $this;
    }
    public function setHeaderOptions()
    {
        if ($this->season_id) {
            $this->lists['options']['standings'] = $this->season_id;
            $this->lists['options']['calendar'] = $this->season_id;
        }
        $this->lists['options']['tourn'] = classJsportDlists::getSeasonsPlayerList($this->season_id);
    }
    
    public function getXML(){
        header('Content-Type: text/xml'); 
        //var_dump($this->lists['players']);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><jspstat></jspstat>');

        $xml->addAttribute('version', '1.0');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $xml->addChild('seasonid', $this->season_id);
        
        $tfields = $xml->addChild('tfields');
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
                $fieldEV = $value->getEmblem() . $value->getEventName();
                $tfield4 = $tfields->addChild('tfield', $fieldEV);
                $tfield4->addAttribute('uid',$key);
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
        
        $json_array['tfields'] = array();
        $json_array['tfields']['emblem'] = array('name'=>classJsportLanguage::get('BLFA_NAME'));
        $json_array['tfields']['playername'] = array('name'=>classJsportLanguage::get('BLFA_NAME'));
        if (isset($this->lists['played_matches_col']) && $this->lists['played_matches_col']) {
            $json_array['tfields']['playedmatches'] = array('name'=>$this->lists['played_matches_col']);
        }
        if (count($this->lists['events_col'])) {
            foreach ($this->lists['events_col'] as $key => $value) {
                $fieldEV = $value->getEmblem() . $value->getEventName();
                $json_array['tfields'][$key] = array('name'=>$fieldEV);
            }   
        }    
        if (count($this->lists['ef_table'])) {
            foreach ($this->lists['ef_table'] as $ef) {
                $key = 'efields_'.$ef->id;
                $value = $ef->name;

                $json_array['tfields'][$key] = array('name'=>$value);
            }
        }
        
        $json_array['players'] = array();
        for ($intA = 0; $intA < count($this->lists['players']); ++$intA) {
            $player = $this->lists['players'][$intA];
            $playerevents = $player->lists['tblevents'];
            
            $img = $player->getEmblem(false, 0, '');
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
            
            $json_array['players'][$player->object->id]['partcolumns']['emblem'] = $src;

            $json_array['players'][$player->object->id]['playername'] = $player->getName(false);
            if (isset($this->lists['played_matches_col']) && $this->lists['played_matches_col']) {
                $json_array['players'][$player->object->id]['playedmatches'] = $player->played_matches;
            }
            
                
            if (count($this->lists['events_col'])) {
                foreach ($this->lists['events_col'] as $key => $value) {
                    
                    if (isset($playerevents->{$key})) {

                        if (is_float(floatval($playerevents->{$key}))) {
                            $eval = round($playerevents->{$key}, 3);

                            
                        } else {
                            $eval = floatval($playerevents->{$key});

                        }
                        $json_array['players'][$player->object->id][$key] = $eval;
                    }
                    
                }
            }
            
            if (count($this->lists['ef_table'])) {
                foreach ($this->lists['ef_table'] as $ef) {
                    $key = 'ef_'.$ef->id;
                    $value = $ef->name;
                    
                    if (isset($player->{$key})) {
                        $eval = $player->{$key};

                    }else{
                        $eval = '';
                    }
                    $json_array['players'][$player->object->id][$key] = $eval;

                }
            }
            

        }
        
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }
}
