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
require_once JS_PATH_ENV_CLASSES.'class-jsport-participant.php';
require_once JS_PATH_OBJECTS.'class-jsport-group.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-getmdays.php';
require_once JS_PATH_ENV_CLASSES.'class-jsport-calc-player-list.php';
class classJsportTournMatches
{
    private $id = null;
    private $object = null;
    public $lists = null;
    public $pagination = null;
    const VIEW = 'calendar';

    public function __construct($object)
    {
        $this->object = $object;
        $this->id = $this->object->s_id;
    }

    private function loadObject()
    {
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getTable($group_id)
    {
        global $jsDatabase;

        $query = 'SELECT * FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id
                .' ORDER BY ordering';
        $table = $jsDatabase->select($query);
        if (!$table) {
            classJsportPlugins::get('generateTableStanding', array('season_id' => $this->id));
            $query = 'SELECT * FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id
                .' ORDER BY ordering';
            $table = $jsDatabase->select($query);
            //$table = $this->getTournColumnsVar($group_id);
        }
        $this->getExtraFieldsTable($table);
        
        if (isset($this->lists['columns']['curform_chk']) && $this->lists['columns']['curform_chk']) {
            $this->getTeamFormGraph($table);
        }

        return $table;
    }

    public function getTeamFormGraph(&$tbl)
    {
        global $jsDatabase;

        for ($intT = 0; $intT < count($tbl); ++$intT) {
            $tid = $tbl[$intT]->participant_id;
            if ($this->object->t_single == 1) {
                $query = "SELECT m.*, CONCAT(p1.first_name,' ',p1.last_name) as home, CONCAT(p2.first_name,' ',p2.last_name) as away"
                    .' FROM '.DB_TBL_MATCH.' as m'
                    .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                    .' JOIN '.DB_TBL_PLAYERS.' as p1 ON p1.id = m.team1_id'
                    .' JOIN '.DB_TBL_PLAYERS.' as p2 ON p2.id = m.team2_id'
                    .' WHERE md.s_id = '.$this->id
                    ." AND (m.team1_id = {$tid} OR m.team2_id = {$tid})"
                        ." AND m.m_played = '1' AND m.published = '1' AND md.t_type = 0 AND md.is_playoff = 0"
                    //." AND md.end_date < '".date("Y-m-d")." 23:59:59'"        
.' ORDER BY m.m_date DESC, m.m_time DESC, m.id LIMIT 5';
            } else {
                $query = 'SELECT m.*, p1.t_name as home, p2.t_name as away'
                    .' FROM '.DB_TBL_MATCH.' as m'
                    .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                    .' JOIN '.DB_TBL_TEAMS.' as p1 ON p1.id = m.team1_id'
                    .' JOIN '.DB_TBL_TEAMS.' as p2 ON p2.id = m.team2_id'
                    .' WHERE md.s_id = '.$this->id
                    ." AND (m.team1_id = {$tid} OR m.team2_id = {$tid})"
                        ." AND m.m_played = '1' AND m.published = '1' AND md.t_type = 0 AND md.is_playoff = 0"
                    //." AND md.end_date < '".date("Y-m-d")." 23:59:59'"        
.' ORDER BY m.m_date DESC, m.m_time DESC, m.id LIMIT 5';
            }

            $mdays = $jsDatabase->select($query);

            $mdays = array_reverse($mdays);
                    
            $from_str = '';

            for ($intA = 0; $intA < 5; ++$intA) {
                $from_str .= jsHelper::JsFormViewElement(isset($mdays[$intA]) ? ($mdays[$intA]) : null, $tid);
            }
            $tbl[$intT]->curform_chk = $from_str;
        }

        //return $from_str;
    }

    public function calculateTable($allcolumns = false, $group_id = 0)
    {
        global $jsDatabase;
        //get knockout
        $this->getKnock();
        $this->getPlayoffs();
        //get matchdays group

        $query = 'SELECT COUNT(md.id)'
                .' FROM '.DB_TBL_MATCHDAY.' as md '
                .' JOIN '.DB_TBL_SEASONS.' as s ON s.s_id = md.s_id'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t ON t.id = s.t_id'
                .' WHERE md.s_id = '.$this->id.' AND md.t_type = 0 AND is_playoff = 0';

        $mdays_count = $jsDatabase->selectValue($query);

        if ($mdays_count || !count($this->lists['knockout'])) {
            //get groups
            $groupsObj = new classJsportGroup($this->id);
            $groups = $groupsObj->getGroups();
            $this->lists['columns'] = $this->getTournColumns($allcolumns);
            $this->lists['groups'] = $groups;
            $columnsCell = array();
            //get participants
            if (count($groups)) {
                foreach ($groups as $group) {
                    if($group_id == 0 || $group_id == $group->id){
                        $columnsCell[$group->group_name] = $this->getTable($group->id);
                    }
                }
            } else {
                $columnsCell[] = $this->getTable(0);
            }
            $this->lists['columnsCell'] = $columnsCell;
        }
        //get season options
        //get variables for table view
        // multisort
        // save to db
    }

    public function getTournColumns($allcolumns)
    {
        global $jsDatabase;
        $this->lists['available_options'] = array(
            'played_chk' => classJsportLanguage::get('BL_TBL_PLAYED'),
            'emblem_chk' => '',
            'win_chk' => classJsportLanguage::get('BL_TBL_WINS'),
            'lost_chk' => classJsportLanguage::get('BL_TBL_LOST'),
            'draw_chk' => classJsportLanguage::get('BL_TBL_DRAW'),
            'otwin_chk' => classJsportLanguage::get('BL_TBL_EXTRAWIN'),
            'otlost_chk' => classJsportLanguage::get('BL_TBL_EXTRALOST'),
            'diff_chk' => classJsportLanguage::get('BL_TBL_DIFF'),
            'gd_chk' => classJsportLanguage::get('BL_TBL_GD'),
            'point_chk' => classJsportLanguage::get('BL_TBL_POINTS'),
            'percent_chk' => classJsportLanguage::get('BL_TBL_WINPERCENT'),
            'goalscore_chk' => classJsportLanguage::get('BL_TBL_TTGSC'),
            'goalconc_chk' => classJsportLanguage::get('BL_TBL_TTGCC'),
            'winhome_chk' => classJsportLanguage::get('BL_TBL_TTWHC'),
            'winaway_chk' => classJsportLanguage::get('BL_TBL_TTWAC'),
            'drawhome_chk' => classJsportLanguage::get('BL_TBL_TTDHC'),
            'drawaway_chk' => classJsportLanguage::get('BL_TBL_TTDAC'),
            'losthome_chk' => classJsportLanguage::get('BL_TBL_TTLHC'),
            'lostaway_chk' => classJsportLanguage::get('BL_TBL_TTLAC'),
            'pointshome_chk' => classJsportLanguage::get('BL_TBL_TTPHC'),
            'pointsaway_chk' => classJsportLanguage::get('BL_TBL_TTPAC'),
            'grwin_chk' => classJsportLanguage::get('BL_WINGROUP'),
            'grlost_chk' => classJsportLanguage::get('BL_LOSTGROUP'),
            'grwinpr_chk' => classJsportLanguage::get('BL_PRCGROUP'),
            'curform_chk' => classJsportLanguage::get('BLFA_CURFORM'),
            );

        $lists = array();

        $query = 'SELECT *'
                .' FROM '.DB_TBL_SEASON_OPTION
                .' WHERE s_id = '.$this->id." AND opt_name != 'equalpts_chk'"
                .($allcolumns ? '' : " AND opt_value='1'")
                .' ORDER BY ordering';
        $listsss = $jsDatabase->select($query);
        for ($i = 0;$i < count($listsss);++$i) {
            $vars = get_object_vars($listsss[$i]);
            $lists[$vars['opt_name']] = $vars['opt_value'];
        }

        return $lists;
    }

    public function getKnock()
    {
        global $jsDatabase;
        if($this->id){
            $query = 'SELECT md.*, t.t_single'
                    .' FROM '.DB_TBL_MATCHDAY.' as md '
                    .' JOIN '.DB_TBL_SEASONS.' as s ON s.s_id = md.s_id'
                    .' JOIN '.DB_TBL_TOURNAMENT.' as t ON t.id = s.t_id'
                    .' WHERE md.s_id = '.$this->id.' AND (md.t_type = 1 OR md.t_type = 2 OR md.t_type = 3)'
                    . ' AND s.published="1" AND t.published="1" '
                    .' ORDER BY md.ordering,md.id';

            $mdays = $jsDatabase->select($query);
            $this->lists['knockout'] = array();

            for ($intA = 0; $intA < count($mdays); ++$intA) {
                if ($mdays[$intA]->t_type == 1) {
                    require_once JS_PATH_OBJECTS.'matchdays'.DIRECTORY_SEPARATOR.'class-jsport-knockout.php';
                    $knockObj = new classJsportKnockout($mdays[$intA], $mdays[$intA]->t_single);
                    $this->lists['knockout'][] = $knockObj->lists['knockout'];
                } elseif ($mdays[$intA]->t_type == 2) {
                    require_once JS_PATH_OBJECTS.'matchdays'.DIRECTORY_SEPARATOR.'class-jsport-knockout_de.php';
                    $knockObj = new classJsportKnockoutDe($mdays[$intA], $mdays[$intA]->t_single);
                    $this->lists['knockout'][] = $knockObj->lists['knockout'];
                }elseif ($mdays[$intA]->t_type == 3) {
                    
                    require_once JS_PATH_OBJECTS.'matchdays'.DIRECTORY_SEPARATOR.'class-jsport-knockout_complex.php';
                    $knockObj = new classJsportKnockoutComplex($mdays[$intA], $mdays[$intA]->t_single);
                    $this->lists['knockout'][] = $knockObj->lists['knockout'];
                }
            }
        }
    }

    public function getPartById($partId)
    {
        $obj = new classJsportParticipant($this->id);
        $participant = $obj->getParticipiantObj($partId);

        return $participant;
    }

    //calendar
    public function getCalendar($options = array())
    {
        global $jsConfig;
        $this->lists['enable_search'] = $jsConfig->get('enbl_calmatchsearch');
        if (classJsportRequest::get('tmpl') == 'component') {
            $this->lists['enable_search'] = 0;
        }
        if ($this->lists['enable_search'] && $jsConfig->get('jscalendar_theme',0) == 0) {
            $this->lists['options']['tourn'] = '<a href="javascript:void(0);" id="aSearchFieldset">'.classJsportLanguage::get('BLFA_SEARCH_MATCHES').'</a>';
        }

        $options['season_id'] = $this->id;
        $filtersvar = classJsportRequest::get('filtersvar');

        if ($filtersvar) {
            classJsportSession::set('filtersvar_calendar_'.$this->id, json_encode($filtersvar));
        }
        $apply_filters = false;
        if (classJsportSession::get('filtersvar_calendar_'.$this->id)) {
            $filters = json_decode(classJsportSession::get('filtersvar_calendar_'.$this->id));

            $this->lists['filtersvar'] = $filters;
            if ($filters->mday) {
                $options['matchday_id'] = $filters->mday;
                $apply_filters = true;
            }
            if (isset($filters->partic) && $filters->partic) {
                $options['team_id'] = $filters->partic;
                $apply_filters = true;
            }
            if (isset($filters->date_from) && $filters->date_from) {
                $options['date_from'] = $filters->date_from;
                $apply_filters = true;
            }
            if (isset($filters->date_to) && $filters->date_to) {
                $options['date_to'] = $filters->date_to;
                $apply_filters = true;
            }
            if (isset($filters->place) && $filters->place) {
                $options['place'] = $filters->place;
                $apply_filters = true;
            }
        }
        $this->lists['apply_filters'] = $apply_filters;

        $this->lists['filters'] = array();
        $mdoptions = $options;
        if($jsConfig->get('jscalendar_theme',0) == 1){
            $mdoptions['ordering'] = 'md.ordering, md.m_name, md.id';
            $mday = $this->getLastMday();
            if (!isset($options['matchday_id'])) {
                $options['matchday_id'] = $mday;
                $this->lists['filtersvar'] = new stdClass();
                $this->lists['filtersvar']->mday = $mday;
            }  
            $this->lists['prevlink'] = $this->getPrev($options['matchday_id']);
            $this->lists['nextlink'] = $this->getNext($options['matchday_id']);
        }
        $this->lists['filters']['mday_list'] = classJsportgetmdays::getMdays($mdoptions);
        $partObj = new classJsportParticipant($this->id);
        $partic = $partObj->getParticipants();
        for ($intA = 0; $intA < count($partic); ++$intA) {
            $item = $partObj->getParticipiantObj($partic[$intA]->id);
            $this->lists['filters']['partic_list'][$partic[$intA]->id] = $item->getName(false);
        }
        $group_id = classJsportRequest::get('group_id');
        if($group_id){
            $link = classJsportLink::calendar('', $this->id, true).'?group_id='.$group_id;
            $options['group_id'] = $group_id;
        }else{
            $link = classJsportLink::calendar('', $this->id, true);
        }
        
        $pagination = new classJsportPagination($link);
        if($jsConfig->get('jscalendar_theme',0) == 0){
            $options['limit'] = $pagination->getLimit();
            $options['offset'] = $pagination->getOffset();
        }
        if (classJsportRequest::get('jsformat') == 'json'){
            $options['limit'] = 0;
            $options['matchday_id'] = null;
        }

        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList($this->object->t_single);
        if($jsConfig->get('jscalendar_theme',0) == 0){
            $pagination->setPages($rows['count']);
        }
        $this->pagination = $pagination;
        $matches = array();
        //require_once JS_PATH_ENV_CLASSES . 'class-jsport-calc-player-list.php';
        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->id, JSCONF_ENBL_MATCH_TOOLTIP);
                $match->getPlayerObj($match->lists['m_events_home']);
                $match->getPlayerObj($match->lists['m_events_away']);
                $matches[] = $match->getRow();
            }
        }

        return $matches;
    }

    public function getPlayoffs()
    {
        global $jsDatabase;

        $query = 'SELECT m.*,m.id as mid,m.team1_id as home, m.team2_id as away, md.m_name '
                .' FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m'
                .'  WHERE  m.m_id = md.id AND md.s_id = '.$this->id.''
                .' AND m.published = 1 AND md.is_playoff = 1'
                .' AND md.t_type = 0 '
                .' ORDER BY md.ordering,md.id,m.id';

        $rows = $jsDatabase->select($query);
        $matches = array();
        if ($rows) {
            foreach ($rows as $row) {
                $match = new classJsportMatch($row->id, false);
                $matches[] = $match->getRow();
                //$obj = new classJsportCalcPlayerList($row->id);
            }
        }
        $this->lists['playoffs'] = $matches;
    }

    public function getExtraFieldsTable(&$table)
    {
        $type = $this->object->t_single ? 0 : 1;
        $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable($type);
        if (count($ef) && count($table)) {
            for ($intA = 0; $intA < count($table); ++$intA) {
                for ($intB = 0; $intB < count($ef); ++$intB) {
                    $table[$intA]->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB]->id, $table[$intA]->participant_id, $type, $this->id);
                }
            }
        }
    }

    public function getCalendarView()
    {
        global $jsConfig;
        if($jsConfig->get('jscalendar_theme',0) == 1){
            return 'calendar_mday';
            exit();
        }
        return self::VIEW;
    }
    public function getLastMday(){
        $db = JFactory::getDBO();
        $query = "SELECT md.id"
                . " FROM #__bl_matchday as md"
                . " JOIN #__bl_match as m ON md.id = m.m_id "
                . " WHERE md.s_id = {$this->id}"
                . " AND m.m_played = '1'"
                . " ORDER BY m.m_date desc,m.m_time desc"
                . " LIMIT 1";
        $db->setQuery($query);        
        $mdID = (int) $db->loadResult();       
        if(!$mdID){
            $mdoptions['season_id'] = $this->id;
            $mdoptions['ordering'] = 'md.ordering, md.m_name, md.id';
            $mdays = classJsportgetmdays::getMdays($mdoptions);
            if(isset($mdays[0])){
                $mdID = $mdays[0]->id;
            }
        }
        return $mdID;
    }
    public  function getMdayArray(){
        $mdaysArray = array();
        $mdoptions['season_id'] = $this->id;
        $mdoptions['ordering'] = 'md.ordering, md.m_name, md.id';
        $mdays = classJsportgetmdays::getMdays($mdoptions);
        for($intA=0; $intA < count($mdays); $intA++){
            $mdaysArray[] =  $mdays[$intA]->id;
        }
        return $mdaysArray;
    }
    public  function getNext($mdId){
        $html = '&nbsp;';
        $mdays = $this->getMdayArray();
        if(isset($mdays[0])){
            $key = (int) array_search($mdId, $mdays);
            if(isset($mdays[$key+1])){
                $link = classJsportLink::calendar('', $this->id, true,'','&filtersvar[mday]='.$mdays[$key+1]);
                $html = '<a href="'.$link.'">'.JText::_('BLFA_JSMODMDNEXT').'</a>';
            }
        }
        return $html;
    }
    public  function getPrev($mdId){
        $html = '&nbsp;';
        $mdays = $this->getMdayArray();
        if(isset($mdays[0])){
            $key = (int) array_search($mdId, $mdays);
            if(isset($mdays[$key-1])){
                $link = classJsportLink::calendar('', $this->id, true,'&filtersvar[mday]='.$mdays[$key-1]);
                $html = '<a href="'.$link.'">'.JText::_('BLFA_JSMODMDPREV').'</a>';
            }
        }
        return $html;
    }
}
