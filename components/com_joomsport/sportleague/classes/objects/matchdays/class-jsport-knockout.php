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
class classJsportKnockout
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
        $k_format = $this->object->k_format;
        $t_single = $this->single;
        $s_id = $this->object->s_id;

        $participantObj = new classJsportParticipant($s_id);

        $stages = array(
            2 => 2,
            4 => 3,
            8 => 4,
            16 => 5,
            32 => 6,
            64 => 7,
        );
        $first_match_offset = array(
            1 => 0,
            2 => 1,
            3 => 3,
            4 => 7,
            5 => 15,
            6 => 31,
            7 => 63,
            8 => 127,
        );
        $matrix_row = array_fill(1, $stages[$k_format] - 1, '');
        $matrix = array_fill(0, $k_format, $matrix_row);

        $border = 64;
        foreach ($match as $m) {
            if ($m->team1_id > 0) {
                $participant = $participantObj->getParticipiantObj($m->team1_id);
                $m->home = $participant->getName(true);
                $m->partEmblemH = $participant->getEmblem();
            } else {
                $m->home = '';
                $m->partEmblemH = jsHelperImages::getEmblem('', 0, 'emblInline', 20);
            }
            if ($m->team2_id > 0) {
                $participant = $participantObj->getParticipiantObj($m->team2_id);
                $m->away = $participant->getName(true);
                $m->partEmblemA = $participant->getEmblem();
            } else {
                $m->away = '';
                $m->partEmblemA = jsHelperImages::getEmblem('', 0, 'emblInline', 20);
            }

            //$first_index = $first_match_offset[$m->k_stage] + $m->k_ordering*pow(2,$m->k_stage+1);
            //$second_index = $first_index + $first_match_offset[$m->k_stage+1] + 1;
            //$middle_index = $first_index + ($first_match_offset[$m->k_stage+1]+1)/2;
            $first_index = pow(2, $m->k_stage - 1) + ($m->k_ordering) * pow(2, $m->k_stage) - 1;

            $second_index = $first_index + 1;

            $class = 'even';
            if (($m->team2_id == -1 && $m->team1_id != -1) || ($m->m_played && (($m->score1 > $m->score2) || (($m->score1 == $m->score2) && ($m->aet1 > $m->aet2)) || (($m->score1 == $m->score2) && ($m->aet1 == $m->aet2) && ($m->p_winner == $m->hm_id))))) {
                $class = 'first';
            }
            $class_hover = '';
            if ($m->team1_id > 0) {
                $class_hover = ' knockHover'.$m->team1_id;
            }

            $html_emblems = '<div class="knockembl">'.$m->partEmblemH.'</div>';
            $html_partic = '<div class="knockplName'.$class_hover.'">'.($m->team1_id != -1 ? ($m->home) : (classJsportLanguage::get('BLBE_BYE'))).'</div>';
            $html = '<div class="player '.($m->k_stage > 1 ? ' ml9' : '').'"><div class="kntmprow">'.$html_emblems.$html_partic.'<div class="knockscore">'.((isset($m->score1) && $m->m_played) ? $m->score1.($m->is_extra ? " (<abbr title='".classJsportLanguage::get('BLFA_TT_AET')."'>".$m->aet1.'</abbr>)' : '') : '').'</div></div></div>';
            if (isset($m->id)) {
                $match_link = classJsportLink::match('&nbsp;', (isset($m->id) ? ($m->id) : ''), false, 'go2');
                $html .= '<div class="go2div">'.$match_link.'</div>';
            }
            $matrix[$first_index][$m->k_stage] = array('class' => $class.' knocktop', 'html' => $html);

           // $matrix[$middle_index][$m->k_stage] = array('class'=>'middle', 'html' => $html);

            if (($m->m_played || ($m->team1_id == -1 && $m->team2_id != -1))  && $class == 'even') {
                $class = 'first';
            } elseif (($m->m_played || ($m->team1_id == -1 && $m->team2_id != -1))  && $class == 'first') {
                $class = 'even';
            }
            $class_hover = '';
            if ($m->team2_id > 0) {
                $class_hover = ' knockHover'.$m->team2_id;
            }
            $html_emblems = '<div class="knockembl">'.$m->partEmblemA.'</div>';
            $html_partic = '<div class="knockplName'.$class_hover.'">'.($m->team2_id != -1 ? ($m->away) : (classJsportLanguage::get('BLBE_BYE'))).'</div>';

            $html = '<div class="player '.($m->k_stage > 1 ? ' ml9' : '').'"><div class="kntmprow">'.$html_emblems.$html_partic.'<div class="knockscore">'.((isset($m->score2) && $m->m_played) ? $m->score2.($m->is_extra ? " (<abbr title='".classJsportLanguage::get('BLFA_TT_AET')."'>".$m->aet2.'</abbr>)' : '') : '').'</div></div></div>';
            $matrix[$second_index][$m->k_stage] = array('class' => $class.' knockbot', 'html' => $html);
        }
        //final
        /*if ($m->m_played) {            
            $first_index = $first_match_offset[$m->k_stage+1] + $m->k_ordering*pow(2,$m->k_stage+1+1);
            if (($m->team2_id == -1 && $m->team1_id != -1) || ($m->m_played && (($m->score1 > $m->score2) || (($m->score1 == $m->score2) && ($m->aet1 > $m->aet2)) || (($m->score1 == $m->score2) && ($m->aet1 == $m->aet2) && ($m->p_winner == $m->hm_id))))) {
                $winner = $m->home;
                $winner_id = $m->hm_id;
            } else {
                $winner = $m->away;
                $winner_id = $m->aw_id;
            }
               
            $html = '<div class="player ml9">'.$winner.'</div>'; 
            $matrix[$first_index][$m->k_stage+1] = array('class'=> "first", 'html' => $html);
        }*/

        $return = '<div class="main drawBracket"><div class="drawBracketContainer"><div class="table-responsive">';
        $intRow = 1;

        $return .= '<table class="table"><tbody>';

        $border = array(
                array(),
                array(1),
                array(1, 2),
                array(2),
                array(2, 3),
                array(1, 2, 3),
                array(1, 3),
                array(3),
                array(3, 4),
                array(1, 3, 4),
                array(1, 2, 3, 4),
                array(2, 3, 4),
                array(2, 4),
                array(1, 2, 4),
                array(1, 4),
                array(4),
                array(4, 5),
                array(1, 4, 5),
                array(1, 2, 4, 5),
                array(2, 4, 5),
                array(2, 3, 4, 5),
                array(1, 2, 3, 4, 5),
                array(1, 3, 4, 5),
                array(3, 4, 5),
                array(3, 5),
                array(1, 3, 5),
                array(1, 2, 3, 5),
                array(2, 3, 5),
                array(2, 5),
                array(1, 2, 5),
                array(1, 5),
                array(5),

            );
        $count = round(count($matrix) / 2);
        $border = array_slice($border, 0, $count);
        $border = array_merge($border, array_reverse($border));
        foreach ($matrix as $row) {
            $intCell = 1;

            $return .= '<tr>';
            foreach ($row as $cell) {
                $addclass = '';
                if (in_array($intCell, $border[$intRow - 1])) {
                    $addclass = ' borderKnRight';
                }
                if (is_array($cell)) {
                    $return .= '<td class="'.$cell['class'].$addclass.' jsNoWrap">'.$cell['html'].'</td>';
                } else {
                    $return .= '<td class="'.$addclass.'"></td>';
                }
                ++$intCell;
            }
            $return .= '</tr>';
            ++$intRow;
        }

        $return .= '</tbody></table></div></div></div>';

        return $return;
    }
}
