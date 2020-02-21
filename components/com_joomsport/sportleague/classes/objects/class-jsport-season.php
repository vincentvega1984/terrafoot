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

require_once JS_PATH_MODELS.'model-jsport-season.php';
require_once JS_PATH_OBJECTS.'class-jsport-seasonlist.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-participant.php';
foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'tournament_types/*.php') as $filename) {
    include $filename;
}

class classJsportSeason
{
    private $id = null;
    public $object = null;
    public $season = null;
    public $lists = null;
    public $modelObj = null;
    public $gr_id = 0;

    public function __construct($id = 0)
    {
        $this->id = $id;
        if (!$this->id) {
            $this->id = classJsportRequest::get('sid');
            $this->gr_id = classJsportRequest::get('gr_id');
        }
        if (!$this->id) {
            die('ERROR! SEASON ID not DEFINED');
        }
        $this->loadObject($this->id);
    }

    private function loadObject($id)
    {
        $obj = $this->modelObj = new modelJsportSeason($id);
        $this->object = $obj->getRow();

        $this->lists = $obj->loadLists();
        if(!empty($this->object)){
            $this->lists['optionsT']['title'] = $this->object->tsname;
        }
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getSingle()
    {
        if(isset($this->object->t_single))
        return (int)$this->object->t_single;
    }
    public function getTournType()
    {
        return (int)$this->object->tournament_type;
    }

    //

    public function getChild()
    {
  
        $type = $this->getTournType();
        if ($type == 0) {
            $this->season = new classJsportTournMatches($this->object);
            //$this->season->calculateTable();
        } else {
            $this->season = new classJsportTournRace($this->object);
            //$this->season->calculateTable();
        }

        return $this->season;
    }

    public function getRow()
    {
        if(!empty($this->object)){
            //$obj = new classJsportSeason($this->id);
            $child = $this->getChild();
            $child->calculateTable(false, $this->gr_id);
            $this->getLists();
            $this->lists['options']['title'] = $this->lists['optionsT']['title'];
            $this->lists['bonuses'] = $this->getSeasonBonuses();
            $this->setHeaderOptions();
            return $this;
        }else{
            JError::raiseError('404', 'Not found');
        }
        
        
    }

    public function getLists()
    {
        $this->lists['options'] = json_decode($this->object->season_options,true);
        $this->season->lists['tblcolors'] = $this->modelObj->getColors();

        $this->lists['colors'] = $this->modelObj->lists['colors'];
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab

        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_TBL');
        $tabs[$intA]['body'] = 'table-group.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'tableS';

        //about
        if ($this->object->s_descr) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_about';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_ABOUT');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = classJsportText::getFormatedText($this->object->s_descr);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }
        //rules
        if ($this->object->s_rules) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_rules';
            $tabs[$intA]['title'] = classJsportLanguage::get('BL_TAB_RULES');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = classJsportText::getFormatedText($this->object->s_rules);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }

        return $tabs;
    }

    public function setHeaderOptions()
    {
        global $jsConfig;
        $this->lists['options']['calendar'] = $this->id;
        $seaslistObj = new classJsportSeasonlist();
        if ($seaslistObj->canJoin($this->object)) {
            $this->lists['options']['joinseason'] = $this->id;
        }
        if (!$this->getSingle() && $jsConfig->get('enbl_linktoplayerlist') == '1') {
            $this->lists['options']['playerlist'] = $this->id;
        }
        $this->lists['options']['print'] = '<a href="javascript:void(0);" onclick="componentPopup();"><span class="glyphicon glyphicon-print"></span></a>';
        //social
        if ($jsConfig->get('jsbp_season') == '1') {
            $this->lists['options']['social'] = true;
            classJsportAddtag::addCustom('og:title', $this->object->tsname);
            $img = $this->object->tourn_logo;
            if (is_file(JS_PATH_IMAGES.$img)) {
                classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$img);
            }
            classJsportAddtag::addCustom('og:description', $this->object->s_descr);
        }
    }
    public function getSeasonBonuses(){
        $obj = new classJsportParticipant($this->id);
        $participants = $obj->getParticipants();
        $html = '';
        for($intA = 0; $intA < count($participants); $intA++){
            if($participants[$intA]->bonus_point && $participants[$intA]->bonus_point != '0.00'){
                $p = $obj->getParticipiantObj($participants[$intA]->id);
                $html .= "<div>" . $p->getName(false) . " - " . $participants[$intA]->bonus_point . "</div>";
            }
        }
        return $html;
    }
    
    public function getXML(){
        header('Content-Type: text/xml'); 
        //var_dump($this->season->lists['columnsCell']);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><jstable></jstable>');

        $xml->addAttribute('version', '1.0');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $xml->addChild('tsname', $this->object->tsname);
        if($this->object->tourn_logo){
            $img  = jsHelperImages::getEmblemBig($this->object->tourn_logo, 1, 'emblInline', '150', false);
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
        }else{
            $src = '';
        }
        $xml->addChild('tourndescr', $src);
        $xml->addChild('tournlogo', $this->object->tourn_logo);
        
        $sextras = $xml->addChild('seasonextras');
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            foreach ($this->lists['ef'] as $key => $value) {
                $sextra = $sextras->addChild('seasonextra');
                $sextra->addChild('extraname', $key);
                $sextra->addChild('extravalue', $value);
            }
        }
        $groups = $xml->addChild('seasongroups');
        if(isset($this->season->lists['columnsCell']) && count($this->season->lists['columnsCell'])){
            foreach ($this->season->lists['columnsCell'] as $key => $value) {
                $group = $groups->addChild('seasongroup');
                $group->addChild('groupname', $key);
                
                $tfields = $group->addChild('tfields');
                $tfield1 = $tfields->addChild('tfield', classJsportLanguage::get('BL_TBL_RANK'));
                $tfield1->addAttribute('uid','rank');
                $tfield2 = $tfields->addChild('tfield', classJsportLanguage::get($this->getSingle()?'BL_PARTICS':'BLFA_ADMIN_TEAM'));
                $tfield2->addAttribute('uid','particname');
                if (count($this->season->lists['columns'])) {
                    foreach ($this->season->lists['columns'] as $keyF => $valueF) {
                        
                            $tfield3 = $tfields->addChild('tfield', $this->season->lists['available_options'][$keyF]);
                            $tfield3->addAttribute('uid',$keyF);
 
                    }
                }
                if (isset($this->season->lists['ef_table']) && count($this->season->lists['ef_table'])) {
                    foreach ($this->season->lists['ef_table'] as $ef) {
                        $tfield4 = $tfields->addChild('tfield', $ef->name);
                        $tfield4->addAttribute('uid','ef_'.$ef->id);
                    }
                }
                
                
                $participants = $group->addChild('participants');
                $rank = 1;
                for($intA=0;$intA<count($value);$intA++){
                    $participant = $participants->addChild('participant');
                    $options = json_decode($value[$intA]->options, true);

                    $partObj = $this->season->getPartById($options['id']);
                    
                    
                    $participant->addChild('particid',$value[$intA]->participant_id);
                    $partcolumns = $participant->addChild('partcolumns');
                    $partcolumn1 = $partcolumns->addChild('partcolumn',$rank);
                    $partcolumn1->addAttribute('uid','rank');
                    $partcolumn2 = $partcolumns->addChild('pname',$partObj->getName(false));
                    $partcolumn2->addAttribute('uid','particname');
                    if (count($this->season->lists['columns'])) {
                        foreach ($this->season->lists['columns'] as $key2 => $value2) {
                            if ($key2 != 'emblem_chk') {
                                if ($key2 != 'curform_chk') {
                                    $partcolumn3 = $partcolumns->addChild('partcolumn',(isset($options[$key2]) ? $options[$key2] : ''));
                                    $partcolumn3->addAttribute('uid',$key2);
                                } else {
                                    $partcolumn3 = $partcolumns->addChild('partcolumn',(isset($value[$intA]->$key2) ? $value[$intA]->$key2 : ''));
                                    $partcolumn3->addAttribute('uid',$key2);

                                }
                            }else{
                                $emblem = $partObj->getEmblem(false);
                                $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
                                $src = $xpath->evaluate("string(//img/@src)");
                                $partcolumn3 = $partcolumns->addChild('partcolumn',$src);
                                $partcolumn3->addAttribute('uid','emblem');
                            }
                        }
                    }
                    if (isset($this->season->lists['ef_table']) && count($this->season->lists['ef_table'])) {
                        foreach ($this->season->lists['ef_table'] as $ef) {
                            $efid = 'ef_'.$ef->id;
                            $partcolumn4 = $partcolumns->addChild('partcolumn', $value[$intA]->{$efid});
                            $partcolumn4->addAttribute('uid',$efid);

                        }
                    }
                    $rank++;
                }
                
            }
        }

        echo $xml->asXML();
    }
    public function getJSON(){
        $json_array = array();
        $json_array['datetime'] = date('Y-m-d H:i:s');
        $json_array['tsname'] = $this->object->tsname;
        $json_array['tsingle'] = $this->object->t_single;
        if($this->object->tourn_logo){
            $img  = jsHelperImages::getEmblemBig($this->object->tourn_logo, 1, 'emblInline', '150', false);
            $xpath = new DOMXPath(@DOMDocument::loadHTML($img));
            $src = $xpath->evaluate("string(//img/@src)");
        }else{
            $src = '';
        }
        //$json_array['tourndescr'] = $src;
        $json_array['tournlogo'] = $src;
        
        $json_array['seasonextras'] = array();
        if(isset($this->lists['ef']) && count($this->lists['ef'])){
            $intA = 0;
            foreach ($this->lists['ef'] as $key => $value) {
                
                $json_array['seasonextras'][$intA]['extraname'] = $key;
                $json_array['seasonextras'][$intA]['extravalue'] = $value;
                $intA++;
            }
        }
        $json_array['tfields'] = array();
        $json_array['seasongroups'] = array();
        if(isset($this->season->lists['columnsCell']) && count($this->season->lists['columnsCell'])){
            $intGR = 0;
            foreach ($this->season->lists['columnsCell'] as $key => $value) {
                $json_array['seasongroups'][$intGR]['groupname'] =  $key;
                
                $json_array['tfields']['rank'] = classJsportLanguage::get('BL_TBL_RANK');
                $json_array['tfields']['particname'] = classJsportLanguage::get($this->getSingle()?'BL_PARTICS':'BLFA_ADMIN_TEAM');
                if (count($this->season->lists['columns'])) {
                    foreach ($this->season->lists['columns'] as $keyF => $valueF) {
                        if($keyF != 'curform_chk')
                        $json_array['tfields'][$keyF] = $this->season->lists['available_options'][$keyF]; 
                    }
                }
                if (isset($this->season->lists['ef_table']) && count($this->season->lists['ef_table'])) {
                    foreach ($this->season->lists['ef_table'] as $ef) {
                        $json_array['tfields']['ef_'.$ef->id] = $ef->name;
                    }
                }
                
                
                $json_array['seasongroups'][$intGR]['participants'] = array();
                $rank = 1;
                for($intA=0;$intA<count($value);$intA++){
                    
                    $options = json_decode($value[$intA]->options, true);

                    $partObj = $this->season->getPartById($options['id']);
                    
                    $json_array['seasongroups'][$intGR]['participants'][$intA]['particid'] = $value[$intA]->participant_id;
                    $json_array['seasongroups'][$intGR]['participants'][$intA]['partcolumns']['rank'] = $rank;
                    $json_array['seasongroups'][$intGR]['participants'][$intA]['partcolumns']['particname'] = $partObj->getName(false);

                    if (count($this->season->lists['columns'])) {
                        foreach ($this->season->lists['columns'] as $key2 => $value2) {
                            if ($key2 != 'emblem_chk') {
                                if ($key2 != 'curform_chk') {
                                    $json_array['seasongroups'][$intGR]['participants'][$intA]['partcolumns'][$key2] = isset($options[$key2]) ? $options[$key2] : '';
                    
                                }
                            }else{
                                $emblem = $partObj->getEmblem(false);
                                $xpath = new DOMXPath(@DOMDocument::loadHTML($emblem));
                                $src = $xpath->evaluate("string(//img/@src)");
                                $json_array['seasongroups'][$intGR]['participants'][$intA]['partcolumns']['emblem'] = $src;
                    
                            }
                        }
                    }
                    if (isset($this->season->lists['ef_table']) && count($this->season->lists['ef_table'])) {
                        foreach ($this->season->lists['ef_table'] as $ef) {
                            $efid = 'ef_'.$ef->id;
                            $json_array['seasongroups'][$intGR]['participants'][$intA]['partcolumns'][$efid] = $value[$intA]->{$efid};
                    

                        }
                    }
                    $rank++;
                }
               $intGR++; 
            }
        }
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($json_array);
        die();
    }
}
