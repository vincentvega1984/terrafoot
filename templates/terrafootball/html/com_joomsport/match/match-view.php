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

global $jsConfig;
$width = $jsConfig->get('teamlogo_height');
$match = $rows;
$partic_home = $match->getParticipantHome();
$partic_away = $match->getParticipantAway();

$moptions = json_decode($match->object->options, true);
$jstimeline = json_decode($jsConfig->get('jstimeline',''));
if(isset($moptions['duration'])){
    $jstimeline->duration = $moptions['duration'];
}
if(isset($jstimeline->tldisplay) && $jstimeline->tldisplay == '1' && isset($jstimeline->duration) && $jstimeline->duration){
$hmev = json_encode($rows->lists['m_events_home']);
$awev = json_encode($rows->lists['m_events_away']);
if(count($rows->lists['m_events_home']) || count(($rows->lists['m_events_away']))){

?>
<div class="table-responsive timline-wrapper">
    <div id="jsTimeLineDivHome"></div>
    <div id="jsTimeLineDiv">
        <?php //echo $jstimeline->duration; // ?>
    </div>
    <div id="jsTimeLineDivAway"></div>
</div>    
<script>
    jQuery(document).on("ready", function(){
       var hm = <?php echo $hmev;?>; 
       var aw = <?php echo $awev;?>;
       var duration = parseInt('<?php echo $jstimeline->duration;?>');
       var stepJSD = jQuery('#jsTimeLineDiv').width()/duration;

       function calcJSTl(stepJSD){
           var same_minutes = [];
           for(var i=0; i<hm.length; i++){
                //var hmJS = jQuery.parseJSON(hm[i].obj);
                var imgEv = hm[i].objEvent.object.e_img;
                if(imgEv){
                    var img = '<?php echo JS_LIVE_URL_IMAGES_EVENTS?>'+imgEv;
                    if(parseInt(hm[i].minutes) >= 1){  
                        var tooltip = '<span calss="tooltip-inner"><span>'+hm[i].minutes+'\'</span><span><img src="'+img+'" width="16"></span><span>'+hm[i].obj.object.first_name+' '+hm[i].obj.object.last_name+'</span></span>'; 
                    
                        var dv = jQuery('<div />', {
                         "class": 'jsTLEvent',
                         text: ""});
                         dv.css("left",hm[i].minutes*stepJSD-6);
                         if(same_minutes[hm[i].minutes]){
                             dv.css('bottom',28*same_minutes[hm[i].minutes]);
                         }
                         var dvimg = jQuery('<div />',{"class": 'jsTLEventInner'});
                            
                         dvimg.append(jQuery('<img />', {
                        "class": "jsImgTL",    
                        "src": img,
                        "data-html":"true",
                            "data-toggle2":"tooltipJSF",
                            "data-placement":"top",
                            "title":"",
                            "data-original-title":tooltip
                        }));
                    
                         dv.append(dvimg);
                         dv.append(jQuery('<div />', {
                         "class": 'tlArrow'}));
                        jQuery('#jsTimeLineDivHome').append(dv);
                        if(!same_minutes[hm[i].minutes]){
                            same_minutes[hm[i].minutes] = 1;
                        }else{
                             same_minutes[hm[i].minutes] = parseInt(same_minutes[hm[i].minutes])+1;
                        } 
                    } 
                }
            }
            var same_minutes = [];
            for(var i=0; i<aw.length; i++){
                var imgEv = aw[i].objEvent.object.e_img;
                if(imgEv){
                    var img = '<?php echo JS_LIVE_URL_IMAGES_EVENTS?>'+imgEv;
                    if(parseInt(aw[i].minutes) >= 1){
                        
                        var tooltip = '<span calss="tooltip-inner"><span>'+aw[i].minutes+'\'</span><span><img src="'+img+'" width="16"></span><span>'+aw[i].obj.object.first_name+' '+aw[i].obj.object.last_name+'</span></span>'; 
                    
                        var dv = jQuery('<div />', {
                         "class": 'jsTLEvent',
                         text: ""});
                         dv.css("left",aw[i].minutes*stepJSD-6);
                         dv.append(jQuery('<div />', {
                         "class": 'tlArrow'}));
                         if(same_minutes[aw[i].minutes]){
                             dv.css('top',28*same_minutes[aw[i].minutes]);
                         } 
                         var dvimg = jQuery('<div />',{"class": 'jsTLEventInner'});
                         dvimg.append(jQuery('<img />', {
                        "class": "jsImgTL",    
                        "src": img,
                            "data-html":"true",
                            "data-toggle2":"tooltipJSF",
                            "data-placement":"bottom",
                            "title":"",
                            "data-original-title":tooltip
                        }));
                         dv.append(dvimg);
                        jQuery('#jsTimeLineDivAway').append(dv);
                        
                        if(!same_minutes[aw[i].minutes]){
                            same_minutes[aw[i].minutes] = 1;
                        }else{
                             same_minutes[aw[i].minutes] = parseInt(same_minutes[aw[i].minutes])+1;
                        } 
                    } 
                }    
            }
       }    
       calcJSTl(stepJSD);
       
       jQuery(window).trigger('resize');

        jQuery(window).resize(function(){
            jQuery('#jsTimeLineDivHome').html('');
            jQuery('#jsTimeLineDivAway').html('');
            var stepJSD = jQuery('#jsTimeLineDiv').width()/duration;
            calcJSTl(stepJSD);
        });
    });
</script>    
<?php
} 
}                                        
?>


<div class="table-responsive">

    <?php
    if (count($rows->lists['m_events_home']) || count($rows->lists['m_events_away'])) {
        ?>
    <div class="center-block jscenter timeline-stats">
        <h2 class="jsInlineBlock"><?php echo classJsportLanguage::get('BL_PBL_TIMELINE_STAT');?></h2>
    </div>

    <div class="jsPaddingBottom30 timeline-block additional-heading">
        <div class="jsOverflowHidden">
            
            <div class="jsInline">
            
                <div class="jsminhg50">
                    <div class="jstable-cell ">
                        <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';?>
                    </div>
                    <div class="jstable-cell ">
                        <?php echo ($partic_home) ? ($partic_home->getName(true)) : '';?>
                    </div>
                </div>

                <?php if ($rows->lists['m_events_display'] == 1){ ?>

                <table class="jsTblMatchTab firstTeam">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php echo classJsportLanguage::get('BLFA_QTY');?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_EVENT');?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_TIME');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($intP = 0; $intP < count($rows->lists['m_events_home']); ++$intP) {
                        ?>
                        <tr class="jsMatchTRevents">
                            <td class="evPlayerName">
                                <?php echo $rows->lists['m_events_home'][$intP]->obj->getName(true);
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->ecount;
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->objEvent->getEmblem(false);
                        ?>
                            </td>
                            
                            
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->minutes ? $rows->lists['m_events_home'][$intP]->minutes."'" : '';
                        ?>
                            </td>
                        </tr>    
                        <?php

                    }
        if (!count($rows->lists['m_events_home'])) {
            //echo "&nbsp";
        }
        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
            <div  class="jsInline">
                <div class="jsminhg50" style="text-align: right;">
                    
                    
                    <div class="jstable-cell" style="display:inline-block;">

                        <?php

                            echo ($partic_away) ? ($partic_away->getName(true)) : '';
        ?>
                    </div>
                    <div class="jstable-cell" style="display:inline-block;">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                </div>
                <?php 
                if($rows->lists['m_events_display'] == 1){
                ?>
                <table class="jsTblMatchTab">
                    <thead>
                        <tr>
                            <th><?php echo classJsportLanguage::get('BLFA_TIME');
        ?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_EVENT');
        ?></th>
                            <th><?php echo classJsportLanguage::get('BLFA_QTY');
        ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($intP = 0; $intP < count($rows->lists['m_events_away']); ++$intP) {
                        ?>
                        <tr class="jsMatchTRevents">
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->minutes ? $rows->lists['m_events_away'][$intP]->minutes."'" : '';
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->objEvent->getEmblem(false);
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->ecount;
                        ?>
                            </td>
                            <td class="evPlayerName">
                                <?php echo $rows->lists['m_events_away'][$intP]->obj->getName(true);
                        ?>
                            </td>
                            
                        </tr>    
                        <?php

                    }
        if (!count($rows->lists['m_events_away'])) {
            //echo "&nbsp";
        }
        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
        if($rows->lists['m_events_display'] == 0){

        ?>
        <table class="jsTblVerticalTimeLine table table-striped">
            <tbody>
                <?php 
                for($intE=0;$intE<count($rows->lists['m_events_all']);$intE++){
                ?>
                <tr>
                    <td>
                        <?php 
                        if($partic_home->object->id == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->obj->getName(true);
                        }else{
                            echo '<span class="timeline-empty-cell"></span>';
    }
                        
    ?>
                    </td>
                    <td>
    <?php
                        if($partic_home->object->id == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->objEvent->getEmblem(false);
                        }else{
                            echo '<span class="timeline-empty-cell"></span>';
                        }
                        
                        ?>
                    </td>
                    <td>
                        <?php echo $rows->lists['m_events_all'][$intE]->minutes ? $rows->lists['m_events_all'][$intE]->minutes."'" : '';?>
                    </td>
                    <td>
                        <?php 
                        if($partic_away->object->id == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->objEvent->getEmblem(false);
                        }else{
                            echo '<span class="timeline-empty-cell"></span>';
                        }
                        
                        ?>
                        
                    </td>
                    <td>
                        <?php 
                        if($partic_away->object->id == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->obj->getName(true);
                        }else{
                            echo '<span class="timeline-empty-cell"></span>';
                        }
                        
                        ?>
                        
                    </td>
                </tr>
                
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php
        }
    }
    ?>
    <?php
    if (count($rows->lists['team_events'])) {
        ?>
    <div class="center-block jscenter jsMarginBottom30">
        <h3 class="jsInlineBlock"><?php echo classJsportLanguage::get('BLFA_MATCHSTATS');
        ?></h3>
    </div>
    <div class="jsPaddingBottom30 jsTeamStat">
        <div class="jsOverflowHidden">
            <div class="jsInline">
                <div class="jsminhg50">
                    
                    <div class="jstable-cell ">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                    <div class="jstable-cell ">

                        <?php

                            echo ($partic_home) ? ($partic_home->getName(true)) : '';
        ?>
                    </div>
                </div>
            </div> 
            <div class="jsInline">
                <div class="jsminhg50" style="text-align: right;">
                    
                    
                    <div class="jstable-cell" style="display:inline-block;">

                        <?php

                            echo ($partic_away) ? ($partic_away->getName(true)) : '';
        ?>
                    </div>
                    <div class="jstable-cell" style="display:inline-block;">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                </div>
            </div>
            <div class="jstable">
                <?php
                for ($intP = 0; $intP < count($rows->lists['team_events']); ++$intP) {
                    $graph_sum = $rows->lists['team_events'][$intP]->home_value + $rows->lists['team_events'][$intP]->away_value;
                    $graph_home_class = ' jsGray';
                    $graph_away_class = ' jsRed';
                    if ($graph_sum) {
                        $graph_home = round(100 * $rows->lists['team_events'][$intP]->home_value / $graph_sum);
                        $graph_away = round(100 * $rows->lists['team_events'][$intP]->away_value / $graph_sum);
                        if ($graph_home > $graph_away) {
                            //$graph_home_class = ' jsRed';
                        } else {
                            //$graph_away_class = ' jsRed';
                        }
                    }
                    if (!$graph_home) {
                        $graph_home_class = '';
                    }
                    if (!$graph_away) {
                        $graph_away_class = '';
                    }
                    ?>
                    <div class="jstable-row jsColTeamEvents">
                        
                        <div class="jstable-cell jsCol5">
                            <div class="teamEventGraph">
                                <div class="teamEventGraphHome<?php echo $graph_home_class?>" style="width:<?php echo $graph_home?>%"><?php echo $rows->lists['team_events'][$intP]->home_value;
                    ?></div>
                            </div>
                            
                        </div>
                        <div class="jstable-cell jsCol6">

                            <div>
                                <?php 
                                echo $rows->lists['team_events'][$intP]->objEvent->getEmblem();
                    echo $rows->lists['team_events'][$intP]->objEvent->getEventName();
                    ?>
                            </div>
 
                        </div>
                        <div class="jstable-cell jsCol5">
                            <div class="teamEventGraph">
                                <div class="teamEventGraphAway<?php echo $graph_away_class?>" style="width:<?php echo $graph_away?>%"><?php echo $rows->lists['team_events'][$intP]->away_value;
                    ?></div>
                            </div>
                            
                        </div>
                        

                    </div>    
                    <?php

                }
        ?>
            </div>
            
        </div>
    </div>
    <?php

    }
    ?>
    <?php

    if (jsHelper::getADF($rows->lists['ef'])) {
        ?>
        <div class="center-block jscenter">
            <h2><?php echo classJsportLanguage::get('BL_EBL_VAL');
        ?></h2>
        </div>
        <div class="matchExtraFields jsPaddingBottom30">
            <?php
            $ef = $rows->lists['ef'];
        if (count($ef)) {
            foreach ($ef as $key => $value) {
                if ($value != null) {
                    echo '<div class="JSplaceM">';
                    echo  '<div class="labelEFM">'.$key.'</div>';
                    echo  '<div class="valueEFM">'.$value.'</div>';
                    echo  '</div>';
                }
            }
        }
        ?>
        </div>
    <?php

    }
    ?>
</div>