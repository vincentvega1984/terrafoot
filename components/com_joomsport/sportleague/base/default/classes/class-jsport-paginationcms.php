<?php

class classJsportPaginationcms
{
    private $pagination = null;
    public function __construct()
    {
        $this->pagination = new stdClass();
    }

    public function getLimit()
    {
        return 50;
    }
    public function getOffset()
    {
        return 0;
    }
}
