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

<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        echo $lists['panel'];
        ?>
    </nav>
    <!-- /.navbar -->

    <form action="<?php echo JURI::base(); ?>index.php?option=com_joomsport&controller=admin&sid=<?php echo $this->s_id; ?>&Itemid=<?php echo $Itemid; ?>" method="post" name="adminForm" id="adminForm">
        <div class="main adminPlayer">
            <div class="heading col-xs-12 col-lg-12">
                <h4><?php echo $this->lists['tournname']; ?></h4>
                <h1><?php echo JText::_('BLFA_PLAYERSLIST') ?></h1>
            </div>
            <div class="navbar-link col-xs-12 col-lg-12">
                <ul>
                    <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                    <?php if (!$this->lists['t_single']) {
    ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_team&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_ADMIN_TEAM') ?></a></li>
                    <?php 
} ?>
                    <li class="active"><a href="#" title=""><?php echo JText::_('BLFA_PLAYER') ?></a></li>
                </ul>
            </div>
            <div class="tools col-xs-12 col-lg-12 text-right"> 

                <a href="#" title="<?php echo JText::_('BLFA_NEW') ?>" onclick="javascript:bl_submit('adplayer_edit', 0);
        return false;"><i class="add"></i> <?php echo JText::_('BLFA_NEW') ?></a>
                   <?php if ($lists['jssa_editplayer']) {
    ?>
                    <a href="#" title="<?php echo JText::_('BLFA_EDIT') ?>" onclick="javascript:bl_submit('adplayer_edit', 1);
            return false;"><i class="edit"></i> <?php echo JText::_('BLFA_EDIT') ?></a>
                   <?php 
} ?>
                   <?php if ($lists['jssa_deleteplayers']) {
    ?>
                    <a href="#" title="<?php echo JText::_('BLFA_DELETE') ?>" onclick="javascript:bl_submit('adplayer_del', 1);
            return false;"><i class="delete"></i> <?php echo JText::_('BLFA_DELETE') ?></a>
                   <?php 
} ?>

            </div>
            <div class="table-responsive col-xs-12 col-lg-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="w30"><?php echo JText::_('BLFA_NUM'); ?></th>
                            <th class="w50"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
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
                                if ($lists['jssa_editplayer']) {
                                    $link = JRoute::_('index.php?option=com_joomsport&task=adplayer_edit&controller=admin&sid='.$this->s_id.'&cid[]='.$row->id.'&Itemid='.$Itemid);
                                } else {
                                    $link = JRoute::_('index.php?option=com_joomsport&task=player&sid='.$this->s_id.'&id='.$row->id.'&Itemid='.$Itemid);
                                }
                                $checked = @JHTML::_('grid.checkedout', $row, $i);
                                ?>
                                <tr class="<?php echo $i % 2 ? 'active' : '';
                                ?>">
                                    <td class="w30">
                                        <?php echo $page->getRowOffset($i);
                                ?>
                                    </td>
                                    <td class="w30">
                                        <?php echo $checked;
                                ?>
                                    </td>
                                    <td>
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
            <div class="pages">
                <?php
                $link_page = 'index.php?option=com_joomsport&view=adlist_player&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit;
                echo $this->page->getLimitPage();
                echo $this->page->getPageLinks($link_page);
                echo $this->page->getLimitBox();
                ?>
            </div>
        </div>
        <input type="hidden" name="option" value="com_joomsport" />
        <input type="hidden" name="task" value="adlist_player" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>