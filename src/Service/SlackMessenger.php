<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 14:56
 */

namespace Webberig\SlackHangBot\Service;


use CL\Slack\Api\Method\MethodFactory;
use Symfony\Component\Templating\EngineInterface;
use Webberig\SlackHangBot\Entity\Game;
use Webberig\SlackHangBot\Entity\GameAction;
use Webberig\SlackHangBot\SlackCommandRequest;

class SlackMessenger
{
    private $slack;
    private $twig;

    public function __construct($slack, EngineInterface $twig)
    {
        $this->slack = $slack;
        $this->twig = $twig;
    }

    private function post($view, GameAction $action, $char = null)
    {
        $content = $this->twig->render(
            "::". $view .".text.twig",
            array(
                'game' => $action->getGame(),
                'action' => $action,
                'char' => $char
            )
        );
        $response = $this->slack->send(
            MethodFactory::METHOD_CHAT_POSTMESSAGE,
            [
                'text' => $content,
                'channel' => $action->getChannelName(),
                'username' => "Hangbot"
            ]
        );
        return $response;
    }

    public function postStarting(GameAction $action)
    {
        return $this->post("startgame", $action);
    }

    public function postChangeHint(GameAction $action)
    {
        return $this->post("changehint", $action);
    }

    public function postLost(GameAction $action)
    {
        return $this->post("gamelost", $action);
    }

    public function postWon(GameAction $action)
    {
        return $this->post("gamewon", $action);
    }

    public function postGuessCharacterSuccess(GameAction $action, $char)
    {
        return $this->post("character_success", $action, $char);
    }

    public function postGuessCharacterFail(GameAction $action, $char)
    {
        return $this->post("character_fail", $action, $char);
    }

    public function postGuessWordFail(GameAction $action, $word)
    {
        return $this->post("word_fail", $action, $word);
    }

    public function postAbort(GameAction $action)
    {
        return $this->post("abort", $action);
    }
}