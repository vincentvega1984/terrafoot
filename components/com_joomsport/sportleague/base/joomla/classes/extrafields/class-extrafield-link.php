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
class classExtrafieldLink
{
    public static function getValue($ef)
    {
        $html = '';
        if ($ef->fvalue) {
            $html = "<a target='_blank' href='".(substr($ef->fvalue, 0, 7) == 'http://' ? $ef->fvalue : 'http://'.$ef->fvalue)."'>".$ef->fvalue.'</a>';
        }

        return $html;
    }
}
