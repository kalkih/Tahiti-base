<?php
/**
 * User class
 *
 */
class CUser {

    // Construktor
    public function __construct($db) {
        $this->db=$db;
    }

     public function Login($user, $password) 
    { 
        $sql = "SELECT acronym, name FROM USER WHERE acronym = ? AND password = md5(concat(?, salt))";
        $params = array();
        $params=[htmlentities($user),  htmlentities($password)];
        $res=$this->db->ExecuteSelectQueryAndFetchAll($sql, $params);
        if(isset($res[0])) {
            $_SESSION['user'] = $res[0];
            return true;
        }
        else{
            return false;
        }
    }
    
    public function Logout() {
        unset($_SESSION['user']);
    }

    public function IsAuthenticated()
    {
       if(isset($_SESSION['user'])){
            return true;
        }
        else{
            return false;
        }
    }

    public function GetStatus()
    {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;
        if($acronym) {
            $output = "Inloggad som: $acronym ({$_SESSION['user']->name}).";
        }
        else {
            $output = "Inte inloggad.";
        }
        return $output;
    }

    public function GetAcronym() {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

        if($acronym) {
            $output = "{$_SESSION['user']->acronym}";
        }
        return $output;
    }

    public function GetName() {
        $user = isset($_SESSION['user']) ? $_SESSION['user']->user : null;

        if($user) {
            $output = "{$_SESSION['user']->name}";
        }
        return $output;
    }

}