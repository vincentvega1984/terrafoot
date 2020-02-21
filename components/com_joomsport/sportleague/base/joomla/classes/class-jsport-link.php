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
class classJsportLink
{
    public static function season($text, $season_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=table&sid='.$season_id.'&Itemid='.$Itemid);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function calendar($text, $season_id, $onlylink = false, $Itemid = '', $params = '')
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=calendar&sid='.$season_id.$params.'&Itemid='.$Itemid);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function tournament($text, $tournament_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return '<a href="'.JRoute::_('index.php?option=com_joomsport&task=tournlist&id='.$tournament_id.'&Itemid='.$Itemid).'">'.$text.'</a>';
    }
    public static function team($text, $team_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$team_id.'&sid='.intval($season_id).'&Itemid='.$Itemid);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function match($text, $match_id, $onlylink = false, $class = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=match&id='.$match_id.'&Itemid='.$Itemid);
        if ($onlylink) {
            return $link;
        }

        return '<a class="'.$class.'" href="'.$link.'">'.$text.'</a>';
    }
    public static function player($text, $player_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=player&id='.$player_id.'&sid='.intval($season_id).'&Itemid='.$Itemid);
        
        $db		= JFactory::getDBO();
        $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='display_profile'";
        $db->setQuery($query);
        $profile_usr = $db->loadResult();
        
        
        switch ($profile_usr){
            case 'cb':
                    $user_id = null;
                    $query = "SHOW TABLES LIKE '%__comprofiler'";
                    $db->setQuery($query);
                    $like = $db->loadResult();
                    if($like){
                        $query = "SELECT p.usr_id FROM #__bl_players as p"
                        ." JOIN #__comprofiler as u ON p.usr_id = u.user_id"
                        . " WHERE p.id=".$player_id;
                        $db->setQuery($query);
                        $user_id = $db->loadResult();
                    }
                    if($user_id){
                        $link = JRoute::_('index.php?option=com_comprofiler&task=userprofile&user='.$user_id."&Itemid=".$Itemid);
                    }
                    
                    break;
            case 'jsocial':
                    $user_id = null;
                    $query = "SHOW TABLES LIKE '%__community_users'";
                    $like = $db->loadResult();
                    if($like){
                        $query = "SELECT p.usr_id FROM #__bl_players as p"
                                ." JOIN #__community_users as u ON p.usr_id = u.userid"
                                . " WHERE p.id=".$player_id;
                        $db->setQuery($query);
                        $user_id = $db->loadResult();
                    }    
                    if($user_id){
                        $link = JRoute::_('index.php?option=com_community&view=profile&userid='.$user_id."&Itemid=".$Itemid);
                    }
                    
                    break;
            
                
        }
        
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function matchday($text, $matchday_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return '<a href="'.JRoute::_('index.php?option=com_joomsport&task=matchday&id='.$matchday_id.'&Itemid='.$Itemid).'">'.$text.'</a>';
    }
    public static function venue($text, $venue_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=venue&id='.$venue_id.'&Itemid='.$Itemid);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function club($text, $club_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return '<a href="'.JRoute::_('index.php?option=com_joomsport&task=club&id='.$club_id.'&Itemid='.$Itemid).'">'.$text.'</a>';
    }
    public static function playerlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return ''.JRoute::_('index.php?option=com_joomsport&task=playerlist&sid='.$season_id.$params.'&Itemid='.$Itemid);
    }
    public static function teamlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return ''.JRoute::_('index.php?option=com_joomsport&task=teamlist&sid='.$season_id.'&Itemid='.$Itemid).$params;
    }
    public static function seasonlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return JRoute::_('index.php?option=com_joomsport&task=seasonlist'.'&Itemid='.$Itemid).$params;
    }
    public static function joinseason($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return JRoute::_('index.php?option=com_joomsport&task=join_season&sid='.$season_id.'&Itemid='.$Itemid).$params;
    }
    public static function jointeam($season_id, $team_id, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        return JRoute::_('index.php?option=com_joomsport&task=jointeam&sid='.$season_id.'&tid='.$team_id.'&Itemid='.$Itemid).$params;
    }
    public static function person($text, $person_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = JRoute::_('index.php?option=com_joomsport&task=person&id='.$person_id.'&Itemid='.$Itemid);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function getItemId()
    {
        $app = JFactory::getApplication();
        $Itemid = $app->input->get('Itemid', 0, 'int');

        return $Itemid;
    }
}
