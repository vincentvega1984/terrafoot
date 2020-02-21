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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
/**
 * HTML View class for the Registration component.
 *
 * @since 1.0
 */
class bleagueViewedit_player extends JViewLegacy
{
    public function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
        $pathway = $mainframe->getPathway();
        $document = JFactory::getDocument();
        $params = $mainframe->getParams();
        $editor = JFactory::getEditor();
        // Page Title
        $menus = &JSite::getMenu();
        $menu = $menus->getActive();
        $is_id = 0;

        $cid = JRequest::getVar('cid', array(0), '', 'array');

        $msg = JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW);
        JArrayHelper::toInteger($cid, array(0));
        if ($cid[0]) {
            $is_id = $cid[0];
        }

        $db = &JFactory::getDBO();

        //----checking for rights----//
        $s_id = JRequest::getVar('sid', 0, '', 'int');
        if ($is_id) {
            $query = 'SELECT COUNT(*) FROM #__esb_players as p, #__esb_players_team as bp, #__esb_teams as t, #__esb_seasons as s, #__esb_season_teams as st, #__esb_tournament as tr WHERE p.id=bp.player_id AND bp.team_id=t.id AND s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id='.$s_id.' AND p.team_id = t.id  AND p.id = '.$is_id;
            $db->setQuery($query);

            if (!$db->loadResult()) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        }

        //---------------------------//

        $row = new JTablePlayer($db);

        $row->load($is_id);

        $query = 'SELECT * FROM #__esb_teams ORDER BY t_name';

        $db->setQuery($query);

        $teams = $db->loadObjectList();

        $lists['teams'] = JHTML::_('select.genericlist',   $teams, 'team_id', 'class="inputbox" size="1"', 'id', 't_name', $row->team_id);

        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__esb_assign_photos as ap, #__esb_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '.$row->id.'';

        $db->setQuery($query);

        $lists['photos'] = $db->loadObjectList();

        $query = 'SELECT ef.*,ev.fvalue as fvalue FROM #__esb_extra_filds as ef LEFT JOIN #__esb_extra_values as ev ON ef.id=ev.f_id AND ev.uid='.intval($row->id)." WHERE  ef.published=1 AND ef.type='0' ORDER BY ef.ordering";
        $db->setQuery($query);

        $lists['ext_fields'] = $db->loadObjectList();

        $this->assignRef('params',        $params);

        $this->assignRef('lists',        $lists);
        $this->assignRef('rows', $row);
        $this->assignRef('s_id', $s_id);

        $this->assignRef('maz_filesize', $maz_filesize);

        $this->assignRef('msg', $msg);
        $this->assignRef('editor', $editor);
        parent::display($tpl);
    }
}
