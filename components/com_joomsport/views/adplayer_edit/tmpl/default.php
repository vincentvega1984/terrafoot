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
JHTML::_('behavior.formvalidation');
if (isset($this->message)) {
    $this->display('message');
}

$row = $this->row;

$lists = $this->lists;

$Itemid = JRequest::getInt('Itemid');

for ($i = 0; $i < count($this->lists['player_reg']); ++$i) {
    foreach ($this->lists['player_reg'][$i] as $dta) {
        $tmp[$i][] = '\''.addslashes($dta).'\'';
    }
}
for ($j = 0; $j < count($tmp); ++$j) {
    $arr1[] = $tmp[$j][0];
    $arr2[] = $tmp[$j][1];
}

$fname = implode(',', $arr1);
$lname = implode(',', $arr2);

if (count($lists['ext_fields'])) {
    foreach ($lists['ext_fields'] as $ext) {
        if ($ext->reg_require == 1) {
            $arr_extra[] = $ext->id;
        }
    }
    $extra_id = isset($arr_extra) ? "'".implode("','", $arr_extra)."'" : '';
}

if ($this->lists['reg_lastname'] == 1) {
    ?>
    <script type="text/javascript">

        function confirmDelete(pressbutton) {
            var fName = document.getElementsByName('first_name')[0].value;
            var lName = document.getElementsByName('last_name')[0].value;
            var msg = '';

            var arrFName = new Array(<?php echo $fname;?>);
            var arrLName = new Array(<?php echo $lname;?>);

            for (var i = 0; i < arrFName.length; i++) {
                if (arrFName[i] == fName && arrLName[i] == lName) {
                    msg = 1;
                }

            }

            if (msg) {
                if (confirm("Player with such First Name and Last Name already exist. Do you wants to continue?")) {
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

    </script>
<?php 
} ?>
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
            <h2 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $row->id ? JText::_('BLFA_PLAYER_EDIT') : JText::_('BLFA_PLAYER_NEW'); ?></h2>
            <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">
                <?php if ($this->acl == 2): ?>

                    <form action='<?php echo JURI::base(); ?>index.php?option=com_joomsport&task=adplayer_edit&controller=moder&tid=<?php echo $lists['tid']; ?>&cid[]=<?php echo $row->id ?>&Itemid=<?php echo $Itemid ?>' method='post' name='chg_team'>
                        <?php if (isset($this->lists['seass_filtr'])) {
    ?>
                            <label class="selected"><?php echo $this->lists['tourn_name'];
    ?></label>
                            <div class="data">
                                <?php echo $this->lists['seass_filtr'];
    ?>                            
                            </div>
                        <?php 
} ?>
                    </form>
                <?php endif; ?>
            </div>            
        </div>        
        <div class="tools col-xs-12 col-lg-12 text-right"> 
            <?php if ($this->acl == 1) {
    ?>
                <?php if ($lists['jssa_editplayer'] || !$row->id) {
    ?>
                    <a href="javascript:return false;" title="<?php echo JText::_('BLFA_SAVE') ?>" onclick="javascript:submitbutton('adplayer_save');
                return false;"><i class="save"></i> <?php echo JText::_('BLFA_SAVE') ?></a>
                   <?php 
}
    ?>
                <a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_player&sid='.$lists['s_id'].'&Itemid='.$Itemid);
    ?>" title="<?php echo JText::_('BLFA_CLOSE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_CLOSE') ?></a>
            <?php 
} else {
    ?>
                <a href="#" title="<?php echo JText::_('BLFA_SAVE') ?>" onclick="javascript:submitbutton('adplayer_save');
            return false;"><i class="save"></i> <?php echo JText::_('BLFA_SAVE') ?></a>
                <a href="#" onclick="javascript:submitbutton('admin_player');
            return false;" title="<?php echo JText::_('BLFA_CLOSE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_CLOSE') ?></a>
               <?php 
} ?>
        </div>
        <!-- Nav tabs -->
        <?php
        if ($this->acl == 2 && !$lists['canmore']) {
            echo '<div>'.JText::_('BLFA_PLAYERLIMITIS').'</div>';
        } else {
            if (!count($row)) {
                echo "<div id='system-message'>".JText::_('BLFA_NOITEMS').'</div>';
            }
            $formlink = JURI::base().'index.php?option=com_joomsport&controller=admin&sid='.$lists['s_id'].'&Itemid='.$Itemid;
            if ($this->acl == 2) {
                $formlink = JURI::base().'index.php?option=com_joomsport&controller=moder&tid='.$lists['tid'].'&Itemid='.$Itemid;
            }
            ?>   
            <script type="text/javascript">
        Joomla.submitbutton = function(task) {
            submitbutton(task);
        }
        function submitbutton(pressbutton) {
            var arrId = new Array(<?php echo $extra_id;?>)
            var form = document.adminForm;
            if (pressbutton == 'adplayer_apply' || pressbutton == 'adplayer_save') {
                var del = document.formvalidator.isValid(form);
                if (del == false) {
                    alert('<?php echo JText::_('BLFA_JSMDNOT1');
            ?>');
                } else {
                    if ('<?php echo isset($lists['teams_seas']) ?>' == '1' && form.teams_seas.value == 0) {
                        alert('<?php echo JText::_('BLFA_SELTEAM');
            ?>');
                    } else {
                        var player_f = "<?php echo $row->first_name;
            ?>";
                        if (!player_f && '<?php echo $this->lists['reg_lastname'] ?>' == '1') {
                            confirmDelete(pressbutton);
                        } else {
                            submitform(pressbutton);
                            return;
                        }
                    }
                }
            } else {
                submitform(pressbutton);
                return;
            }
        }
            </script>
            <div class="jsClear"></div>
            <form action="<?php echo $formlink;
            ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate" >
                <div class="">

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo JText::_('User');
            ?>: <i class="fa fa-question-circle"></i></label>
                        <div class="col-sm-4">
                            <?php echo $lists['usrid'];
            ?>                                
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo JText::_('BLFA_FIRSTNAME');
            ?>: <i class="fa fa-question-circle"></i></label>
                        <div class="col-sm-4">
                            <input type="text" maxlength="255" class="required" id="first_name" name="first_name" value="<?php echo htmlspecialchars($row->first_name) ?>" />                                
                        </div>
                    </div>
                    <?php if ($lists['reg_lastname']) {
    ?>	
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo JText::_('BLFA_LASTNAME');
    ?>: <i class="fa fa-question-circle"></i></label>
                            <div class="col-sm-4">
                                <input type="text" maxlength="255" class="<?php echo ($this->lists['reg_lastname_rq'] == 1) ? (' required') : ('') ?>" id="last_name" name="last_name" value="<?php echo htmlspecialchars($row->last_name);
    ?>" />                                
                            </div>
                        </div>

                        <?php

}
            if ($lists['nick_reg']) {
                ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo JText::_('BL_NICK');
                ?>: <i class="fa fa-question-circle"></i></label>
                            <div class="col-sm-4">
                                <input type="text" maxlength="255" class="<?php echo ($this->lists['nick_reg_rq']) ? ' required' : '';
                ?>" id="nick" name="nick" value="<?php echo htmlspecialchars($row->nick) ?>" />                                
                            </div>
                        </div>
                        <?php

            }
            if ($lists['country_reg']) {
                ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo JText::_('BL_COUNTRY');
                ?>: <i class="fa fa-question-circle"></i></label>
                            <div class="col-sm-4">
                                <?php echo $lists['country'] ?>
                            </div>
                        </div>
                    <?php 
            }
            ?>
                    <?php if (isset($lists['teams_seas'])): ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo JText::_('BLFA_TEAM');
            ?>: <i class="fa fa-question-circle"></i></label>
                            <div class="col-sm-4">
                                <?php echo $lists['teams_seas'] ?>
                            </div>
                        </div>
                    <?php endif;
            ?>

                    <?php
                    for ($p = 0; $p < count($lists['ext_fields']); ++$p) {
                        if ($lists['ext_fields'][$p]->field_type == '3' && !isset($lists['ext_fields'][$p]->selvals)) {
                            //
                        } else {
                            if ($lists['s_id'] && $lists['ext_fields'][$p]->season_related) {
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
                        <label for="about" class="col-sm-2 control-label"><?php echo JText::_('BLFA_ABOUT_PLAYER');
            ?>: <i class="fa fa-question-circle"></i></label>
                        <div class="col-sm-6">
                            <textarea  name="about" id="about" rows="6"><?php echo htmlspecialchars($row->about, ENT_QUOTES);
            ?></textarea>
                        </div>
                    </div>


                    <div class="upload col-xs-12 col-lg-12">
                        <?php
                        if ($this->acl == 1 && !$lists['jssa_editplayer'] && $row->id) {
                        } else {
                            ?> 
                            <div class="form-group">
                                <div class="jsDivCenter"><label><?php echo JText::_('BLFA_UPLOADPHOT');
                            ?> <i class="fa fa-question-circle"></i></label></div>
                                <div class="col-xs-12">
                                    <input type="file" id="player_photo_1" name="player_photo_1">                                    
                                </div>
                                <div class="col-xs-12">
                                    <input type="file" id="player_photo_2" name="player_photo_2">                                    
                                </div>
                                <div class="col-xs-12">                                    
                                    <button id="player_photo" type="button" class="btn"><?php echo JText::_('BLFA_UPLOAD');
                            ?></button>
                                </div>
                            </div>
                            <script type="text/javascript">
                                var photo1 = document.getElementById("player_photo_1");
                                var photo2 = document.getElementById("player_photo_2");
                                var but_on = document.getElementById("player_photo");
                                var serv_sett = <?php echo $lists['post_max_size'];
                            ?>;
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
                                        submitbutton('<?php echo ($this->acl == 1) ? 'adplayer_apply' : 'adplayer_save';
                            ?>');
                                    }
                                };
                            </script>
                        <?php 
                        }
            ?>
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
}
            ?>
                        </div>
                    </div>
                    <div class="jsClear"></div>
                </div>
                <input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
                <?php echo JHTML::_('form.token');
            ?>
            </form>
        <?php 
        } ?>
    </div>
</div>
</div>