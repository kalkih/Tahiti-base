<?php
/**
 * Dice class
 *
 */
class CDice {

    // Variables
    private $faces;
    private $lastRoll;

    // Constructor
    public function __construct($faces = 6) {
        $this->faces = $faces;
    }

    // Member Functions
    public function Roll($times) {
        $this->rolls = array();

        for ($i = 0; $i < $times; $i++) {
            $this->lastRoll = rand(1, $this->faces);
            $this->rolls[] = $this->lastRoll;
        }
    }

    public function GetRoll() {
        return $this->lastRoll;
    }

    public function GetRollAsImage() {
        $html = "<div class='dice dice-{$this->lastRoll}'></div>";
        return $html;
    }
}

?>