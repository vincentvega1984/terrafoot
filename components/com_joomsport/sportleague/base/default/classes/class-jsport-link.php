<?php

class classJsportLink
{
    public static function season($text, $season_id, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=season&sid='.$season_id.'">'.$text.'</a>';
    }
    public static function calendar($text, $season_id, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=calendar&sid='.$season_id.'">'.$text.'</a>';
    }
    public static function tournament($text, $tournament_id, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=tournament&id='.$tournament_id.'">'.$text.'</a>';
    }
    public static function team($text, $team_id, $season_id = 0, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=team&tid='.$team_id.'&sid='.intval($season_id).'">'.$text.'</a>';
    }
    public static function match($text, $match_id, $class = '', $Itemid = '', $linkable = true)
    {
        return '<a class="'.$class.'" href="index.php?task=match&id='.$match_id.'">'.$text.'</a>';
    }
    public static function player($text, $player_id, $season_id = 0, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=player&id='.$player_id.'&sid='.intval($season_id).'">'.$text.'</a>';
    }
    public static function matchday($text, $matchday_id, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=matchday&id='.$matchday_id.'">'.$text.'</a>';
    }
    public static function venue($text, $venue_id, $season_id = 0, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=venue&id='.$venue_id.'&sid='.$season_id.'">'.$text.'</a>';
    }
    public static function club($text, $club_id, $season_id = 0, $Itemid = '', $linkable = true)
    {
        return '<a href="index.php?task=club&id='.$club_id.'">'.$text.'</a>';
    }
    public static function playerlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        return 'index.php?task=playerlist&sid='.$season_id.$params;
    }
}
