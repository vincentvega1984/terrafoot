<?php
/* ------------------------------------------------------------------------
  # JoomSport Professional
  # ------------------------------------------------------------------------
  # BearDev development company
  # Copyright (C) 2011 JoomSport.com. All Rights Reserved.
  # @license - http://joomsport.com/news/license.html GNU/GPL
  # Websites: http://www.JoomSport.com
  # Technical Support:  Forum - http://joomsport.com/helpdesk/
  ------------------------------------------------------------------------- */
// no direct access
defined('_JEXEC') or die('Restricted access');
if (isset($this->message)) {
    $this->display('message');
}
$row = $this->row;
$lists = $this->lists;
$s_id = $lists['s_id'];
$match = $lists['match'];
global $Itemid;

if ($this->acl == 1) {
    $ctrl = 'admin';
} elseif ($this->acl == 2) {
    $ctrl = 'moder';
} else {
    $ctrl = 'users';
}

$Itemid = JRequest::getInt('Itemid');
$doc = JFactory::getDocument();

?>
<div id="joomsport-container">
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php echo $lists['panel']; ?>
    </nav>
    <!-- /.navbar -->

    <div class="main <?php echo $lists['t_type'] ? 'editDraw' : 'editMatchDay' ?>">
        <div class="heading col-xs-12 col-lg-12">
            <h2 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $row->id ? JText::_('BLFA_MDAY_EDIT') : JText::_('BLFA_MDAY_NEW'); ?></h2>

            <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right" style="text-align:right;">
                <?php if ($this->acl == 1): ?>                
                    <form action='<?php echo JURI::base(); ?>index.php?option=com_joomsport&task=edit_matchday&controller=moder&Itemid=<?php echo $Itemid ?>' method='post' name='chg_team'>                                
                        <?php if (isset($this->lists['seass_filtr'])): ?>
                            <label class="selected"><?php echo $this->lists['tourn_name']; ?></label>
                        <?php endif; ?>
                        <div class="data">
                            <?php if (isset($this->lists['seass_filtr'])): ?>                                
                                <?php echo $this->lists['seass_filtr']; ?>
                            <?php endif; ?>
                            <?php 
                            if (isset($this->lists['tm_filtr'])) {
                                echo $this->lists['tm_filtr'];
                            }
                            ?>
                        </div>
                    </form>
                    <form action="<?php echo JUri::base().'index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid='.$lists['tid'].'&Itemid='.$Itemid; ?>" method="post" name="filtrForm">
                        <div class="data">
                            <?php if (isset($lists['md_filtr']) && $lists['md_filtr']) {
    ?>
                                <?php echo $lists['md_filtr'];
    ?>
                            <?php 
} ?>
                        </div>
                    </form>
                <?php endif; ?>
                <?php if ($this->acl == 3): ?>
                    <form action="<?php echo 'index.php?option=com_joomsport&view=moderedit_umatchday&controller=users&Itemid='.$Itemid; ?>" method="post" name="filtrForm">
                        <label class="selected"><?php echo JText::_('BLFA_FILTERS') ?></label>
                        <div class="data">
                            <?php echo $lists['seas_filtr']; ?>
                            <?php if ($lists['md_filtr']) {
    ?>
                                <?php echo $lists['md_filtr'];
    ?>
                            <?php 
} ?>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if ($this->acl == 2): ?>
                    <form action='<?php echo JURI::base(); ?>index.php?option=com_joomsport&task=edit_matchday&tid=<?php echo $lists['tid']?>&controller=moder&Itemid=<?php echo $Itemid ?>' method='post' name='chg_team'>
                        <?php if (isset($this->lists['seass_filtr_nofr'])) {
    ?>
                            <label class="selected"><?php echo $this->lists['tourn_name'];
    ?></label>
                        <?php 
} ?>
                        <div class="data">
                            <?php echo $this->lists['tm_filtr']; ?>
                            <?php if (isset($this->lists['seass_filtr_nofr'])) {
    ?>
                                <?php echo $this->lists['seass_filtr_nofr'];
    ?>
                            <?php echo $lists['md_filtr'];
} ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>


        <div class="navbar-link col-xs-12 col-lg-12">
            <?php if ($this->acl == 2): ?>            
                <ul>
                    <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_team&tid='.$lists['tid'].'&Itemid='.$Itemid); ?>" title=""><?php echo JText::_('BLFA_TEAM') ?></a></li>
                    <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid='.$lists['tid'].'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                    <?php if ($lists['moder_addplayer']) {
    ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=admin_player&tid='.$lists['tid'].'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_PLAYER') ?></a></li>
                    <?php 
} ?>
                </ul>
                <!-- </tab box> -->
            <?php endif; ?>
            <?php if ($this->acl == 3): ?>
                <ul>
                    <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&task=regplayer&Itemid='.$Itemid); ?>" title=""><?php echo JText::_('BLFA_PLAYERR') ?></a></li>
                    <li class="active"><a href="<?php echo juri::base().('index.php?option=com_joomsport&view=edit_matchday&controller=users&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                </ul>
            <?php endif; ?>            
        </div>
        <div class="tools col-xs-12 col-lg-12 text-right"> 
            <?php if ($this->acl == 1): ?>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_APPLY') ?>" onclick="javascript:submitbutton('matchday_apply');
                        return false;"><i class="apply"></i> <?php echo JText::_('BLFA_APPLY') ?></a>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_SAVE') ?>" onclick="javascript:submitbutton('matchday_save');
                        return false;"><i class="save"></i> <?php echo JText::_('BLFA_SAVE') ?></a>
                <a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_matchday&sid='.$s_id.'&Itemid='.$Itemid) ?>" title="<?php echo JText::_('BLFA_CLOSE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_CLOSE') ?></a>
            <?php endif; ?>
            <?php if (($this->acl == 2 && isset($this->lists['seass_filtr'])) || $this->acl == 3): ?>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_SAVE') ?>" onclick="javascript:submitbutton('matchday_save');
                        return false;"><i class="save"></i> <?php echo JText::_('BLFA_SAVE') ?></a>
               <?php endif; ?>    
        </div>
        <div class="jsClear"></div>
        <div >
            <form name="adminForm" id="adminForm" action="" class="form-horizontal" method="post">
                <?php if ($this->acl == 1) {
    ?>
                    <div class="form-group">
                        <label for="inputMatchDay" class="col-sm-2 control-label"><?php echo JText::_('BLFA_MATCHDAYNAME');
    ?></label>
                        <div class="col-sm-6">
                            <input type="text" maxlength="255"   name="m_name" value="<?php echo htmlspecialchars($row->m_name) ?>" />                        
                        </div>
                    </div>
                    <?php if (!$lists['t_type']) {
    ?>
                        <div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_ISPLAYOFF');
    ?></label>
                            <div class="col-sm-6">                    
                                <input type="radio" name="is_playoff" id="optionsRadios1" value="0" <?php echo!$row->is_playoff ? 'checked="checked"' : '' ?>/>
                                <?php echo JText::_('JNO');
    ?>
                                <input type="radio" name="is_playoff" id="optionsRadios1" value="1" <?php echo $row->is_playoff ? 'checked="checked"' : '' ?>/>
                                <?php echo JText::_('JYES');
    ?> 
                            </div>
                        </div>			
                    <?php 
}
    ?>
                <?php 
} ?>
                <?php
                if ($lists['t_type'] && $this->acl == 1) {
                    if ($lists['t_type'] == 1) {
                        $javascript = 'jQuery.post( \''.JUri::Base().'index.php?tmpl=component&option=com_joomsport&task=get_format&controller=admin&format=row&sid='.$s_id.'&t_single='.$lists['t_single'].'&fr_id=\'+jQuery(\'#format_post\').val(), function( data ) {jQuery(\'#mapformat\').html( data );});';
                    } elseif ($lists['t_type'] == 2) {
                        $javascript = 'jQuery.post( \''.JUri::Base().'index.php?tmpl=component&option=com_joomsport&task=get_formatkn&controller=admin&format=row&sid='.$s_id.'&t_single='.$lists['t_single'].'&fr_id=\'+jQuery(\'#format_post\').val(), function( data ) {jQuery(\'#mapformat\').html( data );});';
                    }
                    ?>
                    <div class="form-group">
                        <label for="inputMatchDay" class="col-sm-2 control-label"><?php echo $lists['t_type'] == 1 ? JText::_('BLFA_KNOCK') : JText::_('BLFA_DOUBLE') ?> </label>
                        <div class="col-sm-4">
                            <?php echo $lists['format'];
                    ?>
                        </div>
                        <div class="col-sm-6">
                            <button onclick="<?php echo $javascript;
                    ?>" type="button" class="btn btn-default">Generate</button>
                        </div>
                    </div>

                    <!--draw starts-->
                    <div class="table-responsive col-xs-12 col-lg-12" id="mapformat">
                        <?php
                        if (count($lists['match']) && isset($lists['knock_layout'])) {
                            echo $lists['knock_layout'];
                        }
                    ?>
                    </div>
                    <!--draw ends--> 
                <?php 
                } else {
                    ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center"><?php echo JText::_('BLFA_MATCHRESULTS');
                    ?></th>
                                </tr>
                                
                            </thead>
                            <tbody id="new_matches">
                                <?php
                                $cx = 0;
                    if (count($match)) {
                        foreach ($match as $curmatch) {
                            $moder_or_pl = ($this->acl == 3) ? ($curmatch->team1_id == $this->lists['usr']->id) : (in_array($curmatch->team1_id, $this->lists['teams_season']));
                            $moder_or_pl2 = ($this->acl == 3) ? ($curmatch->team2_id == $this->lists['usr']->id) : (in_array($curmatch->team2_id, $this->lists['teams_season']));

                            switch ($this->acl) {
                                            case '2':
                                                $match_link = 'index.php?option=com_joomsport&amp;controller=moder&amp;view=edit_match&amp;cid[]='.$curmatch->id.'&amp;controller=moder&amp;sid='.$s_id.'&amp;tid='.$lists['tid'].'&Itemid='.$Itemid;
                                                break;
                                            case '3':
                                                $match_link = jUri::base().'index.php?option=com_joomsport&amp;controller=users&amp;view=moderedit_umatch&amp;cid[]='.$curmatch->id.'&Itemid='.$Itemid;
                                                break;
                                            default:
                                                $match_link = 'index.php?option=com_joomsport&amp;controller=admin&amp;view=edit_match&amp;cid[]='.$curmatch->id.'&amp;controller=admin&amp;sid='.$s_id.'&amp;Itemid='.$Itemid;
                                        }
                            ?>
                                        <tr class="<?php echo $cx % 2 ? 'active' : 'noBB' ?>">
                                            <td class="tdJsRemove" rowspan="2">
                                                <?php
                                                if ($this->acl != 1 && ($lists['t_type'] == 1 || ($lists['jsmr_mark_played'] == 0 && $curmatch->m_played == 1) || $lists['t_type'] == 2)) {
                                                    echo '<input type="hidden" name="match_id[]" value="'.$curmatch->id.'" />';
                                                } else {
                                                    echo '<input type="hidden" name="match_id[]" value="'.$curmatch->id.'" /><button type="button" onclick="Delete_tbl_row_md(this); return false;" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only">'.JText::_('BLFA_DELETE').'</span></button>';
                                                }
                            ?>
                                            </td>
                                            <td><input type="hidden" name="home_team[]" value="<?php echo $curmatch->team1_id ?>" /><?php echo $curmatch->home_team ?></td>
                                            <td>
                                                <?php
                                                if ($this->acl == 1) {
                                                    echo '<input class="score" type="text" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /> : <input class="score" type="text" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                                                } else {
                                                    if ($lists['t_type'] == 1 || $lists['t_type'] == 2) {
                                                        echo '<input class="score" disabled="true" type="text" readonly="true" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" /> : <input class="score" disabled="true" type="text" readonly="true" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" />';
                                                    } else {
                                                        $hidden1 = false;
                                                        $hidden2 = false;
                                                        if (($lists['jsmr_mark_played'] == 0 && $curmatch->m_played) == 1 || (($this->lists['jsmr_editresult_opposite'] == 0 && !$moder_or_pl) || ($this->lists['jsmr_editresult_yours'] == 0 && $moder_or_pl))) {
                                                            $hidden1 = true;
                                                        }
                                                        if (($lists['jsmr_mark_played'] == 0 && $curmatch->m_played) == 1 || (($this->lists['jsmr_editresult_opposite'] == 0 && !$moder_or_pl2) || ($this->lists['jsmr_editresult_yours'] == 0 && $moder_or_pl2))) {
                                                            $hidden2 = true;
                                                        }
                                                        echo '<input class="score" type="text" name="home_score[]" value="'.$curmatch->score1.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" '.($hidden1 ? 'disabled="true"' : '').' />';
                                                        echo ' : <input class="score" type="text" name="away_score[]" value="'.$curmatch->score2.'" size="3" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" '.($hidden2 ? 'disabled="true"' : '').' />';
                                                    }
                                                }
                            ?>                                                
                                            </td>
                                            <td>
                                            <?php
                                            if ($lists['s_enbl_extra']) {
                                                echo '<input type="checkbox" name="extra_time[]" value="'.(($curmatch->is_extra) ? 1 : 0).'" '.(($curmatch->is_extra) ? 'checked' : '').' />';
                                                echo '<img src="administrator/components/com_joomsport/img/quest.png" />';
                                            }
                            ?>
                                            </td>    
                                            <td><input type="hidden" name="away_team[]" value="<?php echo $curmatch->team2_id ?>" /><?php echo $curmatch->away_team ?></td>
                                            <td>
                                                <?php
                                                if ($lists['jsmr_mark_played'] == 0 && $curmatch->m_played == 1 && $this->acl != 1) {
                                                    echo '&nbsp;';
                                                } else {
                                                    echo '<a href="'.$match_link.'">'.JText::_('BLFA_MATCHDETAILS').'</a>';
                                                }
                            ?>
                                            </td>
                                        </tr>
                                        <tr class="<?php echo $cx % 2 ? 'active' : 'noBB' ?> jsmfirstTR">
                                            <td>
                                                <input class="jsdtime" type="date" name="match_data[]" maxlength="5" size="12" value="<?php echo $curmatch->m_date ?>" />
                                            </td>
                                            <td>
                                                <input class="jstime" type="text" name="match_time[]" maxlength="5" size="12" value="<?php echo substr($curmatch->m_time, 0, 5) ?>" />
                                            </td>
                                            <td colspan="2"><?php echo $curmatch->venue_name ?></td>
                                            <td>
                                                <?php
                                                if ($lists['jsmr_mark_played'] == 1) {
                                                    //$selvals = $this->_lists['m_played_i'] = array_merge($tmp_arr,$selvals);
                                                    echo JHTML::_('select.genericlist',   $lists['m_played_i'], 'match_played[]', 'class="inputbox" size="1"', 'id', 'value', $curmatch->m_played);

                                                    //echo JText::_('BLFA_ISPLAYED') . ' <input type="checkbox" name="match_played[]" value="' . ($curmatch->m_played ? 1 : 0) . '" ' . ($curmatch->m_played ? "checked" : "") . ' />';
                                                }
                            ?>
                                            </td>
                                            
                                        </tr>
                                        
                                        <?php
                                        ++$cx;
                        }
                    }
                    ?>
                            </tbody>
                        </table>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                    <?php
                    if ((($lists['t_type'] == 1 || $lists['t_type'] == 2) && $this->acl != 1) || ($lists['moder_create_match']!='1' &&  $this->acl == 2)) {
                    } else {
                        ?>
                        <div class="table-responsive">
                            <table class="table jsaddmatch">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-center"><?php echo JText::_('BLFA_ADDMATCHRESULTS');
                        ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong><?php echo $lists['t_single'] ? JText::_('BLFA_HOMEPLAYER') : JText::_('BLFA_HOMETEAM');
                        ?></strong></td>
                                        <td><?php echo $lists['teams1'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo JText::_('BLFA_SCORE');
                        ?></strong></td>
                                        <td>
                                            <input type="text" class="score" name="add_score1" id="add_score1" placeholder=" ">
                                            :
                                            <input type="text" class="score" name="add_score2" id="add_score2" placeholder="">
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo $lists['t_single'] ? JText::_('BLFA_AWAYPLAYER') : JText::_('BLFA_AWAYTEAM');
                        ?></strong></td>
                                        <td><?php echo $lists['teams2'] ?></td>
                                    </tr>
                                      
                                    <?php
                                    if ($lists['jsmr_mark_played'] == 1) {
                                        ?>
                                        <tr>
                                        <td><strong><?php echo JText::_('JSTATUS');
                                        ?></strong></td>
                                        <td id="jstmplayedclone"><?php echo $lists['m_played'];
                                        ?></td>
                                        </tr>
                                    <?php 
                                    }
                        ?>
                                    <?php if ($lists['s_enbl_extra']) {
    ?>
                                        <tr>
                                        <td><strong><?php echo JText::_('BLFA_EXTRATIMEF');
    ?></strong></td>
                                        <td><input type="checkbox" name="extra_timez" id="extra_timez" /></td>
                                        </tr>					
                                    <?php 
}
                        ?> 
                                     <tr>
                                        <td><strong><?php echo JText::_('BLFA_DATE');
                        ?></strong></td>
                                        <td><input type="date" class="date" id="tm_date" name="tm_date"></td>
                                    </tr>     
                                    <tr>
                                        <td><strong><?php echo JText::_('BLFA_TIME');
                        ?></strong></td>
                                        <td><input type="text" class="time" name="match_time_new" id="match_time_new" maxlength="5" size="12" value="00:00" /></td>
                                    <tr>
                                        <td><strong><?php echo JText::_('BLFA_VENUE');
                        ?></strong></td>
                                        
                                        <td>
                                            <?php echo $lists['venue_name'];
                        ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group text-right">
                            <div class="col-xs-12 ccol-sm-12">
                                <button type="button" onclick="bl_add_match();
                            return false;" class="btn"><?php echo JText::_('BLFA_ADD');
                        ?></button>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    <?php 
                    }
                    ?>		
                <?php 
                } ?>
                <input type="hidden" name="return_sh" value="0" />
                <input type="hidden" name="task" value="admin_matchday" />
                <input type="hidden" name="id" value="<?php echo $row->id ?>" />
                <?php if ($row->id) {
    ?>
                <input type="hidden" name="format_post" value="<?php echo $row->k_format ?>" />
                <?php 
} ?>

                <input type="hidden" name="isapply" value="0" />
                <?php if ($this->acl == 1) {
    ?>
                    <input type="hidden" name="sid" value="<?php echo $s_id;
    ?>" />
                    <input type="hidden" name="t_single" value="<?php echo $lists['t_single'] ?>" />
                    <input type="hidden" name="t_knock" value="<?php echo $lists['t_type'] ?>" />
                    <!--<input type="hidden" name="t_type" value="<?php //echo $lists['t_type']  ?>" />-->
                <?php 
} else {
    ?>
                    <input type="hidden" name="tid" value="<?php echo $lists['tid'] ?>" />
                    <input type="hidden" name="sid" value="<?php echo $s_id ?>" />
                    <input type="hidden" name="mid" value="<?php echo $lists['mid'] ?>" />
                    <input type="hidden" name="t_knock" value="<?php echo $lists['t_type'] ?>" />
                    <input type="hidden" name="t_type" value="<?php echo $lists['t_type'] ?>" />
                <?php 
} ?>
                <?php echo JHTML::_('form.token'); ?>
            </form>
          </div> 
        <div class="jsClear"></div>
    </div>
</div>
</div>
<script type="text/javascript">
<!--
                function in_array(what, where) {
                    for (var i = 0, length_array = where.length; i < length_array; i++)
                        if (what == where[i])
                            return true;
                    return false;
                }
                function submitbutton(pressbutton) {
                    var form = document.adminForm;
                    if (pressbutton == 'matchday_save' || pressbutton == 'matchday_apply') {
                        if ('<?php echo $this->acl ?>' == 1) {
                            if (form.m_name.value == "" || form.sid.value == 0) {
                                alert("<?php echo JText::_('BL_ENTRNTS'); ?>");
                                return false;
                            }

                            if ('<?php echo $lists['t_type'] ?>' == 1 || '<?php echo $lists['t_type'] ?>' == 2) {
                                var arrpl = new Array();
                                var partip = eval("document.adminForm['teams_kn[]']");
                                var partip_aw = eval("document.adminForm['teams_kn_aw[]']");
                                if (partip) {
                                    if (partip.options) {
                                        if (!in_array(partip.value, arrpl)) {
                                            if (partip.value != '0' && partip.value != '-1') {
                                                arrpl.push(partip.value);
                                            }

                                        } else {
                                            alert(partip.options[partip.selectedIndex].text + ' <?php echo JText::_('BLFA_KN_DUBL'); ?>');
                                            return false;
                                        }
                                    } else {
                                        for (i = 0; i < partip.length; i++) {



                                            if (!in_array(partip[i].value, arrpl)) {
                                                if (partip[i].value != '0' && partip[i].value != '-1') {
                                                    arrpl.push(partip[i].value);
                                                }

                                            } else {
                                                alert(partip[i].options[partip[i].selectedIndex].text + ' <?php echo JText::_('BLFA_KN_DUBL'); ?>');
                                                return false;
                                            }

                                        }

                                    }
                                }
                                if (partip_aw) {
                                    if (partip_aw.options) {

                                        if (!in_array(partip_aw.value, arrpl)) {
                                            if (partip_aw.value != '0' && partip_aw.value != '-1') {
                                                arrpl.push(partip_aw.value);
                                            }

                                        } else {
                                            alert(partip_aw.options[partip_aw.selectedIndex].text + ' <?php echo JText::_('BLFA_KN_DUBL'); ?>');
                                            return false;
                                        }

                                    } else {
                                        for (i = 0; i < partip_aw.length; i++) {



                                            if (!in_array(partip_aw[i].value, arrpl)) {
                                                if (partip_aw[i].value != '0' && partip_aw[i].value != '-1') {
                                                    arrpl.push(partip_aw[i].value);
                                                }

                                            } else {
                                                alert(partip_aw[i].options[partip_aw[i].selectedIndex].text + ' <?php echo JText::_('BLFA_KN_DUBL'); ?>');
                                                return false;
                                            }

                                        }
                                    }
                                }
                            }
                        }
                        var extras = eval("document.adminForm['extra_time[]']");
                        if (extras) {
                            if (extras.length) {
                                for (i = 0; i < extras.length; i++) {
                                    if (extras[i].checked) {
                                        extras[i].value = 1;
                                    } else {
                                        extras[i].value = 0;
                                    }
                                    extras[i].checked = true;
                                }
                            } else {
                                if (extras.checked) {
                                    extras.value = 1;
                                } else {
                                    extras.value = 0;
                                }
                                extras.checked = true;
                            }
                        }
                       
                        ///}
                        if (pressbutton == 'matchday_apply') {
                            form.isapply.value = '1';
                            pressbutton = 'matchday_save';
                        }

                        var errortime = '';
                        var mt_time = eval("document.adminForm['match_time[]']");
                        if (mt_time) {
                            if (mt_time.length) {
                                for (i = 0; i < mt_time.length; i++) {
                                    var regE = /[0-2][0-9]:[0-5][0-9]/;
                                    if (!regE.test(mt_time[i].value) && mt_time[i].value != '') {
                                        errortime = '1';
                                        mt_time[i].style.border = "1px solid red";
                                    } else {
                                        mt_time[i].style.border = "1px solid #C0C0C0";
                                    }
                                }
                            } else {
                                var regE = /[0-2][0-9]:[0-5][0-9]/;
                                if (!regE.test(mt_time.value) && mt_time.value != '') {
                                    errortime = '1';
                                    mt_time.style.border = "1px solid red";
                                } else {
                                    mt_time.style.border = "1px solid #C0C0C0";
                                }
                            }
                        }

                        if (errortime) {
                            alert("<?php echo JText::_('BLBE_JSMDNOT7'); ?>");
                            return;
                        } else {
                            submitform(pressbutton);
                            return;
                        }

                    } else {
                        submitform(pressbutton);
                        return;
                    }
                }

                function bl_add_match() {
                    var team1 = getObj('teams1');
                    var team2 = getObj('teams2');
                    var score1 = getObj('add_score1').value;
                    var score2 = getObj('add_score2').value;
                    var venue_id = getObj('venue_id_new');
                    if ('<?php echo $lists['jsmr_mark_played'] ?>' == 1) {
                        var tm_played = getObj('tm_played').checked;
                    }

                    if (team1.value == 0 || team2.value == 0) {
                        alert("<?php echo JText::_('BLFA_JSMDNOT1') ?>");
                        return false;
                    }
                    if ('<?php echo $lists['jsmr_mark_played'] ?>' == 1) {
                        if (((score1) == '' || (score2) == '') && tm_played) {
                            alert("<?php echo JText::_('BLFA_JSMDNOT1') ?>");
                            return;
                        }
                    }
                    if ('<?php echo $this->acl ?>' == 1) {
                        if (team1.value == team2.value) {
                            alert("<?php echo ($lists['t_single'] == 1) ? JText::_('BLFA_JSMDNOTPL2') : JText::_('BLFA_JSMDNOT2') ?>");
                            return false;
                        }
                    } else {
                        if (team1.value == team2.value || (team1.value != '<?php echo isset($lists['tid']) ? $lists['tid'] : 0 ?>' && team2.value != '<?php echo isset($lists['tid']) ? $lists['tid'] : 0 ?>')) {

                            alert("<?php echo ($this->acl == 3) ? JText::_('BLFA_JSMDNOTPART') : JText::_('BLFA_JSMDNOTPART') ?>");
                            return false;

                        }
                    }

                    var regE = /[0-2][0-9]:[0-5][0-9]/;
                    if (!regE.test(getObj('match_time_new').value) && getObj('match_time_new').value != "") {
                        alert("<?php echo JText::_('BLBE_JSMDNOT7') ?>");
                        return false;
                    }
                    var tbl_elem = getObj('new_matches');
                    var row = tbl_elem.insertRow(tbl_elem.rows.length);
                    
                    var cell1 = document.createElement("td");
                    var cell2 = document.createElement("td");
                    var cell3 = document.createElement("td");
                    var cell4 = document.createElement("td");
                    var cell5 = document.createElement("td");
                    var cell6 = document.createElement("td");

                    var input_hidden = document.createElement("input");
                    input_hidden.type = "hidden";
                    input_hidden.name = "match_id[]";
                    input_hidden.value = 0;
                    cell1.appendChild(input_hidden);
                    cell1.innerHTML = '<button type="button" onclick="Delete_tbl_row_md(this); return false;" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE'); ?></span></button>';
                    cell1.setAttribute("rowspan", 2);
                    var input_hidden = document.createElement("input");
                    input_hidden.type = "hidden";
                    input_hidden.name = "home_team[]";
                    input_hidden.value = team1.value;
                    cell2.innerHTML = team1.options[team1.selectedIndex].text;
                    cell2.appendChild(input_hidden);

                    var input_hidden = document.createElement("input");
                    input_hidden.type = "text";
                    input_hidden.className = "score";
                    input_hidden.name = "home_score[]";
                    input_hidden.value = score1;
                    input_hidden.size = 3;
                    input_hidden.setAttribute("maxlength", 5);
                    cell3.align = "center";
                    input_hidden.onblur = function() {
                        extractNumber(this, 0, false);
                    };
                    input_hidden.onkeyup = function() {
                        extractNumber(this, 0, false);
                    };
                    input_hidden.onkeypress = function() {
                        return blockNonNumbers(this, event, true, false);
                    };
                    cell3.appendChild(input_hidden);
                    var txtnode = document.createTextNode(" : ");
                    cell3.appendChild(txtnode);
                    var input_hidden = document.createElement("input");
                    input_hidden.type = "text";
                    input_hidden.className = "score";
                    input_hidden.name = "away_score[]";
                    input_hidden.value = score2;
                    input_hidden.size = 3;
                    input_hidden.setAttribute("maxlength", 5);
                    input_hidden.onblur = function() {
                        extractNumber(this, 0, false);
                    };
                    input_hidden.onkeyup = function() {
                        extractNumber(this, 0, false);
                    };
                    input_hidden.onkeypress = function() {
                        return blockNonNumbers(this, event, true, false);
                    };
                    cell3.appendChild(input_hidden);
                    if ('<?php echo $lists['s_enbl_extra'] ?>' == '1') {
                        var input_hidden = document.createElement("input");
                        input_hidden.type = "checkbox";
                        input_hidden.name = "extra_time[]";

                        if (getObj('extra_timez').checked) {
                            input_hidden.checked = true;
                            input_hidden.value = '1';
                        } else {
                            input_hidden.value = '0';
                        }
                        cell4.appendChild(input_hidden);
                        var iiinn = document.createElement("span");
                        iiinn.className = "editlinktip hasTip";
                        iiinn.setAttribute('title', "<?php echo JText::_('BLFA_ET') ?>");
                        var innn = document.createElement("img");
                        innn.src = "administrator/components/com_joomsport/img/quest.png";
                        iiinn.appendChild(innn);
                        cell4.appendChild(iiinn);
                    }
                    var input_hidden = document.createElement("input");
                    input_hidden.type = "hidden";
                    input_hidden.name = "away_team[]";
                    input_hidden.value = team2.value;
                    cell5.innerHTML = team2.options[team2.selectedIndex].text;
                    cell5.appendChild(input_hidden);
                    cell6.innerHTML = '';
                    

                    ////-------------new---------------////

                    var cell7 = document.createElement("td");
                    var cell8 = document.createElement("td");
                    var cell9 = document.createElement("td");
                    var cell10 = document.createElement("td");

                    var input_hidden = document.createElement("input");
                    input_hidden.type = "date";
                    input_hidden.className = "jsdtime";
                    input_hidden.name = "match_data[]";
                    input_hidden.value = getObj('tm_date').value;
                    //input_hidden.size = 10;
                    input_hidden.setAttribute("maxlength", 10);
                    
                    cell7.appendChild(input_hidden);
                    cell7.align = "left";


                    var input_hidden = document.createElement("input");
                    input_hidden.type = "text";
                    input_hidden.className = "jstime";
                    input_hidden.name = "match_time[]";
                    input_hidden.value = getObj('match_time_new').value;
                    //input_hidden.size = 5;
                    input_hidden.setAttribute("maxlength", 5);
                    
                    cell8.appendChild(input_hidden);


                    cell8.align = "left";

                    var input_hidden = document.createElement("input");
                    input_hidden.type = "hidden";
                    input_hidden.name = "venue_id[]";
                    input_hidden.value = venue_id.value;
                    cell9.innerHTML = venue_id.selectedIndex != 0 ? venue_id.options[venue_id.selectedIndex].text : '';
                    cell9.appendChild(input_hidden);
                    cell9.setAttribute("colspan", 2);
                    if ('<?php echo $lists['jsmr_mark_played'] ?>' == 1) {

                        var jstmplayedclone = jQuery('#jstmplayedclone').children(0).clone();
                        var jstmplayedclone2 = jQuery('#jstmplayedclone').children(':first-child').next().clone();
                        var rand = Math.floor((Math.random() * 10000) + 1);
                        jstmplayedclone.removeAttr("tabindex");
                        jstmplayedclone.removeAttr("aria-hidden");
                        jstmplayedclone.prop({id:"match_played"+rand, name:"match_played[]", class:""});
                        
                        //cell10.innerHTML = jstmplayedclone;
                        cell10.appendChild(jstmplayedclone.get(0));
                        
                        //cell10.appendChild(jstmplayedclone2.get(0));
                    }
                    ////------------/new---------------////

                    row.appendChild(cell1);
                    row.appendChild(cell2);
                    row.appendChild(cell3);

                    row.appendChild(cell4);
                    row.appendChild(cell5);
                    row.appendChild(cell6);
                    var row2 = tbl_elem.insertRow(tbl_elem.rows.length);
                    
                    row2.className='jsmfirstTR';
                    row2.appendChild(cell7);
                    row2.appendChild(cell8);
                    row2.appendChild(cell9);
                    row2.appendChild(cell10);
                    if ('<?php echo $lists['jsmr_mark_played'] ?>' == 1) {
                        jQuery("#match_played"+rand).val(jQuery("#tm_played").val());
                        jQuery("#match_played"+rand).select2({minimumResultsForSearch: 20});
                        
                    }
                    
                    getObj('teams1').value = 0;
                    getObj('teams2').value = 0;
                    getObj('add_score1').value = '';
                    getObj('add_score2').value = '';
                    getObj('venue_id_new').value = 0;
                    if ('<?php echo $lists['s_enbl_extra'] ?>' == '1') {
                        getObj('extra_timez').checked = false;
                    }
                    return false;
                }
//-->
</script>