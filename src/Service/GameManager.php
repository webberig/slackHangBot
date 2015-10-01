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

class GameManager
{
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
        $game = $repo->findOneBy(
            [
                "channel" => $channel,
                "status" => Game::STATUS_IN_PROGRESS
            ]
        );
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
        $action->setGame($game);
        $this->slack->postStarting($action);
        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }

    public function changeHint(GameAction $action, $hint)
    {
        $game = $action->getGame();
        if (!$game) {
            throw new \InvalidArgumentException("No game in progress");
        }

        if ($game->getUserStarted() !== $action->getPlayerId()) {
            throw new \InvalidArgumentException("You are not the game master!");
        }
        $game->setHint($hint);
        $this->slack->postChangeHint($action);
        $this->em->flush();
    }

    public function guessWord(GameAction $action, $word)
    {
        $game = $action->getGame();
        if (!$game) {
            throw new \InvalidArgumentException("No game in progress.");
        }
        if ($game->getUserStarted() == $action->getPlayerId()) {
            throw new \InvalidArgumentException("Cannot guess during your own game.");
        }
        $return = $game->guess($word, $action->getPlayerId());
        if ($game->isWon()) {
            $this->slack->postWon($action);
        } elseif ($game->isLost()) {
            $this->slack->postLost($action);
        } else {
	        $this->slack->postGuessWordFail($action, $word);
        }
        $this->em->flush();
        return $return;
    }

    public function guessCharacter(GameAction $action, $char)
    {
        $game = $action->getGame();
        if (!$game) {
            throw new \InvalidArgumentException("No game in progress");
        }
        if ($game->getUserStarted() == $action->getPlayerId()) {
            throw new \InvalidArgumentException("Cannot guess during your own game.");
        }
        $return = $game->char($char, $action->getPlayerId());
        if ($return) {
            $this->slack->postGuessCharacterSuccess($action, $char);
            if ($game->isWon()) {
                $this->slack->postWon($action);
            }
        } else {
            $this->slack->postGuessCharacterFail($action, $char);
            if ($game->isLost()) {
                $this->slack->postLost($action);
            }
        }
        $this->em->flush();
        return $return;
    }

    public function abort(GameAction $action)
    {
        $game = $action->getGame();
        if (!$game) {
            throw new \InvalidArgumentException("No game in progress");
        }
        if ($game->getUserStarted() !== $action->getPlayerId()) {
            throw new \InvalidArgumentException("You are not the game master!");
        }
        $game->abort();
        $this->slack->postAbort($action);
        $this->em->flush();
    }

    public function showHelp(GameAction $action)
    {
        $this->slack->postHelp($action);
        $this->em->flush();
    }
}