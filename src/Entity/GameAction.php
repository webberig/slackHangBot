<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 18:52
 */

namespace Webberig\SlackHangBot\Entity;


class GameAction {
    private $game;
    private $channel_id;
    private $channel_name;
    private $player_id;
    private $player_name;

    function __construct($channel_id, $channel_name, $player_id, $player_name)
    {
        $this->channel_id = $channel_id;
        $this->channel_name = $channel_name;
        $this->player_id = $player_id;
        $this->player_name = $player_name;
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->channel_id;
    }

    /**
     * @return mixed
     */
    public function getChannelName()
    {
        return $this->channel_name;
    }

    /**
     * @return Game|null
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }


    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->player_id;
    }

    /**
     * @return mixed
     */
    public function getPlayerName()
    {
        return $this->player_name;
    }


} 