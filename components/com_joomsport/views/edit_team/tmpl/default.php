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
$Itemid = JRequest::getInt('Itemid');

$new_tmp = '';
if (!empty($this->lists['team_reg'])) {
    $tmp = array_map('addslashes', $this->lists['team_reg']);
    $new_tmp = "'".implode("','", $tmp)."'";
}
$extra_id = '';
if (count($lists['ext_fields'])) {
    foreach ($lists['ext_fields'] as $ext) {
        if ($ext->reg_require == 1) {
            $arr_extra[] = $ext->id;
        }
    }
    $extra_id = isset($arr_extra) ? "'".implode("','", $arr_extra)."'" : '';
}
$fCity = $this->_model->getCustomField('team_city', array('team_id' => $Itemid));
?>
<script type="text/javascript">
    function confirmDelete(pressbutton) {
        var arrId = new Array(<?php echo $extra_id; ?>);
        var tagName = document.getElementsByName('t_name')[0].value;
        var msg;

        var arrTeam = new Array(<?php echo $new_tmp; ?>)

        for (var i = 0; i < arrTeam.length; i++) {
            if (arrTeam[i] == tagName) {
                msg = 1;
            }

        }
        for (var i = 0; i < arrId.length; i++) {
            valExtra = document.getElementsByName('extraf[' + arrId[i] + ']')[0].value;
            if (valExtra == "") {
                document.getElementsByName('extraf[' + arrId[i] + ']')[0].style.setProperty("border-color", "red", "important");
                return;
            }
        }
        if (msg) {
            if (confirm('<?php echo addslashes(JText::_('BLFA_WARNTEAM')); ?>')) {
                submitform(pressbutton);
                return;
            } else {
                return false;
            }
        } else {
            submitform(pressbutton);
            return;
        }

    }
    function bl_submit(task, chk) {
        if (chk == 1 && document.adminForm.boxchecked.value == 0) {
            alert('<?php echo JText::_('BLFA_SELECTITEM') ?>');
        } else {
            document.adminForm.task.value = task;
            document.adminForm.submit();
        }
    }
    function delete_logo() {
        getObj("logoiddiv").innerHTML = '';
    }


    function submitbutton(pressbutton) {
        var form = document.adminForm;
        var arrId = new Array(<?php echo $extra_id; ?>);
        if (pressbutton == 'team_save' || pressbutton == 'team_apply') {


            if (form.t_name.value != "") {
<?php if (!(empty($fCity['enabled']) || empty($fCity['required']))): ?>
                    if (form.t_city.value.trim() == "") {
                        alert("<?php echo JText::_('BLFA_ENTERCITY'); ?>");
                        return;
                    }
<?php endif; ?>
                var team_n = "<?php echo htmlspecialchars($row->t_name); ?>";

                if (!team_n) {
                    confirmDelete(pressbutton);

                } else {
                    for (var i = 0; i < arrId.length; i++) {
                        valExtra = document.getElementsByName('extraf[' + arrId[i] + ']')[0].value;
                        if (valExtra == "") {
                            document.getElementsByName('extraf[' + arrId[i] + ']')[0].style.setProperty("border-color", "red", "important");
                            return;
                        }
                    }
                    submitform(pressbutton);
                    return;
                }
            } else {
                alert("<?php echo JText::_('BLFA_ENTERNAME') ?>");
            }
        } else {
            submitform(pressbutton);
            return;
        }
    }

    function addplayer() {
        if (getObj('playerz_id').value == 0) {
            alert("<?php echo JText::_('BLFA_SELPLAYER'); ?>");
            return false;
        }

        var tbl = getObj('add_pl');
        var row = tbl.insertRow(tbl.rows.length - 1);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");

        var input_hd = document.createElement('input');
        input_hd.type = 'hidden';
        input_hd.name = 'teampl[]';
        input_hd.value = getObj('playerz_id').value;
        cell1.innerHTML = '<button onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE'); ?>" type="button" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE'); ?></span></button>';
        cell1.appendChild(input_hd);
        cell2.innerHTML = getObj('playerz_id').options[getObj('playerz_id').selectedIndex].text;
        cell2.className = 'w97';
        row.appendChild(cell1);
        row.appendChild(cell2);

        if ('<?php echo $this->acl; ?>' == '2') {
            if ('<?php echo isset($lists['esport_invite_player']) ? $lists['esport_invite_player'] : 0; ?>' == '1') {
                var cell3 = document.createElement("td");
                cell3.style.textAlign = 'center';
                cell3.align = "center";
                cell3.innerHTML = '<img src="components/com_joomsport/img/ico/negative.png" width="20" border="0" alt="">';
                row.appendChild(cell3);
            }
        }
    }

    function inviteemail() {
        var regex = /^[a-zA-Z0-9._-]+(\+[a-zA-Z0-9._-]+)*@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
        if (!regex.test(getObj('invemail').value)) {
            alert("Incorrect email");
            return false;
        }
        var tbl = getObj('initetbl');
        var row = tbl.insertRow(tbl.rows.length - 1);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");
        cell1.style.width = '50px';
        cell1.innerHTML = '<button onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('BLFA_DELETE'); ?>" type="button" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE'); ?></span></button>';
        cell2.innerHTML = getObj('invemail').value;
        cell2.className = 'w97';
        var input_hd = document.createElement('input');
        input_hd.type = 'hidden';
        input_hd.name = 'emlinv[]';
        input_hd.value = getObj('invemail').value;
        cell2.appendChild(input_hd);
        row.appendChild(cell1);
        row.appendChild(cell2);
        getObj('invemail').value = "";
    }
</script>
<div id="joomsport-container">
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        echo $lists['panel'];
        ?>
    </nav>
    <!-- /.navbar -->

    <div class="main editTeam">
        <div class="heading col-xs-12 col-lg-12">
            <h2 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $row->id ? JText::_('BLFA_TEAM_EDIT') : JText::_('BLFA_NTEAM'); ?></h2>
            <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">
                <?php if ($this->acl == 2): ?>
                    <form action='<?php echo JURI::base(); ?>index.php?option=com_joomsport&task=team_edit&controller=moder&Itemid=<?php echo $Itemid ?>' method='post' name='chg_team'>
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
                            <?php 
} ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <?php if ($this->lists['waiting_players_count']) {
    ?>
                <h6 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo JText::_('BLFA_APPROVE').':';
    ?>:</h6>
                <?php for ($i = 0; $i < count($this->lists['waiting_players_count']); ++$i) {
    ?>
                    <a href="<?php echo JURI::base();
    ?>index.php?option=com_joomsport&task=team_edit&controller=moder&tid=<?php echo $row->id;
    ?>&moderseason=<?php echo $this->lists['waiting_players_count'][$i]->s_id;
    ?>&jscurtab=etab_pl" class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4" href="#"><?php echo $this->lists['waiting_players_count'][$i]->s_name.' ('.$this->lists['waiting_players_count'][$i]->kol.')';
    ?></a><br/>
                <?php 
}
    ?>
            <?php 
} ?>
        </div>
        <div class="navbar-link col-xs-12 col-lg-12">
            <?php if ($this->acl != 1) {
    ?>
                <ul>
                    <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_team&tid='.$row->id.'&Itemid='.$Itemid);
    ?>"><?php echo JText::_('BLFA_TEAM') ?></a></li>
                    <?php if (!empty($lists['moder_matchday'])): ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_matchday&tid='.$row->id.'&Itemid='.$Itemid) ?>"><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                    <?php endif;
    ?>
                    <?php if ($lists['moder_addplayer']) {
    ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=admin_player&tid='.$row->id.'&Itemid='.$Itemid) ?>"><?php echo JText::_('BLFA_PLAYER') ?></a></li>
                    <?php 
}
    ?>
                </ul>
            <?php 
} ?>
        </div>
        <div class="tools col-xs-12 col-lg-12 text-right"> 
            <a href="#" title="<?php echo JText::_('BLFA_SAVE') ?>" onclick="javascript:submitbutton('team_save');
        return false;"><i class="save"></i> <?php echo JText::_('BLFA_SAVE') ?></a> 
               <?php if ($this->acl == 1): ?>
                <a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_team&sid='.$lists['s_id'].'&Itemid='.$Itemid); ?>" title="<?php echo JText::_('BLFA_CLOSE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_CLOSE') ?></a> 
            <?php endif; ?>
        </div>
        <!-- Nav tabs -->
        <div class="jsClear"></div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#team" role="tab" data-toggle="tab"><i class="hidden-xs flag"></i><?php echo JText::_('BLFA_TEAM'); ?></a></li>
            <li role="presentation"><a href="#players" role="tab" data-toggle="tab"><i class="users"></i><?php echo JText::_('BLFA_PLAYER'); ?></a></li>
        </ul>
        <form action="" method="post" name="adminForm" id="adminForm" role="form" class="form-horizontal form-validate" enctype="multipart/form-data">        
            <!-- Tab panels -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="team">
                    <div class="">

                        <div class="form-group">
                            <label for="t_name" class="col-sm-2 control-label"><?php echo JText::_('BLFA_TEAMNAME'); ?>: <i class="fa fa-question-circle"></i></label>
                            <div class="col-sm-4">
                                <input type="text" maxlength="255" name="t_name" id="t_name" value="<?php echo htmlspecialchars($row->t_name) ?>" />                                
                            </div>
                        </div>
                        <?php if ($fCity['enabled']): ?>
                            <div class="form-group">
                                <label for="t_city" class="col-sm-2 control-label"><?php echo JText::_('BLFA_CITY'); ?>: <i class="fa fa-question-circle"></i></label>
                                <div class="col-sm-4">
                                    <input type="text" maxlength="255" id="t_city" name="t_city" value="<?php echo htmlspecialchars($row->t_city) ?>" />                                
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($lists['enbl_club']): ?>
                            <div class="form-group">
                                <label for="t_city" class="col-sm-2 control-label"><?php echo JText::_('BLFA_CLUB');?>: </label>
                                <div class="col-sm-4">
                                    <?php echo $lists['club'];?>                             
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php
                        for ($p = 0; $p < count($lists['ext_fields']); ++$p) {
                            if ($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)) {
                            } else {
                                if ($lists['s_id'] > 0 && $lists['ext_fields'][$p]->season_related) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $lists['ext_fields'][$p]->name;
                                    ?></label>
                                        <div class="col-sm-4">
                                            <?php
                                            switch ($lists['ext_fields'][$p]->field_type) {

                                                case '1': echo $lists['ext_fields'][$p]->selvals;
                                                    break;
                                                case '2': echo '<textarea name="extraf['.$lists['ext_fields'][$p]->id.']" rows="6">'.htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text) ? ($lists['ext_fields'][$p]->fvalue_text) : '', ENT_QUOTES).'</textarea>';
                                                    break;
                                                case '3': echo $lists['ext_fields'][$p]->selvals;
                                                    break;
                                                case '0':
                                                default: echo '<input type="text" class="'.($lists['ext_fields'][$p]->reg_require ? ' required' : '').'" maxlength="255" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue) ? htmlspecialchars($lists['ext_fields'][$p]->fvalue) : '').'" />';
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
                                } elseif (!$lists['ext_fields'][$p]->season_related && $lists['ext_fields'][$p]->reg_exist) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $lists['ext_fields'][$p]->name;
                                    ?></label>
                                        <div class="col-sm-4">
                                            <?php
                                            switch ($lists['ext_fields'][$p]->field_type) {

                                                case '1': echo $lists['ext_fields'][$p]->selvals;
                                                    break;
                                                case '2': echo '<textarea name="extraf['.$lists['ext_fields'][$p]->id.']"  rows="6">'.htmlspecialchars(isset($lists['ext_fields'][$p]->fvalue_text) ? ($lists['ext_fields'][$p]->fvalue_text) : '', ENT_QUOTES).'</textarea>';
                                                    break;
                                                case '3': echo $lists['ext_fields'][$p]->selvals;
                                                    break;
                                                case '0':
                                                default: echo '<input type="text" class="'.($lists['ext_fields'][$p]->reg_require ? ' required' : '').'" maxlength="255" name="extraf['.$lists['ext_fields'][$p]->id.']" value="'.(isset($lists['ext_fields'][$p]->fvalue) ? htmlspecialchars($lists['ext_fields'][$p]->fvalue) : '').'" />';
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
                        }
                        ?>
                        <div class="form-group">
                            <label for="uploadPhoto" class="col-xs-12 col-sm-2 control-label"><?php echo JText::_('BLFA_TEAM_LOGO'); ?></label>
                            <div class="col-xs-12 col-sm-6 col-md-5">                                
                                <input type="file" name="t_logo" id="uploadPhoto" />
                                <button class="btn send-button" onclick="javascript:submitbutton('<?php echo ($this->acl == 1) ? 'team_apply' : 'team_save'; ?>');" ><?php echo JText::_('BLFA_UPLOAD'); ?></button>
                            </div>
                        </div>
                        <?php if ($row->t_emblem && is_file('media/bearleague/'.$row->t_emblem)) {
    ?>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 control-label"></label>
                                <div class="col-xs-12 col-sm-6 col-md-5">   
                                    <img width="120" class="img-thumbnail" src="<?php echo JURI::base().'media/bearleague/'.$row->t_emblem ?>" />   
                                    <input type="hidden" name="istlogo" value="1" />
                                    <button type="button" onclick="javascript:delete_logo();" class="closerem"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_REMOVE');
    ?></span></button>
                                </div>
                            </div>                  
                        <?php 
} ?>                        
                        <div class="form-group">
                            <label for="t_descr" class="col-sm-2 control-label"><?php echo JText::_('BLFA_ABOUT_TEAM'); ?>: <i class="fa fa-question-circle"></i></label>
                            <div class="col-sm-6">
                                <textarea name="t_descr" id="t_descr" rows="6"><?php echo htmlspecialchars($row->t_descr, ENT_QUOTES); ?></textarea>
                            </div>
                        </div>            
                        <div class="upload col-xs-12 col-lg-12">
                            <div class="form-group">
                                <div class="jsDivCenter"><label><?php echo JText::_('BLFA_UPLFOTO'); ?> <i class="fa fa-question-circle"></i></label></div>
                                <div class="col-xs-12">
                                    <input type="file" id="player_photo_1" name="player_photo_1">                                    
                                </div>
                                <div class="col-xs-12">
                                    <input type="file" id="player_photo_2" name="player_photo_2">                                    
                                </div>
                                <div class="col-xs-12">                                    
                                    <button id="player_photo" type="button" class="btn"><?php echo JText::_('BLFA_UPLOAD'); ?></button>
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
            submitbutton('<?php echo ($this->acl == 1) ? 'team_apply' : 'team_save'; ?>');
        }
    };
                            </script>
                            <div class="table-responsive col-xs-12 col-lg-12">
                                <?php if (count($lists['photos'])) {
    ?>
                                    <table class="table jsTdCentered">
                                        <thead>
                                            <tr>
                                                <th><?php echo JText::_('BLFA_DELETE');
    ?></th>
                                                <th><?php echo JText::_('BLFA_DEFAULT');
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
                                                    ?>" onclick="javascript:Delete_tbl_row(this);"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_REMOVE');
                                                    ?></span></button>
                                                        </td>
                                                        <td class="">
                                                            <?php
                                                            $ph_checked = ($row->def_img == $photos->id) ? 'checked="true"' : '';
                                                    ?>
                                                            <input type="radio" name="ph_default" value="<?php echo $photos->id;
                                                    ?>" <?php echo $ph_checked ?>/>
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
                        </div>
                        <div class="jsClear"></div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="players">
                    <div class="">
                        <?php if ($this->acl == 1) {
    ?>
                            <div class="table-responsive col-xs-12 col-lg-12">                            	
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="">#</th>
                                            <th class="w97"><?php echo JText::_('BLFA_PLAYER') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="add_pl">
                                        <?php
                                        for ($i = 0; $i < count($lists['team_players']); ++$i) {
                                            $pl = $lists['team_players'][$i];
                                            ?>
                                            <tr class="<?php echo $i % 2 ? '' : 'active' ?>">
                                                <td class="">
                                                    <button type="button" class="closerem" onclick="javascript:Delete_tbl_row(this);
                return false;" title="<?php echo JText::_('BLFA_DELETE') ?>"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE') ?></span></button>
                                                    <input type="hidden" name="teampl[]" value="<?php echo $pl->id ?>" />
                                                </td>
                                                <td class="w97"><?php echo $pl->name ?></td>
                                            </tr>
                                            <?php

                                        }
    ?>                                    
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-lg-12">
                                    <?php
                                    if (!$lists['s_id']) {
                                        echo '<div class="jswarningbox">';
                                        echo '<p>'.JText::_('BLFA_WARN_TEAMNASSIGN').'</p>';
                                        echo '</div>';
                                    }
                                    ?>
                                    <?php echo $lists['player'];
    ?>
                                    <button type="button" class="btn" onclick="addplayer();
            return false;"><?php echo JText::_('BLFA_ADD');
    ?></button>
                                </div>
                            </div>

                            <input type="hidden" name="option" value="com_joomsport" />
                            <input type="hidden" name="controller" value="admin" />
                            <input type="hidden" name="task" value="edit_team" />
                            <input type="hidden" name="id" value="<?php echo $row->id ?>" />
                            <input type="hidden" name="boxchecked" value="0" />
                            <input type="hidden" name="sid" value="<?php echo $lists['s_id'];
    ?>" />
                            <input type="hidden" name="jscurtab" id="jscurtab" value="" />

                        <?php 
} else {
    ?>
                            <?php
                            if (isset($this->lists['seass_filtr']) || !empty($this->lists['is_friendly_season'])) {
                                ?>
                                <div class="table-responsive col-xs-12 col-lg-12">                            	
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="30" class="">#</th>
                                                <th class="w97"><?php echo JText::_('BLFA_PLAYER') ?></th>
                                                <?php
                                                if ($lists['esport_invite_player']) {
                                                    echo '<th class="w97" width="50">'.JText::_('BLFA_CONFIRMED').'</th>';
                                                }
                                ?>
                                            </tr>
                                        </thead>
                                        <tbody id="add_pl">
                                            <?php
                                            for ($i = 0; $i < count($lists['team_players']); ++$i) {
                                                $pl = $lists['team_players'][$i];
                                                ?>
                                                <tr class="<?php echo $i % 2 ? '' : 'active' ?>">
                                                    <td class="">
                                                        <button type="button" class="closerem" onclick="javascript:Delete_tbl_row(this);
                    return false;" title="<?php echo JText::_('BLFA_DELETE') ?>"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo JText::_('BLFA_DELETE') ?></span></button>
                                                        <input type="hidden" name="teampl[]" value="<?php echo $pl->id ?>" />
                                                    </td>
                                                    <td class="w97"><?php echo $pl->name ?></td>
                                                    <?php
                                                    if ($lists['esport_invite_player']) {
                                                        $imgs = ($pl->confirmed == 0) ? 'ico/active.png' : 'ico/negative.png';
                                                        echo '<td align="center" style="text-align:center;"><img src="components/com_joomsport/img/'.$imgs.'" border="0" alt=""></td>';
                                                    }
                                                ?>
                                                </tr>
                                                <?php

                                            }
                                ?>                                    
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12 col-lg-12">
                                        <?php
                                    if (intval($lists['s_id']) < 1) {
                                        echo '<div class="jswarningbox">';
                                        echo '<p>'.JText::_('BLFA_WARN_TEAMNASSIGN').'</p>';
                                        echo '</div>';
                                    }
                                    ?>
                                        <?php echo $lists['player'];
                                ?>
                                        <button type="button" class="btn" onclick="addplayer();
                return false;">
                                                    <?php
                                                    if ($lists['esport_invite_player']) {
                                                        echo JText::_('BLFA_INVITE');
                                                    } else {
                                                        echo JText::_('BLFA_ADD');
                                                    }
                                ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if ($lists['esport_invite_unregister']) {
    ?>			

                                    <div class="form-group">
                                        <div class="col-sm-12"> <span><?php echo JText::_('BLFA_INVITEUNREG');
    ?></span> </div>
                                    </div>
                                    <table class="table table-striped">
                                        <tbody id="initetbl">
                                            <tr>
                                                <td colspan="2">
                                                    <input type="text" id="invemail" name="invemail" value="" />
                                                    <button class="btn send-button" onclick="inviteemail();
                    return false;" ><span><?php echo JText::_('BLFA_INVITE');
    ?></span></button>
                                                </td>
                                            </tr>
                                        </tbody>	
                                    </table>
                                <?php 
}
                                ?>

                                <?php if ($lists['esport_join_team'] && count($lists['waiting_players'])) {
    ?>
                                    <div class="form-group">
                                        <div class="col-sm-12"> <span><?php echo JText::_('BLFA_WAITINGAPPROVAL');
    ?></span> </div>
                                    </div>
                                    <?php for ($z = 0; $z < count($lists['waiting_players']); ++$z) {
    ?>
                                        <div class="form-group">
                                            <label for="select" class="col-sm-1">
                                                <input type="hidden" name="appr_pl[]" value="<?php echo $lists['waiting_players'][$z]->id;
    ?>" />
                                                <?php echo $lists['waiting_players'][$z]->name;
    ?>
                                            </label>
                                            <div class="col-xs-12 col-lg-12">
                                                <?php echo JHTML::_('select.genericlist', $lists['arr_action'], 'action_'.$lists['waiting_players'][$z]->id, ' size="1"', 'id', 'name', 0);
    ?>
                                            </div>
                                        </div>
                                    <?php 
}
    ?>				
                                <?php 
}
                                ?>
                            <?php 
                            }
    ?>
                            <input type="hidden" name="option" value="com_joomsport" />
                            <input type="hidden" name="controller" value="moder" />
                            <input type="hidden" name="task" value="edit_team" />
                            <input type="hidden" name="boxchecked" value="0" />
                            <input type="hidden" name="tid" value="<?php echo $row->id;
    ?>" />
                        <?php 
} ?>
                            <div class="jsClear"></div>
                    </div>
                </div>
            </div>
            <?php echo JHTML::_('form.token'); ?>
        </form>
    </div>
</div>
</div>