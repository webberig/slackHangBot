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

    private function post($message, GameAction $action)
    {
        $content = $this->twig->render(
            "::slack.text.twig",
            array(
                'game' => $action->getGame(),
                'message' => $message
            )
        );
        $response = $this->slack->send(
            MethodFactory::METHOD_CHAT_POSTMESSAGE,
            [
                'text' => "```" . $content . "```",
                'channel' => $action->getChannelName()
            ]
        );
        return $response;
    }

    public function postStarting(GameAction $action)
    {
        return $this->post($action->getPlayerName() . " has started a game", $action);
    }

    public function postChangeHint(GameAction $action)
    {
        return $this->post($action->getPlayerName() . " changed the hint", $action);
    }

    public function postLost(GameAction $action)
    {
        return $this->post(
            "The game is lost, nobody wins. The word was: " . $action->getGame()->getWord() . ".",
            $action
        );
    }

    public function postWon(GameAction $action)
    {
        return $this->post(
            $action->getPlayerName() . " won the game! The word was: " . $action->getGame()->getWord() . ".",
            $action
        );
    }

    public function postGuessCharacterSuccess(GameAction $action, $char)
    {
        return $this->post($action->getPlayerName() . " guessed a character: " . $char, $action);
    }

    public function postGuessCharacterFail(GameAction $action, $char)
    {
        return $this->post($action->getPlayerName() . " tried '" . $char . "' but it's not in the word", $action);
    }

    public function postGuessWordFail(GameAction $action, $word)
    {
        return $this->post(
            $action->getPlayerName() . "'s tried to guess the word '" . $word . "', it's not correct",
            $action
        );
    }

    public function postAbort(GameAction $action)
    {
        return $this->post(
            $action->getPlayerName() . " has aborted the game. The word was: " . $action->getGame()->getWord(),
            $action
        );
    }
}