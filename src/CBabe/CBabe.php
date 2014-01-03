<?php
/**
 * Babe class
 * Get babe pic for each month
 */
class CBabe {
    private $babe;
    private $month;

    public function __construct($month) {
        $this->month = $month;
        $this->babe = null;
    }

    public function GetBabe() {
        $babes = array('1'=>'Januari', '2'=>'Februari', '3'=>'Mars', '4'=>'April', '5'=>'Maj', '6'=>'Juni', '7'=>'Juli', '8'=>'Augusti', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'December');
        return 'img/babe/' . $babes[$this->month] . '.jpg';
    }

}

?>