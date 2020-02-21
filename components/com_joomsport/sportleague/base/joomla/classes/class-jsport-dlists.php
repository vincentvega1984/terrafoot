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
class classJsportDlists
{
    public static function getSeasonsPlayerList($season_id)
    {
        global $jsDatabase;
        $is_tourn = array();
		
        $query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
        $tourn = $jsDatabase->select($query);
        $javascript = " onchange='this.form.submit();'";
        $jqre = '<select name="sid" id="sid" class="styled jfsubmit" size="1" '.$javascript.'>';
        $jqre .= '<option value="0">'.JText::_('BLFA_ALL').'</option>';
        for($i=0;$i<count($tourn);$i++){
                $is_tourn2 = array();
                $query = "SELECT s.s_id as id,s.s_name as s_name"
                                ." FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id"
                                ." WHERE s.published = '1' AND t.id=".$tourn[$i]->id
                                ."  ORDER BY s.ordering";
                $rows = $jsDatabase->select($query);

                if(count($rows)){
                        $jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';
                        for($g=0;$g<count($rows);$g++){
                                $jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $season_id)?"selected":"").'>'.$rows[$g]->s_name.'</option>';
                        }
                        $jqre .= '</optgroup>';
                }
        }
        $jqre .= '</select>';
        $jqre .= '<input type="hidden" name="page" value="1" />';
        return $jqre;
    }
    public static function getSeasonsTeamList($season_id){
        global $jsDatabase;
        $is_tourn = array();
        $is_tourn[] = JHTML::_('select.option',0,  JText::_('BLFA_ALL'), 'id', 's_name' ); 
        $query = "SELECT * FROM #__bl_tournament WHERE published = '1' AND t_single='0' ORDER BY name";

        $tourn = $jsDatabase->select($query);

        $javascript = " onchange='this.form.submit();'";
        $jqre = '<select name="sid" id="sid" class="styled jfsubmit" size="1" '.$javascript.'>';
        $jqre .= '<option value=0"">'.JText::_('BLFA_ALL').'</option>';
        for($i=0;$i<count($tourn);$i++){
                $is_tourn2 = array();
                $query = "SELECT s.s_id as id,s.s_name as s_name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id WHERE s.published = '1' AND t.id=".$tourn[$i]->id."  ORDER BY s.ordering";
                $rows = $jsDatabase->select($query);

                if(count($rows)){
                        $jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';
                        for($g=0;$g<count($rows);$g++){
                                $jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $season_id)?"selected":"").'>'.$rows[$g]->s_name.'</option>';
                        }
                        $jqre .= '</optgroup>';
                }
        }
        $jqre .= '</select>';

        $jqre .= '<input type="hidden" name="page" value="1" />';
        return $jqre;
    }
    public static function getSeasonsTournList($tournament_id){
        global $jsDatabase;
        $is_tourn = array();
        $is_tourn[] = JHTML::_('select.option',0,  JText::_('BLFA_ALL'), 'id', 'name' ); 
        $javascript = 'onchange = "this.form.submit();"';

        $query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";

        $tourn = $jsDatabase->select($query);

        if(count($tourn)){
                $is_tourn = array_merge($is_tourn,$tourn);
        }
        $jqre = JHTML::_('select.genericlist',   $is_tourn, 'filtr_tourn', 'class="styled jfsubmit" size="1" '.$javascript, 'id', 'name', $tournament_id );
        $jqre .= '<input type="hidden" name="page" value="1" />';
        return $jqre;
    }
}
