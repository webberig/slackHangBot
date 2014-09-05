<?php
/**
 * Created by PhpStorm.
 * User: mathieum
 * Date: 26/08/14
 * Time: 12:56
 */

namespace Webberig\SlackHangBot\Service;


use Webberig\SlackHangBot\Entity\Game;

class HangmanExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'Hangman';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('hangart', array($this, 'getHangmanArt')),
            new \Twig_SimpleFunction('hangmanGuesses', array($this, 'getHangmanGuesses')),
            new \Twig_SimpleFunction('hangmanWord', array($this, 'getHangmanWord'))
        );
    }

    public function getHangmanArt($line, $tries)
    {
        $line = $line + ($tries * 7);
        $txt = file_get_contents(__DIR__ . "/../../app/Resources/hangman.txt");
        $lines = explode("\n", $txt);
        return $lines[$line];
    }

    public function getHangmanGuesses(Game $game)
    {
        return implode(" ", $game->getCharacters());
    }

    public function getHangmanWord(Game $game)
    {
        $res = "";

        if($game->isLost() || $game->isWon()){
            return implode(" ", str_split($game->getWord()));
        }

        $chars = str_split($game->getWord());
        $guesses = $game->getCharacters();
        foreach ($chars as $char) {
            if (in_array($char, $guesses)) {
                $res .= $char;
            } else {
                $res .= "_";
            }
        }
        return implode(" ", str_split($res));
    }
}
 