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
<?php

//environment
define('JS_ENV', 'joomla');
//define root
define('JS_PATH_JOOMLA', JPATH_ROOT);
//environment
define('JS_TEMPLATE', 'default');
// main directory
define('JS_PATH', __DIR__.DIRECTORY_SEPARATOR);
// css directory
define('JS_PATH_CSS', JS_PATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR);
// js directory
define('JS_PATH_JS', JS_PATH.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR);
// images directory
define('JS_PATH_IMAGES', JPATH_ROOT.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'bearleague'.DIRECTORY_SEPARATOR);

// event images directory
define('JS_PATH_IMAGES_EVENTS', JS_PATH_IMAGES.'events'.DIRECTORY_SEPARATOR);
//thumb
define('JS_PATH_IMAGES_THUMB', JS_PATH_IMAGES.'thumb'.DIRECTORY_SEPARATOR);

// classes directory
define('JS_PATH_CLASSES', JS_PATH.'classes'.DIRECTORY_SEPARATOR);
// helpers directory
define('JS_PATH_HELPERS', JS_PATH.'helpers'.DIRECTORY_SEPARATOR);
// views directory
define('JS_PATH_VIEWS', JS_PATH.'views'.DIRECTORY_SEPARATOR.JS_TEMPLATE.DIRECTORY_SEPARATOR);
// views elements directory
define('JS_PATH_VIEWS_ELEMENTS', JS_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR);
// objects directory
define('JS_PATH_OBJECTS', JS_PATH_CLASSES.'objects'.DIRECTORY_SEPARATOR);

// plugins directory
define('JS_PATH_PLUGINS', JS_PATH.'plugins'.DIRECTORY_SEPARATOR);

// classes directory
define('JS_PATH_ENV', JS_PATH.'base'.DIRECTORY_SEPARATOR.JS_ENV.DIRECTORY_SEPARATOR);
// classes directory
define('JS_PATH_ENV_CLASSES', JS_PATH_ENV.'classes'.DIRECTORY_SEPARATOR);
// models directory
define('JS_PATH_MODELS', JS_PATH_ENV.'models'.DIRECTORY_SEPARATOR);

//

define('JS_LIVE_URL', JUri::base());
define('JS_LIVE_URL_IMAGES', JS_LIVE_URL.'media/bearleague/');
define('JS_LIVE_URL_IMAGES_THUMB', JS_LIVE_URL_IMAGES.'thumb/');
define('JS_LIVE_URL_IMAGES_EVENTS', JS_LIVE_URL_IMAGES.'events/');
define('JS_LIVE_ASSETS', JS_LIVE_URL.'components/com_joomsport/sportleague/assets/');

//defines database table names

define('DB_TBL_ADDONS', '#__bl_addons');
define('DB_TBL_ASSIGN_PHOTOS', '#__bl_assign_photos');
define('DB_TBL_CLUB', '#__bl_club');
define('DB_TBL_COMMENTS', '#__bl_comments');
define('DB_TBL_CONFIG', '#__bl_config');
define('DB_TBL_COUNTRIES', '#__bl_countries');
define('DB_TBL_EVENTS', '#__bl_events');
define('DB_TBL_EXTRA_FILDS', '#__bl_extra_filds');
define('DB_TBL_EXTRA_SELECT', '#__bl_extra_select');
define('DB_TBL_EXTRA_VALUES', '#__bl_extra_values');
define('DB_TBL_FEADMINS', '#__bl_feadmins');
define('DB_TBL_GROUPS', '#__bl_groups');
define('DB_TBL_GRTEAMS', '#__bl_grteams');
define('DB_TBL_MAPS', '#__bl_maps');
define('DB_TBL_MAPSCORE', '#__bl_mapscore');
define('DB_TBL_MATCH', '#__bl_match');
define('DB_TBL_MATCHDAY', '#__bl_matchday');
define('DB_TBL_MATCH_EVENTS', '#__bl_match_events');
define('DB_TBL_MODERS', '#__bl_moders');
define('DB_TBL_PHOTOS', '#__bl_photos');
define('DB_TBL_PLAYERS', '#__bl_players');
define('DB_TBL_PLAYERS_TEAM', '#__bl_players_team');
define('DB_TBL_RANKSORT', '#__bl_ranksort');
define('DB_TBL_ROUNDS', '#__bl_rounds');
define('DB_TBL_ROUNDS_EXTRACOL', '#__bl_rounds_extracol');
define('DB_TBL_ROUNDS_PARTICIPIANTS', '#__bl_rounds_participiants');
define('DB_TBL_SEASONS', '#__bl_seasons');
define('DB_TBL_SEASON_OPTION', '#__bl_season_option');
define('DB_TBL_SEASON_PLAYERS', '#__bl_season_players');
define('DB_TBL_SEASON_TEAMS', '#__bl_season_teams');
define('DB_TBL_SEAS_MAPS', '#__bl_seas_maps');
define('DB_TBL_SQUARD', '#__bl_squard');
define('DB_TBL_SUBSIN', '#__bl_subsin');
define('DB_TBL_TBLCOLORS', '#__bl_tblcolors');
define('DB_TBL_TEAMS', '#__bl_teams');
define('DB_TBL_TEMPLATES', '#__bl_templates'); //not used?
define('DB_TBL_TOURNAMENT', '#__bl_tournament');
define('DB_TBL_VENUE', '#__bl_venue');
define('DB_TBL_MATCH_STATUSES', '#__bl_match_statuses');

//nuevo
define('DB_TBL_SEASON_TABLE', '#__bl_season_table');
define('DB_TBL_PLAYER_LIST', '#__bl_playerlist');
define('DB_TBL_PERSONS', '#__bl_persons');
define('DB_TBL_PERSONS_CATEGORY', '#__bl_persons_category');
//some config
define('JSCONF_SCORE_SEPARATOR', ' - ');
define('JSCONF_SCORE_SEPARATOR_VS', ' v ');
define('JSCONF_PLAYER_DEFAULT_IMG', 'player_st.png');
define('JSCONF_TEAM_DEFAULT_IMG', 'teams_st.png');
define('JSCONF_VENUE_DEFAULT_IMG', 'event_st.png');

define('JSCONF_ENBL_MATCH_TOOLTIP', true);
// Google map API KEY
define('JSCONF_GMAP_API_KEY', 'AIzaSyA1NR_RmgpTgzBwKwrvt_yGXw5Cw4Kj_io');
?>