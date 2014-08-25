<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 13:22
 */

namespace Webberig\SlackHangBot;


use Symfony\Component\HttpFoundation\Request;

class SlackCommandRequest {
    private $token;
    private $team_id;
    private $channel_id;
    private $channel_name;
    private $user_id;
    private $user_name;
    private $command;
    private $text;

    public function __construct(Request $req)
    {
        $data = $req->isMethod("post") ? $req->request : $req->query;
        $this->token = $data->get('token');
        $this->team_id = $data->get('team_id');
        $this->channel_id = $data->get('channel_id');
        $this->channel_name = $data->get('channel_name');
        $this->user_id = $data->get('user_id');
        $this->user_name = $data->get('user_name');
        $this->command = $data->get('command');
        $this->text = $data->get('text');
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
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }
}
