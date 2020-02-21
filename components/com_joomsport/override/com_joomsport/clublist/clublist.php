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
    <div class="jstable">
        <?php 

            for ($intA = 0; $intA < count($rows); ++$intA) {
                ?>

                <div class="jstable-row">
                    <div class="jstable-cell">
                        <div class="jsDivLineEmbl">
                            <?php
                            echo jsHelperImages::getEmblem($rows[$intA]->getDefaultPhoto(), 0, '');
                echo jsHelper::nameHTML($rows[$intA]->getName(true));
                ?>

                        </div>

                    </div>

                </div>
            <?php

            }
            ?>
    </div>

</div>
