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

class classJsportPagination
{
    public $pages = 1;
    public $link = '';
    public $limit = 20;
    public $offset = 0;
    public $additvars = array();
    public $limit_array = array(5, 10, 20, 25, 50, 100, 0);

    public function __construct($link)
    {
        $mainframe = JFactory::getApplication();
         $mainframe = JFactory::getApplication();

        // Get the pagination request variables
        $this->limit = $mainframe->getUserStateFromRequest('com_joomsport.jslimit', 'jslimit', $mainframe->getCfg('list_limit'), 'int');
        
        //$this->limit = $mainframe->getCfg('list_limit');

        if (classJsportRequest::get('jslimit') != null) {
            $this->limit = classJsportRequest::get('jslimit');
        }
        if (classJsportRequest::get('jsformat') == 'json'){
            $this->limit = 0;
        }
        
        $this->link = $link;
    }
    public function setLimit()
    {
        return $this->limit;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getOffset()
    {
        $this->offset = $this->getLimit() * $this->getCurrentPage();

        return $this->offset;
    }

    public function setPages($count)
    {
        $limit = $this->getLimit();
        if ($limit) {
            $this->pages = ceil($count / $limit);
        } else {
            $this->pages = 1;
        }
    }
    public function getCurrentPage()
    {
        global $jsDatabase;
        $page = (int) classJsportRequest::get('page');
        $calfilter = classJsportRequest::get('calfilter');
        
        if(!isset($_GET['page']) && !$calfilter && isset($_REQUEST['view']) &&  $_REQUEST['view'] == 'calendar'){
            $season_id = (int) classJsportRequest::get('sid');
            $query = 'SELECT COUNT(m.id) '
                .'FROM '.DB_TBL_MATCHDAY.' as md'
                .' JOIN '.DB_TBL_MATCH.' as m ON md.id = m.m_id'
                
                .' WHERE 1=1'
                
                .(isset($season_id) && $season_id ? ' AND md.s_id = '.$season_id : '')
                ." AND m.m_played = '1'"
                
                .' AND (m.team1_id != 0 OR m.team2_id != 0)';
                
               
            $matches_played = $jsDatabase->selectValue($query);
            
            if($matches_played 
                    ){
                if($this->limit){
                    $page = ceil($matches_played / $this->limit);
                }else{
                    $page = 1;
                }
                $page = intval($page)>1?$page:1;
                
            }
        }else{
            $page = $page ? $page : 1;
        }

        return ($page - 1 > 0)?($page - 1):0;
    }
    public function getLimitBox($val = '')
    {
        $kl = '<div class="display col-xs-12 col-sm-12 col-md-4 col-lg-4 text-right" style="min-width: 170px; float: right;"><label>'.classJsportLanguage::get('BL_TAB_DISPLAY').'</label>';
        $jas = 'onchange = "this.form.submit();"';
        foreach ($this->limit_array as $lim) {
            $limbox[] = JHTML::_('select.option', $lim, $lim ? $lim : classJsportLanguage::get('BLFA_ALL'), 'id', 'name');
        }
        $kl .= JHTML::_('select.genericlist', $limbox, 'jslimit'.$val, 'class="pull-right" style="width:70px;" size="1" '.$jas, 'id', 'name', $this->limit);
        $kl .= '</div>';

        return $kl;
    }
    public function setAdditVar($name, $var)
    {
        $this->additvars[$name] = $var;
    }
}
