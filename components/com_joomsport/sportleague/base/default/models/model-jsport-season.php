<?php

class modelJsportSeason
{
    public $season_id = null;
    public $lists = null;
    public $object = null;

    public function __construct($id)
    {
        $this->season_id = $id;

        if (!$this->season_id) {
            die('ERROR! SEASON ID not DEFINED');
        }
        global $jsDatabase;
        $this->object = $jsDatabase->selectObject('SELECT s.*, t.name,t.t_single,'
                ."t.tournament_type, CONCAT(t.name, ' ', s.s_name) as tsname,"
                .'t.descr as tourn_descr, t.logo as tourn_logo '
                .'FROM '.DB_TBL_SEASONS.' as s'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t  ON s.t_id = t.id'
                .' WHERE s.s_id = '.intval($this->season_id));
    }
    public function getRow()
    {
        return $this->object;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->season_id, '3', $this->season_id);

        return $this->lists;
    }
    public function getType()
    {
        return $this->object->tournament_type;
    }

    public function getSingle()
    {
        return $this->object->t_single;
    }
}
