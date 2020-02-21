<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$document		= JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_players/css/mod_js_players.css');
require_once("components/com_joomsport/includes/func.php");
//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$ph_width = $params->get( 'photo_width' );
$is_width = $params->get( 'photo_is' );
$displ_team = $params->get( 'displ_team' );
$cItemId = $params->get('customitemid');
$ssss_id = $params->get( 'sidgid' );
$ex = explode('|',$ssss_id );
$s_id = $ex[0];
$Itemid = JRequest::getInt('Itemid');
if(!$cItemId){
	$cItemId = $Itemid;
}
?>
<div class="jsm_playerstat">
	<?php if(count($list)){?>
	<?php foreach ($list as $player) { ?>
	<div class="item"><?php
		$defimg = modBlPlayersHelper::getPhoto($player);
		$teams = modBlPlayersHelper::getTeamName($player ,$params);

		if($is_width){
			echo '<div class="jsblc-team-embl" style="width:'.($ph_width+2).'px;">';
			if($defimg && is_file('media/bearleague/'.$defimg)){
			//echo '<img style="border:1px solid #aaa;" src="media/bearleague/'.$defimg.'" title="'.$player->e_name.'" height="'.$ph_width.'" />';

				echo '<div class="team-embl" style="width:'.$ph_width.'px;">'.jsHelperImages::getEmblem($defimg).'</div>';
			}else{
		//echo "&nbsp;";
				echo '<div class="team-embl"><img class="player-ico" src="'.JURI::base().'media'.DIRECTORY_SEPARATOR.'bearleague'.DIRECTORY_SEPARATOR.'player_st.png" width="30" height="30" alt=""></div>';
			}
			echo '</div>';

		} ?>
		<div class="jsblc-team-info">
			<?php
			$player->e_name = classJsportTranslation::get('events_'.$player->evid, 'e_name',$player->e_name);
	//else{
			if($player->e_img && is_file('media/bearleague/events/'.$player->e_img)){
				echo '<img src="media/bearleague/events/'.$player->e_img.'" title="'.$player->e_name.'" height="24" />';
			}
	//}
			$player->first_name = classJsportTranslation::get('player_'.$player->id, 'first_name',$player->first_name);
			$player->last_name = classJsportTranslation::get('player_'.$player->id, 'last_name',$player->last_name);

			$player->name = $player->first_name .' '.$player->last_name;
			$link = "<a href='".JRoute::_('index.php?option=com_joomsport&amp;task=player&amp;id='.$player->id.'&amp;sid='.($s_id).'&amp;Itemid='.$cItemId)."'>".(($plname && $player->nick)?$player->nick:$player->name)."</a>";

			echo ' <strong>'.$player->cnt.'</strong>  '.$link;
			if($teams && $displ_team){
				echo $teams;
			}

			?></div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>
