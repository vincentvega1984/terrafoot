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
global $Itemid;
$Itemid = JRequest::getInt('Itemid');
$options = $this->options;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div id="joomsport-container">
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php
        echo $lists['panel'];
        ?>
    </nav>
    <!-- /.navbar -->

    <div class="main clubLayout">
        <div class="history col-xs-12 col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#" onclick="history.back(-1);"><i class="fa fa-long-arrow-left"></i><?php echo JText::_('BL_BACK') ?></a></li>
            </ol>
        </div>
        <div class="heading col-xs-12 col-lg-12">
            <h2 class="pull-left col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php echo $this->escape($this->params->get('page_title')); ?></h2>
        </div>
        <div class="jsClear"></div>
        <div class="joinSeason"> 
            <div>                   
                <div > <span ><strong><?php echo JText::_('BL_STARTDATE') ?>:</strong></span> <span ><?php echo $this->lists['season_par']->reg_start ?></span> </div>
                <div > <span ><strong><?php echo JText::_('BL_ENDDATE') ?>:</strong></span> <span ><?php echo $this->lists['season_par']->reg_end ?></span> </div>
                <div > <span ><strong><?php echo JText::_('BL_PARTIC') ?>:</strong></span> <span ><?php echo $this->lists['season_par']->s_participant.' ('.JText::_('BL_NOW').' '.$this->lists['part_count'].')'; ?></span> </div>

                <?php
                if ($this->lists['unable_reg']) {
                    if ($options->paypal_on) {
                        $cap = '';
                        if ($this->lists['t_single']) {
                            //echo "<input type='hidden' name='reg_team' value='".$this->user->id."' />";
                            //echo "<input type='hidden' name='is_team' value='0' />";
                        } elseif (!$this->lists['no_team']) {
                            $cap .= "<div class='div_for_styled'>";
                            $cap .= "<span class='down'><!-- --></span>";
                            $cap .= $this->lists['cap'];
                            $cap .= '</div>';
                        }

                        $options->paypalcancel = JURI::getInstance();
                        if ($this->lists['t_single']) {
                            $options->paypalreturn = JURI::base().'index.php?option=com_joomsport%26task=joinmePaypl%26sid='.$this->lists['s_id'].'%26usr_j='.$this->user->id.'%26is_team=0';
                            $options->notifyurl = JURI::base().'index.php?option=com_joomsport%26controller=paypal%26task=ipn%26sid='.$this->lists['s_id'].'%26usr_j='.$this->user->id.'%26is_team=0';
                            
                        } elseif (!$this->lists['no_team']) {
                            $options->paypalreturn = JURI::base().'index.php?option=com_joomsport%26task=joinmePaypl%26sid='.$this->lists['s_id'].'%26usr_j='.$this->user->id.'%26is_team=1';
                            $options->notifyurl = JURI::base().'index.php?option=com_joomsport%26controller=paypal%26task=ipn%26sid='.$this->lists['s_id'].'%26usr_j='.$this->user->id.'%26is_team=1';
                        }

                        if ($this->lists['no_team']) {
                            echo "<span class='errjoin'>".JText::_('BL_NOCAP').'</span>';
                        } elseif (!$this->lists['bluid'] && $this->lists['t_single']) {
                            echo "<span class='errjoin'>".JText::_('Register in component first').'</span>';
                        } else {
                            echo JHtml::_('paypal.getPaypalForm', $options, $cap);
                        }
                    } else {
                        ?>
                        <form method="POST" action="">
                            <?php
                            if ($this->lists['t_single']) {
                                echo "<input type='hidden' name='reg_team' value='".$this->user->id."' />";
                                echo "<input type='hidden' name='is_team' value='0' />";
                            } elseif (!$this->lists['no_team']) {
                                echo '<div >';
                                echo $this->lists['cap'];
                                echo "<input type='hidden' name='is_team' value='1' />";
                                echo '</div>';
                            }
                        ?>
                            <?php
                            if ($this->lists['no_team']) {
                                echo '<div class="line">'.JText::_('BL_NOCAP').'</div>';
                            } else {
                                ?>
                                <input type="hidden" name="task" value="joinme" />
                                <input type="hidden" name="sid" value="<?php echo $this->lists['s_id'];
                                ?>" />

                                <div style="padding-top:10px;">
                                    <a href="javascript:void(0);">
                                    <button type="submit" class="btn btn-default"><i class='arrow-right'></i><?php echo JText::_('BL_JOINSEAS');
                                ?></button>
                                    </a>
                                </div>
                            <?php 
                            }
                        ?>
                        </form>
                        <?php

                    }
                }
                ?>                
            </div>
        </div>
    </div>
</div>
</div>    