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
// no direct access
defined('_JEXEC') or die('Restricted access');
if (isset($this->message)) {
    $this->display('message');
}
$lists = $this->lists;
$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<?php
if ($this->tmpl != 'component') {
    echo $lists['panel'];

    $lnk = "window.open('".JURI::base().'index.php?tmpl=component&option=com_joomsport&amp;view=calendar&amp;sid='.$lists['s_id']."','jsmywindow','width=750,height=700,scrollbars=1,resizable=1');";
} else {
    $lnk = 'window.print();';
}

?>

<!-- <module middle> -->
<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_joomsport&view=calendar&sid='.$lists['s_id'].'&Itemid='.$Itemid.($this->tmpl == 'component' ? '&tmpl=component' : ''));?>" method="post">
<div class="module-middle">
	
	<!-- <back box> -->
	<?php if ($this->tmpl != 'component') {
    ?>
			<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_('BL_BACK')?>">&larr; <?php echo JText::_('BL_BACK')?></a></div>
	<?php 
} ?>
	<!-- </back box> -->
	
	<!-- <title box> -->
	<div class="title-box">
		<h2><?php //echo $this->escape($this->params->get('page_title'));
         echo $this->escape($this->ptitle); ?></h2>
		<a class="print" href="#" onClick="<?php echo $lnk;?>" title="<?php echo JText::_('BLFA_PRINT');?>"><?php echo JText::_('BLFA_PRINT');?></a>
		
	</div>

	
	<!-- </div>title box> -->
	
</div>
<!-- </module middle> -->
<!-- <content module> -->
<?php
if ($this->tmpl == 'component') {
    echo '<div id="wr-module">';
}
?>
			<div class="content-module padd-off" style="overflow:visible !important;">
				<?php

                echo '<table class="match-day" cellpadding="0" cellspacing="0" border="0">';
                for ($i = 0;$i < count($lists['mdays_list']);++$i) {
                    $match = $lists['mdays_list'][$i];

                    ?>
				<tr class="<?php echo $i % 2 ? '' : 'gray';
                    ?>">
					
					<td class="match-day-date">
					<?php //echo $this->formatDate(strtotime($match->m_date . ' ' . $match->m_time));
                        if ($match->start_date) {
                            if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $match->start_date)) {
                                echo $this->formatDate($match->start_date);
                            } else {
                                echo $match->start_date;
                            }
                        }
                    ?>
					</td>
					
					<td class="team-h">
						
					<?php

                                $link = JRoute::_('index.php?option=com_joomsport&view=matchday&id='.$match->id.'&Itemid='.$Itemid);

                    echo '<a href="'.$link.'">'.$match->m_name.'</a>';

                    ?>

					</td>
					
				</tr>		
				<?php

                }
            ?>

			
			
	</table>
	</div>
<?php
if ($this->tmpl == 'component') {
    echo '</div>';
}
?>	
	<!-- </content module> -->
</form>