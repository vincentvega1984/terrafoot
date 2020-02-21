<?php

class modelJsportTournament
{
    public $row = null;
    public $lists = null;

    public function __construct($id)
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select("SELECT s.*, t.name, CONCAT(t.name, ' ', s.s_name) as tsname "
                .'FROM '.DB_TBL_SEASONS.' as s'
                .' JOIN '.DB_TBL_TOURNAMENT.' as t  ON s.t_id = t.id'
                .' WHERE t.id = '.intval($id)
                .' ORDER BY t.name, s.ordering');
    }
    public function getRow()
    {
        return $this->row;
    }
}
