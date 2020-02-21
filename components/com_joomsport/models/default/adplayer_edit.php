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

class adplayer_editJSModel extends JSPRO_Models
{
    public $_data = null;
    public $_lists = null;
    public $season_id = null;
    public $id = null;
    public $tid = null;
    public $is_first = null;
    /*moder rights  1 - season admin, 2 - team moderator, 3 - registered player*/
    public $acl = null;
    public function __construct($acl)
    {
        $this->acl = $acl;
        parent::__construct();
        $cid = JRequest::getVar('cid', array(0), '', 'array');

        JArrayHelper::toInteger($cid, array(0));
        if ($cid[0]) {
            $is_id = $cid[0];
            $this->id = $is_id;
        }
        if ($this->acl == 1) {
            $s_id = JRequest::getVar('sid', 0, '', 'int');
            $this->season_id = $s_id;
        } else {
            $this->season_id = JRequest::getVar('moderseason', 0, 'int');
            //$this->season_id	= $this->mainframe->getUserStateFromRequest( 'com_joomsport.moderseason', 'moderseason', 0, 'int' );
        }

        if ($this->acl == 2) {
            $this->tid = JRequest::getVar('tid', 0, '', 'int');
        }
    }
    public function admAccess()
    {
        $is_s = $this->getTournOpt($this->season_id);

        $this->_lists['jssa_editplayer'] = $this->getJS_Config('jssa_editplayer');
        $this->_lists['jssa_deleteplayers'] = $is_s->t_single ? $this->getJS_Config('jssa_deleteplayers_single') : $this->getJS_Config('jssa_deleteplayers');
    }
    public function getData()
    {
        $this->getPlayer();

        $this->_lists['post_max_size'] = $this->getValSettingsServ('post_max_size');

        if ($this->acl == 1) {
            $this->admAccess();
        } else {
            $this->_lists['teams_season'] = $this->teamsToModer();
            $all = array();
            if (count($this->_lists['teams_season']) && $this->id) {
                $query = 'SELECT team_id, season_id FROM #__bl_players_team WHERE team_id IN('.(implode(',', $this->_lists['teams_season'])).') AND player_id = '.$this->id.' AND season_id > 0';
                $this->db->setQuery($query);
                $all = $this->db->loadObjectList();

                if (!$this->season_id && isset($all[0]->season_id)) {
                    $this->season_id = $all[0]->season_id;
                }
            }

            $this->getGlobFilters(false, false, (count($all) ? $all : array(0)));
            /*$query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$this->tid." ORDER BY s.s_id desc";
            $this->db->setQuery($query);
            $seass = $this->db->loadObjectList();
            if(!$this->season_id) {$this->season_id = $seass[0]->id;};
            $isinseas = false;
            for($j=0;$j<count($seass);$j++){
                if($this->season_id == $seass[$j]->id){
                    $isinseas = true;
                }
            }
            if(!$isinseas && count($seass)){
                
                $this->season_id = $seass[0]->id;
            }
        }*/
        }
        $this->_params = $this->JS_PageTitle('');
        //----checking for rights----//
        if ($this->acl == 2) {
            $this->_lists['moder_addplayer'] = $this->getJS_Config('moder_addplayer');
            if (!$this->_lists['moder_addplayer']) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        }

        $row = new JTablePlayer($this->db);
        $row->load($this->id);
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->getPCountry($row->country_id);
        $this->getUsers($row->usr_id);
        //extra fields
        if ($this->acl == 1) {
            // $this->_lists['ext_fields'] = $this->getAddFields($row->id,0,"player",$this->season_id);
            $this->_lists['ext_fields'] = $this->getBEAdditfields('0', $row->id, $this->season_id);
//print_r($this->_lists['ext_fields']);
            //teams
            $tourn = $this->getTournOpt($this->season_id);
            if (!$tourn->t_single && !$this->id) {
                $query = 'SELECT * FROM #__bl_teams as t , #__bl_season_teams as st'
                        .' WHERE st.team_id = t.id AND st.season_id = '.$this->season_id
                        .' ORDER BY t.t_name';
                $this->db->setQuery($query);
                $team = $this->db->loadObjectList();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                $is_team[] = JHTML::_('select.option',  0, JText::_('BLFA_SELTEAM'), 'id', 't_name');
                if (count($team)) {
                    $is_team = array_merge($is_team, $team);
                }
                $this->_lists['teams_seas'] = JHTML::_('select.genericlist',   $is_team, 'teams_seas', 'class="selectpicker" size="1" id="teams_seas"', 'id', 't_name', 0);
            }
        } else {
            $this->_lists['ext_fields'] = $this->getBEAdditfields('0', $row->id, $this->season_id, true);
            $this->getCanMore($this->id);
        }
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                .' FROM #__bl_assign_photos as ap, #__bl_photos as p'
                ." WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '".$row->id."'";
        $this->db->setQuery($query);
        $this->_lists['photos'] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $this->_data = $row;
        $this->_lists['teams_season'] = $this->teamsToModer();
    //print_r($this->_lists["teams_season"]);
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, null, 0);
        //$this->_lists['ext_fields'] = $this->getBEAdditfields('0',$row->id,$this->season_id);
        $this->_lists['reg_lastname_rq'] = $this->getJS_Config('reg_lastname_rq');
        $this->_lists['reg_lastname'] = $this->getJS_Config('reg_lastname');
        //acl = 2
        $this->_lists['nick_reg_rq'] = $this->getJS_Config('nick_reg_rq');
        $this->_lists['nick_reg'] = $this->getJS_Config('nick_reg');
        $this->_lists['country_reg_rq'] = $this->getJS_Config('country_reg_rq');
        $this->_lists['country_reg'] = $this->getJS_Config('country_reg');
//print_r($this->_lists['country_reg_rq']);
    }

    public function getPlayer()
    {
        $query = 'SELECT p.first_name, p.last_name FROM #__bl_players as p';
        $this->db->setQuery($query);
        $player_reg = $this->db->loadRowList();
        $this->_lists['player_reg'] = $player_reg;
    }

    public function getCanMore($is_id)
    {
        $user = JFactory::getUser();
        $canmore = $is_id ? true : false;
        $query = 'SELECT COUNT(*) FROM #__bl_players WHERE created_by='.$user->id;
        $this->db->setQuery($query);
        $curcap = $this->db->loadResult();

        $teams_per_account = $this->getJS_Config('players_per_account');

        if ($curcap < $teams_per_account) {
            $canmore = true;
        }
        $this->_lists['canmore'] = $canmore;
    }
    public function getPCountry($country_id)
    {
        $query = 'SELECT * FROM #__bl_countries ORDER BY country';
        $this->db->setQuery($query);
        $country = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists['country_reg_rq'] = $this->getJS_Config('country_reg_rq');
        $cntr[] = JHTML::_('select.option',  '', JText::_('BLFA_SELCOUNTRY'), 'id', 'country');
        $countries = array_merge($cntr, $country);
        $this->_lists['country'] = JHTML::_('select.genericlist',   $countries, 'country_id', 'class="selectpicker '.($this->_lists['country_reg_rq'] ? ' required' : '').'" size="1"', 'id', 'country', $country_id);
    }
    public function getUsers($usr_id)
    {
        $query = "SELECT usr_id FROM #__bl_players WHERE usr_id != '".$usr_id."'";
        $this->db->setQuery($query);
        $ex_users = $this->db->loadColumn();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $query = 'SELECT * FROM #__users '.(count($ex_users) ? 'WHERE id NOT IN ('.implode(',', $ex_users).')' : '').' ORDER BY username';
        $this->db->setQuery($query);
        $f_users = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $is_player[] = JHTML::_('select.option',  0, JText::_('BLFA_SELUSR'), 'id', 'username');
        $f_users = array_merge($is_player, $f_users);
        $this->_lists['usrid'] = JHTML::_('select.genericlist',   $f_users, 'usr_id', 'class="selectpicker" size="1"', 'id', 'username', $usr_id);
    }
    public function savAdmPlayer()
    {
        $post = JRequest::get('post');
        $post['about'] = JRequest::getVar('about', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['def_img'] = JRequest::getVar('ph_default', 0, 'post', 'int');
        $s_id = JRequest::getVar('sid', 0, '', 'int');
        $tid = JRequest::getVar('tid', 0, '', 'int');
        $row = new JTablePlayer($this->db);
        $user = JFactory::getUser();
        $row->created_by = $user->id;

        if ($this->acl == 2) {
            $canmore = $post['id'] ? true : false;
            $query = 'SELECT COUNT(*) FROM #__bl_players WHERE created_by='.$user->id;
            $this->db->setQuery($query);
            $curcap = $this->db->loadResult();

            $teams_per_account = $this->getJS_Config('players_per_account');

            if ($curcap < $teams_per_account) {
                $canmore = true;
            }
            if (!$canmore) {
                JError::raiseError(403, JText::_('Access Forbidden'));

                return;
            }
        }

        if (!$row->bind($post)) {
            JError::raiseError(500, $row->getError());
        }

        if ($this->acl == 1 && $row->id && $this->_lists['jssa_editplayer']) {
            JError::raiseError(500, $row->getError());
        }

        if (!$row->check()) {
            JError::raiseError(500, $row->getError());
        }
        // if new item order last in appropriate group
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
        }
        if ($this->acl == 1) {
            $topt = $this->getTournOpt($s_id);
            if ($topt->t_single) {
                $query = 'INSERT IGNORE INTO #__bl_season_players(season_id,player_id) VALUES('.$s_id.','.$row->id.')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            } elseif (!$post['id'] && intval($post['teams_seas'])) {
                $query = 'INSERT IGNORE INTO #__bl_players_team(team_id,player_id,season_id) VALUES('.intval($post['teams_seas']).','.$row->id.','.$s_id.')';
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }

        $row->checkin();
        $query = 'DELETE FROM #__bl_assign_photos WHERE cat_type = 1 AND cat_id = '.$row->id;
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
                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$photo_id.','.$row->id.',1)';
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
            //echo $bl_filename;
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
                // if new item order last in appropriate group
                if (!$img1->store()) {
                    JError::raiseError(500, $img1->getError());
                }
                 $img1->checkin();
                 $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img1->id.','.$row->id.',1)';
                 $this->db->setQuery($query);
                 $this->db->query();
             }
        } else {
            if ($_FILES['player_photo_1']['error'] == 1) {
                //$this->mainframe->redirect( 'index.php?option=com_joomsport&task=adplayer_edit&controller=moder&tid='.$tid.'&cid[]='.$row->id,JText::_( 'BLBE_WRNGPHOTO' ),'warning');
                if ($this->acl == 1) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=adplayer_edit&controller=admin&sid='.$this->season_id.'&cid[]='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                } elseif ($this->acl == 2) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=adplayer_edit&controller=moder&tid='.$tid.'&cid[]='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
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
                // if new item order last in appropriate group
                if (!$img2->store()) {
                    JError::raiseError(500, $img2->getError());
                }
                $img2->checkin();
                $query = 'INSERT INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img2->id.','.$row->id.',1)';
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
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=adplayer_edit&controller=admin&sid='.$this->season_id.'&cid[]='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                } elseif ($this->acl == 2) {
                    $this->mainframe->redirect('index.php?option=com_joomsport&task=adplayer_edit&controller=moder&tid='.$tid.'&cid[]='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
                }
            }
        }
        if ($this->acl == 2) {
            $s_id = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
            $query = "SELECT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id AND t.team_id=".$tid.' ORDER BY s.s_id desc';
            $this->db->setQuery($query);
            $seass = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if (!$s_id) {
                $s_id = $seass[0]->id;
            }
        }
        //-------extra fields-----------//
        if (isset($_POST['extraf']) && count($_POST['extraf'])) {
            foreach ($_POST['extraf'] as $p => $dummy) {
                if (intval($_POST['extra_id'][$p])) {
                    $query = "SELECT season_related FROM `#__bl_extra_filds` WHERE id='".intval($_POST['extra_id'][$p])."'";
                    $this->db->setQuery($query);
                    $season_related = $this->db->loadResult();

                    $db_season = $season_related ? $s_id : 0;

                    $query = 'DELETE FROM #__bl_extra_values WHERE f_id = '.intval($_POST['extra_id'][$p]).' AND uid = '.$row->id." AND season_id='".$db_season."'";
                    $this->db->setQuery($query);
                    $this->db->query();
                    if ($_POST['extra_ftype'][$p] == '2') {
                        $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text,season_id) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."',{$db_season})";
                    } else {
                        $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue,season_id) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."',{$db_season})";
                    }
                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        }
        $this->id = $row->id;
        $this->season_id = $s_id;
        $this->tid = $tid;
        $this->is_first = ($post['id']) ? 1 : 0;
    }
    public function delAdmPlayer()
    {
        $cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));
        if ($this->acl == 1) {
            $this->admAccess();
            $user = JFactory::getUser();
            if (count($cid) && $this->_lists['jssa_deleteplayers']) {
                $cids = implode(',', $cid);
                $query = "UPDATE `#__bl_players` SET created_by='-".$user->id."' WHERE id IN (".$cids.") AND created_by='".$user->id."'";
                $this->db->setQuery($query);
                $this->db->query();

                $toud = $this->getTournOpt($this->season_id);// model

                if ($toud->t_single != 1) {
                    $query = 'DELETE FROM `#__bl_players_team` WHERE player_id IN ('.$cids.') AND season_id = '.$this->season_id;
                } else {
                    $query = 'DELETE FROM `#__bl_season_players` WHERE player_id IN ('.$cids.') AND season_id = '.$this->season_id;
                }
                $this->db->setQuery($query);
                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                $query = 'SELECT s.s_id FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.t_id=t.id AND t_single = 1 AND s.s_id='.$this->season_id;
                $this->db->setQuery($query);
                $sid = $this->db->loadColumn();
                
                if (count($sid)) {
                    require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'sportleague'.DIRECTORY_SEPARATOR.'sportleague.php';

                    
                    $sids = implode(',', $sid);
                    $query = 'SELECT id FROM #__bl_matchday WHERE s_id IN ('.$sids.')';
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
                    }
                    foreach ($sid as $recalcSeason) {
                        
                        classJsportPlugins::get('generateTableStanding', array('season_id' => $recalcSeason));
                        
                        //update player list
                        classJsportPlugins::get('generatePlayerList', array('season_id' => $recalcSeason));
                    }
                }
            }
        } else {
            if (count($cid)) {
                $cids = implode(',', $cid);

                $this->db->setQuery('DELETE FROM #__bl_players WHERE id IN ('.$cids.')');

                $this->db->query();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }
    }
}
