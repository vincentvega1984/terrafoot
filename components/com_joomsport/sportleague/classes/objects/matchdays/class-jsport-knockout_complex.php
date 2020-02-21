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
require_once JS_PATH_MODELS.'model-jsport-knockout.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-participant.php';
class classJsportKnockoutComplex
{
    public $object = null;
    public $lists = null;
    public $single = null;
    const VIEW = 'knockout';

    public function __construct($matchdayObj, $single = 0)
    {
        $this->object = $matchdayObj;
        $this->single = $single;

        if (empty($this->object)) {
            die('ERROR! MatchDay ID not DEFINED');
        }

        $model = new modelJsportKnockout($this->object->id);
        $matches = $model->getMatches($this->single);
        $this->lists['knockout'] = $this->HorKnView($matches);
    }

    public function getView()
    {
        return self::VIEW;
    }

    public function HorKnView($match)
    {
        $kformat = $this->object->k_format;
        $t_single = $this->single;
        $s_id = $this->object->s_id;
        
        
        if(isset($this->object->knock_str)){
            $knockoutView = unserialize($this->object->knock_str);
        }else{
            return '';
        }
        
        //wp_enqueue_style('jscssbracket22',plugin_dir_url( __FILE__ ).'../../../sportleague/assets/css/drawBracketBE.css');
        
        $matrix_stages = array(
            2 => 1,
            4 => 2,
            8 => 3,
            16 => 4,
            32 => 5,
            64 => 6,
            128 => 7
        );
        

        $stages = $matrix_stages[$kformat];
        //echo pow( 64, 1/2);
        //$participiants = JoomSportHelperObjects::getParticipiants($this->_seasonID);
        
        $html = '<div class="jsOverXdiv">';
        $html .= '<div class="drawBracketContainerBE">';
            $html .= '<table border="0" cellpadding="0" cellspacing="0" class="table">';
            

            for($intA=0; $intA < intval($kformat/2); $intA++){
                $html .= '<tr>';
                for($intB=0; $intB < $stages; $intB ++){
                    if($intA == 0 || ($intA % (pow(2,$intB)))==0){
                        
                        $kvalues = array(
                            "home" => 0,
                            "away" => 0,
                            "score1" => "",
                            "score2" => "",
                            "match_id" => ""
                        );
                        if(isset($knockoutView[$intB][$intA])){
                            $kvalues = array(
                                "home" => $knockoutView[$intB][$intA]["home"],
                                "away" => $knockoutView[$intB][$intA]["away"],
                                "score1" => $knockoutView[$intB][$intA]["score1"],
                                "score2" => $knockoutView[$intB][$intA]["score2"],
                                "match_id" => $knockoutView[$intB][$intA]["match_id"]
                            );
                        }
                        
                        $html .= '<td class="even" id="knocktd_'.$intA.'_'.$intB.'" data-game="'.$intA.'" data-level="'.$intB.'" rowspan="'.(pow(2,$intB)).'">';
                        $morefaclass = '';
                        if($intA % (pow(2 ,($intB+1))) == 0 && $intB != $stages-1){
                            $html .= '<div class="jsborderI"></div>';
                            
                        }elseif($intB == $stages-1){
                            $html .= '<div class="jsborderIFin"></div>';
                        }else{
                            $morefaclass = ' facirclebot';
                        }

                        if($kvalues["match_id"]){
                            $match = new classJsportMatch($kvalues["match_id"][0], false);
                            
                            if(isset($match->object->id) && $kvalues["home"] != -1 && $kvalues["away"] != -1){
                               // $html .=  classJsportLink::match('<i class="fa fa-cog jsmatchconf" aria-hidden="true"></i>', $kvalues["match_id"], false, '');
                            
                            }
                            $partic_home = $match->getParticipantHome();
                            $partic_away = $match->getParticipantAway();
                        }
                        
                        $html .= '<div class="player knocktop ml9">'
                                . '<div class="kntmprow">'
                                . '<div class="knockplName">';
                        
                        
                            if($kvalues["home"] > 0){
                                $html .= '<div class="knwinner">';
                                if(is_object($partic_home)){
                                   $html .= $partic_home->getEmblem();
                                   $html .= jsHelper::nameHTML($partic_home->getName(true)); 
                                }else{
                                   $html .= '<div class="js_div_particName"></div>'; 
                                }
                                
                                $html .= '</div>';
                               
                            }elseif($kvalues["home"] == -1){
                                $html .= '<div class="knwinner"><div class="js_div_particName">BYE</div></div>';
                               
                            }
                        
                              
                        $html .=  '</div>';
                        if(count($kvalues["match_id"]) && $kvalues["match_id"]){
                            $intZ=0;
                            foreach ($kvalues["match_id"] as $kmid) {
                                $match = new classJsportMatch($kmid, false);
                                $html .= '<div class="knockscore">';
                                if(isset($match->object->id) && $kvalues["home"] != -1 && $kvalues["away"] != -1){
                                   $html .=  classJsportLink::match(($kvalues["score1"][$intZ]!=''?$kvalues["score1"][$intZ]:'&nbsp;'), $kmid, false, '');

                                }else{
                                    $html .= ($kvalues["score1"][$intZ]!=''?$kvalues["score1"][$intZ]:'&nbsp;');
                                }
                                $html .= '</div>';
                                
                            
                                $intZ++;
                            }
                        }    
                        $html .=  '</div></div>';
                        $html .= '<div class="player knockbot ml9">'
                                . '<div class="kntmprow">'
                                . '<div class="knockplName">';
                        
                            if($kvalues["away"] > 0){
                                
                                $html .= '<div class="knwinner">';
                                if(is_object($partic_away)){
                                   $html .= $partic_away->getEmblem();
                                   $html .= jsHelper::nameHTML($partic_away->getName(true)); 
                                }else{
                                   $html .= '<div class="js_div_particName"></div>'; 
                                }
                                
                                $html .= '</div>';
                                
                            }elseif($kvalues["away"] == -1){
                                $html .= '<div class="knwinner"><div class="js_div_particName">BYE</div></div>';
                                
                            }
                        
                                
                        $html .= '</div>';
                        if(count($kvalues["match_id"]) && $kvalues["match_id"]){
                            $intZ=0;
                            foreach ($kvalues["match_id"] as $kmid) {
                                
                                $match = new classJsportMatch($kmid, false);
                                $html .= '<div class="knockscore">';
                                //var_dump($match);
                                if(isset($match->object->id) && $kvalues["home"] != -1 && $kvalues["away"] != -1){
                                   $html .=  classJsportLink::match(($kvalues["score2"][$intZ]!=''?$kvalues["score2"][$intZ]:'&nbsp;'), $kmid, false, '');

                                }else{
                                    $html .= ($kvalues["score2"][$intZ]!=''?$kvalues["score2"][$intZ]:'&nbsp;');
                                }
                                $html .= '</div>';
                                $intZ++;
                            }
                        }  
                        $html .= '</div></div>';
                        $html .= '</td>';
                    }
                }

                $html .= '</tr>';
            }
           
            $html .= '</table>';

        $html .= '</div>';
        $html .= '</div>';
        return $html;
        
    }
}
