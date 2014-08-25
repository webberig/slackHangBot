<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 13:14
 */
namespace Webberig\SlackHangBot\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webberig\SlackHangBot\Entity\GameAction;
use Webberig\SlackHangBot\Service\CommandHandler;
use Webberig\SlackHangBot\SlackCommandRequest;

class Slack {
    private $handler;
    public function __construct(CommandHandler $handler) {
        $this->handler = $handler;
    }
    public function inboundSlashCommand(Request $request) {
        $req = new SlackCommandRequest($request);
        $action = new GameAction(
            $req->getChannelId(),
            $req->getChannelName(),
            $req->getUserId(),
            $req->getUserName()
        );

        try {
            $return = $this->handler->execute(
                $req->getText(),
                $action
            );
        } catch (\InvalidArgumentException $e) {
            $return = $e->getMessage();
        }
        return new Response($return);
    }
} 