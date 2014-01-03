<?php
/**
 * Round class
 *
 */
class CRound {

    // Variables
    private $rounds;
    private $roundScore;
    private $totalScore;

    // Constructor
    public function __construct() {
        $this->rounds = 1;
        $this->roundScore = 0;
        $this->totalScore = 0;
    }

    // Member Functions
    public function GetRounds() {
        return $this->rounds;
    }

    public function GetRoundScore() {
        return $this->roundScore;
    }

    public function GetTotalScore() {
        return $this->totalScore;
    }

    public function AddRoundScore($value){
        $this->roundScore += $value;
    }

    public function AddTotalScore(){
        $this->totalScore += $this->roundScore;
    }

    public function NewRound(){
        $this->rounds++;
        $this->roundScore = 0;
    }
}

?>