<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 14:56
 */

namespace Webberig\SlackHangBot\Service;


use CL\Slack\Api\Method\MethodFactory;
use Webberig\SlackHangBot\Entity\Game;
use Webberig\SlackHangBot\SlackCommandRequest;

class SlackMessenger {
    private $slack;
    private $twig;

    public function __construct($slack, $twig)
    {
        $this->slack = $slack;
        $this->twig = $twig;
    }

    public function postStarting(GameManager $game) {
        return;
        $response = $this->slack->send(MethodFactory::METHOD_CHAT_POSTMESSAGE, [
            'text' => "Sending a test",
            'channel'   => $game
        ]);
        return $response;
    }
} 