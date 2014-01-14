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

    public function register($acronym, $name, $password) 
    { 
        $sql = "INSERT INTO Users (acronym, name, salt) VALUES 
                (?, ?, unix_timestamp());
                UPDATE Users SET password = md5(concat(?, salt)) WHERE acronym = ?;
                ";
        $params = array($acronym, $name, $password, $acronym);
        $res = $this->db->ExecuteQuery($sql, $params);

        if($res) {
            $output = 'Kontot skapades!';
        }
        else {
            $output = 'Kontot kunde inte skapas, kontrollera inmatning!';
        }
        return $output;
    }

    public function DeleteUser($acronym) 
    { 
        $sql = "DELETE FROM Users WHERE acronym = ?";
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($acronym));

        return "Användaren är borttagen!";
    }

    public function EditUser($acronym, $name, $age, $admin, $balance, $description) 
    { 
        $sql = '
            UPDATE Users SET
                acronym = ?,
                name    = ?,
                age     = ?,
                admin   = ?,
                balance = ?,
                description = ?
            WHERE 
                acronym = ?
        ;';
        $params = array($acronym, $name, $age, $admin, $balance, $description, $acronym) ;
        $res = $this->db->ExecuteQuery($sql, $params);
        if($res) {
            $output = 'Uppdateringen lyckades!';
        }
        else {
            $output = 'Uppdateringen misslyckades!';
        }
        return $output;
    }

    public function Login($user, $password) 
    { 
        $sql = "SELECT acronym, name, admin, balance FROM Users WHERE acronym = ? AND password = md5(concat(?, salt))";
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

    public function IsAdmin()
    {
       if(isset($_SESSION['user']) && $_SESSION['user']->admin == 'yes'){
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
            $output = "Inloggad som: <a href='profile.php?user={$acronym}'>{$acronym}</a> ({$_SESSION['user']->name})";
        }
        else {
            $output = "Inte inloggad";
        }
        return $output;
    }

    public function GetAcronym() {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

        $output = null;
        if($acronym) {
            $output = "{$_SESSION['user']->acronym}";
        }

        return $output;
    }

    public function GetName() {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

        if($acronym) {
            $output = "{$_SESSION['user']->name}";
        }
        return $output;
    }

    public function GetBalance() {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

        if($acronym) {
            $sql = 'SELECT balance FROM Users WHERE acronym = ?;';
            $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($acronym));
            $output = $res[0]->balance;
        }
        return $output;
    }

    public function Purchase($price) {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;
        

        if($acronym) {
            $sql = 'SELECT balance FROM Users WHERE acronym = ?;';
            $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($acronym));
            $balance = $res[0]->balance;

            if ($balance >= $price) {
                $output = true;
                $balance -= $price;

                $sql = 'UPDATE Users SET balance = ? WHERE acronym = ?;';
                $this->db->ExecuteQuery($sql, array($balance, $acronym));
                $_SESSION['user']->balance = $balance;
            }
            else {
                $output = false;
            }
        }
        else {
            $output = false;
        }

        return $output;
    }

    public function Refill($price) {
        $acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;
        
        if($acronym) {
            $sql = 'SELECT balance FROM Users WHERE acronym = ?;';
            $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($acronym));
            $balance = $res[0]->balance;

            $output = true;
            $balance += $price;

            $sql = 'UPDATE Users SET balance = ? WHERE acronym = ?;';
            $this->db->ExecuteQuery($sql, array($balance, $acronym));
            $_SESSION['user']->balance = $balance;
        }
        else {
            $output = false;
        }

        return $output;
    }

    public function GetFormContent($acronym) {
        $name    = null;
        $age     = null;
        $admin   = null;
        $balance = null;
        $description = null;
        
        // Select from database
        $sql = 'SELECT * FROM Users WHERE acronym = ?';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($acronym));
        
        if(isset($res[0])) {
          $c = $res[0];
        }
        else {
          die('Misslyckades: det finns ingen anvvändare med denna akronym!');
        }
        
        // Sanitize content before using it.
        $acronym    = htmlentities($c->acronym, null, 'UTF-8');
        $name       = htmlentities($c->name, null, 'UTF-8');
        $age        = $c->age;
        $admin      = $c->admin;
        $balance    = $c->balance;
        $description = $c->description;
    
        $content = array('acronym'=>$acronym,'name'=>$name,'age'=>$age,'admin'=>$admin,'balance'=>$balance,'description'=>$description);
        
        return $content;
    }

}