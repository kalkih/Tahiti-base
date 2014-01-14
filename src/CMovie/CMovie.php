<?php
/**
 * CMovie class
 *
 */
class CMovie {

    // Construktor
    public function __construct($db) {
        $this->db=$db;
    }

    public function RegisterMovie($title, $year) 
    { 
        $sql = "INSERT INTO Movie (title, YEAR, image, added) VALUES (?,?,?,NOW());";
        $params = array($title, $year, 'movies/default.jpg');
        $res = $this->db->ExecuteQuery($sql, $params);

        if($res) {
            $output = true;
        }
        else {
            $output = false;
        }
        return $output;
    }

    public function DeleteMovie($id) 
    { 
        $sql = "DELETE FROM Movie2Genre WHERE idMovie = ?";
        $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));

        $sql = "DELETE FROM Movie WHERE id = ?";
        $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));

        $output = 'Filmen togs bort!';

        return $output;
    }

    public function EditMovie($title, $length, $year, $plot, $image, $imdb, $trailer, $cost, $id, $genres) 
    { 
        $sql = '
            UPDATE Movie SET
                title   = ?,
                LENGTH  = ?,
                YEAR    = ?,
                plot    = ?,
                image   = ?,
                imdb    = ?,
                trailer = ?,
                cost    = ?
            WHERE
                id = ?
        ;';
        $params = array($title, $length, $year, $plot, $image, $imdb, $trailer, $cost, $id);
        $res = $this->db->ExecuteQuery($sql, $params);
        if($res) {
            $output = 'Uppdateringen lyckades!';
        }
        else {
            $output = 'Uppdateringen misslyckades!';
        }

        $count = 0;
        foreach ($genres as $val) {
            if ($val) {
                $sql = 'INSERT INTO Movie2Genre (idMovie, idGenre) VALUES (?, ?);';
                $this->db->ExecuteQuery($sql, array($id, $count));
            }
            $count++;
        }

        return $output;
    }

    public function GetFormContent($id) {
        $title  = null;
        $length = null;
        $year   = null;
        $plot   = null;
        $image  = null;
        $imdb   = null;
        $trailer = null;
        $cost   = null;
        
        // Select from database
        $sql = 'SELECT * FROM Movie WHERE id = ?;';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));
        
        if(isset($res[0])) {
          $c = $res[0];
        }
        else {
          die('Misslyckades: det finns ingen film med detta id!');
        }
        
        // Sanitize content before using it.
        $title      = htmlentities($c->title, null, 'UTF-8');
        $length     = $c->LENGTH;
        $year       = $c->YEAR;
        $plot       = htmlentities($c->plot, null, 'UTF-8');
        $image      = $c->image;
        $imdb       = $c->imdb;
        $trailer    = $c->trailer;
        $cost       = $c->cost;

        // Genre
        $comedy    = null;
        $romance   = null;
        $college   = null;
        $crime     = null;
        $drama     = null;
        $thriller  = null;
        $animation = null;
        $adventure = null;
        $family    = null;
        $svenskt   = null;
        $action    = null;
        $horror    = null;

        $genres = array('unknown', $comedy, $romance, $college, $crime, $drama, $thriller, $animation, $adventure, $family, $svenskt, $action, $horror);

        $sql = 'SELECT idGenre AS genre FROM Movie2Genre WHERE idMovie = ?;';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));

        if(isset($res[0])) {
            foreach ($res as $val) {
                $count = 0;
                foreach ($genres as $genre) {
                    if ($val->genre == $count) {
                        $genres[$count] = 1;
                    }
                    $count++;
                }
            }
        }

        $content = array('title'=>$title,'length'=>$length,'year'=>$year,'plot'=>$plot,'image'=>$image,'imdb'=>$imdb,'trailer'=>$trailer,'cost'=>$cost,'genres'=>$genres);
        
        return $content;
    }

}