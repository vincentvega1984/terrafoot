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
class classJsportKnockoutDe
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
        $matchesDe = $model->getMatchesDE($this->single);
        //var_dump($matchesDe);
        $this->lists['knockout'] = $this->HorKnView($matches, $matchesDe);
    }

    public function getView()
    {
        return self::VIEW;
    }

    public function HorKnView($match, $matchDE)
    {
        $k_format = $this->object->k_format;
        $t_single = $this->single;
        $s_id = $this->object->s_id;

        $participantObj = new classJsportParticipant($s_id);

        $stages = array(
            2 => 2,
            4 => 4,
            8 => 6,
            16 => 8,
            32 => 10,
            64 => 12,
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
        $matrix = array_fill(0, $k_format * 2 + ($k_format / 2 * 2), $matrix_row);
        $border = 64;

        foreach ($match as $i => $m) {
            if ($m->k_stage > log($k_format, 2)) {
                break;
            }

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

            /*$first_index = $first_match_offset[$m->k_stage] + $m->k_ordering*pow(2,$m->k_stage+1);
            $second_index = $first_index + $first_match_offset[$m->k_stage+1] + 1;
            $middle_index = $first_index + ($first_match_offset[$m->k_stage+1]+1)/2;  */
            $first_index = pow(2, $m->k_stage - 1) + ($m->k_ordering) * pow(2, $m->k_stage) - 1;
            $second_index = $first_index + 1;

            $class = 'even';
            if (/*($m->team2_id == -1 && $m->team1_id != -1) ||*/ ($m->m_played && (($m->score1 > $m->score2) || (($m->score1 == $m->score2) && ($m->aet1 > $m->aet2)) || (($m->score1 == $m->score2) && ($m->aet1 == $m->aet2) && ($m->p_winner == $m->hm_id))))) {
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

            if (($m->m_played /*|| ($m->team1_id == -1 && $m->team2_id != -1)*/)  && $class == 'even') {
                $class = 'first';
            } elseif (($m->m_played /*|| ($m->team1_id == -1 && $m->team2_id != -1)*/)  && $class == 'first') {
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
        $last_middle = $second_index;
        $last_stage = $m->k_stage;

        $first_match_offset_de = array(
            1 => 1,
            2 => 0,
            3 => 1,
            4 => 0,
            5 => 2,
            6 => -3,
            7 => 1,
            8 => -7,
            9 => 1,
            10 => -15,
        );

        $match_size = array(
            1 => 2,
            2 => 2,
            3 => 4,
            4 => 4,
            5 => 8,
            6 => 8,
            7 => 16,
            8 => 16,
            9 => 32,
            10 => 32,
        );
        $stage = 0;
        foreach ($matchDE as $m) {
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

            $stage = $m->k_stage;

            switch ($m->k_stage) {
                case 1:
                        $step = 1;
                        $offset = 1;
                    break;
                case 2:
                        $step = 1;
                        $offset = 0;
                    break;
                case 3:
                        $step = 3;
                        $offset = 1;
                    break;
                case 4:
                        $step = 3;
                        $offset = 0;
                    break;
                case 5:
                        $step = 7;
                        $offset = 2;
                    break;
                case 6:
                        $step = 7;
                        $offset = 1;
                    break;
                case 7:
                        $step = 15;
                        $offset = 5;
                    break;
                case 8:
                        $step = 15;
                        $offset = 4;
                    break;
                case 9:
                        $step = 31;
                        $offset = 13;
                    break;

                default:
                        $step = 31;
                        $offset = 12;
            }

            $first_index = $k_format + 1 + $m->k_ordering * ($step + 1) + $offset;
            $second_index = $first_index + 1;
            //$first_index = $k_format + 1 + $first_match_offset_de[$m->k_stage] + $m->k_ordering*($stage+$kr) +($m->k_ordering > 0 && $m->k_stage>2?1*$m->k_ordering*($m->k_stage>3?($m->k_stage - 3 - $kf):1):0);
            //$second_index = $first_index + 1;
            //$middle_index = $first_index + $match_size[$m->k_stage]/2;
            //$first_index = pow(2,$m->k_stage-1) + ($m->k_ordering)*pow(2,$m->k_stage)-1 + $first_match_offset_de[$m->k_stage];

            //$second_index = $first_index + + $match_size[$m->k_stage]/2;

            $class = 'even';
            if (/*($m->team2_id == -1 && $m->team1_id != -1) ||*/ ($m->m_played && (($m->score1 > $m->score2) || (($m->score1 == $m->score2) && ($m->aet1 > $m->aet2)) || (($m->score1 == $m->score2) && ($m->aet1 == $m->aet2) && ($m->p_winner == $m->hm_id))))) {
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

            $class = 'even';
            if (/*($m->team2_id == -1 && $m->team1_id != -1) ||*/ ($m->m_played && (($m->score1 < $m->score2) || (($m->score1 == $m->score2) && ($m->aet1 < $m->aet2)) || (($m->score1 == $m->score2) && ($m->aet1 == $m->aet2) && ($m->p_winner == $m->aw_id))))) {
                $class = 'first';
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
        //$last_middle_de = $middle_index;
        $last_stage_de = $m->k_stage;

        //final
        $m = $match[$i];
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

        $first_index = round($last_middle - 2 + $second_index) / 2;
        $second_index = $first_index + 1;
        //$middle_index = $first_index + ($second_index - $first_index)/2;

        ++$last_stage_de;
        ++$stage;

        $class = 'even';
        if (/*($m->team2_id == -1 && $m->team1_id != -1) ||*/ ($m->m_played && (($m->score1 > $m->score2) || (($m->score1 == $m->score2) && ($m->aet1 > $m->aet2)) || (($m->score1 == $m->score2) && ($m->aet1 == $m->aet2) && ($m->p_winner == $m->hm_id))))) {
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
        $matrix[$first_index][$last_stage_de] = array('class' => $class.' knocktop', 'html' => $html);

        for ($l = $last_stage; $l < $last_stage_de; ++$l) {

           // $matrix[$first_index][$l] = array('class'=> '', 'html' => '<div class="border">&nbsp;</div>');
        }
        //$match_link = classJsportLink::match('&nbsp;', (isset($m->id)?($m->id):''));

        if (($m->m_played /*|| ($m->team1_id == -1 && $m->team2_id != -1)*/) && $class == 'even') {
            $class = 'first';
        } else {
            $class = 'even';
        }
        $class_hover = '';
        if ($m->team2_id > 0) {
            $class_hover = ' knockHover'.$m->team2_id;
        }
        $html_emblems = '<div class="knockembl">'.$m->partEmblemA.'</div>';
        $html_partic = '<div class="knockplName'.$class_hover.'">'.($m->team2_id != -1 ? ($m->away) : (classJsportLanguage::get('BLBE_BYE'))).'</div>';

        $html = '<div class="player '.($m->k_stage > 1 ? ' ml9' : '').'"><div class="kntmprow">'.$html_emblems.$html_partic.'<div class="knockscore">'.((isset($m->score2) && $m->m_played) ? $m->score2.($m->is_extra ? " (<abbr title='".classJsportLanguage::get('BLFA_TT_AET')."'>".$m->aet2.'</abbr>)' : '') : '').'</div></div></div>';
        $matrix[$second_index][$last_stage_de] = array('class' => $class.' knockbot', 'html' => $html);

        $return = '<div class="main drawBracket"><div class="drawBracketContainer"><div class="table-responsive">';

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
        $count = round(($k_format) / 2);
        $border = array_slice($border, 0, $count);
        $border = array_merge($border, array_reverse($border));

        $max_row = $k_format + 2 + $k_format / 2;

        $border_de = array();
        $index_de = $k_format + 1;
        $border_de[0] = array();
        $border_de[1] = array();
        /*for($intC = 2; $intC < round($count)+2; $intC++){
            $border_de[$intC] = array();
            for($intD = 0; $intD < round($count/4)+2; $intD++){
                $brow = null;
                if($intD % 2 != 0 && $intC % 2 == 0){
                    $brow = $intD;
                }
                if($brow){
                    array_push($border_de[$intC], $brow);
                }
            }
        }*/
        $border_de = array(
            array(),
            array(),
            array(1, 2, 3, 4),
            array(2, 4, 5, 6),
            array(1, 4, 6),
            array(4, 6),
            array(1, 2, 3, 6, 7, 8),
            array(2, 6, 8),
            array(1, 6, 8),
            array(6, 8),
            array(1, 2, 3, 4, 6, 8),
            array(2, 4, 5, 8),
            array(1, 4, 8),
            array(4, 8),
            array(1, 2, 3, 8, 9),
            array(2, 8),
            array(1, 8),
            array(8),
            array(1, 2, 3, 4, 8),
            array(2, 4, 5, 6, 8),
            array(1, 4, 6, 8),
            array(4, 6, 8),
            array(1, 2, 3, 6, 7),
            array(2, 6),
            array(1, 6),
            array(6),
            array(1, 2, 3, 4, 6),
            array(2, 4, 5),
            array(1, 4),
            array(4),
            array(1, 2, 3),
            array(2),
            array(1),
            array(),
        );
        $offsetknde_cols = array(
            4 => 2,
            8 => 4,
            16 => 6,
            32 => 8,
            64 => 10,
        );

        for ($intZ = 0;$intZ < count($border_de);++$intZ) {
            for ($intX = 0;$intX < count($border_de[$intZ]);++$intX) {

                //echo $border_de[$intZ][$intX] .">=". $offsetknde_cols[$k_format]."<br/>";
                if ($border_de[$intZ][$intX] >= $offsetknde_cols[$k_format]) {
                    $border_de[$intZ][$intX] = 0;
                }
            }
        }

        $offsetknde = array(
            2 => 2,
            4 => 4,
            8 => 6,
            16 => 8,
            32 => 10,
            64 => 12,
        );
        $offsetknde_overall_columns = array(
            4 => 3,
            8 => 3,
            16 => 4,
            32 => 5,
            64 => 6,
        );
        /*$offsetknde_overall_columns = array(
            4 => round($k_format/4),
            8 => round($k_format/4),
            16 => round($k_format/4),
            32 => round($k_format/4)-1,
            64 => round($k_format/4)-1
        );*/
        $max_de = $offsetknde[$k_format / 2];
        $line_end = $offsetknde_overall_columns[$k_format];

        for ($intC = 0; $intC < $line_end - 1; ++$intC) {
            array_push($border_de[$intC], $max_de);
        }
        for ($intC = round($k_format / 2); $intC < round($k_format); ++$intC) {
            array_push($border[$intC], $max_de);
        }
        //for($intC = $offsetknde_overall_columns[$k_format]; $intC < $max_de; $intC++){
           // array_push($border[$k_format/2], $intC);
            //borderTop
        //}

        //var_dump($border_de);

        $border = array_merge($border, $border_de);

        $intRow = 1;
        foreach ($matrix as $row) {
            if ($intRow <= $max_row) {
                $intCell = 1;
                $return .= '<tr>';
                foreach ($row as $cell) {
                    $addclass = '';
                    if (isset($border[$intRow - 1]) && in_array($intCell, $border[$intRow - 1])) {
                        $addclass = ' borderKnRight';
                    }
                    if (($intRow - 1 == $k_format / 2) && ($offsetknde_overall_columns[$k_format] < $intCell && $intCell <= $max_de)) {
                        $addclass .= ' borderKnTop';
                    }

                    if (is_array($cell)) {
                        $return .= '<td class="'.$cell['class'].$addclass.' jsNoWrap">'.$cell['html'].'</td>';
                    } else {
                        $return .= '<td class="'.$addclass.'">&nbsp;</td>';
                    }
                    ++$intCell;
                }

                $return .= '</tr>';
                ++$intRow;
            }
        }

        $return .= '</tbody></table></div></div></div>';

        return $return;
    }
}
