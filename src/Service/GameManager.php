<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 09:36
 */

namespace Webberig\SlackHangBot\Service;


use Doctrine\ORM\EntityManagerInterface;
use Webberig\SlackHangBot\Entity\Game;
use Webberig\SlackHangBot\Entity\GameAction;

class GameManager {
    private $slack;
    private $em;

    public function __construct(EntityManagerInterface $em, SlackMessenger $slack)
    {
        $this->em = $em;
        $this->slack = $slack;
    }


    public function getGameInProgress($channel)
    {
        // @todo: Get game from db
        $repo = $this->em->getRepository("Webberig\\SlackHangBot\\Entity\\Game");
        $game = $repo->findOneBy([
                "channel" => $channel,
                "status" => Game::STATUS_IN_PROGRESS
            ]);
        return $game;
    }

    public function startGame(GameAction $action, $word, $hint = null)
    {
        if ($action->getGame()) {
            throw new \InvalidArgumentException("A game is already in progress");
        }

        $game = new Game(
            $action->getPlayerId(),
            $action->getPlayerName(),
            $action->getChannelId(),
            $word
        );
        if ($hint) {
            $game->setHint($hint);
        }
        $this->slack->postStarting($this);
        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }
    public function changeHint(GameAction $action, $hint)
    {
        if (!$action->getGame()) {
            throw new \InvalidArgumentException("No game in progress");
        }

        if ($action->getGame()->getUserStarted() !== $action->getPlayerId()) {
            throw new \InvalidArgumentException("You are not the game master!");
        }
        $action->getGame()->setHint($hint);

    }
    public function guessWord(GameAction $action, $word) {
        if (!$action->getGame()) {
            throw new \InvalidArgumentException("No game in progress");
        }

        return $action->getGame()->guess($word, $action->getPlayerId());
    }

    public function guessCharacter(GameAction $action, $char) {
        if (!$action->getGame()) {
            throw new \InvalidArgumentException("No game in progress");
        }

        return $action->getGame()->char($char, $action->getPlayerId());
    }

    public function abort(GameAction $action) {
        if (!$action->getGame()) {
            throw new \InvalidArgumentException("No game in progress");
        }
        if ($action->getGame()->getUserStarted() !== $action->getPlayerId()) {
            throw new \InvalidArgumentException("You are not the game master!");
        }
        $action->getGame()->abort();
    }
}