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

class regplayerJSModel extends JSPRO_Models
{
    public $_lists = null;
    public $_user = null;
    public $_usrjs = null;
    public $_enmd = null;
    public $usrnew = null;
    public $sid = null;
    public $title = null;
    public $p_title = null;
    public function __construct()
    {
        parent::__construct();
        $this->_user = JFactory::getUser();
        $this->title = JFactory::getDocument()->getTitle();
        $this->_enmd = 0;
        $this->sid = $this->mainframe->getUserStateFromRequest('com_joomsport.sid', 'sid', 0, 'int');
    }

    public function getData()
    {
        $this->getPlayer();

        //title
        $this->p_title = JText::_('BLFA_EDITFIPROF');
        $this->_params = $this->JS_PageTitle($this->title ? $this->title : JText::_('BLFA_EDITFIPROF'));
        $this->_lists['post_max_size'] = $this->getValSettingsServ('post_max_size');
        //return
        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = $return;
            if (!JURI::isInternal(base64_decode($return))) {
                $return = '';
            }
        }
        if ($this->_user->get('guest')) {
            $return_url = $_SERVER['REQUEST_URI'];
            $return_url = base64_encode($return_url);

            if (getVer() >= '1.6') {
                $uopt = 'com_users';
            } else {
                $uopt = 'com_user';
            }
            $return = 'index.php?option='.$uopt.'&view=login&return='.$return_url;

            // Redirect to a login form
            $this->mainframe->redirect($return, JText::_('BLMESS_NOT_LOGIN'));
        }
        $this->_lists['return'] = $return;
        $this->getJSreg();

        //Player Country registration
        $this->_lists['country_reg'] = $this->getJS_Config('country_reg');
        $this->_lists['country_reg_rq'] = $this->getJS_Config('country_reg_rq');
        $this->getCountries();
        //Nick registration
        $this->_lists['nick_reg'] = $this->getJS_Config('nick_reg');
        $this->_lists['nick_reg_rq'] = $this->getJS_Config('nick_reg_rq');

        //Last Name registration
        $this->_lists['reg_lastname'] = $this->getJS_Config('reg_lastname');
        $this->_lists['reg_lastname_rq'] = $this->getJS_Config('reg_lastname_rq');

        $this->_lists['teams_season'] = $this->teamsToModer();
        $this->_lists['panel'] = $this->getePanel($this->_lists['teams_season'], 0, 0, 1);

        //$this->_lists['seas_pl_reg'] = ""; //
    }

    public function getPlayer()
    {
        $query = 'SELECT p.first_name, p.last_name FROM #__bl_players as p';
        $this->db->setQuery($query);
        $player_reg = $this->db->loadRowList();
        $this->_lists['player_reg'] = $player_reg;
    }

    public function getCountries()
    {
        $query = 'SELECT * FROM #__bl_countries ORDER BY country';
        $this->db->setQuery($query);
        $country = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $cntr[] = JHTML::_('select.option',  '', JText::_('BLFA_SELCOUNTRY'), 'id', 'country');
        $countries = array_merge($cntr, $country);

        $this->_lists['country'] = JHTML::_('select.genericlist',   $countries, 'country_id', 'class="selectpicker '.($this->_lists['country_reg_rq'] ? ' required' : '').'" size="1"', 'id', 'country', isset($this->_lists['usr']->country_id) ? $this->_lists['usr']->country_id : 0);
    }
    public function getJSreg()
    {
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$this->_user->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists['usr'] = $usr ? $usr : new stdClass();
        if (!isset($this->_lists['usr']->about)) {
            $this->_lists['usr']->about = '';
        }
        if (isset($usr->id)) {
            $this->_lists['ed_seas'] = $this->getSeasFiltr($usr->id);
        }
    //print_r($this->_lists["ed_seas"]);
////////////////////////////////////	
        if ($usr) {  ///updt					

            $this->_lists['adf'] = $this->getBEAdditfields(0, $usr->id, $this->sid);

            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                    .' FROM #__bl_assign_photos as ap, #__bl_photos as p, #__bl_players as pl'
                    .' WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '.$usr->id.' AND pl.id=cat_id';
            $this->db->setQuery($query);
            $this->_lists['photos'] = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $query = 'SELECT COUNT(*) FROM #__bl_season_players as sp, #__bl_matchday as m, #__bl_tournament as t, #__bl_seasons as s'
                    ." WHERE t.id=s.t_id AND s.s_id=m.s_id AND s.published='1' AND t.published='1' AND m.s_id=sp.season_id AND sp.player_id = ".$usr->id;
            $this->db->setQuery($query);
            $this->_enmd = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $this->_lists['ed_seas'] = $this->getSeasFiltr($usr->id);
            $query = "SELECT t.name FROM #__bl_seasons as s, #__bl_tournament as t WHERE s.s_id = '".$this->sid."' AND t.id = s.t_id";
            $this->db->setQuery($query);
            $this->_lists['tourn_name'] = $this->db->loadResult();
        } else {
            $player_reg = $this->getJS_Config('player_reg');

            if (!$player_reg) {
                echo JText::_('BLFA_OPTDISAB');
                exit();
            }

            $query = "SELECT *,'' as fvalue,'' as fvalue_text  FROM #__bl_extra_filds WHERE reg_exist='1' AND type='0' AND season_related='0'";
            $this->db->setQuery($query);
            $adf = $this->db->loadObjectList();

            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $mj = 0;
            if (isset($adf)) {
                foreach ($adf as $extr) {
                    if ($extr->field_type == '3') {
                        $query = 'SELECT * FROM #__bl_extra_select WHERE fid='.$extr->id;
                        $this->db->setQuery($query);
                        $selvals = $this->db->loadObjectList();
                        if (count($selvals)) {
                            $adf[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="selectpicker styled-long'.($extr->reg_require ? ' required' : '').'" size="1"', 'id', 'sel_value', $extr->fvalue);
                        }
                    }
                    if ($extr->field_type == '1') {
                        $adf[$mj]->selvals = JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue);
                    }
                    ++$mj;
                }
            }
            $this->_lists['adf'] = $adf;
        }
    }

    public function getSeasFiltr($plid)
    {
        $query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
        $this->db->setQuery($query);
        $tourn = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        //$javascript = " onchange='document.chg_seas.submit();'";
        $javascript = 'onchange="javascript:document.filterForm.submit();"';
        $jqre = '<select name="sid" id="sid" size="1" '.$javascript.'>';
        $jqre .= '<option value="0">'.JText::_('BLFA_ALL').'</option>';

        $error = $this->db->getErrorMsg();

        if ($error) {
            return JError::raiseError(500, $error);
        }

        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        for ($i = 0;$i < count($tourn);++$i) {
            $tsingl = $tourn[$i]->t_single;
            if ($tsingl) {
                $query = 'SELECT s.s_id as id,s.s_name as s_name'
                        .' FROM #__bl_season_players as sp, #__bl_seasons as s'
                        ." WHERE s.published = '1' AND s.t_id=".$tourn[$i]->id.' AND s.s_id=sp.season_id AND sp.player_id='.$plid;
            } else {
                $query = 'SELECT DISTINCT(s.s_id) as id,s.s_name as s_name'
                        .' FROM #__bl_seasons as s, #__bl_season_teams as st, #__bl_players_team as pt'
                        ." WHERE pt.confirmed='0' AND s.published = '1' AND s.t_id=".$tourn[$i]->id.' AND st.season_id=s.s_id AND st.team_id=pt.team_id'
                        .' AND pt.season_id=s.s_id AND pt.player_id='.$plid
                        .'  ORDER BY s.s_name';
            }
            $is_tourn2 = array();
            $this->db->setQuery($query);
            $rows = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $this->_lists['seas_pl_reg'] = ($rows) ? (1) : (0);
            if (count($rows)) {
                $jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';
                for ($g = 0;$g < count($rows);++$g) {
                    $jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $this->sid) ? 'selected' : '').'>'.$rows[$g]->s_name.'</option>';
                    $this->seasplayed[] = $rows[$g]->id;
                }
                $jqre .= '</optgroup>';
            }
        }
        $jqre .= '</select>';
//<---------------------------------		
        return $jqre;
    }

    public function SaveRegPlayer()
    {
        $post = JRequest::get('post');
        $row = new JTablePlayer($this->db);
        $row->registered = 1;
        $user = JFactory::getUser();
        $istlogo = JRequest::getVar('istlogo', 0, 'post', 'int');
        $post['def_img'] = JRequest::getVar('ph_default', 0, 'post', 'int');
        if ($user->get('guest')) {
            JError::raiseError(403, JText::_('Access Forbidden'));

            return;
        }
        $row->usr_id = $user->id;
        if (!$row->bind($post)) {
            JError::raiseError(500, $row->getError());
        }
        if (!$row->check()) {
            JError::raiseError(500, $row->getError());
        }
        // if new item order last in appropriate group
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
        }
        $row->checkin();
        $curid = $row->id;

        $this->usrnew = $curid;

        if (!$istlogo && !$row->id) {
            $query = "DELETE FROM #__bl_assign_photos WHERE cat_type='1' AND cat_id=".$curid;
            $this->db->setQuery($query);
            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }

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
                $query = 'INSERT IGNORE INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$photo_id.','.$row->id.',1)';
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
                 $query = 'INSERT IGNORE INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES('.$img1->id.','.$row->id.',1)';
                 $this->db->setQuery($query);
                 $this->db->query();
                 $error = $this->db->getErrorMsg();
                 if ($error) {
                     return JError::raiseError(500, $error);
                 }
             }
        } else {
            if ($_FILES['player_photo_1']['error'] == 1) {
                $this->mainframe->redirect('index.php?option=com_joomsport&task=regplayer&cid[]='.$row->id, JText::_('BLBA_WRNGPHOTO'), 'warning');
            }
        }

        /*if(isset($img1)){
            $query = "INSERT IGNORE INTO #__bl_assign_photos(photo_id,cat_id,cat_type) VALUES(".$img1->id.",".$row->id.",1)";
             $this->db->setQuery($query);
            $this->db->query();
            $error = $this->db->getErrorMsg();
            if ($error)
            {
                return JError::raiseError(500, $error);
            }
        }*/

        //-------extra fields-----------//
        if (isset($_POST['extraf']) && count($_POST['extraf'])) {
            foreach ($_POST['extraf'] as $p => $dummy) {
                if (intval($_POST['extra_id'][$p])) {
                    $query = "SELECT season_related FROM `#__bl_extra_filds` WHERE id='".intval($_POST['extra_id'][$p])."'";
                    $this->db->setQuery($query);
                    $season_related = $this->db->loadResult();

                    $db_season = $season_related ? $this->sid : 0;

                    $query = 'DELETE FROM #__bl_extra_values WHERE f_id = '.intval($_POST['extra_id'][$p]).' AND uid = '.$row->id." AND season_id='".$db_season."'";
                    $this->db->setQuery($query);
                    $this->db->query();
                    if ($_POST['extra_ftype'][$p] == '2') {
                        $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue_text,season_id) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".addslashes($_POST['extraf'][$p])."',{$db_season})";
                    } else {
                        $query = 'INSERT INTO #__bl_extra_values(f_id,uid,fvalue,season_id) VALUES('.$_POST['extra_id'][$p].','.$row->id.",'".$_POST['extraf'][$p]."',{$db_season})";
                    }
                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        }
    }
}
