<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 24/08/14
 * Time: 09:37
 */

namespace Webberig\SlackHangBot\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Expense
 *
 * @ORM\Table(name="games")
 * @ORM\Entity
 */
class Game {
    const STATUS_IN_PROGRESS = 0;
    const STATUS_WON = 1;
    const STATUS_ABORTED = 2;
    const STATUS_LOST = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStarted", type="date", nullable=false)
     */
    protected $dateStarted;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=100, nullable=false)
     */
    protected $word;

    /**
     * @var string
     *
     * @ORM\Column(name="hint", type="string", length=255, nullable=false)
     */
    protected $hint;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=30, nullable=false)
     */
    protected $channel;

    /**
     * @var string
     *
     * @ORM\Column(name="characters", type="string", length=50, nullable=false)
     */
    protected $characters = "";

    /**
     * @var integer
     *
     * @ORM\Column(name="tries", type="integer", nullable=false)
     */
    protected $tries = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="userStarted", type="string", length=50, nullable=false)
     */
    protected $userStarted;

    /**
     * @var string
     *
     * @ORM\Column(name="userStartedName", type="string", length=50, nullable=false)
     */
    protected $userStartedName;

    /**
     * @var string
     *
     * @ORM\Column(name="userWonId", type="string", length=50, nullable=true)
     */
    protected $userWonId = "";

    /**
     * @var string
     *
     * @ORM\Column(name="userWonName", type="string", length=50, nullable=true)
     */
    protected $userWonName = "";

    /**
     * Create a new game
     * @param $user string User who's starting the new game
     * @param $channel string Channel in which the game is being played
     * @param $word string Word that players need to guess
     */
    public function __construct($user_id, $user_name, $channel_id, $word) {
        $this->channel = $channel_id;
        $this->userStarted = $user_id;
        $this->userStartedName = $user_name;
        $this->word = strtolower($word);
        $this->status = self::STATUS_IN_PROGRESS;
        $this->tries = 0;
        $this->dateStarted = new \DateTime();
    }

    /**
     * Set a hint (category or description)
     * Only the creating user should be able to do this!
     * @param $hint
     */
    public function setHint($hint) {
        $this->hint = $hint;
    }

    /**
     * Abort the game. Nobody wins, nobody loses
     * Only the starting user should abort
     */
    public function abort() {
        $this->status = self::STATUS_ABORTED;
    }

    /**
     * User has given a character
     * @param $char string the given character (should be a string of length 1)
     * @param $user string the user who's taking a guess
     * @return bool returns true of the given character is present, otherwise false
     * @throws \InvalidArgumentException Will throw exception if the character is already tried or invalid
     */
    public function char($char, $user) {
        $chars = str_split($this->characters);
        if (strlen($char) !== 1) {
            throw new \InvalidArgumentException("Char must be a single letter");
        }
        $char = strtolower($char);
        if (in_array($char, $this->$chars)) {
            throw new \InvalidArgumentException("Already guessed this letter, focus!");
        }
        $chars[] = $char;

        $this->characters = implode("", $chars);
        if (strpos($this->word, $char) === false) {
            $this->tries++;
            $this->checkStatus($user);
            return false;
        } else {
            $this->checkStatus($user);
            return true;
        }
    }

    /**
     * User guesses the complete word
     * @param $word string word being guessed
     * @param $user string the user trying to guess
     * @return bool returns true if the answer is correct (and the game is won!), false otherwise
     */
    public function guess($word, $user) {
        if ($word == $this->word) {
            $this->userWon = $user;
            $this->status = self::STATUS_WON;
            return true;
        } else {
            $this->tries++;
            $this->checkStatus($user);
            return false;
        }
    }

    /**
     * Checks if the game is lost (10 tries) or won (all characters are guessed)
     * @param $user
     */
    private function checkStatus($user) {
        if ($this->tries >= 10) {
            $this->status = self::STATUS_LOST;
        }

        $triedCharacters = str_split($this->characters);
        // Check if all characters have been guessed
        $guessed = 0;
        $chars = str_split($this->word);
        foreach ($chars as $c) {
            if (in_array($c, $this->$triedCharacters)) {
                $guessed++;
            }
        }
        if ($guessed == count($chars)) {
            $this->status = self::STATUS_WON;
            $this->userWon = $user;
        }
    }

    /**
     * @return mixed
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getTries()
    {
        return $this->tries;
    }

    /**
     * @return string
     */
    public function getUserStarted()
    {
        return $this->userStarted;
    }

    /**
     * @return mixed
     */
    public function getUserWon()
    {
        return $this->userWon;
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }


    public function isWon() {
        return $this->status == self::STATUS_WON;
    }
    public function isLost() {
        return $this->status == self::STATUS_LOST;
    }
}