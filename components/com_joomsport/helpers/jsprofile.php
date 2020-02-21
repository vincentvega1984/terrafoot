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
// no direct access
defined('_JEXEC') or die;

class JHtmlJsprofile
{
    public static function ProfileLink($link, $player_id)
    {
        $Itemid = JRequest::getInt('Itemid');
        $db = JFactory::getDBO();
        $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='display_profile'";
        $db->setQuery($query);
        $profile_usr = $db->loadResult();

        switch ($profile_usr) {
            case 'cb':
                    $user_id = null;
                    $query = "SHOW TABLES LIKE '%__comprofiler'";
                    $db->setQuery($query);
                    $like = $db->loadResult();
                    if ($like) {
                        $query = 'SELECT p.usr_id FROM #__bl_players as p'
                        .' JOIN #__comprofiler as u ON p.usr_id = u.user_id'
                        .' WHERE p.id='.$player_id;
                        $db->setQuery($query);
                        $user_id = $db->loadResult();
                    }
                    if ($user_id) {
                        return JRoute::_('index.php?option=com_comprofiler&task=userprofile&user='.$user_id.'&Itemid='.$Itemid);
                    } else {
                        return $link;
                    }

                    break;
            case 'jsocial':
                    $user_id = null;
                    $query = "SHOW TABLES LIKE '%__community_users'";
                    $like = $db->loadResult();
                    if ($like) {
                        $query = 'SELECT p.usr_id FROM #__bl_players as p'
                                .' JOIN #__community_users as u ON p.usr_id = u.userid'
                                .' WHERE p.id='.$player_id;
                        $db->setQuery($query);
                        $user_id = $db->loadResult();
                    }
                    if ($user_id) {
                        return JRoute::_('index.php?option=com_community&view=profile&userid='.$user_id.'&Itemid='.$Itemid);
                    } else {
                        return $link;
                    }

                    break;
            default:
                return $link;
                break;

        }
    }
}
