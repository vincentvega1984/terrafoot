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
<form role="form" method="post" lpformnum="1">
    <!-- Matchday selectbox !-->
    <div class="searchMatchesDiv poolJSRight">
        <select name="filtersvar[mday]" id="matchDay" onchange="this.form.submit();">
          <?php
          
          if (count($lists['filters']['mday_list'])) {
              foreach ($lists['filters']['mday_list'] as $mday) {
                  echo '<option value="'.$mday->id.'" '.((isset($lists['filtersvar']->mday) && $lists['filtersvar']->mday == $mday->id) ? 'selected' : '').'>'.$mday->m_name.'</option>';
              }
          }
          ?>
        </select>
    </div>
    <!-- Matchday navigation !-->
    <div class="jscalMdayNav">
        <div class="jscalMdayPrev">
            <?php echo $lists['prevlink'];?>
        </div>
        <div class="jscalMdayNext">
            <?php echo $lists['nextlink'];?>
        </div>
    </div>
    <?php
    if(isset($rows[0])){
    $optionsPl = array("season_id" => $rows[0]->season_id, "group_id" => classJsportRequest::get('group_id'));
    classJsportPlugins::get('addCalendarBeforeMatchList', $optionsPl);
    }
    ?>
    <div class="table-responsive">
        <?php
        echo jsHelper::getMatches($rows, $lists['pagination']);
        ?>
    </div>
</form>
    
</div>
