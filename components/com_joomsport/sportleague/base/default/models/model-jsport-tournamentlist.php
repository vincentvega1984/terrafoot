<?php

class modelJsportTournamentlist
{
    public $row = null;
    public $lists = null;

    public function __construct()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select('SELECT t.* '
                .'FROM '.DB_TBL_TOURNAMENT.' as t '
                .' ORDER BY t.name, t.id');
    }
    public function getRow()
    {
        return $this->row;
    }
}
