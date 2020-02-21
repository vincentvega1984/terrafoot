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
<div class="jsOverflowHidden">
    <ul>
        
        <?php
        if (count($rows->lists['photos'])) {
            foreach ($rows->lists['photos'] as $photo) {
                ?>
                <li class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
                    <?php echo jsHelperImages::getEmblemBig($photo->filename, 2, 'emblInline', 120);
                ?>
                </li>
                <?php

            }
        }
        ?>
    </ul>
</div>