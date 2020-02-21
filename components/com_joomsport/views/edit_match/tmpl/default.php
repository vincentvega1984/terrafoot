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
$s_id = $this->lists['s_id'];
$Itemid = JRequest::getInt('Itemid');
$user = JFactory::getUser();
?>
<script type="text/javascript">
<!--
    Joomla.submitbutton = function(task) {
        submitbutton(task);
    }
    function submitbutton(pressbutton) {
        var form = document.adminForm;

        if (pressbutton == 'match_apply') {
            form.isapply.value = '1';
            pressbutton = 'match_save';
        }
        if ('<?php echo $this->acl; ?>' == 1 && '<?php echo $lists['t_type'] ?>' == 0 && document.adminForm.m_id.value == 0) {
            alert("<?php echo JText::_('BLFA_SELMATCHDAY'); ?>");
            return false;
        }

        var regE = /[0-2][0-9]:[0-5][0-9]/;
        if (!regE.test(document.adminForm.m_time.value) && document.adminForm.m_time.value != '') {
            alert("<?php echo JText::_('BLBE_JSMDNOT7'); ?>");
            return;
        } else {
            if (pressbutton == 'match_invite' && !document.adminForm.inv_mtitle.value) {
                alert("<?php echo JText::_('BLFA_JSMFILLTITLE'); ?>");
                return;
            } else {
                submitform(pressbutton);
                return;
            }
        }

    }

    function bl_add_event() {
        var cur_event = getObj('event_id');

        //var e_count = getObj('e_count').value;
        var e_minutes = getObj('e_minutes').value;
        var e_player = getObj('playerz_id');
        var re_count = getObj('re_count').value;
        if (cur_event.value == 0) {
            alert("<?php echo JText::_('BLFA_SELEVENT'); ?>");
            return;
        }
        if (e_player.value == 0) {
            alert("<?php echo JText::_('BLFA_SELPLAYER'); ?>");
            return;
        }

        var tbl_elem = getObj('new_events');
        var row = tbl_elem.insertRow(tbl_elem.rows.length);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");

        var cell4 = document.createElement("td");
        var cell5 = document.createElement("td");
        var cell6 = document.createElement("td");
        var cell7 = document.createElement("td");
        var cell8 = document.createElement("td");
        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = "em_id[]";
        input_hidden.value = 0;
        cell1.appendChild(input_hidden);
        cell1.innerHTML = '<button type="button" class="closerem" onclick="Delete_tbl_row(this); return false;"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE'); ?></span></button>';

        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = "new_eventid[]";
        input_hidden.value = cur_event.value;
        cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
        cell2.appendChild(input_hidden);


        var input_hidden = document.createElement("input");
        input_hidden.type = "text";
        input_hidden.name = "e_minuteval[]";
        input_hidden.className = "score";
        input_hidden.value = e_minutes;
        //cell4.innerHTML = e_minutes;
        input_hidden.setAttribute("maxlength", 5);
        input_hidden.setAttribute("size", 5);
        input_hidden.onblur = function() {
            extractNumber(this, 0, false);
        };
        input_hidden.onkeyup = function() {
            extractNumber(this, 0, false);
        };
        input_hidden.onkeypress = function() {
            return blockNonNumbers(this, event, true, false);
        };
        cell4.appendChild(input_hidden);

        var input_player = document.createElement("input");
        input_player.type = "hidden";
        input_player.name = "new_player[]";
        input_player.value = e_player.value;
        if (e_player.value != 0) {
            cell5.innerHTML = e_player.options[e_player.selectedIndex].text;
        }
        cell5.appendChild(input_player);
        var input_hidden = document.createElement("input");
        input_hidden.type = "text";
        input_hidden.className = "score";
        input_hidden.name = "e_countval[]";
        input_hidden.value = re_count;
        //cell4.innerHTML = e_minutes;
        input_hidden.setAttribute("maxlength", 5);
        input_hidden.setAttribute("size", 5);
        input_hidden.onblur = function() {
            extractNumber(this, 0, false);
        };
        input_hidden.onkeyup = function() {
            extractNumber(this, 0, false);
        };
        input_hidden.onkeypress = function() {
            return blockNonNumbers(this, event, true, false);
        };
        cell6.appendChild(input_hidden);

        row.appendChild(cell1);
        row.appendChild(cell2);
        row.appendChild(cell5);
        row.appendChild(cell4);
        row.appendChild(cell6);
        row.appendChild(cell7);
        row.appendChild(cell8);
        getObj('event_id').value = 0;
        getObj('playerz_id').value = 0;
        getObj('e_minutes').value = '';

        ReAnalize_tbl_Rows('new_events');

    }
    function bl_add_tevent() {
        var cur_event = getObj('tevent_id');

        var e_count = getObj('et_count').value;
        var e_player = getObj('teamz_id');

        if (cur_event.value == 0) {
            alert("<?php echo JText::_('BLFA_SELEVENT'); ?>");
            return;
        }
        if (e_player.value == 0) {
            alert("<?php echo JText::_('BLFA_SELTEAM'); ?>");
            return;
        }

        var exevs = eval('document.adminForm["new_teventid\[\]"]');
        var exiev = eval('document.adminForm["new_tplayer\[\]"]');
        if (exevs && exiev) {
            var ransw2 = exevs.length;
            if (ransw2) {
                for (var i = 0; i < ransw2; i++) {
                    if (exiev[i].value == e_player.value && exevs[i].value == cur_event.value) {
                        alert("<?php echo JText::_('BLFA_JSMDNOT66'); ?>");
                        return;
                    }
                }
            } else {
                if (exiev.value == e_player.value && exevs.value == cur_event.value) {
                    alert("<?php echo JText::_('BLFA_JSMDNOT66'); ?>");
                    return;
                }
            }

        }

        var tbl_elem = getObj('new_tevents');
        var row = tbl_elem.insertRow(tbl_elem.rows.length);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");
        var cell3 = document.createElement("td");
        var cell4 = document.createElement("td");
        var cell5 = document.createElement("td");
        var cell6 = document.createElement("td");
        var cell7 = document.createElement("td");
        var cell8 = document.createElement("td");

        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = "et_id[]";
        input_hidden.value = 0;
        cell1.appendChild(input_hidden);
        cell1.innerHTML = '<button type="button" class="closerem" onclick="Delete_tbl_row(this); return false;"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE'); ?></span></button>';

        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = "new_teventid[]";
        input_hidden.value = cur_event.value;
        cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
        cell2.appendChild(input_hidden);

        var input_hidden = document.createElement("input");
        input_hidden.type = "text";
        input_hidden.className = "score";
        input_hidden.name = "et_countval[]";
        input_hidden.value = e_count;
        input_hidden.setAttribute("maxlength", 5);
        input_hidden.setAttribute("size", 5);
        input_hidden.onblur = function() {
            extractNumber(this, 0, false);
        };
        input_hidden.onkeyup = function() {
            extractNumber(this, 0, false);
        };
        input_hidden.onkeypress = function() {
            return blockNonNumbers(this, event, true, false);
        };
        cell3.align = "center";
        //cell3.innerHTML = e_count;
        cell3.appendChild(input_hidden);


        var input_player = document.createElement("input");
        input_player.type = "hidden";
        input_player.name = "new_tplayer[]";
        input_player.value = e_player.value;
        if (e_player.value != 0) {
            cell5.innerHTML = e_player.options[e_player.selectedIndex].text;
        }
        cell5.appendChild(input_player);

        row.appendChild(cell1);
        row.appendChild(cell2);
        row.appendChild(cell5);
        row.appendChild(cell6);
        row.appendChild(cell3);
        row.appendChild(cell7);
        row.appendChild(cell8);


        getObj('tevent_id').value = 0;
        getObj('teamz_id').value = 0;
        getObj('et_count').value = 1;

        //ReAnalize_tbl_Rows('new_tevents');
    }

    function bl_add_squard(tblid, selid, elname) {
        var cur_event = getObj(selid);


        if (cur_event.value == 0) {
            alert("<?php echo JText::_('BLFA_SELPLAYER'); ?>");
            return;
        }


        var tbl_elem = getObj(tblid);
        var row = tbl_elem.insertRow(tbl_elem.rows.length);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");



        cell1.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE'); ?>"><img src="components/com_joomsport/img/ico/close.png"  border="0" alt="Delete"></a>';

        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = elname;
        input_hidden.value = cur_event.value;
        cell2.innerHTML = cur_event.options[cur_event.selectedIndex].text;
        cell2.appendChild(input_hidden);



        row.appendChild(cell1);
        row.appendChild(cell2);


        getObj(selid).value = 0;

    }
    function enblnp() {
        if (document.adminForm.new_points1.checked) {
            getObj("newp1").removeAttribute('readonly');
            getObj("newp2").removeAttribute('readonly');
        } else {
            getObj("newp1").setAttribute('readonly', 'readonly');
            getObj("newp2").setAttribute('readonly', 'readonly');
        }
    }
    function chng_disbl_aet() {

        if (getObj('is_extra1').checked) {

            getObj('aet1').disabled = '';
            getObj('aet2').disabled = '';
        } else {

            getObj('aet1').disabled = 'true';
            getObj('aet2').disabled = 'true';
        }
    }
    function sqchng(nid, nid2) {
        if (getObj(nid).checked) {

            getObj(nid2).checked = false;
        }
    }
    function js_add_subs(tblid, pl1, pl2, minutes) {
        var tbl_elem = getObj(tblid);
        if (getObj(pl1).value == getObj(pl2).value || getObj(pl1).value == 0 || getObj(pl2).value == 0) {
            return false;
        }
        var row = tbl_elem.insertRow(tbl_elem.rows.length);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");
        var cell3 = document.createElement("td");
        var cell4 = document.createElement("td");

        cell1.innerHTML = '<button type="button" class="closerem" onclick="Delete_tbl_row(this); return false;"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE'); ?></span></button>';

        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = pl1 + "_arr[]";
        input_hidden.value = getObj(pl1).value;
        cell2.innerHTML = getObj(pl1).options[getObj(pl1).selectedIndex].text;
        cell2.appendChild(input_hidden);
        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = pl2 + "_arr[]";
        input_hidden.value = getObj(pl2).value;
        cell3.innerHTML = getObj(pl2).options[getObj(pl2).selectedIndex].text;
        cell3.appendChild(input_hidden);

        var input_hidden = document.createElement("input");
        input_hidden.type = "text";
        input_hidden.className = "score";
        input_hidden.name = minutes + "_arr[]";
        input_hidden.value = getObj(minutes).value;
        input_hidden.setAttribute("maxlength", 5);
        input_hidden.setAttribute("size", 5);
        cell4.appendChild(input_hidden);

        row.appendChild(cell1);
        row.appendChild(cell2);
        row.appendChild(cell3);
        row.appendChild(cell4);

        getObj(minutes).value = 0;
    }

    function ReAnalize_tbl_Rows(tbl_id) {
        start_index = 0;
        var tbl_elem = getObj(tbl_id);
        if (tbl_elem.rows[start_index]) {
            for (var i = start_index; i < tbl_elem.rows.length; i++) {

                if (i > start_index) {
                    tbl_elem.rows[i].cells[5].innerHTML = '<i onclick="Up_tbl_row(this);" class="fa fa-caret-up"></i>';
                } else {
                    tbl_elem.rows[i].cells[5].innerHTML = '';
                }
                if (i < (tbl_elem.rows.length - 1)) {
                    tbl_elem.rows[i].cells[6].innerHTML = '<i onclick="Down_tbl_row(this);" class="fa fa-caret-down"></i>';
                } else {
                    tbl_elem.rows[i].cells[6].innerHTML = '';
                }
            }
        }
    }




    function Up_tbl_row(element) {
        if (element.parentNode.parentNode.sectionRowIndex > 0) {
            var sec_indx = element.parentNode.parentNode.sectionRowIndex;
            var table = element.parentNode.parentNode.parentNode;
            var tbl_id = table.id;

            var row = table.insertRow(sec_indx - 1);

            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            //row.appendChild(element.parentNode.parentNode.cells[0]);

            var cell5 = document.createElement("td");
            var cell6 = document.createElement("td");
            row.appendChild(cell5);
            row.appendChild(cell6);
            element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

            ReAnalize_tbl_Rows(tbl_id);
        }
    }

    function Down_tbl_row(element) {
        if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
            var sec_indx = element.parentNode.parentNode.sectionRowIndex;
            var table = element.parentNode.parentNode.parentNode;
            var tbl_id = table.id;

            var row = table.insertRow(sec_indx + 2);

            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            row.appendChild(element.parentNode.parentNode.cells[0]);
            //row.appendChild(element.parentNode.parentNode.cells[0]);

            var cell5 = document.createElement("td");
            var cell6 = document.createElement("td");
            row.appendChild(cell5);
            row.appendChild(cell6);
            element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);

            ReAnalize_tbl_Rows(tbl_id);
        }
    }
    function chng_disbl_aet(){
		
				if(getObj('is_extra1').checked){
					
					getObj('aet1').disabled = '';
					getObj('aet2').disabled = '';
				}				else{

					getObj('aet1').disabled = 'true';
					getObj('aet2').disabled = 'true';
				}
			}

//-->
</script>
<div id="joomsport-container">
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        echo $lists['panel'];
        ?>
    </nav>
    <!-- /.navbar -->

    <div class="main editMatch">
        <div class="heading col-xs-12 col-lg-12">
            <h2 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo JText::_('BLFA_MATCH_EDIT'); ?></h2>

            <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">           
                <?php if ($this->acl == 2): ?>
                    <form action='<?php echo JURI::base(); ?>index.php?option=com_joomsport&task=team_edit&controller=moder&Itemid=<?php echo $Itemid ?>' method='post' name='chg_team'>                        
                        <label class="selected"><?php echo $this->lists['tourn_name']; ?></label>                        
                        <div class="data">
                            <?php echo $this->lists['seass_filtr']; ?>
                            <?php echo $this->lists['tm_filtr']; ?>                  
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="tools col-xs-12 col-lg-12 text-right"> 
            <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_SAVE') ?>" onclick="javascript:submitbutton('match_save');
        return false;"><i class="save"></i> <?php echo JText::_('BLFA_SAVE') ?></a>
            <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_APPLY') ?>" onclick="javascript:submitbutton('match_apply');
        return false;"><i class="apply"></i> <?php echo JText::_('BLFA_APPLY') ?></a>
               <?php if ($this->acl == 1) {
    ?>
                <a href="<?php echo JRoute::_('index.php?option=com_joomsport&view=edit_matchday&controller=admin&sid='.$s_id.'&cid[]='.$row->m_id.'&Itemid='.$Itemid);
    ?>" title="<?php echo JText::_('BLFA_CLOSE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_CLOSE') ?></a>
            <?php 
} elseif ($this->acl == 2) {
    ?>
                <a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid='.$lists['tid'].'&mid='.$row->m_id.'&sid='.$s_id.'&Itemid='.$Itemid) ?>" title="<?php echo JText::_('BLFA_CLOSE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_CLOSE') ?></a>
            <?php 
} ?>
        </div>
        <div class="jsClear"></div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#mainm" role="tab" data-toggle="tab"><i class="hidden-xs flag"></i></i><?php echo JText::_('BLFA_MAIN') ?></a></li>
            <?php if (!$lists['t_single']) {
    ?>
            <li role="presentation"><a href="#squad" role="tab" data-toggle="tab"><i class="flag"></i><?php echo JText::_('BLFA_SQUARD') ?></a></li>
            <?php 
} ?>
        </ul>
        <!-- Tab panels -->
        <form class="form-horizontal" role="form" action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">    
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="mainm">                
                    <div class="">
                        <div class="jscenter" style="margin-top:10px;">
                            <label class=""><?php echo $lists['teams1']?></label>&nbsp; vs &nbsp;<label><?php echo $lists['teams2']?></label>
                        </div>
                        <div class="form-group">
                            <label for="inputFirst" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_MATCHDAYNAME'); ?></label>
                            <div class="col-xs-12 col-sm-10">
                                <?php echo $lists['mday']; ?>                                
                            </div>
                        </div>
                        <?php
			if(count($lists['maps'])){
			?>
			
			<div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_( 'BLFA_MAPS' ); ?></label>
                            <div class="col-sm-10">
					
					<table border="0" class="jsMapsTable"><tr>
						<th><?php echo JText::_( 'BLFA_MAPS' ); ?></th>
						<th><?php echo $lists['teams1'];?></th>
						<th><?php echo $lists['teams2'];?></th>
						</tr>
					<?php 
					for($i=0;$i<count($lists['maps']);$i++){
						echo "<tr>";
						echo "<td>".$lists['maps'][$i]->m_name."</td>";
						echo "<td><input type='text' name='t1map[]' size='5' value='".(isset($lists['maps'][$i]->m_score1)?$lists['maps'][$i]->m_score1:"")."' /></td>";
						echo "<td><input type='text' name='t2map[]' size='5' value='".(isset($lists['maps'][$i]->m_score2)?$lists['maps'][$i]->m_score2:"")."' /></td>";
						echo "<input type='hidden' name='mapid[]' value='".$lists['maps'][$i]->id."'/>";
						echo "</tr>";
					}
					?>
					</table>
                            </div>     
			</div>
			<?php
			}
  
			?>
                        <div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_RESULTS'); ?></label>
                            <div class="col-sm-10">
                                <?php
                                $moder_or_pl = ($this->acl == 3) ? ($row->team1_id == $this->lists['usr']->id) : (in_array($row->team1_id, $this->lists['teams_season']));
                                $moder_or_pl2 = ($this->acl == 3) ? ($row->team2_id == $this->lists['usr']->id) : (in_array($row->team2_id, $this->lists['teams_season']));
                                if ( ($this->acl != 1 && (($this->lists['jsmr_editresult_opposite'] == 0 && !$moder_or_pl) || ($this->lists['jsmr_editresult_yours'] == 0 && $moder_or_pl)))) {
                                    echo '<input type="hidden" name="score1" value="'.$row->score1.'" size="5" maxlength="5"  />'.$row->score1;
                                } else {
                                    echo '<input class="score" type="text" name="score1" value="'.$row->score1.'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                                }
                                
                                if ($lists['t_type']) {
                                    echo JText::_('W').'<input class="score" type="checkbox" id="spenwin_1" '.(($row->p_winner && $row->p_winner == $row->team1_id) ? 'checked' : '').' name="penwin[]" value="'.$row->team1_id.'" onchange="sqchng(\'spenwin_1\',\'spenwin_2\');" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                                }
                                if ( ($this->acl != 1 && (($this->lists['jsmr_editresult_opposite'] == 0 && !$moder_or_pl2) || ($this->lists['jsmr_editresult_yours'] == 0 && $moder_or_pl2)))) {
                                    echo '&nbsp;<span>:</span>&nbsp;<input type="hidden" name="score2" value="'.$row->score2.'" size="5" maxlength="5"  />'.$row->score2.'';
                                } else {
                                    echo '&nbsp;<span>:</span>&nbsp;<input  class="score" type="text" name="score2" value="'.$row->score2.'" size="5" maxlength="5" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                                }
                                //'.($lists['s_enbl_extra']?"":"disabled").'

                                
                                if ($lists['t_type']) {
                                    echo JText::_('W').'<input class="score" type="checkbox" id="spenwin_2" '.(($row->p_winner && $row->p_winner == $row->team2_id) ? 'checked' : '').' onchange="sqchng(\'spenwin_2\',\'spenwin_1\');" name="penwin[]" value="'.$row->team2_id.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />';
                                }
                                ?>
                            </div>
                        </div>
                        <?php if (!$lists['t_type']) {
    ?>
                            <div class="form-group">
                                <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BL_TBL_POINTS');
    ?> </label>
                                <div class="col-sm-10">                             
                                    
                                    <input type="text" size="5" class="score" name="points1" value="<?php echo floatval($row->points1);
    ?>" <?php echo !$row->new_points ? "readonly='readonly'" : '' ?> />
                                    <span>:</span>
                                    <input type="text" size="5" class="score" name="points2" value="<?php echo floatval($row->points2);
    ?>" <?php echo !$row->new_points ? "readonly='readonly'" : '' ?> />
                                    
                                    <br />
                                    <?php echo JText::_('BLFE_MANUAL_POINT') ?>
                                    <input type="radio"  name="new_points" id="optionsRadios1" value="0" <?php echo!$row->new_points ? 'checked="checked"' : '' ?>/>
                                    <?php echo JText::_('JNO');
    ?>
                                    <input type="radio" name="new_points" id="optionsRadios1" value="1" <?php echo $row->new_points ? 'checked="checked"' : '' ?>/>
                                    <?php echo JText::_('JYES');
    ?> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_BONUS');
    ?></label>
                                <div class="col-sm-10">
                                    
                                    <input type="text" size="5" class="score" name="bonus1" value="<?php echo floatval($row->bonus1) ?>" />
                                    <span>:</span>
                                    <input type="text" size="5" class="score" name="bonus2" value="<?php echo floatval($row->bonus2) ?>" >
                                    
                                </div>
                            </div>
                        <?php 
} ?>
                        <?php if ($lists['s_enbl_extra'] == 1) {
    ?>
                            <div class="form-group">
                                <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_ET');
    ?></label>
                                <div class="col-sm-10">
                                    <input type="radio" name="is_extra" id="is_extra0" onclick="chng_disbl_aet()" value="0" <?php echo!$row->is_extra ? 'checked="checked"' : '' ?>/>
                                    <?php echo JText::_('JNO');
    ?>
                                    <input type="radio" name="is_extra" id="is_extra1" onclick="chng_disbl_aet()" value="1" <?php echo $row->is_extra ? 'checked="checked"' : '' ?>/>
                                    <?php echo JText::_('JYES');
    ?> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_TT_AET');
    ?></label>
                                <div class="col-sm-10">
                                        <?php
                                            echo $lists['teams1'];
                                        ?>
                                            <input type="text" style="width:50px;" id="aet1" name="aet1" value="<?php echo $row->aet1?>" size="5" maxlength="5" <?php echo $row->is_extra ? '' : 'disabled';
                                        ?> onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            :&nbsp;<input type="text" id="aet2" style="width:50px;" name="aet2" value="<?php echo $row->aet2?>" size="5" maxlength="5" <?php echo $row->is_extra ? '' : 'disabled';
                                        ?> onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            <?php
                                            echo $lists['teams2'];
                                        ?>
                                </div>
                            </div>    
                        <?php 
} ?>
                        <?php if(($this->acl != 1 && $lists['jsmr_mark_played'] == 1) || $this->acl == 1){?>
                            <div class="form-group">
                                <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_PLAYED');
    ?></label>
                                <div class="col-sm-10">
                                    <?php echo $lists['m_played'];?>

                                </div>
                            </div>
                        <?php 
} ?>
                        <div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_DATE'); ?></label>
                            <div class="col-xs-12 col-sm-3">
                                <input type="date" class="date" id="date" name="m_date" value="<?php echo $row->m_date ? $row->m_date : date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_TIME'); ?></label>
                            <div class="col-xs-12 col-sm-3">
                                <input type="text" class="time" id="inputTime" name="m_time" value="<?php echo substr($row->m_time, 0, 5); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_LOCATION'); ?></label>
                            <div class="col-xs-12 col-sm-3">                                
                                <input type="text" name="m_location" value="<?php echo htmlspecialchars($row->m_location); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputName" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_VENUE'); ?></label>
                            <div class="col-xs-12 col-sm-3">
                                <?php echo $lists['venue']; ?>
                            </div>
                        </div>

                        <?php
                        for ($p = 0; $p < count($lists['ext_fields']); ++$p) {
                            if ($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)) {
                                //
                            } else {
                                ?>
                                <div class="form-group">
                                    <label for="" class="col-xs-12 col-sm-2 control-label"><?php echo $lists['ext_fields'][$p]->name;
                                ?></label>
                                    <div class="col-xs-12 col-sm-3">
                                        <td>
                                            <?php
                                            switch ($lists['ext_fields'][$p]->field_type) {

                                                case '1': echo $lists['ext_fields'][$p]->selvals;
                                                    break;
                                                case '2': echo '<textarea name="extraf['.$lists['ext_fields'][$p]->id.']" rows="6">'.htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text) ? ($lists['ext_fields'][$p]->fvalue_text) : '', ENT_QUOTES).'</textarea>';
                                                    break;
                                                case '3': echo $lists['ext_fields'][$p]->selvals;
                                                    break;
                                                case '0':
                                                default: echo '<input type="text" maxlength="255" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue) ? htmlspecialchars($lists['ext_fields'][$p]->fvalue) : '').'" />';
                                                    break;
                                            }
                                ?>
                                            <input type="hidden" name="extra_ftype[<?php echo $lists['ext_fields'][$p]->id;
                                ?>]" value="<?php echo $lists['ext_fields'][$p]->field_type ?>" />
                                            <input type="hidden" name="extra_id[<?php echo $lists['ext_fields'][$p]->id;
                                ?>]" value="<?php echo $lists['ext_fields'][$p]->id ?>" />
                                    </div>
                                </div>
                                <?php

                            }
                        }
                        ?>

                        <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label"><?php echo JText::_('BLFA_ABOUTMATCH'); ?></label>
                            <div class="col-sm-7">
                                <textarea id="inputMatch" name="match_descr" rows="6"><?php echo htmlspecialchars($row->match_descr, ENT_QUOTES); ?></textarea>
                            </div>
                        </div>    


                        <div class="table-responsive col-xs-12 col-lg-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="7" class="text-center"><?php echo JText::_('BLFA_PLAYEREVENTS'); ?></th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo JText::_('BLFA_PLAYEREVENT'); ?></th>
                                        <th><?php echo JText::_('BLFA_PLAYERR'); ?></th>
                                        <th><?php echo JText::_('BLFA_MINUTES'); ?></th>
                                        <th><?php echo JText::_('BLFA_COUNT'); ?></th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody id="new_events">                                
                                    <?php
                                    $ps = 0;
                                    if (count($lists['m_events'])) {
                                        foreach ($lists['m_events'] as $m_events) {
                                            echo '<tr>';
                                            $moder_or_pl = ($this->acl == 3) ? ($m_events->player_id != $this->lists['usr']->id) : (!in_array($m_events->t_id, $this->lists['teams_season']));
                                            $moder_or_pl2 = ($this->acl == 3) ? ($m_events->player_id == $this->lists['usr']->id) : (in_array($m_events->t_id, $this->lists['teams_season']));
                                            if ($this->acl == 1 || (($this->lists['jsmr_edit_playerevent_opposite'] == 1 && $moder_or_pl) || ($this->lists['jsmr_edit_playerevent_yours'] == 1 && $moder_or_pl2))) {
                                                echo '<td><input type="hidden" name="em_id[]" value="'.$m_events->id.'" /><button type="button" class="closerem" onclick="Delete_tbl_row(this); return false;" ><span aria-hidden="true">&times;</span><span class="sr-only">'.JText::_('BLFA_DELETE').'</span></button></td>';
                                                echo '<td><input type="hidden" name="new_eventid[]" value="'.$m_events->e_id.'" />'.$m_events->e_name.'</td>';
                                                echo '<td><input type="hidden" name="new_player[]" value="'.$m_events->player_id.'" />'.$m_events->p_name.'</td>';
                                                echo '<td><input class="score" type="text" size="5" maxlenght="5" name="e_minuteval[]" value="'.$m_events->minutes.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);"  /></td>';
                                                echo '<td><input class="score" type="text" size="5" maxlength="5" name="e_countval[]" value="'.$m_events->ecount.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /></td>';
                                                echo '<td class="scroll">';
                                                if ($ps > 0) {
                                                    echo '<i onclick="Up_tbl_row(this); return false;" class="fa fa-caret-up"></i>';
                                                }
                                                echo '</td>';
                                                echo '<td class="scroll">';
                                                if ($ps < count($lists['m_events']) - 1) {
                                                    echo '<i onclick="Down_tbl_row(this); return false;" class="fa fa-caret-down"></i>';
                                                }
                                                echo '</td>';
                                            } else {
                                                echo '<td>&nbsp;<input type="hidden" name="em_id_n[]" value="'.$m_events->id.'" /></td>';

                                                echo '<td>'.$m_events->e_name.'</td>';

                                                echo '<td>'.$m_events->p_name.'</td>';

                                                echo '<td>'.$m_events->minutes.'</td>';
                                                echo '<td>'.$m_events->ecount.'</td>';
                                                echo '<td class="scroll">';
                                                if ($ps > 0) {
                                                    echo '<i onclick="Up_tbl_row(this); return false;" class="fa fa-caret-up"></i>';
                                                }
                                                echo '</td>';
                                                echo '<td class="scroll">';
                                                if ($ps < count($lists['m_events']) - 1) {
                                                    echo '<i onclick="Down_tbl_row(this); return false;" class="fa fa-caret-down"></i>';
                                                }
                                                echo '</td>';
                                            }

                                            echo '</tr>';
                                            ++$ps;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        if (!isset($lists['jsmr_edit_playerevent_yours']) && !isset($lists['jsmr_edit_playerevent_opposite'])) { //update
                            $lists['jsmr_edit_playerevent_yours'] = '';
                            $lists['jsmr_edit_playerevent_opposite'] = '';
                        }
                        if (!$lists['jsmr_edit_playerevent_yours'] && !$lists['jsmr_edit_playerevent_opposite'] && ($this->acl == 2 || $this->acl == 3)) {
                        } else {
                            ?>
                            <div class="table-responsive col-xs-12 col-lg-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-center"><?php echo JText::_('BLFA_ADDPLEVENTS');
                            ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div >
                                                    <?php echo $lists['events'];
                            ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div >
                                                    <?php echo $lists['players'];
                            ?>
                                                </div>
                                            </td>
                                            <td><input type="text" class="score" size="5" maxlength="5" name="e_minutes" id="e_minutes"></td>
                                            <td><input type="text" class="score" size="5" maxlength="5" name="re_count" id="re_count">
                                                <button type="button" class="btn btn-default add" style="min-height:33px;margin-bottom: 0px;" onclick="bl_add_event();" ><?php echo JText::_('BLFA_ADD');
                            ?></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php 
                        } ?>
                        <?php if (!$lists['t_single']) {
    ?>
                            <div class="table-responsive col-xs-12 col-lg-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-center"><?php echo JText::_('BLFA_MATCHSTATS');
    ?></th>
                                        </tr>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo JText::_('BLFA_MATCHSTATSEV');
    ?></th>
                                            <th><?php echo JText::_('BLFA_TEAM');
    ?></th>
                                            <th>&nbsp;</th>
                                            <th><?php echo JText::_('BLFA_COUNT');
    ?></th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="new_tevents">
                                        <?php
                                        $ps1 = 0;

    if (count($lists['t_events'])) {
        foreach ($lists['t_events'] as $m_events) {
            echo '<tr>';
            if ($this->acl != 1 && (($this->lists['jsmr_edit_matchevent_opposite'] == 0 && !in_array($m_events->pid, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_matchevent_yours'] == 0 && in_array($m_events->pid, $this->lists['teams_season'])))) {
                echo '<td>&nbsp;</td>';
                echo '<td>'.$m_events->e_name.'</td>';
                echo '<td>'.$m_events->p_name.'</td>';
                echo '<td></td>';
                echo '<td>'.$m_events->ecount.'</td>';
                echo '<td>';
                echo '</td>';
                echo '<td>';
                echo '</td>';
            } else {
                echo '<td><input type="hidden" name="et_id[]" value="'.$m_events->id.'" /><button type="button"  onclick="Delete_tbl_row(this);" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only">'.JText::_('BLFA_DELETE').'</span></button>';
                echo '<td><input type="hidden" name="new_teventid[]" value="'.$m_events->e_id.'" />'.$m_events->e_name.'</td>';
                echo '<td><input type="hidden" name="new_tplayer[]" value="'.$m_events->pid.'" />'.$m_events->p_name.'</td>';
                echo '<td></td>';
                echo '<td><input class="score" type="text" size="5" maxlenght="5" name="et_countval[]" value="'.$m_events->ecount.'" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" /></td>';
                echo '<td>';
                echo '</td>';
                echo '<td>';
                echo '</td>';
            }
            echo '</tr>';
            ++$ps1;
        }
    }
    ?>                                    
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            if (empty($lists['jsmr_edit_matchevent_yours']) && empty($lists['jsmr_edit_matchevent_opposite']) && $this->acl == 2) {
                                //
                            } else {
                                ?>
                                <div class="table-responsive col-xs-12 col-lg-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="7" class="text-center"><?php echo JText::_('BLFA_ADDSTATTOMATCH') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><div>
                                                        <?php echo $lists['team_events'];
                                ?>
                                                    </div></td>
                                                <td><div>
                                                        <?php echo $lists['sel_team'];
                                ?>
                                                    </div></td>
                                                <td><input name="e_count" id="et_count" type="text" class="score" />
                                                    <button onclick="bl_add_tevent();"  type="button" class="btn btn-default add"><?php echo JText::_('BLFA_ADD');
                                ?></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php 
                            }
                            ?>
                            <?php echo $lists['boxhtml'];?>
                        <?php 
                        } ?>
                        <div class="upload col-xs-12 col-lg-12">
                            <div class="form-group">
                                <div class="jsDivCenter"><label><?php echo JText::_('BLFA_UPLPHTOMTCH'); ?> <i class="fa fa-question-circle"></i></label></div>
                                <div class="col-xs-12">
                                    <input type="file" name="player_photo_1" value="" class="feed-back inp-small" id="player_photo_1"/>                                
                                </div>
                                <div class="col-xs-12">
                                    <input type="file" name="player_photo_2" value="" class="feed-back inp-small" id="player_photo_2"/>
                                    <button type="button" id="player_photo" class="btn"><?php echo JText::_('BLFA_UPLOAD'); ?></button>
                                    <p><?php echo JText::_('BLFA_ONEPHSEL'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive col-xs-12 col-lg-12">
                            <?php if (count($lists['photos'])) {
    ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?php echo JText::_('BLFA_DELETE');
    ?></th>                                    
                                            <th><?php echo JText::_('BLFA_TITLE');
    ?></th>
                                            <th><?php echo JText::_('BLFA_IMAGE');
    ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($lists['photos'] as $photos) {
                                            if (is_file(JPATH_ROOT.'/media/bearleague/'.$photos->filename)) {
                                                ?>
                                                <tr>
                                                    <td class="">
                                                        <button type="button" class="closerem" title="<?php echo JText::_('BLFA_REMOVE');
                                                ?>" onclick="Delete_tbl_row(this);"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_REMOVE');
                                                ?></span></button>
                                                        <input type="hidden" name="photos_id[]" value="<?php echo $photos->id;
                                                ?>"/>
                                                    </td>                                            
                                                    <td class="w50">                                                
                                                        <input type="text" maxlength="255" name="ph_names[]" value="<?php echo htmlspecialchars($photos->name) ?>" />                                                            
                                                    </td>
                                                    <td class="">                                                            
                                                        <?php
                                                        $imgsize = getimagesize(JPATH_ROOT.'/media/bearleague/'.$photos->filename);
                                                if ($imgsize[0] > 200) {
                                                    $width = 200;
                                                } else {
                                                    $width = $imgsize[0];
                                                }
                                                ?>
                                                        <img src="<?php echo JURI::base();
                                                ?>media/bearleague/<?php echo $photos->filename ?>" class="img-responsive w50" width="<?php echo $width;
                                                ?>" />
                                                    </td>
                                                </tr>
                                                <?php

                                            }
                                        }
    ?>
                                    </tbody>
                                </table>
                            <?php 
} ?>
                        </div>
                        <div class="jsClear"></div>
                    </div>
                </div>
                <script type="text/javascript">
    var photo1 = document.getElementById("player_photo_1");
    var photo2 = document.getElementById("player_photo_2");
    var but_on = document.getElementById("player_photo");
    var serv_sett = <?php echo $lists['post_max_size']; ?>;
    but_on.onclick = function() {
        if (photo1.files[0]) {
            var size_img = photo1.files[0].size;
        } else if (photo2.files[0]) {
            var size_img = photo2.files[0].size;
        }

        if (size_img > serv_sett) {
            alert("Image too big (change settings post_max_size)");
            return false;
        } else {
            submitbutton('match_apply');
        }
    };
                </script>
                <?php if (!$lists['t_single']) {
    ?>
                <div role="tabpanel" class="tab-pane" id="squad">
                    <h5><?php echo JText::_('BLFA_LINEUP');
    ?>:</h5>
                    <div class="row">
                        <div class="table-responsive col-xs-6 col-lg-6 tableRight">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-center"><?php echo $lists['teams1'];
    ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo JText::_('BLFA_PLAYER');
    ?></th>
                                        <th><?php echo JText::_('BLFA_LINEUP');
    ?>:</th>
                                        <th><?php echo JText::_('BLFA_SUBSTITUTES');
    ?>:</th>
                                    </tr>
                                </thead>
                                <tbody id="new_squard1">
                                    <?php
                                    if (count($lists['pl1'])) {
                                        foreach ($lists['pl1'] as $m_events) {
                                            echo '<tr>';
                                            echo '<td>'.$m_events->p_name.'</td>';
                                            $main_chk = false;
                                            $main_chk_r = false;
                                            if (count($lists['squard1']) && in_array($m_events->pid, $lists['squard1'])) {
                                                $main_chk = true;
                                            }
                                            if (count($lists['squard1_res']) && in_array($m_events->pid, $lists['squard1_res'])) {
                                                $main_chk_r = true;
                                            }

                                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team1_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team1_id, $this->lists['teams_season'])))) {
                                                echo '<td align="center">'.($main_chk ? "<img src='components/com_joomsport/img/ico/active.png' />" : '&nbsp;').'</td>';
                                                echo '<td align="center">'.($main_chk_r ? "<img src='components/com_joomsport/img/ico/active.png' />" : '&nbsp;').'</td>';
                                            } else {
                                                echo '<td><input type="checkbox" name="t1_squard[]" id="t1sq_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk ? "checked='true'" : '').' onclick="sqchng(\'t1sq_'.$m_events->pid.'\',\'t1sqr_'.$m_events->pid.'\');" /></td>';
                                                echo '<td><input type="checkbox" name="t1_squard_res[]" id="t1sqr_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk_r ? "checked='true'" : '').' onclick="sqchng(\'t1sqr_'.$m_events->pid.'\',\'t1sq_'.$m_events->pid.'\');"  /></td>';
                                            }
                                            echo '</tr>';
                                        }
                                    }
    ?>                       
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive col-xs-6 col-lg-6 tableLeft">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-center"><?php echo $lists['teams2'];
    ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo JText::_('BLFA_PLAYER');
    ?></th>
                                        <th><?php echo JText::_('BLFA_LINEUP');
    ?>:</th>
                                        <th><?php echo JText::_('BLFA_SUBSTITUTES');
    ?>:</th>
                                    </tr>
                                </thead>
                                <tbody id="new_squard2">
                                    <?php
                                    if (count($lists['pl2'])) {
                                        foreach ($lists['pl2'] as $m_events) {
                                            echo '<tr>';
                                            echo '<td>'.$m_events->p_name.'</td>';
                                            $main_chk = false;
                                            $main_chk_r = false;
                                            if (count($lists['squard2']) && in_array($m_events->pid, $lists['squard2'])) {
                                                $main_chk = true;
                                            }
                                            if (count($lists['squard2_res']) && in_array($m_events->pid, $lists['squard2_res'])) {
                                                $main_chk_r = true;
                                            }
                                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team2_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team2_id, $this->lists['teams_season'])))) {
                                                echo '<td align="center">'.($main_chk ? "<img src='components/com_joomsport/img/ico/active.png' />" : '&nbsp;').'</td>';
                                                echo '<td align="center">'.($main_chk_r ? "<img src='components/com_joomsport/img/ico/active.png' />" : '&nbsp;').'</td>';
                                            } else {
                                                echo '<td><input type="checkbox" name="t2_squard[]" id="t2sq_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk ? "checked='true'" : '').' onclick="sqchng(\'t2sq_'.$m_events->pid.'\',\'t2sqr_'.$m_events->pid.'\');" /></td>';
                                                echo '<td><input type="checkbox" name="t2_squard_res[]" id="t2sqr_'.$m_events->pid.'" value="'.$m_events->pid.'" '.($main_chk_r ? "checked='true'" : '').' onclick="sqchng(\'t2sqr_'.$m_events->pid.'\',\'t2sq_'.$m_events->pid.'\');"  /></td>';
                                            }
                                            echo '</tr>';
                                        }
                                    }
    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive col-xs-6 col-lg-6 tableRight">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo JText::_('BLFA_PLAYERIN');
    ?></th>
                                        <th><?php echo JText::_('BLFA_PLAYEROUT');
    ?></th>
                                        <th><?php echo JText::_('BLFA_MINUTES');
    ?></th>
                                    </tr>
                                </thead>
                                <tbody id="subsid_1">
                                        
                                    <?php
                                    if (count($lists['subsin1'])) {
                                        for ($i = 0; $i < count($lists['subsin1']); ++$i) {
                                            $subs = $lists['subsin1'][$i];
                                            echo '<tr>';
                                            echo '<td>';
                                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team1_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team1_id, $this->lists['teams_season'])))) {
                                                echo '&nbsp;';
                                            } else {
                                                echo '<button type="button" onclick="Delete_tbl_row(this); return false;" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only">'.JText::_('BLFA_DELETE').'</span></button>';
                                            }
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<input type="hidden" value="'.$subs->player_in.'" name="playersq1_id_arr[]" />'.$subs->plin;
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<input type="hidden" value="'.$subs->player_out.'" name="playersq1_out_id_arr[]" />'.$subs->plout;
                                            echo '</td>';
                                            echo '<td>';
                                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team1_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team1_id, $this->lists['teams_season'])))) {
                                                echo $subs->minutes;
                                            } else {
                                                echo '<input class="score" type="text" value="'.$subs->minutes.'" name="minutes1_arr[]" maxlength="5" size="5" />';
                                            }
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    }
    ?>                             
                                </tbody>
                            </table>
                            <?php
                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team1_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team1_id, $this->lists['teams_season'])))) {
                            } else {
                                ?>
                            <table class="table">
                                <tr>
                                    <td><?php echo JText::_('BLFA_PLAYERIN');
                                ?></td>
                                        
                                    <td>
                                        <?php echo $lists['players_team1'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_('BLFA_PLAYEROUT');
                                ?></td>
                                     
                                    <td>
                                        <?php echo $lists['players_team1_out'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_('BLFA_MINUTES');
                                ?></td>
                                    <td>
                                        <input class="score" type="text" name="minutes1" id="minutes1" value="" maxlength="5" size="5" />
                                                                                  
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><button onclick="js_add_subs('subsid_1', 'playersq1_id', 'playersq1_out_id', 'minutes1');" type="button" class="btn btn-default add"><?php echo JText::_('BLFA_ADD');
                                ?></button>  </td>
                                </tr>
                            </table>    
                            <?php 
                            }
    ?>  
                        </div>
                        <div class="table-responsive col-xs-6 col-lg-6 tableLeft">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo JText::_('BLFA_PLAYERIN');
    ?></th>
                                        <th><?php echo JText::_('BLFA_PLAYEROUT');
    ?></th>
                                        <th><?php echo JText::_('BLFA_MINUTES');
    ?></th>
                                    </tr>
                                </thead>
                                <tbody id="subsid_2">
                                   <?php
                                    if (count($lists['subsin2'])) {
                                        for ($i = 0; $i < count($lists['subsin2']); ++$i) {
                                            $subs = $lists['subsin2'][$i];
                                            echo '<tr>';
                                            echo '<td>';
                                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team2_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team2_id, $this->lists['teams_season'])))) {
                                                echo '&nbsp;';
                                            } else {
                                                echo '<button type="button" onclick="Delete_tbl_row(this); return false;" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only">'.JText::_('BLFA_DELETE').'</span></button>';
                                            }
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<input type="hidden" value="'.$subs->player_in.'" name="playersq2_id_arr[]" />'.$subs->plin;
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<input type="hidden" value="'.$subs->player_out.'" name="playersq2_out_id_arr[]" />'.$subs->plout;
                                            echo '</td>';
                                            echo '<td>';
                                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team2_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team2_id, $this->lists['teams_season'])))) {
                                                echo $subs->minutes;
                                            } else {
                                                echo '<input class="score" type="text" value="'.$subs->minutes.'" name="minutes2_arr[]" maxlength="5" size="5" />';
                                            }
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    }
    ?>                                
                                </tbody>
                            </table>
                             <?php
                            if ($this->acl != 1 && (($this->lists['jsmr_edit_squad_opposite'] == 0 && !in_array($row->team2_id, $this->lists['teams_season'])) || ($this->lists['jsmr_edit_squad_yours'] == 0 && in_array($row->team2_id, $this->lists['teams_season'])))) {
                            } else {
                                ?>
                                <table class="table">
                                    <tr>
                                        <td><?php echo JText::_('BLFA_PLAYERIN');
                                ?></td>

                                        <td>
                                            <?php echo $lists['players_team2'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo JText::_('BLFA_PLAYEROUT');
                                ?></td>

                                        <td>
                                            <?php echo $lists['players_team2_out'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo JText::_('BLFA_MINUTES');
                                ?></td>
                                        <td>
                                            <input class="score" type="text" name="minutes2" id="minutes2"  value="" maxlength="5" size="5" />
                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><button onclick="js_add_subs('subsid_2', 'playersq2_id', 'playersq2_out_id', 'minutes2');"  type="button" class="btn btn-default add"><?php echo JText::_('BLFA_ADD');
                                ?></button>                                            
                                        </td>
                                    </tr>
                                </table>
                                
                                <?php

                            }
    ?>
                        </div>
                    </div>
                </div>
                <?php 
} ?>
            </div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="id" value="<?php echo $row->id ?>" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="sid" value="<?php echo $s_id ?>" />
            <input type="hidden" name="cid[]" value="<?php echo $row->m_id ?>" />
            <input type="hidden" name="isapply" value="0" />
            <input type="hidden" name="jscurtab" id="jscurtab" value="" />
            <?php if ($this->acl == 2): ?>
                <input type="hidden" name="tid" value="<?php echo $lists['tid'] ?>" />
            <?php endif; ?>
            <?php echo JHTML::_('form.token'); ?>
        </form>
    </div>
</div>
    </div>