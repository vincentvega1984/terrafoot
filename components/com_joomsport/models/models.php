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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class JSPRO_Models
{
    public $db = null;
    public $uri = null;
    public $mainframe = null;
    public $document = null;
    protected $js_table = null;
    public $jstab = null;
    public function __construct()
    {
        $this->db = JFactory::getDBO();
        $this->uri = JFactory::getURI();
        $this->mainframe = JFactory::getApplication();
        $this->document = JFactory::getDocument();
    }
    public function JS_PageTitle($p_title)
    {
        // $app 		= JFactory::getApplication();
        $pathway = $this->mainframe->getPathway();
        $params = $this->mainframe->getParams();
        $menus = $this->mainframe->getMenu();
        $menu = $menus->getActive();
        if (is_object($menu)) {
            $menu_params = new JRegistry();
            if (!$menu_params->get('page_title')) {
                $params->set('page_title',    JText::_($p_title));
            }
        } else {
            $params->set('page_title',    JText::_($p_title));
        }
        $this->document->setTitle($params->get('page_title'));
        $pathway->addItem(JText::_($p_title));

        return $params;
    }

    public function unblSeasonReg()
    {
        $unable_reg = 0;
        if ($this->s_id == -1) {
            return 0;
        }
        $tourn = $this->getTournOpt($this->s_id);
        $season_par = $this->getSParametrs($this->s_id);
        $this->_lists['season_par'] = $season_par;
        $this->_lists['enbl_extra'] = $season_par->s_enbl_extra;
        $reg_start = mktime(substr($season_par->reg_start, 11, 2), substr($season_par->reg_start, 14, 2), 0, substr($season_par->reg_start, 5, 2), substr($season_par->reg_start, 8, 2), substr($season_par->reg_start, 0, 4));
        $reg_end = mktime(substr($season_par->reg_end, 11, 2), substr($season_par->reg_end, 14, 2), 0, substr($season_par->reg_end, 5, 2), substr($season_par->reg_end, 8, 2), substr($season_par->reg_end, 0, 4));

        if ($tourn->t_single) {
            $query = 'SELECT COUNT(*) FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = '.$this->s_id;
        } else {
            $query = 'SELECT COUNT(*) FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = '.$this->s_id;
        }
        $this->db->setQuery($query);
        $part_count = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if ($season_par->s_reg && ($part_count < $season_par->s_participant || $season_par->s_participant == 0) && ($reg_start <= time() && (time() <= $reg_end || $season_par->reg_end == '0000-00-00 00:00:00'))) {
            $unable_reg = 1;
        }

        return $unable_reg;
    }

    public function teamsToModer()
    {
        $user = JFactory::getUser();

        if ($user->id) {
            $query = 'SELECT t.id FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid='.$user->id.' ORDER BY t.t_name';
            $this->db->setQuery($query);
            $teams_season = $this->db->loadColumn(); //this!!!!!

            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }
        if (empty($teams_season)) {
            $teams_season = array();
        }

        return $teams_season;
    }

    public function set_JS_tabs()
    {
        require_once JPATH_ROOT.DS.'components'.DS.'com_joomsport'.DS.'includes'.DS.'tabs.php';
        $this->jstab = new esTabs();
    }

    public function get_db_Table()
    {
        $this->js_table = '';
    }

    public function getePanel($team, $reg, $sid, $cal = 0, $inv = 0, $tbl = 0)
    {
        return $this->getePanelMobile($team, $reg, $sid, $cal, $inv, $tbl);

        $Itemid = JRequest::getInt('Itemid');

        $team_reg = $this->getJS_Config('team_reg');

        $link2 = JRoute::_('index.php?option=com_joomsport&amp;view=seasonlist&limitstart=0&Itemid='.$Itemid);

        $kl = '<div class="navbar-header navHeadFull">';

        $img_pop = explode('/', $this->getJS_Config('jsbrand_epanel_image'));

        if ($this->getJS_Config('jsbrand_epanel_image') && is_file(JPATH_ROOT.$this->getJS_Config('jsbrand_epanel_image'))) {
            if (count($img_pop) == 5) {
                $kl .= '<a class="module-logo" href="'.$link2.'" title="JoomSport"><img src="'.JURI::base().$this->getJS_Config('jsbrand_epanel_image').'" height="38" /></a>';
            } else {
                $kl .= '<a class="module-logo" href="'.$link2.'" title="JoomSport"><img '.getImgPop($img_pop[3], 5, 38, 53).' /></a>';
            }
        }
        $kl .= '<ul class="module-menu">';
        $query = 'SELECT COUNT(*) FROM #__bl_moders WHERE tid= '.$inv;
        $this->db->setQuery($query);
        $is_moder = $this->db->loadResult();

        if (isset($team[0])) {
            //$link = JRoute::_('index.php?option=com_joomsport&view=edit_team&tid='.$team[0].'&controller=moder&Itemid='.$Itemid);
            //$kl .= '<li><a href="'.$link.'" title="'.JText::_('BLFA_YTEAM').'"><span class="module-menu-manage-team">'.JText::_('BLFA_YTEAM').'</span></a></li>';
        }
        $tr = false;
        $_users = JFactory::getUser();
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$_users->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if (!$this->getJS_Config('player_reg') && $usr && $_users->id) {
            $tr = true;
        }
        if ($this->getJS_Config('player_reg')) {
            $tr = true;
        }

        if ($team_reg && ($tr || $this->getJS_Config('player_team_reg') == '0')) {
            //$link = JRoute::_('index.php?option=com_joomsport&amp;task=regteam&Itemid='.$Itemid);
            //$kl .= '<li><a href="'.$link.'" title="'.JText::_('BLFA_NTEAM').'"><span class="module-menu-new-team">'.JText::_('BLFA_NTEAM').'</span></a></li>';
        }
        if ($cal && $sid > 0) {
            $kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=calendar&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_CALENDAR').'"><span class="module-menu-calendar">'.JText::_('BLFA_CALENDAR').'</span></a></li>';
        }
        if ($tbl && $sid > 0) {
            $kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=table&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BL_TAB_TBL').'"><span class="module-menu-table">'.JText::_('BL_TAB_TBL').'</span></a></li>';
        }
        if ($reg && $tr) {
            $kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=join_season&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_REGGG').'"<span class="module-menu-join-season">'.JText::_('BLFA_REGGG').'</span></a></li>';
        }
        if ($inv && $this->getJS_Config('esport_join_team') && $sid && $tr && $is_moder) {
            $kl .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&amp;task=jointeam&amp;sid='.$sid.'&amp;tid='.$inv.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_PLJOINTEAM').'"><span class="module-menu-editor">'.JText::_('BLFA_PLJOINTEAM').'</span></a></li>';
        }
        if ($this->getJS_Config('player_reg')) {
            //$link = JRoute::_('index.php?option=com_joomsport&amp;task=regplayer&Itemid='.$Itemid);
            //$kl .= '<li><a href="'.$link.'" title="'.JText::_('BLFA_EDITFIPROF').'"><span class="module-menu-join"><!-- --></span></a><span class="twice-border"></span></li>';
        }
        $kl .= '</ul></div><div class="under-module-header"></div>';

        return $kl;
    }

    public function getePanelMobile($team, $reg, $sid, $cal = 0, $inv = 0, $tbl = 0)
    {
        $Itemid = JRequest::getInt('Itemid');
        $link2 = JRoute::_('index.php?option=com_joomsport&amp;view=seasonlist&limitstart=0&Itemid='.$Itemid);

        $kl = '<div class="navbar-header navHeadFull">';

        $img_pop = explode('/', $this->getJS_Config('jsbrand_epanel_image'));

        if ($this->getJS_Config('jsbrand_epanel_image') && is_file(JPATH_ROOT.$this->getJS_Config('jsbrand_epanel_image'))) {
            if (count($img_pop) == 5) {
                $logo = $this->getJS_Config('jsbrand_epanel_image');
                $logo = str_replace('/logo.png', '/logo-mobile.png', $logo);
                $kl .= '<a class="module-logo" href="'.$link2.'" title="JoomSport"><img src="'.JURI::base().$logo.'" height="38" /></a>';
            } else {
                $kl .= '<a class="module-logo" href="'.$link2.'" title="JoomSport"><img '.getImgPop($img_pop[3], 5, 38, 53).' /></a>';
            }
        }

        $query = 'SELECT COUNT(*) FROM #__bl_moders WHERE tid= '.$inv;
        $this->db->setQuery($query);
        $is_moder = $this->db->loadResult();

        $tr = false;
        $_users = JFactory::getUser();
        $query = 'Select * FROM #__bl_players WHERE usr_id='.$_users->id;
        $this->db->setQuery($query);
        $usr = $this->db->loadObject();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if (!$this->getJS_Config('player_reg') && $usr && $_users->id) {
            $tr = true;
        }
        if ($this->getJS_Config('player_reg')) {
            $tr = true;
        }

        $kl .= '<ul class="nav navbar-nav pull-right navSingle">';
        if ($cal && $sid > 0) {
            $kl .= '<a class="btn btn-default" href="'.JRoute::_('index.php?option=com_joomsport&amp;task=calendar&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_CALENDAR').'"><i class="date pull-left"></i>'.JText::_('BLFA_CALENDAR').'</a>';
        }
        if ($tbl && $sid > 0) {
            $kl .= '<a class="btn btn-default" href="'.JRoute::_('index.php?option=com_joomsport&amp;task=table&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BL_TAB_TBL').'"><i class="tableS pull-left"></i>'.JText::_('BL_TAB_TBL').'</a>';
        }
        if ($reg && $tr) {
            $kl .= '<a class="btn btn-default" href="'.JRoute::_('index.php?option=com_joomsport&amp;task=join_season&amp;sid='.$sid.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_REGGG').'">'.JText::_('BLFA_REGGG').'<i class="fa fa-hand-o-right"></i></a>';
        }
        if ($inv && $this->getJS_Config('esport_join_team') && $sid && $tr && $is_moder) {
            $kl .= '<a class="btn btn-default" href="'.JRoute::_('index.php?option=com_joomsport&amp;task=jointeam&amp;sid='.$sid.'&amp;tid='.$inv.'&Itemid='.$Itemid).'" title="'.JText::_('BLFA_PLJOINTEAM').'">'.JText::_('BLFA_PLJOINTEAM').'<i class="fa fa-sign-in"></i></a>';
        }
        $kl .= '</ul></div>';

        return $kl;
    }

        //2.0.1
    //type 0 - player, 1 - team, 2-match.
    public function getAddFields($id, $type, $suff, $sid = 0)
    {
        $user = JFactory::getUser();

        $query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid='".$id."' WHERE ef.published=1 AND ef.fdisplay = '1' AND ef.type = '".$type."' ".($user->id ? '' : " AND ef.faccess='0'").' ORDER BY ef.ordering';
        if ($type == 1 || $type == 0) {
            $query = 'SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid='.($id ? intval($id) : -1).' AND ((ev.season_id='.($sid > 0 ? $sid : -100)." AND ef.season_related = '1') OR (ev.season_id=0 AND ef.season_related = '0')) WHERE ef.published=1 AND ef.type='".$type."' ".($user->id ? '' : " AND ef.faccess='0'").' ORDER BY ef.ordering';
        }
        $this->db->setQuery($query);
        $res = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $mj = 0;
        if (isset($res)) {
            foreach ($res as $extr) {
                if ($extr->fvalue === null && $extr->field_type == '1') {
                    $res[$mj]->fvalue = '';
                } else {
                    if ($extr->field_type == '3') {
                        $query = "SELECT sel_value FROM #__bl_extra_select WHERE id='".$extr->fvalue."'";
                        $this->db->setQuery($query);
                        $selvals = $this->db->loadResult();
                        $error = $this->db->getErrorMsg();
                        if ($error) {
                            return JError::raiseError(500, $error);
                        }
                        if (isset($selvals) && $selvals) {
                            $res[$mj]->selvals = $selvals;
                        } else {
                            $res[$mj]->fvalue = '';
                        }
                    }
                    if ($extr->field_type == '1') {
                        $res[$mj]->selvals = $extr->fvalue ? JText::_('Yes') : JText::_('No');
                        $res[$mj]->fvalue = $res[$mj]->selvals;
                    }
                    if ($extr->field_type == '2') {
                        $res[$mj]->fvalue = $extr->fvalue_text;
                    }
                    if ($extr->field_type == '4' && $res[$mj]->fvalue) {
                        $res[$mj]->fvalue = "<a target='_blank' href='".(substr($extr->fvalue, 0, 7) == 'http://' ? $extr->fvalue : 'http://'.$extr->fvalue)."'>".$extr->fvalue.'</a>';
                    }
                }
                ++$mj;
            }
        }
        if (count($res)) {
            return $this->_getEFview($res, $suff);
        }
    }
    public function getBEAdditfields($type, $id, $sid = 0, $moder = false)
    {
        $query = 'SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid='.($id ? intval($id) : -1).' WHERE ef.published=1 AND ef.type='.$type.' ORDER BY ef.ordering';
        if ($type == 1 || $type == 0) {
            $query = 'SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid='.($id ? intval($id) : -1).' AND ('.($sid ? '(ev.season_id='.($sid ? $sid : -100)." AND ef.season_related = '1') OR" : '')." (ev.season_id=0 AND ef.season_related = '0'))  WHERE ef.published=1 AND ef.type=".$type.' ORDER BY ef.ordering';
            $this->db->setQuery($query);
            $ext_fields_teams = $this->db->loadObjectList();

            if (!count($ext_fields_teams)) {
                $query = 'SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid='.($id ? intval($id) : -1).' AND ev.season_id=0 WHERE ef.published=1 AND ef.type='.$type.' ORDER BY ef.ordering';
            }
        }

        $this->db->setQuery($query);
        $ext_fields = $this->db->loadObjectList();

        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $mj = 0;
        if (isset($ext_fields)) {
            foreach ($ext_fields as $extr) {
                if ($extr->field_type == '3') {
                    $tmp_arr = array();
                    $query = 'SELECT * FROM #__bl_extra_select WHERE fid='.$extr->id.' ORDER BY eordering';
                    $this->db->setQuery($query);
                    $selvals = $this->db->loadObjectList();
                    $error = $this->db->getErrorMsg();
                    if ($error) {
                        return JError::raiseError(500, $error);
                    }
                    if (count($selvals)) {
                        $tmp_arr[] = JHTML::_('select.option',  '', JText::_('BLBE_SELECTVALUE'), 'id', 'sel_value');
                        $selvals = array_merge($tmp_arr, $selvals);
                        if ($moder && $extr->reg_require) {
                            $mod_cl = 'required';
                        } else {
                            $mod_cl = '';
                        }
                        $ext_fields[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="styled-long '.$mod_cl.'" size="1"', 'id', 'sel_value', $extr->fvalue);
                    }
                }
                if ($extr->field_type == '1') {
                    $ext_fields[$mj]->selvals = JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue);
                }
                ++$mj;
            }
        }

        return $ext_fields;
    }
    public function _getEFview($res, $suff)
    {
        $view_html = '';
        for ($p = 0; $p < count($res); ++$p) {
            if ($res[$p]->fvalue) {
                $view_html = '<div class="place"> <span class="pull-left"><strong>'.$res[$p]->name.':</strong></span> <span class="pull-right">';

                switch ($res[$p]->field_type) {

                        case '1': $view_html .= $res[$p]->selvals;
                            break;
                        case '2': $view_html .= (isset($res[$p]->fvalue) ? ($res[$p]->fvalue) : '');
                            break;
                        case '3': $view_html .= $res[$p]->selvals;
                            break;
                        case '4': $view_html .= $res[$p]->fvalue;
                            break;
                        case '0':
                        default: $view_html .= (isset($res[$p]->fvalue) ? htmlspecialchars($res[$p]->fvalue) : '');
                            break;
                    }

                $view_html .= '</span> </div>';
            }
        }

        return $view_html;
    }

    public function getVer()
    {
        $version = new JVersion();
        $joomla = $version->getShortVersion();

        return substr($joomla, 0, 3);
    }

    public function getJS_Config($val)
    {
        $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='".$val."'";
        $this->db->setQuery($query);

        return $this->db->loadResult();
    }

    public function getAdmLinks()
    {
        $user = JFactory::getUser();
        $Itemid = JRequest::getInt('Itemid');
        $adm_links = '';
        $query = 'SELECT s.*,t.name FROM #__users as u, #__bl_feadmins as f, #__bl_seasons as s, #__bl_tournament as t WHERE f.user_id = u.id AND s.s_id = f.season_id AND s.t_id = t.id AND u.id = '.intval($user->id).' ORDER BY s.ordering';
        $this->db->setQuery($query);

        $sidsss = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if (count($sidsss)) {
            $vr = 0;
            $adm_links .= '<div class="administrations-links"><ul>';
            foreach ($sidsss as $adm_sid) {
                if ($vr) {
                    $adm_links .= '<li class="a-l-dash">|</li>';
                }
                $adm_links .= '<li><a href="'.JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$adm_sid->s_id.'&Itemid='.$Itemid).'">'.$adm_sid->name.' '.$adm_sid->s_name.'</a></li>';
                ++$vr;
            }
            $adm_links .= '</ul></div>';
        }

        return $adm_links;
    }

    public function getSParametrs($sid)
    {
        $query = 'SELECT * FROM #__bl_seasons WHERE s_id = '.$sid;
        $this->db->setQuery($query);

        return $this->db->LoadObject();
    }

    public function getTeam($tid)
    {
        $query = 'SELECT * FROM #__bl_teams WHERE id = '.$tid;
        $this->db->setQuery($query);

        return $this->db->LoadObject();
    }

    public function getSOptions($sid, $val)
    {
        $query = 'SELECT opt_value FROM #__bl_season_option WHERE s_id = '.$sid." AND opt_name='".$val."'";
        $this->db->setQuery($query);

        return $this->db->loadResult();
    }

    public function getTournOpt($sid)
    {
        $query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_single,s.s_enbl_extra,t.tournament_type,s.season_options FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($sid).' AND s.t_id = t.id ORDER BY t.name, s.s_name';
        $this->db->setQuery($query);
        $tourn = $this->db->loadObject();

        return $tourn;
    }

    public function getTournName($sid)
    {
        if (!$sid && $sid == -1) {
            return '';
        }
        $query = "SELECT t.name FROM #__bl_tournament as t JOIN #__bl_seasons as s ON s.t_id = t.id WHERE s.s_id='".$sid."'";
        $this->db->setQuery($query);

        return $this->db->loadResult();
    }

    public function uploadFile($filename, $userfile_name, $dir = '')
    {
        $msg = '';
        if (!$dir) {
            $baseDir = JPATH_ROOT.'/media/bearleague/';
        } else {
            $baseDir = $dir;
        }
        jimport('joomla.filesystem.path');
        if (file_exists($baseDir)) {
            if (is_writable($baseDir)) {
                if (move_uploaded_file($filename, $baseDir.$userfile_name)) {
                    if (JPath::setPermissions($baseDir.$userfile_name)) {
                        return true;
                    } else {
                        $msg = JText::_('BLFA_UPL_PERM');
                    }
                } else {
                    $msg = JText::_('BLFA_UPL_MOVE');
                }
            } else {
                $msg = JText::_('BLFA_UPL_TMP');
            }
        } else {
            $msg = JText::_('BLFA_UPL_TMPEX');
        }
        if ($msg != '') {
            JError::raiseError(500, $msg);
        }

        return false;
    }
    public function selectPlayerName($obj, $fn = 'first_name', $ln = 'last_name', $nk = 'nick')
    {
        $pln = $this->getJS_Config('player_name');
        $q = '';
        if (isset($obj) && $pln && $obj->$nk) {
            $q = $obj->$nk;
        } else {
            if ($obj->$fn || (isset($obj->$ln) && $obj->$ln)) {
                $q = $obj->$fn;
                if (isset($obj->$ln) && $obj->$ln) {
                    $q .= ' '.$obj->$ln;
                }
            }
        }

        return $q;
    }

    //// moderators filters
    public function getGlobFilters($friend = false, $showSelect = false, $all = array())
    {
        $user = JFactory::getUser();
        $sid = JRequest::getVar('sid', 0, 'request', 'int');
        $tid = JRequest::getVar('tid', 0, 'request', 'int');
        $Itemid = JRequest::getInt('Itemid');
        $team_s = array();
        $all_teams = array();
        $query = 'SELECT id,t_name FROM #__bl_teams as t, #__bl_moders as m WHERE m.tid=t.id AND m.uid='.$user->id.' ORDER BY t_name';
        $this->db->setQuery($query);
        $m_teams = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $moderseason = $this->mainframe->getUserStateFromRequest('com_joomsport.moderseason', 'moderseason', 0, 'int');
////////////////////
        if (count($all)) {
            for ($i = 0;$i < count($all);++$i) {
                $team_s[$i] = isset($all[$i]->season_id) ? $all[$i]->season_id : -1;
                $all_teams[$i] = isset($all[$i]->team_id) ? $all[$i]->team_id : -1;
            }

            $team_s = array_unique($team_s);
            $all_teams = array_unique($all_teams);
        }
        //print_r($all_teams);
///////////////////////
        $query = "SELECT DISTINCT CONCAT(tr.name,' ',s.s_name) as t_name,s.s_id as id FROM #__bl_season_teams as t,#__bl_seasons as s,#__bl_tournament as tr WHERE s.published=1 AND tr.id=s.t_id AND s.s_id=t.season_id ".(count($team_s) ? 'AND t.season_id IN('.implode(',', $team_s).')' : '').' AND '.(count($all_teams) ? 't.team_id IN('.implode(',', $all_teams).')' : 't.team_id='.$tid).' ORDER BY s.s_id desc';
        $this->db->setQuery($query);
        $seass = $this->db->loadObjectList();

        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if (!$moderseason) {
            $this->mainframe->setUserState('com_joomsport.moderseason', (isset($seass[0]->id)) ? ($seass[0]->id) : (''));
            $moderseason = (isset($seass[0]->id)) ? ($seass[0]->id) : ('');
        };

        $isinseas = false;
        for ($j = 0;$j < count($seass);++$j) {
            if ($moderseason == $seass[$j]->id) {
                $isinseas = true;
            }
        }
        if ($moderseason == -1) {
            $isinseas = true;
            $this->_lists['is_friendly_season'] = true;
        }
        if (!$isinseas && count($seass)) {
            $this->mainframe->setUserState('com_joomsport.moderseason', $seass[0]->id);
            $moderseason = $seass[0]->id;
        }

        $javascript = "onchange='document.chg_team.submit();'";
        $this->_lists['tm_filtr'] = JHTML::_('select.genericlist',   $m_teams, 'tid', 'class="form-control" size="1"'.$javascript, 'id', 't_name', $tid);

        if ($showSelect || count($seass)) {
            $query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
            $this->db->setQuery($query);
            $tourn = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            $jqre = '<select name="moderseason" id="moderseason" class="form-control" size="1" '.$javascript.'>';
            $jqre2 = '<select name="moderseason" id="moderseason" class="form-control" size="1" '.$javascript.'>';

            $query = 'SELECT COUNT(*) FROM #__bl_matchday WHERE s_id=-1';
            $this->db->setQuery($query);
            $fr_md = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if ($friend && $fr_md) {
                $jqre .= '<option value="-1" '.((-1 == $moderseason) ? 'selected' : '').'>'.JText::_('BLFA_FRIENDLY_MATCHES').'</option>';//friendly
            }
            for ($i = 0;$i < count($tourn);++$i) {
                $is_tourn2 = array();
                $query = "SELECT DISTINCT  s.s_id as id,s.s_name as s_name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id, #__bl_season_teams as st WHERE s.published='1' AND ".(count($all_teams) ? 'st.team_id IN('.implode(',', $all_teams).')' : 'st.team_id='.$tid).' AND s.s_id=st.season_id '.(count($team_s) ? 'AND st.season_id IN('.implode(',', $team_s).')' : '').' AND t.id='.$tourn[$i]->id.'  ORDER BY s.s_name';

                $this->db->setQuery($query);
                $rows = $this->db->loadObjectList();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }

                if (count($rows)) {
                    $jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';
                    $jqre2 .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';
                    for ($g = 0;$g < count($rows);++$g) {
                        $jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $moderseason) ? 'selected' : '').'>'.$rows[$g]->s_name.'</option>';
                        $jqre2 .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $moderseason) ? 'selected' : '').'>'.$rows[$g]->s_name.'</option>';
                        $seasplayed[] = $rows[$g]->id;
                    }
                    $jqre .= '</optgroup>';
                    $jqre2 .= '</optgroup>';
                }
            }
            $jqre .= '</select>';
            $jqre2 .= '</select>';

            $this->_lists['seass_filtr'] = $jqre;
            $this->_lists['seass_filtr_nofr'] = $jqre2;
// 				$this->_lists['seass_filtr_nofr'] = $jqre2;

                 $this->_lists['tourn_name'] = $this->getTournName($moderseason);
        } else {
            $jqre = '<select name="moderseason" id="moderseason" class="styled jfsubmit" size="1" '.$javascript.'>';
            $jqre .= '<option value="-1">'.JText::_('BLFA_ALL').'</option></select>';
            $this->_lists['seass_filtr'] = $jqre;
            $this->_lists['tourn_name'] = '';
        }
// 			else if ($moderseason == -1) {
// 			    $this->_lists['seass_filtr'] = $this->_lists['seass_filtr_nofr'] = '';
// 			}
        //$this->_lists["tourn_name"] = $this->getTournName($moderseason);
    }

    //bettings
    public function isBet()
    {
        $query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='betting'";
        $this->db->setQuery($query);
        $is_betting = $this->db->loadResult();

        return $is_betting;
    }
    public function getUserPoints($user)
    {
        $db = JFactory::getDbo();
        $q = 'SELECT points FROM #__bl_betting_users WHERE iduser='.$user;
        $db->setQuery($q);
        $points = $db->loadResult();
        $this->_data['points'] = $points;

        return !$points ? 0 : $points;
    }

    public function saveBet($points, $idmatch, $idevent, $who)
    {
        $user = JFactory::getUser();
        $userbet = new JTableBettingUsersBets($this->db);
        $userbet->set('iduser', $user->get('id'));
        $userbet->store();
        $bet = new JTableBettingBets($this->db);
        $bet->set('idbet', $userbet->get('id'));
        $bet->set('idmatch', $idmatch);
        $bet->set('idevent', $idevent);
        $bet->set('points', $points);
        $bet->set('who', $who);
        $bet->store();
        $log = new JTableBettingLogs($this->db);
        $log->addToLog($user->get('id'), -$points);
        $betuser = new JTableBettingUsers($this->db);
        $betuser->load(array('iduser' => $user->get('id')));
        $betuser->changePoints(-$points);
    }
    public function getBettingMenu($Itemid)
    {
        $menu = '<div class="betmenu">
					<div>
						<a href="'.JRoute::_('index.php?option=com_joomsport&view=bet_cash_request&Itemid='.$Itemid).'">'.
                            JText::_('BLFA_BET_REQUEST_CASH').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_('index.php?option=com_joomsport&view=bet_points_request&Itemid='.$Itemid).'">'.
                            JText::_('BLFA_BET_REQUEST_POINTS').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_('index.php?option=com_joomsport&view=currentbets&Itemid='.$Itemid).'">'.
                            JText::_('BLFA_BET_CURRENT_BETS').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_('index.php?option=com_joomsport&view=pastbets&Itemid='.$Itemid).'">'.
                            JText::_('BLFA_BET_PAST_BETS').'
						</a>
					</div>
					<div>
						<a href="'.JRoute::_('index.php?option=com_joomsport&view=bet_matches&Itemid='.$Itemid).'">'.
                            JText::_('BLFA_BET_MATCHES').'
						</a>
					</div>
				</div>';

        return $menu;
    }

    public function getUserInfo($model, $Itemid)
    {
        $mainmodel = new self();
        $data = $model->getData();
        $user = JFactory::getUser();
        if ($data) {
            $points = $data['points'];
            $currentBets = count($data['currentbets']);
            $pastBets = count($data['pastbets']);
            $wonBets = count($data['wonbets']);
        } else {
            $points = $mainmodel->getUserPoints($user->get('id'));
            $currentBets = count($model->getCurrentBets());
            $pastBets = count($model->getPastBets());
            $wonBets = count($model->getWonBets());
        }

        return '
			<span>'.$user->get('username').'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_POINTS').'</span><span>'.$points.'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_CURRENTBETS').'</span><span>'.$currentBets.'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_WINBETS').'</span><span>'.$wonBets.'</span><br/>
			<span style="margin-right:10px">'.JText::_('BLFA_BET_PASTBETS').'</span><span>'.$pastBets.'</span><br/>
		';
    }
    /* page 
        1-season layout
        2-team layout
        3-player layout
        4-match layout
        5-venue layout
    */
    public function getSocialButtons($page, $title = '', $img = '', $txt = '')
    {
        $doc = JFactory::getDocument();
        if (!$this->getJS_Config($page)) {
            return '';
        }

        $socbut = '';
        if ($this->getJS_Config('jsb_twitter')) {
            $socbut .= '<div class="jsd_buttons">
							<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" target="_blank">Tweet</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>';
        }
        if ($this->getJS_Config('jsb_gplus')) {
            $socbut .= '<div class="jsd_buttons">
							<g:plusone size="medium"></g:plusone>

							<script type="text/javascript">
							  window.___gcfg = {lang: "en"};

							  (function() {
								var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
								po.src = "https://apis.google.com/js/plusone.js";
								var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
							  })();
							</script>
						</div>';
        }

        if ($this->getJS_Config('jsb_fbshare') || $this->getJS_Config('jsb_fblike') || $this->getJS_Config('jsb_gplus')) {
            if ($title) {
                $doc->addCustomTag('<meta property="og:title" content="'.$title.'"/> ');
            }
            if ($img) {
                $doc->addCustomTag('<meta property="og:image" content="'.$img.'"/> ');
            }
            //if($txt){
                $doc->addCustomTag('<meta property="og:description" content="'.($txt ? $txt : $title).'"/> ');
            //}
        }

        if ($this->getJS_Config('jsb_fbshare')) {
            $socbut .= '<div class="jsd_buttons"  style="margin-right:25px;">
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>';

            $socbut .= '<div class="fb-send" data-href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" data-font="verdana"></div>';

            $socbut .= '</div>';
        }
        if ($this->getJS_Config('jsb_fblike')) {
            $socbut .= '<div class="jsd_buttons">	
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>

							<div class="fb-like" data-send="false" data-layout="button_count" data-width="80" data-show-faces="true" data-font="verdana"></div>
						</div>';
        }
        $socbut .= '<div class="clear"></div>';

        return $socbut;
    }

    public function isTeamInSeason($teamId, $seasonId)
    {
        $prevQuery = $this->db->getQuery();
        $this->db->setQuery('
            SELECT season_id
            FROM #__bl_season_teams
            WHERE team_id = '.(int) $teamId.'
              AND season_id = '.(int) $seasonId.'
            LIMIT 1;
        ');
        $row = $this->db->loadRow();
        $inSeason = !empty($row);
        $this->db->setQuery($prevQuery);

        return $inSeason;
    }

    public function getTeamSeasons($teamId, $limitResults = null)
    {
        $prevQuery = $this->db->getQuery();
        $this->db->setQuery('
            SELECT CONCAT(tr.name,\' \',s.s_name) as t_name,
                s.s_id as id
            FROM #__bl_season_teams as t,
                #__bl_seasons as s,
                #__bl_tournament as tr
            WHERE s.published = 1
                AND tr.id = s.t_id
                AND s.s_id = t.season_id
                AND t.team_id = '.(int) $teamId.'
            ORDER BY s.s_id desc
            '.(is_null($limitResults) ? '' : 'LIMIT '.(int) $limitResults).'
        ');
        $seasons = $this->db->loadObjectList();
        $this->db->setQuery($prevQuery);

        return empty($seasons) ? array() : $seasons;
    }

    public function hasAnyFriendlySquad($teamId)
    {
        $prevQuery = $this->db->getQuery();
        $this->db->setQuery('
            SELECT player_id
            FROM #__bl_players_team
            WHERE team_id = '.(int) $teamId.'
                AND season_id = -1
            LIMIT 1
        ');

        $result = (bool) $this->db->loadResult();
        if (!$result) {
            $query = 'SELECT s.player_id FROM #__bl_match_events AS s JOIN #__bl_match as m ON m.id=s.match_id AND m.m_played = 1 JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1) WHERE s.t_id='.(int) $teamId.'';
            $this->db->setQuery($query);
            $result = (bool) $this->db->loadResult();
        }
        $this->db->setQuery($prevQuery);

        return $result;
    }

    public function getTeamMatchDays($teamId, $seasonId = null)
    {
        $teamId = (int) $teamId;
        $seasonIdInt = (int) $seasonId;
        $sqlSelectFmd = 'SELECT fmd.*
            FROM #__bl_matchday AS fmd
            INNER JOIN #__bl_match AS fm
                ON fm.m_id = fmd.id AND (fm.team1_id = '.$teamId.' || fm.team2_id = '.$teamId.')
            WHERE fmd.s_id = -1
        ';
        $sqlOrder = ' ORDER BY ordering';
        if ($seasonId == -1) {
            $query = $sqlSelectFmd.$sqlOrder;
        } else {
            $query = 'SELECT m.*
                FROM #__bl_season_teams AS t, #__bl_seasons AS s, #__bl_matchday AS m
                WHERE s.published = 1 AND m.s_id = s.s_id AND s.s_id = t.season_id
                  AND t.team_id = '.$teamId;
            if (is_null($seasonId)) {
                $query .= ' UNION '.$sqlSelectFmd;
            } else {
                $query .= ' AND s.s_id = '.$seasonIdInt;
            }
            $query .= $sqlOrder;
        }

        $this->db->setQuery($query);
        $mdays = $this->db->loadResult();

        return empty($mdays) ? array() : $mdays;
    }

    public static function getPlayerDefPhoto($pl_id, $pl_image = null)
    {
        $def_img = '';
        if (empty($pl_id)) {
            return $def_img;
        }
        $db = JFactory::getDBO();
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = '.$pl_id;
        $db->setQuery($query);
        $photos = $db->loadObjectList();
        if ($pl_image) {
            $query = 'SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = '.$pl_image;
            $db->setQuery($query);
            $def_img = $db->loadResult();
        } elseif (isset($photos[0])) {
            $def_img = $photos[0]->filename;
        }

        return $def_img;
    }
    public function getValSettingsServ($val)
    {
        $val_sett = ini_get($val);
        switch (substr($val_sett, -1)) {
            case 'M': case 'm': return (int) $val_sett * 1048576;
            case 'K': case 'k': return (int) $val_sett * 1024;
            case 'G': case 'g': return (int) $val_sett * 1073741824;
            default: return $val_sett;
        }
    }
}
