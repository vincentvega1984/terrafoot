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
$rows = $this->rows;
$page = $this->page;

global $Itemid;
$Itemid = JRequest::getInt('Itemid');
?>
<script type="text/javascript">
    function bl_submit(task, chk) {
        if (chk == 1 && document.adminForm.boxchecked.value == 0) {
            alert('<?php echo JText::_('BLFA_SELECTITEM') ?>');
        } else {
            document.adminForm.task.value = task;
            document.adminForm.submit();
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

    <div class="main adminPlayer">
        <div class="heading col-xs-12 col-lg-12">

            <?php if ($this->acl == 2) {
    ?>
                <h2 class="pull-left col-xs-12 col-sm-12 col-md-4 col-lg-4"><?php echo JText::_('BLFA_PLAYERSLIST') ?></h2>
                <div class="selection col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-right">
                    <form action='<?php echo JURI::base();
    ?>index.php?option=com_joomsport&task=team_edit&controller=moder&Itemid=<?php echo $Itemid ?>' method='post' name='chg_team'>
                        <div class="data">
                            <?php echo $this->lists['tm_filtr'];
    ?>
                        </div>
                    </form>
                </div>
            <?php 
} else {
    ?>
                <h4 class="text-right"><?php echo $this->lists['tournname'];
    ?></h4>
                <h2><?php echo JText::_('BLFA_PLAYERSLIST') ?></h2>
            <?php 
} ?>

        </div>
        <div class="navbar-link col-xs-12 col-lg-12">
            <ul>
                <?php if ($this->acl == 2) {
    ?>
                    <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=edit_team&tid='.$this->tid.'&Itemid='.$Itemid);
    ?>" title=""><?php echo JText::_('BLFA_TEAM') ?></a></li>
                    <?php if ($this->lists['enmd']): ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&task=edit_matchday&tid='.$this->tid.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                    <?php endif;
    ?>
                    <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=moder&view=admin_player&tid='.$this->tid.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_PLAYER') ?></a></li>
                <?php 
} else {
    ?>
                    <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                    <?php if (!$this->lists['t_single']) {
    ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_team&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_ADMIN_TEAM') ?></a></li>
                    <?php 
}
    ?>
                    <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_player&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_PLAYER') ?></a></li>
                <?php 
} ?>                    
            </ul>
        </div>
        <div class="tools col-xs-12 col-lg-12 text-right"> 
            <?php if ($this->acl == 2) {
    ?>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_NEW') ?>" onclick="javascript:bl_submit('adplayer_edit', 0);
            return false;"><i class="add"></i> <?php echo JText::_('BLFA_NEW') ?></a>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_EDIT') ?>" onclick="javascript:bl_submit('adplayer_edit', 1);
            return false;"><i class="edit"></i><?php echo JText::_('BLFA_EDIT') ?></a>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_DELETE') ?>" onclick="javascript:bl_submit('mdplayer_del', 1);
            return false;"><i class="delete"></i><?php echo JText::_('BLFA_DELETE') ?></a>
               <?php 
} else {
    ?>
                   <?php if ($this->lists['jssa_addexteam_single'] == 1 && $this->lists['t_single'] == 1): ?>
                    <a href="javascript:void(0);" onclick="javascript:getObj('div_addexpl').style.display = 'block';
                return false;" title="<?php echo JText::_('BLFA_ADDEXPL') ?>"><i class="add"></i> <?php echo JText::_('BLFA_ADDEXPL') ?></a>
                   <?php endif;
    ?>
                <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_NEW') ?>" onclick="javascript:bl_submit('adplayer_edit', 0);
            return false;"><i class="add"></i> <?php echo JText::_('BLFA_NEW') ?></a>
                   <?php if ($lists['jssa_editplayer']) {
    ?>
                    <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_EDIT') ?>" onclick="javascript:bl_submit('adplayer_edit', 1);
                return false;"><i class="edit"></i><?php echo JText::_('BLFA_EDIT') ?></a>
                   <?php 
}
    ?>
                   <?php if ($lists['jssa_deleteplayers']) {
    ?>
                    <a href="javascript:void(0);" title="<?php echo JText::_('BLFA_REMOVE') ?>" onclick="javascript:bl_submit('adplayer_del', 1);
                return false;"><i class="delete"></i><?php echo JText::_('BLFA_REMOVE') ?></a>
                   <?php 
}
    ?>
               <?php 
} ?>

        </div>
        
        <?php
        $newad = '';
        if ($this->acl == 1) {
            $link = JURI::base().'index.php?option=com_joomsport&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid;
            if ($this->lists['jssa_addexteam_single'] == 1 && $this->lists['t_single'] == 1) {
                $newad .= '<div style="display:none;padding:10px;" id="div_addexpl">';
                $newad .= $this->lists['players_ex'];
                $newad .= '<button class="send-button" onclick="javascript:if(document.adminForm.players_ex.value != 0){bl_submit(\'add_ex_player\',0);};return false;" />';
                $newad .= '<span>'.JText::_('BLFA_ADD').'</span>';
                $newad .= '</button>';
                $newad .= '</div>';
            }
        } else {
            $link = JURI::base().'index.php?option=com_joomsport&controller=moder&tid='.$this->tid.'&Itemid='.$Itemid;
        }
        ?>
        <form action="<?php echo $link; ?>" method="post" name="adminForm" id="adminForm">
        <?php echo $newad;?>
        <div class="jsClear"></div>
        
            <div class="table-responsive col-xs-12 col-lg-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="w30"><?php echo JText::_('BLFA_NUM'); ?></th>
                            <th class="w50"><?php if ($this->acl == 1 && ($lists['jssa_editplayer'] == 1 or $lists['jssa_deleteplayers'])) {
    ?>
                                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);
            ;" />
                                       <?php 
} elseif ($this->acl == 2) {
    ?>
                                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);
            ;" />
                                <?php 
} ?></th>
                            <th><?php echo JText::_('BLFA_PLAYERR'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $k = 0;
                        if (count($rows)) {
                            for ($i = 0, $n = count($rows); $i < $n; ++$i) {
                                $row = $rows[$i];
                                JFilterOutput::objectHtmlSafe($row);
                                if ($this->acl == 2) {
                                    $link = JRoute::_('index.php?option=com_joomsport&controller=moder&task=adplayer_edit&tid='.$this->tid.'&cid[]='.$row->id.'&Itemid='.$Itemid);
                                } else {
                                    if ($lists['jssa_editplayer']) {
                                        $link = JRoute::_('index.php?option=com_joomsport&task=adplayer_edit&controller=admin&sid='.$this->s_id.'&cid[]='.$row->id.'&Itemid='.$Itemid);
                                    } else {
                                        $link = JRoute::_('index.php?option=com_joomsport&task=player&sid='.$this->s_id.'&id='.$row->id.'&Itemid='.$Itemid);
                                    }
                                }
                                $checked = @JHTML::_('grid.checkedout', $row, $i);
                                ?>
                                <tr class="<?php echo $i % 2 ? 'gray' : '';
                                ?>">
                                    <td class="w30">
                                        <?php echo $i + 1 + (($this->page->page - 1) * $this->page->limit);
                                ?>
                                    </td>
                                    <td class="w50">
                                        <?php
                                        if ($this->acl == 1 && ($lists['jssa_editplayer'] == 1 or $lists['jssa_deleteplayers'])) {
                                            echo $checked;
                                        } elseif ($this->acl == 2) {
                                            echo $checked;
                                        }
                                ?>	
                                    </td>
                                    <td>
                                        <?php
                                        if ($row->photo && is_file('media/bearleague/'.$row->photo)) {
                                            echo '<img class="img-thumbnail" '.getImgPop($row->photo, 1).' alt="" />';
                                        } else {
                                            echo '<img class="img-thumbnail" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt="">';
                                        }
                                ?>
                                        <?php
                                        echo '<a href="'.$link.'">'.$row->first_name.' '.$row->last_name.'</a>';
                                ?>
                                    </td>
                                </tr>
                                <?php

                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="jsClear"></div>
            <div class="pages">
                <?php
                if ($this->acl == 1) {
                    $link_page = 'index.php?option=com_joomsport&view=admin_player&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit;
                } elseif ($this->acl == 2) {
                    $link_page = 'index.php?option=com_joomsport&view=admin_player&controller=moder&tid='.$this->tid.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit;
                }
                echo $this->page->getLimitPage();
                echo $this->page->getPageLinks($link_page);
                echo $this->page->getLimitBox();
                ?>
                <div class="jsClear"></div>
            </div>
            <div class="jsClear"></div>
            <input type="hidden" name="option" value="com_joomsport" />
            <input type="hidden" name="task" value="admin_player" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHTML::_('form.token'); ?>
        </form>
    </div>

</div>
</div>    