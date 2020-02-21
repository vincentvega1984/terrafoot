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
<div class="table-responsive">
    <div class="jsOverflowHidden">
    <?php

    if (count($rows->lists['players'])) {
        for ($intA = 0; $intA < count($rows->lists['players']); ++$intA) {
            $player = $rows->lists['players'][$intA];
            ?>
            <div class="jsplayerCart">
                <div class="imgPlayerCart">
                    <div class="innerjsplayerCart">
                        <?php echo $player->getEmblem(true, 2, 'emblInline', null, false);
            ?>
                    </div>
                </div>
                <div class="namePlayerCart"><?php echo jsHelper::nameHTML($player->getName(true));
            ?></div>
            </div>

            <?php

        }
    }
    ?>
    </div>
</div>
