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
    <table class="table table-striped seasonList">
        <thead>
            <tr>
                <th>
                    <?php echo classJsportLanguage::get('BLFA_NAME');?>
                </th>
                <th><?php echo classJsportLanguage::get('BL_TEAMIND');?></th>
                <th><?php echo classJsportLanguage::get('BL_OPENREG');?></th>
                <th><?php echo classJsportLanguage::get('BL_STARTDATE');?></th>
                <th><?php echo classJsportLanguage::get('BL_ENDDATE');?></th>
                <th><?php echo classJsportLanguage::get('BL_PARTICS');?></th>
                <th style="text-align:center;"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($intA = 0; $intA < count($rows); ++$intA) {
                $unable_reg = $this->model->canJoin($rows[$intA]);
                $part_count = $this->model->partCount($rows[$intA]);
                ?>
            <tr>
                <td>
                    <?php echo classJsportLink::season($rows[$intA]->tsname, $rows[$intA]->s_id);
                ?>
                </td>
                <td><?php echo $rows[$intA]->t_single ? classJsportLanguage::get('BL_GTYPEIND') : classJsportLanguage::get('BLFA_TEAM');
                ?></td>
                <td class="open-reg"><?php echo $unable_reg ? '<img src="components/com_joomsport/img/ico/active.png" width="14" height="14" alt="" />' : '<img src="components/com_joomsport/img/ico/negative.png" width="14" height="14" alt="" />'?></td>
                <td><p class="event-date"><?php  if ($rows[$intA]->reg_start != '0000-00-00 00:00:00') {
     echo $rows[$intA]->reg_start;
 }
                ?></p></td>
                <td><p class="event-date"><?php  if ($rows[$intA]->reg_end != '0000-00-00 00:00:00') {
     echo $rows[$intA]->reg_end;
 }
                ?></p></td>
                <td><?php echo $part_count.($rows[$intA]->s_participant ? '('.$rows[$intA]->s_participant.')' : '');
                ?></td>
                <td>
                        <?php

                        if ($unable_reg) {
                            $link = classJsportLink::joinseason($rows[$intA]->s_id);
                            echo "<a href='".$link."' class='join-button'><button type='button' class='btn btn-default'><i class='arrow-right'></i>".classJsportLanguage::get('BL_JOIN').'</button></a>';
                        } else {
                            echo '&nbsp;';
                        }
                ?>
                </td>
            </tr>
            <?php

            }
            ?>
        </tbody>
    </table>
</div>
