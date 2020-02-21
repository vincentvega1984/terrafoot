<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();

$place_display 	= $params->get( 'place_display' );
$ssss_id = $params->get( 'sidgid' );
$yteam_id = $params->get( 'team_id' );
	if($ssss_id){
		$ex = explode('|',$ssss_id );
		$gr_id = $ex[1];
		$s_id = $ex[0];
	}else{
		$gr_id=0;
	}	
$cItemId = $params->get('customitemid');
$Itemid = JRequest::getInt('Itemid');
if(!$cItemId){
	$cItemId = $Itemid;
}
$single = $row->getSingle();
$row = $row->season;

$intM = 0;
?>
<div class="standings-table">
<?php
if(isset($row->lists['columnsCell'])){
    foreach ($row->lists['columnsCell'] as $group => $vals )
    { 
        $intM ++;
        if($group){
            echo '<h2 class="groups">'.$group.'</h2>';
        }
        if(count($vals)){
            ?>
            <div class="table-responsive">
                <table class="table table-striped cansorttbl" id="jstable_<?php echo $intM;?>">
                    <thead>
                        <tr>
                            <th width="5%" class="jsalcenter jsNoWrap">
                                
                                    <?php echo classJsportLanguage::get("MTBL_RANK");?>
                                
                            </th>
                            
                            <th style="text-align:left;" class="jsNoWrap">
                                
                                    <?php echo classJsportLanguage::get($single?"MTBL_PART_PLAYERS":"MTBL_PART_TEAM");?>
                                
                            </th>
                            <?php
                            if(count($row->lists['columns']))
                            foreach($row->lists['columns'] as $key => $value){
                                if($params->get($key)){
                                    if($key != 'emblem_chk'){
                                       if($key != 'curform_chk'){
                                    ?>
                                        <th class="jsalcenter jsNoWrap" width="5%">
                                            
                                                <?php echo $row->lists['available_options'][$key];?>
                                            
                                        </th>
                                    <?php
                                       }else{
                                            ?>
                                                <th class="noSort jsNoWrap">

                                                        <?php echo $row->lists['available_options'][$key];?>

                                                </th>
                                            <?php 
                                       }
                                    }
                                }
                            }
                            ?>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rank = 1;
                    foreach($vals as $val){
                        $options = json_decode($val->options, true);
                        if(($params->get('team_id') && $params->get('team_id') == $options['id']) || !$params->get('team_id')){
                            
                        if($place_display == '0' || $place_display >= $rank){

                        

                        $partObj = $row->getPartById($options['id']);
                        $colortbl = '';

                        /*if(isset($row->lists['tblcolors'][$rank])){
                            $colortbl = $row->lists['tblcolors'][$rank];
                        }*/
                        
                        $coloryteam = $partObj->getYourTeam();
                        
                        ?>
                        <tr <?php echo $coloryteam?'style="background-color:'.$coloryteam.'!important;"':'';?>>
                            <td class="jsalcenter" <?php echo $colortbl?'style="background-color:'.$colortbl.'"':"";?>><?php echo $rank;?></td>
                            
                            <td style="text-align:left;" class="jsNoWrap">
                                <div class="team">
                                <?php 
                                if(isset($row->lists['columns']['emblem_chk']) && $params->get('emblem_chk') == '1'){
                                    echo ($partObj->getEmblem(true, 0, 'emblInline', 0, $cItemId));
                                }
                                echo $partObj->getName(true,$cItemId);
                                ?>
                                </div>
                            </td>
                            <?php
                            if(count($row->lists['columns']))
                            foreach($row->lists['columns'] as $key => $value){
                                if($params->get($key)){
                                    if($key == 'curform_chk'){
                                            ?>
                                    <td class="jsalcenter jsNoWrap">
                                        <?php echo isset($val->$key) ? $val->$key : '';
                                            ?>
                                    </td>
                                        <?php

                                        }else
                                    if($key != 'emblem_chk'){
                                    ?>
                                        <td class="jsalcenter jsNoWrap">
                                            <?php echo isset($options[$key]) ? $options[$key] : '';?>
                                        </td>
                                    <?php
                                    }
                                }
                            }
                            ?>
                            
                        </tr>
                        <?php
                        }
                        }
                        $rank ++;
                    }
                    ?>
                    </tbody>    
                </table>  
            </div>
  
            <?php
        }
    }
    ?>
<div class="more-info">
    <?php 
    if($params->get('enbl_full_link')){
        echo classJsportLink::season(classJsportLanguage::get("MDLTBL_LINK_FULL"), $s_id, false, $cItemId);
    }
    ?>
</div>
    <?php
}
?>
</div>
<script type="text/javascript">

$(function() {
    $( '.jstooltipJSF' ).tooltip({
        html:true,
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
  });
</script>