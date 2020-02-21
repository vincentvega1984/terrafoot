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

?>
<div>

    <div>

        <div>
            <?php
                //var_dump($rows);
                $tabs = $rows->getTabs();
                jsHelperTabs::draw($tabs, $rows);
            ?>
        </div>
    </div>

    
    
    
</div>
