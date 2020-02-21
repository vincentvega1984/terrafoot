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
    <div class="searchMatchesDiv">
        <div>
            
            <div class="searchBar col-xs-12 col-lg-12">
                <?php if (isset($lists['filters']) && $lists['enable_search'] == '1') {
    ?>
                    <div <?php echo ($lists['apply_filters'] == true) ? ' style="display:block;"' : 'style="display:none;"';
    ?> id="jsFilterMatches">

                        
                          <div class="form-group srcTeam">
                            <label for="partic"><?php echo classJsportLanguage::get('BL_PARTIC');
    ?></label>
                            <select name="filtersvar[partic]" id="partic" >
                              <option value="0"><?php echo classJsportLanguage::get('BLFA_ALL');
    ?></option>
                              <?php
                              if (count($lists['filters']['partic_list'])) {
                                  foreach ($lists['filters']['partic_list'] as $key => $value) {
                                      echo '<option value="'.$key.'" '.((isset($lists['filtersvar']->partic) && $lists['filtersvar']->partic == $key) ? 'selected' : '').'>'.$value.'</option>';
                                  }
                              }
    ?>
                            </select>
                            <select name="filtersvar[place]" style="width:80px;" >
                              <option value="0"><?php echo classJsportLanguage::get('BLFA_ALL');
    ?></option>
                              <option value="1" <?php echo  (isset($lists['filtersvar']->place) && $lists['filtersvar']->place == 1) ? 'selected' : ''?>><?php echo classJsportLanguage::get('BLFA_HOME_SHTR');
    ?></option>
                              <option value="2" <?php echo  (isset($lists['filtersvar']->place) && $lists['filtersvar']->place == 2) ? 'selected' : ''?>><?php echo classJsportLanguage::get('BLFA_AWAY_SHTR');
    ?></option>
                            </select>
                          </div>
                          <div class="form-group srcDay">
                            <label for="matchDay"><?php echo classJsportLanguage::get('BLFA_MATCHDAY');
    ?></label>
                            <select name="filtersvar[mday]" id="matchDay">
                              <option value="0"><?php echo classJsportLanguage::get('BLFA_ALL');
    ?></option>
                              <?php
                              if (count($lists['filters']['mday_list'])) {
                                  foreach ($lists['filters']['mday_list'] as $mday) {
                                      echo '<option value="'.$mday->id.'" '.((isset($lists['filtersvar']->mday) && $lists['filtersvar']->mday == $mday->id) ? 'selected' : '').'>'.$mday->m_name.'</option>';
                                  }
                              }
    ?>
                            </select>
                          </div>
                          <div class="form-group srcDate">
                            <label for="date_from"><?php echo classJsportLanguage::get('BLFA_DATE');
    ?></label>
                            
                                <input type="text"  name="filtersvar[date_from]" value="<?php echo  (isset($lists['filtersvar']->date_from) && $lists['filtersvar']->date_from) ? $lists['filtersvar']->date_from : ''?>" class="form-control " id="date_from" placeholder="">
                                <input type="text"  name="filtersvar[date_to]" value="<?php echo  (isset($lists['filtersvar']->date_to) && $lists['filtersvar']->date_to) ? $lists['filtersvar']->date_to : ''?>" class="form-control" id="date_to" placeholder="">
                            
                          </div>
                          <div class="form-group">
                              <button type="button" class="btn btn-default pull-right" onclick="javascript:this.form.submit();"><i class="fa fa-search"></i><?php echo classJsportLanguage::get('BLFA_SEARCH');
    ?></button>
                          </div>
                    </div>
                <?php 
} ?>
            </div>        
            
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" ></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script>
    jQuery( function() { jQuery( 'input[id^="date_"]' ).datepicker(); } );
    </script>
</div>
