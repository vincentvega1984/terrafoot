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
<div class="jsOverflowHidden" style="padding:0px 15px;">
    <?php
    if ($rows->logo) {
        ?>

            <div class="jsObjectPhoto rmpadd">

                    <?php echo jsHelperImages::getEmblemBig($rows->logo, 1, 'emblInline', '150', false);
        ?>



            </div>    

        <?php

    }
    ?>
    <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
        <?php echo $rows->descr;?>
    </div>
</div>    
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
            for ($intA = 0; $intA < count($lists['slist']); ++$intA) {
                ?>
            <tr>
                <td>
                    <?php echo classJsportLink::season($lists['slist'][$intA]->tsname, $lists['slist'][$intA]->s_id);
                ?>
                </td>
            </tr>
            <?php

            }
            ?>
        </tbody>
    </table>
</div>
