<?php

require_once JS_PATH_OBJECTS.'class-jsport-season.php';
require_once JS_PATH_MODELS.'model-jsport-season.php';
class modelJsportMatch
{
    public $match_id = null;
    public $lists = null;
    public $row = null;
    public $season = null;

    public function __construct($match_id)
    {
        $this->match_id = $match_id;

        if (!$this->match_id) {
            die('ERROR! Match ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT m.*,md.m_name '
                .'FROM '.DB_TBL_MATCH.' as m'
                .' JOIN '.DB_TBL_MATCHDAY.' as md ON m.m_id = md.id'
                .' WHERE id = '.$this->match_id);
    }
    public function getSeasonID()
    {
        global $jsDatabase;

        $season_id = $jsDatabase->selectValue('SELECT s_id '
                .'FROM '.DB_TBL_MATCHDAY
                .' WHERE id = '.$this->row->m_id);

        return $season_id;
    }

    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->match_id, '2', 0);
        $this->getPhotos();
        $this->getPlayerEvents();
        $this->getTeamEvents();
        $this->getLineUps();
        $this->getMaps();
    }
    public function getPhotos()
    {
        global $jsDatabase;
        $query = 'SELECT p.ph_name as name,p.id as id,p.ph_filename as filename'
                .' FROM '.DB_TBL_ASSIGN_PHOTOS.' as ap, '.DB_TBL_PHOTOS.' as p'
                .' WHERE ap.photo_id = p.id AND cat_type = 3 AND cat_id = '.$this->match_id;

        $this->lists['photos'] = $jsDatabase->select($query);
    }

    public function getPlayerEvents()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        if ($season_id > 0) {
            $sObj = new modelJsportSeason($season_id);
            $single = $sObj->getSingle();
        } else {
            $single = $this->row->m_single;
        }
        $query = 'SELECT me.*,ev.*,p.id as playerid'
                        .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_PLAYERS.' as p'
                        ." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
                        .' AND me.match_id = '.$this->match_id.' AND '.($single ? 'me.player_id='.intval($this->row->team1_id) : 'me.t_id='.intval($this->row->team1_id))
                        .' ORDER BY me.eordering,CAST(me.minutes AS UNSIGNED)';

        $this->lists['m_events_home'] = $jsDatabase->select($query);

        $query = 'SELECT me.*,ev.*,p.id as playerid'
                        .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_PLAYERS.' as p'
                        ." WHERE me.player_id = p.id AND ev.player_event = '1' AND me.e_id = ev.id"
                        .' AND me.match_id = '.$this->match_id.' AND '.($single ? 'me.player_id='.intval($this->row->team2_id) : 'me.t_id='.intval($this->row->team2_id))
                        .' ORDER BY me.eordering, CAST(me.minutes AS UNSIGNED)';

        $this->lists['m_events_away'] = $jsDatabase->select($query);
    }
    public function getTeamEvents()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        if ($season_id > 0) {
            $sObj = new modelJsportSeason($season_id);
            $single = $sObj->getSingle();
        } else {
            $single = $this->row->m_single;
        }

        $query = 'SELECT DISTINCT ev.e_name as e_name, ev.*'
                .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_TEAMS.' as p'
                        .' WHERE me.t_id = p.id AND me.t_id IN ( '.intval($this->row->team1_id).', '.intval($this->row->team2_id).')'
                        ." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->match_id
                        .' ORDER BY ev.ordering,ev.e_name';

        $this->lists['team_events'] = $jsDatabase->select($query);

        for ($intA = 0; $intA < count($this->lists['team_events']); ++$intA) {
            $query = 'SELECT me.ecount'
                .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_TEAMS.' as p'
                        .' WHERE me.t_id = p.id AND me.t_id = '.intval($this->row->team1_id).''
                        ." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->match_id
                        .' AND ev.id = '.$this->lists['team_events'][$intA]->id
                        .' ORDER BY ev.ordering,ev.e_name';

            $this->lists['team_events'][$intA]->home_value = $jsDatabase->selectValue($query);

            $query = 'SELECT me.ecount'
                .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                        .' '.DB_TBL_EVENTS.' as ev,'
                        .' '.DB_TBL_TEAMS.' as p'
                        .' WHERE me.t_id = p.id AND me.t_id = '.intval($this->row->team2_id).''
                        ." AND ev.player_event = '0' AND me.e_id = ev.id AND me.match_id = ".$this->match_id
                        .' AND ev.id = '.$this->lists['team_events'][$intA]->id
                        .' ORDER BY ev.ordering,ev.e_name';

            $this->lists['team_events'][$intA]->away_value = $jsDatabase->selectValue($query);
        }
    }
    public function getLineUps()
    {
        global $jsDatabase;
        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_out,sb.minutes"
                .' FROM '.DB_TBL_PLAYERS.' as p,'
                                .' '.DB_TBL_SQUARD.' as s'
                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_out AND sb.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team1_id)
                .' WHERE s.team_id='.intval($this->row->team1_id)." AND s.mainsquard = '1'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard1'] = $jsDatabase->select($query);

        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_out,sb.minutes"
                .' FROM '.DB_TBL_PLAYERS.' as p,'
                                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_out AND sb.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team2_id)
                .' WHERE s.team_id='.intval($this->row->team2_id)." AND s.mainsquard = '1'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard2'] = $jsDatabase->select($query);
        var_dump($this->lists['squard2']);

        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_in,sb.minutes"
                .' FROM '.DB_TBL_PLAYERS.' as p,'
                                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_in AND sb.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team1_id)
                .' WHERE s.team_id='.intval($this->row->team1_id)." AND s.mainsquard = '0'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard1_res'] = $jsDatabase->select($query);

        $query = "SELECT p.*,CONCAT(p.first_name,' ',p.last_name) as name,p.def_img,p.id as playerid,sb.player_in,sb.minutes"
                .' FROM '.DB_TBL_PLAYERS.' as p,'
                                .' JOIN  '.DB_TBL_SQUARD.' as s ON p.id=s.player_id AND s.match_id='.$this->match_id
                                .' LEFT JOIN '.DB_TBL_SUBSIN.' as sb ON p.id=sb.player_in AND sb.match_id=s.match_id'
                                .' AND sb.team_id='.intval($this->row->team2_id)
                .' WHERE s.team_id='.intval($this->row->team2_id)." AND s.mainsquard = '0'"
                .' ORDER BY p.first_name,p.last_name';

        $this->lists['squard2_res'] = $jsDatabase->select($query);

        //subs in
        $query = 'SELECT s.*,p1.id as plin,p2.id as plout'
                        .' FROM '.DB_TBL_SUBSIN.' as s, '.DB_TBL_PLAYERS.' as p1, '.DB_TBL_PLAYERS.' as p2'
                        .' WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id='.$this->match_id.''
                        .' AND s.team_id='.intval($this->row->team1_id).' ORDER BY s.minutes';

        $this->lists['subsin1'] = $jsDatabase->select($query);

        $query = 'SELECT s.*,p1.id as plin,p2.id as plout'
                        .' FROM '.DB_TBL_SUBSIN.' as s, '.DB_TBL_PLAYERS.' as p1, '.DB_TBL_PLAYERS.' as p2'
                        .' WHERE p1.id=s.player_in AND p2.id=s.player_out AND s.match_id='.$this->match_id.''
                        .' AND s.team_id='.intval($this->row->team2_id).' ORDER BY s.minutes';
        $this->lists['subsin2'] = $jsDatabase->select($query);
    }

    public function getMaps()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();

        $query = 'SELECT m.*,mp.m_score1,mp.m_score2'
                .' FROM '.DB_TBL_SEAS_MAPS.' as sm'
                .' JOIN '.DB_TBL_MAPS.' as m ON m.id=sm.map_id'
                .' LEFT JOIN '.DB_TBL_MAPSCORE.' as mp ON m.id=mp.map_id AND mp.m_id='.intval($this->match_id)
                .' WHERE m.id=sm.map_id AND sm.season_id='.intval($season_id).''
                .' ORDER BY m.id';
        $this->lists['maps'] = $jsDatabase->select($query);
    }
}
