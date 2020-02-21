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
        {header}
    </div>
    <div>
        {tabs}
    </div>
    
    <div>
        <?php

        for ($intAAA = 0; $intAAA < count($rows); ++$intAAA) {
            ?>
            <div>
                <?php echo classJsportLink::matchday($rows[$intAAA]->m_name, $rows[$intAAA]->id);
            ?>
                                 
            </div>
            <?php

        }
        ?>
    </div>
    
    <?php
    //var_dump($rows);
    ?>
    
    
    
</div>
