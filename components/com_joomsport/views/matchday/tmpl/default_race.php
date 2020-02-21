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
    $Itemid = JRequest::getInt('Itemid');
    JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<?php
echo $lists['panel'];
?>
<form action="<?php echo JRoute::_('index.php?option=com_joomsport&view=matchday&id='.$this->m_id.'&Itemid='.$Itemid);?>" method="post" name="adminForm" id="adminForm">

<div class="module-middle">
	
	<!-- <back box> -->
		<div class="back dotted"><a href="javascript:void(0);" onclick="history.back(-1);" title="<?php echo JText::_('BL_BACK')?>">&larr; <?php echo JText::_('BL_BACK')?></a></div>
	<!-- </back box> -->
	
	<!-- <title box> -->
	<div class="title-box">
            <h2><?php //echo $this->escape($this->params->get('page_title'));
            echo $this->escape($this->ptitle);
            ?></h2>
	</div>
	<!-- </div>title box> -->
	
</div>
<!-- </module middle> -->
<!-- <content module> -->
<div class="content-module padd-off">
    <?php 
    if (count($lists['rounds'])) {
        for ($intQ = 0; $intQ < count($lists['rounds']); ++$intQ) {
            ?>
            <div class="round_container">
   
                <div class="js_round_header_div">
                    <div>
                        <?php
                            echo '<h2 class="dotted">'.$lists['rounds'][$intQ]->round_title.'</h2>';
            ?>
                    </div>    
                </div>
                <div class="js_round_main_div">
                    <table class="season-list team-list">
                        <tr>
                            <th class="sort asc" axis="int">
                                <?php echo JText::_('BL_PARTICS');
            ?>
                            </th>
                            <?php
                            for ($intB = 0; $intB < intval($lists['options']->attempts); ++$intB) {
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo JText::_('BLFA_ATTEMPTS');
                                ?>&nbsp;<?php echo $intB + 1;
                                ?>
                                </th>
                                <?php

                            }
            ?>
                            <?php 
                            if ($lists['options']->penalty == 1) {
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo JText::_('BLFA_PENALTY');
                                ?> (<?php echo $lists['options']->postfix;
                                ?>)
                                </th>

                                <?php

                            }
            ?>
                            <?php
                            for ($intB = 0; $intB < count($lists['extracol']); ++$intB) {
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo $lists['extracol'][$intB]->name;
                                ?>
                                </th>
                                <?php

                            }
            ?>      
                            <th class="sort asc" axis="int">
                                <?php echo JText::_('BLFA_RESULTS');
            ?> (<?php echo $lists['options']->postfix;
            ?>)
                            </th>

                        </tr>    
                        <?php
                        for ($intA = 0; $intA < count($lists['rounds'][$intQ]->res); ++$intA) {
                            ?>
                                <tr <?php echo ($intA % 2) ? '' : 'class="gray"';
                            ?>>
                                    <td class="teams jsNoWrap">
                                        <?php echo $lists['rounds'][$intQ]->res[$intA]->t_name?>
                                    </td>
                                    <?php
                                    $attempts = isset($lists['rounds'][$intQ]->res[$intA]->attempts) ? $lists['rounds'][$intQ]->res[$intA]->attempts : '';
                            $attempts_col = explode('|', $attempts);
                            for ($intB = 0; $intB < intval($lists['options']->attempts); ++$intB) {
                                ?>
                                        <td>
                                            <?php echo isset($attempts_col[$intB]) ? $attempts_col[$intB] : '';
                                ?>
                                        </td>
                                        <?php

                            }
                            ?>
                                    <?php 
                                    if ($lists['options']->penalty == 1) {
                                        ?>
                                        <td>
                                            <?php echo $lists['rounds'][$intQ]->res[$intA]->penalty;
                                        ?>
                                        </td>

                                        <?php

                                    }
                                    //var_dump($lists['race']['rounds'][$index]);
                                    ?>
                                        <?php
                                    $ecol = isset($lists['rounds'][$intQ]->res[$intA]->extracol) ? $lists['rounds'][$intQ]->res[$intA]->extracol : '';
                            $ecol_col = explode('|', $ecol);
                            for ($intB = 0; $intB < count($lists['extracol']); ++$intB) {
                                ?>
                                        <td>
                                            <?php echo isset($ecol_col[$intB]) ? $ecol_col[$intB] : '';
                                ?>
                                        </td>
                                        <?php

                            }
                            ?>  
                                    <td class="js_div_round_result">
                                        <?php echo $lists['rounds'][$intQ]->res[$intA]->result_string;
                            ?>
                                    </td>
                                </tr>

                            <?php

                        }
            ?>
                    </table>   

                </div>
            </div>
            <?php

        }
    }
    ?>
</div>
</form>