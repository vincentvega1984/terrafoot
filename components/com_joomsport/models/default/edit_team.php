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
// No direct access.
defined('_JEXEC') or die;

require dirname(__FILE__).'/../models.php';
require_once realpath(dirname(__FILE__).'/../../').'/includes/utils.php';

class edit_teamJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $season_id = null;
    public $id = null;
    /*moder rights  1 - season admin, 2 - team moderator, 3 - registered player*/
    public $acl = null;

    public function __construct($acl)
    {
        parent::__construct();

        $this->acl = $acl;
        $this->_lists['jscurtab'] = JRequest::getVar('jscurtab', 'etab_team', '', 'string');
        if ($this->acl == 1) {
            $cid = JRequest::getVar('cid', array(0), '', 'array');
            JArrayHelper::toInteger($cid, array(0));
            if ($cid[0]) {
                $is_id = $cid[0];
                $this->id = $is_id;
            }
            $s_id = JRequest::getVar('sid', 0, '', 'int');
            $this->season_id = $s_id;
        } else {
            $this->id = JRequest::getVar('tid', 0, '', 'int');
        }
    }

    public function getData()
    {
        $this->getTeamReg();
        // print_r($this->_lists['team_reg']);
        $this->_params = $this->JS_PageTitle(JText::_('BLFA_TEAM_EDIT'));

        $this->_lists['post_max_size'] = $this->getValSettingsServ('post_max_size');

        if ($this->acl == 1) {
            //----checking for rights----//

            if ($this->id) {
                $query = 'SELECT COUNT(*) FROM #__bl_teams as t, #__bl_seasons as s, #__bl_season_teams as st, #__bl_tournament as tr'
                        .' WHERE s.s_id=st.season_id AND st.team_id = t.id AND s.t_id = tr.id AND s.s_id='.$this->season_id.' AND t.id='.$this->id;
                $this->db->setQuery($query);

                if (!$this->db->loadResult()) {
                    JError::raiseError(403, JText::_('Access Forbidden'));

                    return;
                }
            }
        } else {
            $this->season_id = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
            $teamSeasons = $this->getTeamSeasons($this->id);
            if (!count($teamSeasons) && $this->season_id == 0) {
                $this->season_id = -1;
            } else {
                $isInSeason = false;
                foreach ($teamSeasons as $season) {
                    if ($this->season_id && $season->id == $this->season_id) {
                        $isInSeason = true;
                        break;
                    }
                }
                if (!$isInSeason && count($teamSeasons)) {
                    $this->season_id = $teamSeasons[0]->id;
                }
            }
            $query = 'SELECT COUNT(*) FROM #__bl_season_teams WHERE season_id = '.$this->season_id.' AND team_id = '.$this->id;
            $this->db->setQuery($query);
            $is_seas = $this->db->loadResult();

            if (!$is_seas) {
                $this->season_id = -1;
            }

            $this->_updateUserState(true);
            $this->getGlobFilters(true, true);
        }

        //---------------------------//
        $row = new JTableTeams($this->db);
        $row->load($this->id);
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        //extra fields
        $this->_lists['ext_fields'] = $this->getAddFields($row->id, 1, 'team');
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                ." FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 2 AND cat_id = '".$row->id."'";
        $this->db->setQuery($query);
        $this->_lists['photos'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if ($this->acl == 2) {
            $query = 'SELECT COUNT(*) FROM #__bl_season_teams as sp, #__bl_matchday as m WHERE m.s_id=sp.season_id AND sp.team_id = '.$row->id;
            $this->db->setQuery($query);
            $this->_lists['enmd'] = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $mdays = $this->getTeamMatchDays($row->id, $this->season_id);
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $this->_lists['moder_matchday'] = true;
            if (!count($mdays)) {
                $this->_lists['enmd'] = 0;
                $this->_lists['moder_matchday'] = (bool) $this->_getFriendlyMatchdaysCount();
            }

            $this->getInviteOptions();
            $this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
        }

        $this->getCountPlayersT($row->id);

        $this->getPlayersT($row->id, $this->season_id);
        $this->_data = $row;
        
        $this->_lists['enbl_club'] = $this->getJS_Config('enbl_club');
        if ($this->_lists['enbl_club']) {
            $is_club[] = JHTML::_('select.option',  0, JText::_('BLFA_SELCLUB'), 'id', 'c_name');
            $query = 'SELECT c.* FROM #__bl_club as c ORDER BY c_name';
            $this->db->setQuery($query);
            $club = $this->db->loadObjectList();
            if (count($club)) {
                $is_club = array_merge($is_club, $club);
            }
            $this->_lists['club'] = JHTML::_('select.genericlist',   $is_club, 'club_id', 'class="inputbox" size="1"', 'id', 'c_name', $row->club_id);
        }
        
        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
        $this->_lists['ext_fields'] = $this->getBEAdditfields('1', $row->id, $this->season_id);
    }
    public function getTeamReg()
    {
        $query = 'SELECT t.t_name FROM #__bl_teams as t GROUP BY t.t_name';
        $this->db->setQuery($query);
        $this->_lists['team_reg'] = $this->db->loadColumn();
    }
////////UPDATE
    public function getCountPlayersT($id)
    {
        $query = "SELECT COUNT(pt.player_join) as kol, s.s_name, s.s_id as s_id
					FROM #__bl_players_team as pt, #__bl_seasons as s
					WHERE pt.player_join='1' AND pt.season_id = s.s_id AND pt.team_id='".$id."' GROUP BY s.s_name";
        $this->db->setQuery($query);
        $this->_lists['waiting_players_count'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
    }

    public function getPlayersT($id, $s_id)
    {
        $pln = $this->getJS_Config('player_name');

        $query = "SELECT p.id FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_id=p.id AND t.team_id='".$id."' AND t.season_id='".$s_id."'";
        $this->db->setQuery($query);
        $plint = $this->db->loadColumn();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if ($this->acl == 1) {
            $query = 'SELECT '.($pln ? "IF(IFNULL(nick,'')<>'',nick,CONCAT(first_name,' ',last_name)) AS name" : "CONCAT(first_name,' ',last_name) as name").', id FROM #__bl_players '.(count($plint) ? ' WHERE id NOT IN ('.implode(',', $plint).')' : '').' ORDER BY '.($pln ? 'nick,first_name,last_name' : 'first_name,last_name');
        } else {
            if (!$this->_lists['esport_invite_player']) {
                $query = 'SELECT '.($pln ? "IF(IFNULL(nick,'')<>'',nick,CONCAT(first_name,' ',last_name)) AS name" : "CONCAT(first_name,' ',last_name) as name").', id FROM #__bl_players '.(count($plint) ? ' WHERE id NOT IN ('.implode(',', $plint).')' : '').' ORDER BY '.($pln ? 'nick,first_name,last_name' : 'first_name,last_name');
            } else {
                $query = 'SELECT '.($pln ? "IF(IFNULL(p.nick,'')<>'',p.nick,CONCAT(p.first_name,' ',p.last_name)) AS name" : "CONCAT(p.first_name,' ',p.last_name) as name").', p.id FROM #__bl_players as p, #__users as u WHERE u.id=p.usr_id '.(count($plint) ? ' AND p.id NOT IN ('.implode(',', $plint).')' : '').' ORDER BY '.($pln ? 'p.nick,p.first_name,p.last_name' : 'p.first_name,p.last_name');
            }
        }

        $this->db->setQuery($query);
        $is_pl = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $playerz[] = JHTML::_('select.option',  0, JText::_('BLFA_SELPLAYER'), 'id', 'name');
        if (count($is_pl)) {
            $playerz = array_merge($playerz, $is_pl);
        }

        $this->_lists['player'] = JHTML::_('select.genericlist',   $playerz, 'playerz_id', 'class="selectpicker" size="1" id="playerz" ', 'id', 'name', 0);
        if ($this->acl == 1) {
            $query = 'SELECT p.id,'.($pln ? "IF(IFNULL(p.nick,'')<>'',p.nick,CONCAT(p.first_name,' ',p.last_name)) AS name" : "CONCAT(p.first_name,' ',p.last_name) as name")." FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_id=p.id AND t.team_id='".$id."' AND t.season_id=".$s_id.' ORDER BY '.($pln ? 'p.nick,p.first_name,p.last_name' : 'p.first_name,p.last_name');
        } else {
            $query = 'SELECT p.id,'.($pln ? "IF(IFNULL(p.nick,'')<>'',p.nick,CONCAT(p.first_name,' ',p.last_name)) AS name" : "CONCAT(p.first_name,' ',p.last_name) as name").",t.confirmed FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_join='0' AND t.player_id=p.id AND t.team_id='".$id."' AND t.season_id=".$s_id.' ORDER BY '.($pln ? 'p.nick,p.first_name,p.last_name' : 'p.first_name,p.last_name');
        }
        $this->db->setQuery($query);
        $this->_lists['team_players'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
///UOPDATE		
        if ($this->acl == 2) {
            $query = 'SELECT p.id,'.($pln ? "IF(IFNULL(p.nick,'')<>'',p.nick,CONCAT(p.first_name,' ',p.last_name)) AS name" : "CONCAT(p.first_name,' ',p.last_name) as name").",t.confirmed FROM #__bl_players as p, #__bl_players_team as t WHERE t.player_join='1' AND t.player_id=p.id AND t.team_id='".$id."' AND t.season_id=".$s_id.' ORDER BY '.($pln ? 'p.nick,p.first_name,p.last_name' : 'p.first_name,p.last_name');
            $this->db->setQuery($query);
            $this->_lists['waiting_players'] = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }

        if (!intval($id)) {
            $this->_lists['team_players'] = array();
        }
    }

    public function getInviteOptions()
    {
        $this->_lists['esport_invite_player'] = $this->getJS_Config('esport_invite_player');
        //$this->_lists['esport_invite_confirm'] = $this->getJS_Config('esport_invite_confirm');
        $this->_lists['esport_invite_unregister'] = $this->getJS_Config('esport_invite_unregister');
        $this->_lists['esport_join_team'] = $this->getJS_Config('esport_join_team');

        $arr = array();
        $arr[] = JHTML::_('select.option',  0, JText::_('BLFA_NOACTION'), 'id', 'name');
        $arr[] = JHTML::_('select.option',  1, JText::_('BLFA_PLAPPROVE'), 'id', 'name');
        $arr[] = JHTML::_('select.option',  2, JText::_('BLFA_PLREJECT'), 'id', 'name');
        $this->_lists['arr_action'] = $arr;
    }

    public function InvitePlayer($player_id, $teamname, $seasid, $gen)
    {
        $config = JFactory::getConfig();
        $fromname = $config->get('fromname');
        $mailfrom = $config->get('mailfrom');
        $sitename = $config->get('sitename');
        if ($player_id) {
            $query = 'SELECT u.email FROM #__users as u, #__bl_players as p WHERE p.usr_id = u.id AND p.id = '.$player_id;
            $this->db->setQuery($query);
            $mail = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if ($mail) {
                $emailSubject = JText::_('BLFA_MAIL_INVPLTITLE');

                $link = JUri::base().'index.php?option=com_joomsport&task=confirm_invitings&key='.$gen;
                $link2 = JUri::base().'index.php?option=com_joomsport&task=reject_invitings&key='.$gen;
                $emailBody = JText::_('BLFA_MAIL_INVPLBODY');
                $emailBody = str_replace('{link}', "<a href='".$link."'>", $emailBody);
                $emailBody = str_replace('{link2}', "<a href='".$link2."'>", $emailBody);
                $emailBody = str_replace('{/link}', '</a>', $emailBody);
                $emailBody = str_replace('{team}', $teamname, $emailBody);
                $return = JFactory::getMailer()->sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);

                // Check for an error.
                if ($return !== true) {
                    $this->setError(JText::_('ERROR'));

                    return false;
                }
            }
        }
    }
    public function InviteUnreg($mail, $teamname, $seasid, $gen)
    {
        $config = JFactory::getConfig();
        $fromname = $config->get('fromname');
        $mailfrom = $config->get('mailfrom');
        $sitename = $config->get('sitename');

        if ($mail) {
            $emailSubject = JText::_('BLFA_MAIL_INVPLTITLE');

            $link = JUri::base().'index.php?option=com_joomsport&task=unreg_inviting&key='.$gen;
            $link2 = JUri::base().'index.php?option=com_joomsport&task=unreg_inviting_reject&key='.$gen;
            $emailBody = JText::_('BLFA_MAIL_INVPLBODY');
            $emailBody = str_replace('{link}', "<a href='".$link."'>", $emailBody);
            $emailBody = str_replace('{link2}', "<a href='".$link2."'>", $emailBody);
            $emailBody = str_replace('{/link}', '</a>', $emailBody);
            $emailBody = str_replace('{team}', $teamname, $emailBody);
            $return = JFactory::getMailer()->sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);

                // Check for an error.
                if ($return !== true) {
                    $this->setError(JText::_('ERROR'));

                    return false;
                }
        }
    }

    public function Pl_Approve($player_id, $team_name, $team_id, $s_id)
    {
        $config = JFactory::getConfig();
        $fromname = $config->get('fromname');
        $mailfrom = $config->get('mailfrom');
        $sitename = $config->get('sitename');
        if ($player_id && $team_id) {
            $query = "SELECT u.email FROM #__users as u, #__bl_players as p WHERE p.usr_id = u.id AND p.id = '".$player_id."'";
            $this->db->setQuery($query);
            $mail = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if ($mail) {
                $emailSubject = JText::_('BLFA_MAIL_APPROVEDPLTITLE');

                $emailBody = JText::_('BLFA_MAIL_APPROVEDPLBODY');
                $emailBody = str_replace('{team}', $team_name, $emailBody);
                $return = JFactory::getMailer()->sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);
                $query = "UPDATE #__bl_players_team SET player_join = '0', confirmed = '0' WHERE team_id = ".$team_id.' AND player_id = '.$player_id.'  AND season_id = '.$s_id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                // Check for an error.
                if ($return !== true) {
                    $this->setError(JText::_('ERROR'));

                    return false;
                }
                if($s_id){
                    //update player list
                    require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
                    classJsportPlugins::get('generatePlayerList', array('season_id' => $s_id));
                }
            }
        }
    }
    public function Pl_Reject($player_id, $team_name, $team_id, $s_id)
    {
        $config = JFactory::getConfig();
        $fromname = $config->get('fromname');
        $mailfrom = $config->get('mailfrom');
        $sitename = $config->get('sitename');
        if ($player_id && $team_id) {
            $query = "SELECT u.email FROM #__users as u, #__bl_players as p WHERE p.usr_id = u.id AND p.id = '".$player_id."'";
            $this->db->setQuery($query);
            $mail = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if ($mail) {
                $emailSubject = JText::_('BLFA_MAIL_REJECTPLTITLE');

                $emailBody = JText::_('BLFA_MAIL_REJECTPLBODY');
                $emailBody = str_replace('{team}', $team_name, $emailBody);
                $return = JFactory::getMailer()->sendMail($mailfrom, $fromname, $mail, $emailSubject, $emailBody, 1);

                $query = 'DELETE FROM #__bl_players_team WHERE team_id = '.$team_id.' AND player_id = '.$player_id.'  AND season_id = '.$s_id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                // Check for an error.
                if ($return !== true) {
                    $this->setError(JText::_('ERROR'));

                    return false;
                }
            }
        }
    }

    public function SaveAdmTeam()
    {
        $post = JRequest::get('post');
        if ($this->acl == 1) {
            if (!$this->getJS_Config('jssa_editteam')) {
                JError::raiseError(500, '');
            }
            $s_id = JRequest::getVar('sid', 0, '', 'int');
        } else {
            $post['id'] = JRequest::getVar('tid', 0, 'post', 'int');
        }
        $msg = '';

        $post['t_descr'] = JRequest::getVar('t_descr', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['def_img'] = JRequest::getVar('ph_default', 0, 'post', 'int');

        $row = new JTableTeams($this->db);
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $istlogo = JRequest::getVar('istlogo', 0, 'post', 'int');
        if (!$istlogo) {
            $post['t_emblem'] = '';
        }

        if (isset($_FILES['t_logo']['name']) && $_FILES['t_logo']['tmp_name'] != '' && isset($_FILES['t_logo']['tmp_name'])) {
            $bl_filename = strtolower($_FILES['t_logo']['name']);
            $ext = pathinfo($_FILES['t_logo']['name']);
            $bl_filename = 'bl'.time().rand(0, 3000).'.'.$ext['extension'];
            $bl_filename = str_replace(' ', '', $bl_filename);
            if ($this->uploadFile($_FILES['t_logo']['tmp_name'], $bl_filename)) {
                $post['t_emblem'] = $bl_filename;
            }
        }

        if (!$row->bind($post)) {
            JError::raiseError(500, $row->getError());
        }

        $pzt = 1;
        if (!$row->id) {
            if ($this->acl == 2) {
                return JError::raiseError(500, $error);
            } else {
                $pzt = 0;
            }
        }

        if (!$row->check()) {
            JError::raiseError(500, $row->getError());
        }
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
        }
        $row->checkin();
        if (!$pzt) {
            $query = 'INSERT INTO #__bl_season_teams(season_id,team_id) VALUES('.$s_id.','.$row->id.')';
            $this->db->setQuery($query);
            $this->db->query();
        }

        $query = 'DELETE FROM #__bl_assign_photos WHERE cat_type = 2 AND cat_id = '.$row->id;
        $this->db->setQuery($query);
        $this->db->query();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if (isset($_POST['photos_id']) && count($_POST['photos_id'])) {
            for ($i = 0; $i < count($_POST['photos_id']); ++$i) {
                $photo_id = intval($_POST['photos_id'][$i]);
                $photo_name = addslashes(strval($_POST['ph_names'][$i]));
                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$photo_id.','.$row->id.',2)';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                $query = "UPDATE #__bl_photos SET ph_name = '".($photo_name)."' WHERE id = ".$photo_id;
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }

        if (isset($_FILES['player_photo_1']['name']) && $_FILES['player_photo_1']['tmp_name'] != '' && isset($_FILES['player_photo_1']['tmp_name'])) {
            $bl_filename = strtolower($_FILES['player_photo_1']['name']);
            $ext = pathinfo($_FILES['player_photo_1']['name']);
            $bl_filename = 'bl'.time().rand(0, 3000).'.'.$ext['extension'];
            $bl_filename = str_replace(' ', '', $bl_filename);
            if ($this->uploadFile($_FILES['player_photo_1']['tmp_name'], $bl_filename)) {
                $post1['ph_filename'] = $bl_filename;
                $img1 = new JTablePhotos($this->db);
                $img1->id = 0;
                if (!$img1->bind($post1)) {
                    JError::raiseError(500, $img1->getError());
                }
                if (!$img1->check()) {
                    JError::raiseError(500, $img1->getError());
                }
                if (!$img1->store()) {
                    JError::raiseError(500, $img1->getError());
                }
                $img1->checkin();

                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img1->id.','.$row->id.',2)';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        } else {
            if ($_FILES['player_photo_1']['error'] == 1) {
                if ($this->acl == 1) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=team_edit&controller=admin&cid[]='.$row->id.'&sid='.$this->season_id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                } elseif ($this->acl == 2) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=team_edit&controller=moder&tid='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                }
            }
        }
        if (isset($_FILES['player_photo_2']['name']) && $_FILES['player_photo_2']['tmp_name'] != ''  && isset($_FILES['player_photo_2']['tmp_name'])) {
            $bl_filename = strtolower($_FILES['player_photo_2']['name']);
            $ext = pathinfo($_FILES['player_photo_2']['name']);
            $bl_filename = 'bl'.time().rand(0, 3000).'.'.$ext['extension'];
            $bl_filename = str_replace(' ', '', $bl_filename);
            if ($this->uploadFile($_FILES['player_photo_2']['tmp_name'], $bl_filename)) {
                $post2['ph_filename'] = $bl_filename;
                $img2 = new JTablePhotos($this->db);
                $img2->id = 0;
                if (!$img2->bind($post2)) {
                    JError::raiseError(500, $img2->getError());
                }
                if (!$img2->check()) {
                    JError::raiseError(500, $img2->getError());
                }
                if (!$img2->store()) {
                    JError::raiseError(500, $img2->getError());
                }
                $img2->checkin();

                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img2->id.','.$row->id.',2)';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        } else {
            if ($_FILES['player_photo_2']['error'] == 1) {
                if ($this->acl == 1) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=team_edit&controller=admin&cid[]='.$row->id.'&sid='.$this->season_id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                } elseif ($this->acl == 2) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=team_edit&controller=moder&tid='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                }
            }
        }

        if ($this->acl == 2) {
            $seasf_id = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
            $query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$row->id.' ORDER BY s.s_id desc';
            $this->db->setQuery($query);
            $seass = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if (!$seasf_id) {
                $seasf_id = $seass[0]->id;
            }
            $s_id = $seasf_id;
        }

        //-------extra fields-----------//
        if (isset($_POST['extraf']) && count($_POST['extraf'])) {
            foreach ($_POST['extraf'] as $p => $dummy) {
                $query = "SELECT season_related FROM #__bl_extra_filds WHERE id='".intval($_POST['extra_id'][$p])."'";
                $this->db->setQuery($query);
                $seas_relat = $this->db->loadResult();

                $query = 'DELETE FROM #__bl_extra_values WHERE f_id = '.$_POST['extra_id'][$p].' AND uid = '.$row->id.' '.($seas_relat ? ' AND season_id='.intval($s_id) : '');
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                $fld = ($_POST['extra_ftype'][$p] == 2) ? 'fvalue_text' : 'fvalue';

                $inserted_seas = $seas_relat ? $s_id : 0;

                $query = 'INSERT INTO #__bl_extra_values(f_id,uid,`'.$fld.'`,season_id) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."',".$inserted_seas.')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }

        //-------Players----//
        if ($this->acl == 1) {
            if ($s_id && $row->id) {
                $query = 'DELETE FROM #__bl_players_team WHERE team_id='.$row->id.' AND season_id='.$s_id;
                $this->db->setQuery($query);
                $this->db->query();
                if (isset($_POST['teampl']) && count($_POST['teampl'])) {
                    for ($p = 0;$p < count($_POST['teampl']);++$p) {
                        $query = 'INSERT IGNORE INTO #__bl_players_team(team_id,player_id,season_id) VALUES('.$row->id.','.intval($_POST['teampl'][$p]).','.$s_id.')';
                        $this->db->setQuery($query);
                        $this->db->query();
                    }
                }
                //update player list
                require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
                classJsportPlugins::get('generatePlayerList', array('season_id' => $s_id));
            }

            $this->season_id = $s_id;
        } else {
            $inviteoradd = $this->getJS_Config('esport_invite_player');

            if ($seasf_id) {
                $plzold = array();

                if (isset($_POST['teampl']) && count($_POST['teampl'])) {
                    for ($p = 0;$p < count($_POST['teampl']);++$p) {
                        if (intval($_POST['teampl'][$p])) {
                            $query = 'SELECT player_id FROM #__bl_players_team WHERE player_id = '.intval($_POST['teampl'][$p])." AND team_id = {$row->id} AND season_id = {$seasf_id}";
                            $this->db->setQuery($query);
                            $plzold[] = intval($_POST['teampl'][$p]);
                            if (!$this->db->loadResult()) {
                                $query = 'INSERT IGNORE INTO #__bl_players_team(team_id,player_id,season_id,confirmed) VALUES('.$row->id.','.intval($_POST['teampl'][$p]).','.$seasf_id.','.($inviteoradd ? 1 : 0).')';
                                $this->db->setQuery($query);
                                $this->db->query();
                                $error = $this->db->getErrorMsg();
                                if ($error) {
                                    return JError::raiseError(500, $error);
                                }

                                //invite
                                if ($inviteoradd) {
                                    mt_srand((double) (microtime()));
                                    $gen = mt_rand().'prI'.microtime();
                                    $gen = str_replace(' ', '', $gen);
                                    $this->InvitePlayer(intval($_POST['teampl'][$p]), $row->t_name, $seasf_id, $gen);
                                    $query = "UPDATE #__bl_players_team SET invitekey='".$gen."' WHERE team_id = ".$row->id.' AND player_id = '.intval($_POST['teampl'][$p]).'  AND season_id = '.$seasf_id;
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                    $error = $this->db->getErrorMsg();
                                    if ($error) {
                                        return JError::raiseError(500, $error);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $query = 'DELETE FROM #__bl_players_team WHERE team_id='.$row->id.' AND season_id='.$seasf_id." AND player_join='0'";
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                }

                if (count($plzold)) {
                    $sql = (count($plzold) > 1) ? ('player_id NOT IN ('.implode(',', $plzold).')') : ('player_id != '.$plzold[0]);
                    $query = 'DELETE FROM #__bl_players_team WHERE '.$sql.' AND team_id='.$row->id.' AND season_id='.$seasf_id." AND player_join='0'";
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                }
                //update player list
                require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';
                classJsportPlugins::get('generatePlayerList', array('season_id' => $seasf_id));
            }
            //invite unregs
            if (isset($_POST['emlinv']) && count($_POST['emlinv']) && $row->id) {
                for ($p = 0;$p < count($_POST['emlinv']);++$p) {
                    mt_srand((double) (microtime()));
                    $gen = mt_rand().'prI'.microtime();
                    $gen = str_replace(' ', '', $gen);
                    $query = 'INSERT IGNORE INTO #__bl_players_team(team_id,player_id,season_id,confirmed,invitekey) VALUES('.$row->id.',0,'.$seasf_id.",'1','".$gen."')";
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    $this->InviteUnreg($_POST['emlinv'][$p], $row->t_name, $seasf_id, $gen);
                }
            }
            //action with players join team
            if (isset($_POST['appr_pl']) && count($_POST['appr_pl']) && $row->id) {
                for ($p = 0;$p < count($_POST['appr_pl']);++$p) {
                    $ids = $_POST['appr_pl'][$p];
                    switch ($_POST['action_'.$ids]) {
                        case 1: $this->Pl_Approve($ids, $row->t_name, $row->id, $seasf_id);
                        break;
                        case 2: $this->Pl_Reject($ids, $row->t_name, $row->id, $seasf_id);
                        break;
                    }
                    $query = 'INSERT IGNORE INTO #__bl_players_team(team_id,player_id,season_id,confirmed,invitekey) VALUES('.$row->id.',0,'.$seasf_id.",'1','".$gen."')";
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    $this->InviteUnreg($_POST['emlinv'][$p], $row->t_name, $seasf_id, $gen);
                }
            }
        }

        $this->id = $row->id;
    }
    public function delAdmTeam()
    {
        $cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));

        if (count($cid) && $this->getJS_Config('jssa_delteam')) {
            $cids = implode(',', $cid);
            /*$query = "DELETE FROM `#__bl_teams` WHERE id IN (".$cids.")";
            $this->db->setQuery($query);
            $this->db->query();*/

            $query = 'DELETE FROM `#__bl_season_teams` WHERE team_id IN ('.$cids.') AND season_id='.$this->season_id;
            $this->db->setQuery($query);
            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            
            /*$query = "SELECT s.s_id FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id=t.id AND t_single = 0";
            $this->db->setQuery($query);
            $sid = $this->db->loadColumn();
            if(count($sid)){
                $sids = implode(',',$sid);
                $query = "SELECT id FROM #__bl_matchday WHERE s_id IN (".$sids.")";
                $this->db->setQuery($query);
                $mdid = $this->db->loadColumn();
                
                if(count($mdid)){
                    $mdids = implode(',',$mdid);
                        $query = "DELETE FROM `#__bl_match` WHERE m_id IN (".$mdids.") AND (team1_id IN (".$cids.") OR team2_id IN (".$cids."))";
                        $this->db->setQuery($query);
                        $this->db->query();
                }
            }	*/

                $query = 'SELECT id FROM #__bl_matchday WHERE s_id = '.$this->season_id;
            $this->db->setQuery($query);
            $mdid = $this->db->loadColumn();

            if (count($mdid)) {
                $mdids = implode(',', $mdid);
                $query = 'DELETE FROM `#__bl_match` WHERE m_id IN ('.$mdids.') AND (team1_id IN ('.$cids.') OR team2_id IN ('.$cids.'))';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                $query = 'SELECT id FROM `#__bl_match` WHERE m_id IN ('.$mdids.') AND (team1_id IN ('.$cids.') OR team2_id IN ('.$cids.'))';
                $this->db->setQuery($query);
                $matchiz_id = $this->db->loadColumn();
                if (count($matchiz_id)) {
                    $query = 'DELETE FROM `#__bl_match_events` WHERE t_id IN ('.$cids.') AND match_id IN ('.(implode(',', $matchiz_id)).')';
                    $this->db->setQuery($query);
                    $this->db->query();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                }
            }
            if ($this->season_id > 0) {
                require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';

                //foreach ($sid as $recalcSeason) {
                    classJsportPlugins::get('generateTableStanding', array('season_id' => $this->season_id));

                    //update player list
                    classJsportPlugins::get('generatePlayerList', array('season_id' => $this->season_id));
                //}
            }
        }
    }

    public function getCustomField($field, $data = array())
    {
        return JS_Utils::instance($this->db)->getCustomField($field, $data);
    }

    protected function _updateUserState($updateRequest = false)
    {
        $this->mainframe->setUserState('com_joomsport.moderseason', $this->season_id);
        $updateRequest && JRequest::setVar('moderseason', $this->season_id);
    }

    protected function _getFriendlyMatchdaysCount()
    {
        $this->db->setQuery('
            SELECT COUNT(id)
            FROM #__bl_matchday
            WHERE s_id = -1
        ');

        return $this->db->loadResult();
    }
}
