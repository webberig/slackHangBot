<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 10:58
 */

namespace Webberig\SlackHangBot\Service;


use Webberig\SlackHangBot\Entity\Game;
use Webberig\SlackHangBot\Entity\GameAction;

class CommandHandler {
    private $gm;
    public function __construct(GameManager $gm) {
        $this->gm = $gm;
    }

    /**
     * @param $command string command text given in slash command
     * @param $user string user
     * @param $channel string channel where command is given
     * @return string Message to be returned
     * @throws \InvalidArgumentException Exception message to be returned
     */
    public function execute($command, GameAction $action) {
        $parts = explode(" ", $command);
        $cmd = array_shift($parts);
        $action->setGame($this->gm->getGameInProgress($action->getChannelId()));

        switch($cmd) {
            case 'start':
                // start {word} {hint}
                if (count($parts) == 0) {
                    throw new \InvalidArgumentException("You must specify a word");
                }

                $word = array_shift($parts);

                if (count($parts) > 0) {
                    $hint = implode(" ", $parts);
                } else {
                    $hint = null;
                }
                $this->gm->startGame($action, $word, $hint);
                return "Game has started";
            case 'help':
                return "Help not available yet, ask @mathieu";
            case 'hint':
                // hint {hint}
                if (count($parts) > 0) {
                    $hint = implode(" ", $parts);
                    $this->gm->changeHint($hint);
                    return "Hint is changed";
                } else {
                    throw new \InvalidArgumentException("Please provide a hint");
                }
            case 'guess':
                // guess {char or word}
                if (count($parts) > 0) {
                    $word = array_shift($parts);
                    if (strlen($word) == 1) {
                        $this->gm->guessCharacter($word);
                    } else {
                        $this->gm->guessWord($word);
                    }
                    return "";

                } else {
                    throw new \InvalidArgumentException("Please provide a character or guess the entire word");
                }
            case 'abort':
                // abort
                $this->gm->abort();
                return "Game aborted";
            case 'highscore':
                return "This is not implemented yet, sorry!";
            default:
                throw new \InvalidArgumentException("Invalid command given");
        }
    }
}
