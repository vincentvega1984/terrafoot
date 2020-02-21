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
<?php
$row = $rows->season;
$intM = 0;
if (isset($row->lists['columnsCell'])) {
    foreach ($row->lists['columnsCell'] as $group => $vals) {
        ++$intM;
        if ($group) {
            if(isset($vals[0])){
                $optionsPl = array("season_id" => $vals[0]->season_id, "group_id" => $vals[0]->group_id);
            }else{
                $optionsPl = array();
            }
            
            $kl = classJsportPlugins::get('addGroupCalendarButton', $optionsPl);
            echo '<h2 class="groups">'.$group. $kl.'</h2>';
        }
        if (count($vals)) {
            ?>
            <div class="table-responsive">
                <table class="table table-striped cansorttbl" id="jstable_<?php echo $intM;
            ?>">
                    <thead>
                        <tr>
                            <th class="jsalcenter jsNoWrap jsCell5perc">
                                <a href="javascript:void(0);">
                                    <?php echo classJsportLanguage::get('BL_TBL_RANK');
            ?> <i class="fa"></i>
                                </a>
                            </th>
                            
                            <th class="jsNoWrap">
                                <a href="javascript:void(0);">
                                    <?php echo classJsportLanguage::get($rows->getSingle()?'BL_PARTICS':'BLFA_ADMIN_TEAM');
            ?> <i class="fa"></i>
                                </a>
                            </th>
                            <?php
                            if (count($row->lists['columns'])) {
                                foreach ($row->lists['columns'] as $key => $value) {
                                    if ($key != 'emblem_chk') {
                                        if ($key != 'curform_chk') {
                                            ?>
                                    <th class="jsalcenter jsNoWrap jsCell5perc">
                                        <a href="javascript:void(0);">
                                            <?php echo $row->lists['available_options'][$key];
                                            ?> <i class="fa"></i>
                                        </a>
                                    </th>
                                <?php

                                        } else {
                                            ?>
                                            <th class="noSort jsalcenter jsNoWrap" width="135">
                                                
                                                    <?php echo $row->lists['available_options'][$key];
                                            ?>
                                                
                                            </th>
                                        <?php 
                                        }
                                    }
                                }
                            }
            ?>
                            <?php
                            if (isset($row->lists['ef_table']) && count($row->lists['ef_table'])) {
                                foreach ($row->lists['ef_table'] as $ef) {
                                    ?>
                                        <th nowrap>
                                            <a href="javascript:void(0);">
                                                <?php echo $ef->name;
                                    ?> <i class="fa"></i>
                                            </a>
                                        </th>
                                    <?php

                                }
                            }
            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rank = 1;
            foreach ($vals as $val) {
                $options = json_decode($val->options, true);

                $partObj = $row->getPartById($options['id']);
                $colortbl = '';

                if (isset($row->lists['tblcolors'][$rank])) {
                    $colortbl = $row->lists['tblcolors'][$rank];
                }

                $coloryteam = $partObj->getYourTeam();

                ?>
                        <tr <?php echo $coloryteam ? 'style="background-color:'.$coloryteam.'"' : '';
                ?>>
                            <td class="jsalcenter" <?php echo $colortbl ? 'style="background-color:'.$colortbl.'"' : '';
                ?>><?php echo $rank;
                ?></td>
                            
                            <td style="text-align:left;" class="jsNoWrap">
                                <div class="team">
                                <?php 
                                if (isset($row->lists['columns']['emblem_chk'])) {
                                    echo $partObj->getEmblem();
                                }
                                    echo $partObj->getName(true);
                                ?>
                                </div>
                            </td>
                            <?php
                            if (count($row->lists['columns'])) {
                                foreach ($row->lists['columns'] as $key => $value) {
                                    if ($key != 'emblem_chk') {
                                        if ($key != 'curform_chk') {
                                            ?>
                                    <td class="jsalcenter jsNoWrap">
                                        <?php echo isset($options[$key]) ? $options[$key] : '';
                                            ?>
                                    </td>
                                <?php

                                        } else {
                                            ?>
                                    <td class="jsalcenter jsNoWrap">
                                        <?php echo isset($val->$key) ? $val->$key : '';
                                            ?>
                                    </td>
                                        <?php

                                        }
                                    }
                                }
                            }
                ?>
                            <?php
                            if (isset($row->lists['ef_table']) && count($row->lists['ef_table'])) {
                                foreach ($row->lists['ef_table'] as $ef) {
                                    $efid = 'ef_'.$ef->id;
                                    ?>
                                    <td style="text-align:left;" class="jsNoWrap">
                                            <?php echo $val->{$efid};
                                    ?>
                                        </td>
                                    <?php

                                }
                            }
                ?>
                        </tr>
                        <?php
                        ++$rank;
            }
            ?>
                    </tbody>    
                </table>  
            </div>

<script>
    jQuery(document).ready(function() {
        var theHeaders = {}
        jQuery('#jstable_<?php echo $intM;
            ?>').find('th.noSort').each(function(i,el){
            theHeaders[jQuery(this).index()] = { sorter: false };
        });
        jQuery('#jstable_<?php echo $intM;
            ?>').tablesorter({headers: theHeaders});
    } );
</script>    
            <?php

        }
    }
    ?>
    <div class="matchExtraFields">
        <?php 
        if($rows->lists['bonuses']){
            echo classJsportLanguage::get('BLFA_BONUS');
            echo $rows->lists['bonuses'];
        }
        
        ?>
    </div>
    <div class="matchExtraFields">
        <?php 
        if($rows->lists['colors']){
            foreach($rows->lists['colors'] as $colors){
                echo '<div class="jstbl_legend">';
                echo '<div style="background-color:'.$colors->color.'">&nbsp;</div>';
                echo '<div>'.$colors->s_legend.'</div>';
                echo '</div>';
            }
        }
        
        ?>
    </div>


    <?php
}
if(isset($row->lists['knockout'])){
for ($intK = 0; $intK < count($row->lists['knockout']); ++$intK) {
    ?>
    <div>
        <?php echo $row->lists['knockout'][$intK];
    ?>
    </div>
    <?php

}
}
?>
