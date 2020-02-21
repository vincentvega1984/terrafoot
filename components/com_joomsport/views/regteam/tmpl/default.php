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

$Itemid = JRequest::getInt('Itemid');
$lists = $this->lists;
foreach ($this->lists['team_reg'] as $dta) {
    $tmp[] = '\''.addslashes($dta).'\'';
}
$new_tmp = implode(',', $tmp);
?>
<script type="text/javascript">
    function confirmDelete() {
        var tagName = document.getElementsByName('t_name')[0].value;
        var msg;

        var arrTeam = new Array(<?php echo $new_tmp; ?>)

        for (var i = 0; i < arrTeam.length; i++) {
            if (arrTeam[i] == tagName) {
                msg = 1;
            }

        }
        if (msg) {
            if (confirm('<?php echo JText::_('BLFA_WARNTEAM') ?>')) {
                return true;
            } else {
                return false;
            }
        }
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
    <?php if ($this->lists['canmore']) {
    ?>
        <form method="POST" action="<?php echo 'index.php?option=com_joomsport&task=teamreg_save&Itemid='.$Itemid;
    ?>" enctype="multipart/form-data" id="regteam-form" class="form-horizontal form-validate" >
            <div class="main newTeam">
                <div class="heading col-xs-12 col-lg-12">
                    <h2 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo $this->escape($this->ptitle);
    ?></h2>
                </div>
                <div class="navbar-link col-xs-12 col-lg-12">
            
                </div>

                <div class="jsClear"></div>
                <div class="col-xs-12 col-lg-12">

                    <div class="form-group">
                        <label for="inputNewTeam" class="col-xs-3 col-sm-3 control-label"><?php echo JText::_('BLFA_TEAMNAME');
    ?></label>
                        <div class="col-xs-9 col-sm-6">
                            <input type="text" class="required" name="t_name" id="inputNewTeam" placeholder="">
                        </div>
                    </div>
                    <?php
                    $fCity = $this->_model->getCustomField('team_city', array('team_id' => $Itemid));
    if ($fCity['enabled']):
                        ?>
                        <div class="form-group">
                            <label for="inputCity" class="col-xs-3 col-sm-3 control-label"><?php echo $fCity['title'];
    ?><?php if ($fCity['required']): ?> *<?php endif;
    ?></label>
                            <div class="col-xs-9 col-sm-6">
                                <input name="<?php echo $fCity['input_name'];
    ?>" value="<?php echo $fCity['value'];
    ?>" type="text" class=" <?php if ($fCity['required']): ?> required<?php endif;
    ?>" id="inputCity" placeholder="">
                            </div>
                        </div>
                    <?php endif;
    ?>
                    <div class="form-group">
                        <label for="inputCaptain" class="col-xs-3 col-sm-3 control-label"><?php echo JText::_('BL_TEAMCAP');
    ?></label>
                        <div class="col-xs-9 col-sm-6">
                            <label class="control-label"><?php echo $this->lists['cap'] ?></label>
                        </div>
                    </div>
                    <?php
                    for ($i = 0; $i < count($this->lists['adf']); ++$i) {
                        $adfs = $this->lists['adf'][$i];
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 col-sm-3 control-label"><?php echo $adfs->name.($adfs->reg_require ? ' *' : '');
                        ?></label>
                            <div class="col-xs-9 col-sm-6">
                                <input type="hidden" name="extra_id[<?php echo $adfs->id;
                        ?>]" value="<?php echo $adfs->id;
                        ?>" />
                                <input type="hidden" name="extra_ftype[<?php echo $adfs->id;
                        ?>]" value="<?php echo $adfs->field_type;
                        ?>" />
                        <?php 
                        switch ($adfs->field_type) {

                            case '1': echo $adfs->selvals;
                                break;
                            case '2': echo '<textarea name="extraf['.$adfs->id.']" rows="6">'.htmlspecialchars(isset($adfs->fvalue_text) ? ($adfs->fvalue_text) : '', ENT_QUOTES).'</textarea>';
                                break;
                            case '3': echo $adfs->selvals;
                                break;
                            case '0':
                            default: echo '<input type="text" class="'.($adfs->reg_require ? ' required' : '').'" maxlength="255" name="extraf['.$adfs->id.']" value="" />';
                                break;
                        }
                        echo '</div></div>';
                    }
    ?>
                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-9 col-sm-offset-3 col-sm-6">                                
                            <button class="btn send-button validate button" type="submit" onclick="return document.formvalidator.isValid(document.id('regteam-form'));"><?php echo JText::_('BLFA_REGGG');
    ?></button>
                        </div>
                    </div>

                </div>
            </div>

            <input type="hidden" name="id" value="0" />
        </form></div>
                <div class="jsClear"></div>
    <?php

} else {
    echo '<div>'.JText::_('BLFA_TEAMLIMITIS').'</div>';
}
?>
</div>