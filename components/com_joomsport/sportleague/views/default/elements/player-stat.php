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
defined('_JEXEC') or die('Restricted access');
?>
<div class="table-responsive player-oneline-stat">
<?php
    if (count($rows->lists['career'])) {
?>
  <table class="table table-striped jsTableCareer">
  <?php
  if (count($rows->lists['career_head'])) {
  ?>
    <thead>
        <tr>
        <?php
        foreach($rows->lists['career_head'] as $career) {

            echo '<th>'.$career.'</th>';

        }

        ?>
        </tr>
    </thead>
  <?php
  }
  ?>
  <tbody>
      <?php
        foreach($rows->lists['career'] as $career) {
        ?>
        <tr>
            <?php
            for($intA=0;$intA<count($career);$intA++){
                echo '<td>'.$career[$intA].'</td>';
            }
            ?>
        </tr>

        <?php
        }

    ?>
  </tbody>
</table>

<div class="player-career-seasons">
    <?php foreach($rows->lists['career'] as $career) {
        ?>
            <div class="player-career-season">
                <?php
                    echo '<div class="player-career-season__trigger">'.$career[0].'</div>'; 
                ?>
                <div class="player-career-season__collapsible">
                    <?php for($intA=0; $intA<count($career); $intA++){ 
                        echo '<div class="player-career-season__value">'.$career[$intA].'</div>'; 
                    } ?>
                </div>
            </div>
        <?php
        }
    ?>
</div>
<?php
}
?>
</div>
<?php
if($rows->lists['career_matches']){
?>

<div class="table-responsive player-career">
    <div class="jstable jsMatchDivMain">
        <?php echo $rows->lists['career_matches'];?>
    </div>
</div>
<?php
}

if(isset($rows->lists['boxscore']) && $rows->lists['boxscore']){
    echo '<div class="center-block jscenter">
                    <h3 class="jsCreerMatchStath3">'.  JText::_('BLFA_BOXSCORE').'</h3>
                </div>';
    echo $rows->lists['boxscore'];
}
if(isset($rows->lists['boxscore_matches']) && $rows->lists['boxscore_matches']){
    echo '<div class="center-block jscenter">
                    <h3 class="jsCreerMatchStath3">'.JText::_('BLFA_BOXSCORE_MATCH').'</h3>
                </div>';
    echo $rows->lists['boxscore_matches'];
}
?>
