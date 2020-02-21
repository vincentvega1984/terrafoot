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
    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                    <?php echo classJsportLanguage::get('BLFA_NAME');?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($intA = 0; $intA < count($rows); ++$intA) {
                ?>
            <tr>
                <td>
                    <?php echo classJsportLink::tournament($rows[$intA]->name, $rows[$intA]->id);
                ?>
                </td>
            </tr>
            <?php

            }
            ?>
        </tbody>
    </table>
</div>
