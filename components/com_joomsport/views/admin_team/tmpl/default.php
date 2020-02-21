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
<div  id='joomsport-container'>
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        echo $this->lists['panel'];
        ?>
    </nav>
    <!-- /.navbar -->

    <div class="main adminTeam">
        <div class="heading col-xs-12 col-lg-12">
            <h4 class="text-right"><?php echo $this->lists['tournname']; ?></h4>
            <h2><?php echo JText::_('BLFA_TEAMSLIST') ?></h2>
        </div>
        <div class="navbar-link col-xs-12 col-lg-12">
            <ul>
                <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_team&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_ADMIN_TEAM') ?></a></li>
                <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_player&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_PLAYER') ?></a></li>
            </ul>
        </div>
        <div class="tools col-xs-12 col-lg-12 text-right"> 
            <?php if ($this->lists['jssa_addexteam'] == 1): ?>
                <a href="#" onclick="javascript:getObj('div_addexteam').style.display = 'block';
            return false;" title="<?php echo JText::_('BLFA_ADDEXTEAM') ?>"><i class="add"></i> <?php echo JText::_('BLFA_ADDEXTEAM') ?></a>
               <?php endif; ?>
            <a href="#" onclick="javascript:bl_submit('edit_team', 0);
        return false;" title="<?php echo JText::_('BLFA_NEW') ?>"><i class="add"></i> <?php echo JText::_('BLFA_NEW') ?></a>
               <?php if ($this->lists['jssa_editteam'] == 1): ?>
                <a href="#" onclick="javascript:bl_submit('edit_team', 1);
            return false;" title="<?php echo JText::_('BLFA_EDIT') ?>"><i class="edit"></i> <?php echo JText::_('BLFA_EDIT') ?></a>
               <?php endif; ?>
               <?php if ($this->lists['jssa_delteam'] == 1): ?>
                <a href="#" onclick="javascript:bl_submit('team_del', 1);
            return false;" title="<?php echo JText::_('BLFA_REMOVE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_REMOVE') ?></a>
               <?php endif; ?>

        </div>
        <div class="jsClear"></div>
        <form action="<?php echo JRoute::_('index.php?option=com_joomsport&view=admin_team&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit.'&page=1'); ?>" method="post" name="adminForm" id="adminForm">
            <?php if ($this->lists['jssa_addexteam'] == 1): ?>
                <div style="display:none;padding:10px;" id="div_addexteam">
                    <?php echo $this->lists['teams_ex']; ?>
                    <button class="send-button" onclick="javascript:if (document.adminForm.teams_ex.value != 0) {
                bl_submit('add_ex_team', 0);
            }
            ;
            return false;" />
                    <span><?php echo JText::_('BLFA_ADD') ?></span>
                    </button>
                </div>

            <?php endif; ?>
            <div class="table-responsive col-xs-12 col-lg-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="w30"><?php echo JText::_('BLFA_NUM'); ?></th>
                            <th class="w50"><?php if ($this->lists['jssa_editteam'] == 1 || $this->lists['jssa_delteam'] == 1): ?>
                                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                                <?php endif; ?></th>
                            <th><?php echo JText::_('BLFA_TEAM'); ?></th>
                            <th><?php echo JText::_('BLFA_CITY'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $k = 0;
                        if (count($rows)) {
                            for ($i = 0, $n = count($rows); $i < $n; ++$i) {
                                $row = $rows[$i];
                                JFilterOutput::objectHtmlSafe($row);
                                if ($this->lists['jssa_editteam'] == 1) {
                                    $link = JRoute::_('index.php?option=com_joomsport&controller=admin&task=edit_team&cid[]='.$row->id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
                                } else {
                                    $link = JRoute::_('index.php?option=com_joomsport&task=team&tid='.$row->id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
                                }
                                $checked = @JHTML::_('grid.checkedout', $row, $i);
                                //$published 	= JHTML::_('grid.published', $row, $i);
                                ?>
                                <tr class="<?php echo $i % 2 ? 'active' : '';
                                ?>">
                                    <td class="w30">
                                        <?php echo $i + 1 + (($this->page->page - 1) * $this->page->limit);
                                ?>
                                    </td>
                                    <td class="w50">
                                        <?php
                                        if ($this->lists['jssa_editteam'] == 1 || $this->lists['jssa_delteam'] == 1) {
                                            echo $checked;
                                        }
                                ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo '<a href="'.$link.'">'.$row->t_name.'</a>';
                                ?>
                                    </td>
                                    <td>
                                        <?php echo $row->t_city;
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
                $link_page = 'index.php?option=com_joomsport&view=admin_team&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit;
                echo $this->page->getLimitPage();
                echo $this->page->getPageLinks($link_page);
                echo $this->page->getLimitBox();
                ?>
                <div class="jsClear"></div>
            </div>
            <div class="jsClear"></div>
            <input type="hidden" name="option" value="com_joomsport" />
            <input type="hidden" name="task" value="admin_team" />
            <input type="hidden" name="controller" value="admin" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="sid" value="<?php echo $this->s_id; ?>" />
            <?php echo JHTML::_('form.token'); ?>
        </form>
    </div>
</div>
</div>    