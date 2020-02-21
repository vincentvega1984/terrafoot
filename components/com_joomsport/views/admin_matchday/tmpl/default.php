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
$rows = $this->rows;
$lists = $this->lists;
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
    function submitbutton(pressbutton) {
        var form = document.adminForm;
        submitform(pressbutton);
        return;
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

    <form action="<?php echo JRoute::_('index.php?option=com_joomsport&view=admin_matchday&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit.'&page=1'); ?>" method="post" name="adminForm" id="adminForm">
        <div class="main adminMatchday">
            <div class="heading col-xs-12 col-lg-12">
                <h4 class="text-right"><?php echo $this->lists['tournname']; ?></h4>
                <h2><?php echo JText::_('BLFA_MATCHDAYLIST') ?></h2>
            </div>
            <div class="navbar-link col-xs-12 col-lg-12">
                <ul>
                    <li class="active"><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&view=admin_matchday&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_MATCHDAY') ?></a></li>
                    <?php if (!$this->lists['t_single']) {
    ?>
                        <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_team&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_ADMIN_TEAM') ?></a></li>
                    <?php 
} ?>
                    <li><a href="<?php echo JRoute::_('index.php?option=com_joomsport&controller=admin&task=admin_player&sid='.$this->s_id.'&Itemid='.$Itemid) ?>" title=""><?php echo JText::_('BLFA_PLAYER') ?></a></li>
                </ul>

            </div>
            <div class="tools col-xs-12 col-lg-12 text-right"> 
                <a href="#" onclick="bl_submit('edit_matchday');
        return false;" title="<?php echo JText::_('BLFA_NEW') ?>"><i class="add"></i> <?php echo JText::_('BLFA_NEW') ?></a>
                <a href="#" onclick="bl_submit('edit_matchday', 1);
        return false;" title="<?php echo JText::_('BLFA_EDIT') ?>"><i class="edit"></i> <?php echo JText::_('BLFA_EDIT') ?></a>
                <a href="#" onclick="bl_submit('matchday_del', 1);
        return false;" title="<?php echo JText::_('BLFA_DELETE') ?>"><i class="delete"></i> <?php echo JText::_('BLFA_DELETE') ?></a>
            </div>
            <div class="selection col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-right mt20">
                <div class="data">
                    <?php echo $this->lists['t_type']; ?>
                </div>
            </div>
            <div class="table-responsive col-xs-12 col-lg-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="w30"><?php echo JText::_('BLFA_NUM'); ?></th>
                            <th class="w50"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                            <th><?php echo JText::_('BLFA_MATCHDAY'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $k = 0;
                        if (count($rows)) {
                            for ($i = 0, $n = count($rows); $i < $n; ++$i) {
                                $row = $rows[$i];
                                JFilterOutput::objectHtmlSafe($row);
                                $link = JRoute::_('index.php?option=com_joomsport&controller=admin&view=edit_matchday&cid[]='.$row->id.'&sid='.$this->s_id.'&Itemid='.$Itemid);
                                $checked = @JHTML::_('grid.checkedout', $row, $i);
                                ?>
                                <tr class="<?php echo $i % 2 ? 'active' : '';
                                ?>">
                                    <td class="w30" style="width:30px;">
                                        <?php echo $i + 1 + (($this->page->page - 1) * $this->page->limit);
                                ?>
                                    </td>
                                    <td class="w50" style="width:50px;">
                                        <?php echo $checked;
                                ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo '<a href="'.$link.'">'.$row->m_name.'</a>';
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
                $link_page = 'index.php?option=com_joomsport&view=admin_matchday&controller=admin&sid='.$this->s_id.'&Itemid='.$Itemid.'&jslimit='.$this->page->limit;
                echo $this->page->getLimitPage();
                echo $this->page->getPageLinks($link_page);
                echo $this->page->getLimitBox();
                ?>
            </div>
            <div class="jsClear"></div>
        </div>
        <input type="hidden" name="task" value="admin_matchday" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="controller" value="admin" />
        <input type="hidden" name="sid" value="<?php echo $this->s_id; ?>" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
</div>