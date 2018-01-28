<?php
include_once("settings.inc.php");

class Database {
    
    private $db; // PDO objekt databaze
    
    public function __construct(){
        global $db_server, $db_name, $db_user, $db_pass;        
        // informace se berou ze settings
        $this->db = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
        
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /*public function __construct($host, $dbname, $usr, $pas){
        $this->db = new PDO("mysql:host=$host;dbname=$dbname", $usr, $pas);
        session_start();
    }*/
    
    /**
     *  Provede dotaz a buď vrátí jeho výsledek, nebo null a vypíše chybu.
     *  @param string $dotaz    Dotaz.
     *  @return object          Vysledek dotazu.
     */
    private function executeQuery($dotaz){
        $res = $this->db->query($dotaz);
        if (!$res) {
            $error = $this->db->errorInfo();
            echo $error[2];
            return null;
        } else {
            return $res;            
        }
    }
    
    /**
     *  Prevede vysledny objekt dotazu na pole.
     *  @param object $obj  Objekt s vysledky dotazu.
     *  @return array       Pole s vysledky dotazu.
     */
    private function resultObjectToArray($obj){
        // získat po řádcích            
        /*while($row = $vystup->fetch(PDO::FETCH_ASSOC)){
            $pole[] = $row['login'].'<br>';
        }*/
        return $obj->fetchAll(); // všechny řádky do pole        
    }
    
    /**
     *  Vraci prava uzivatelu.
     *  @return array   Dostupna prava uzivatelu.
     */
    public function allRights(){
        $q = "SELECT * FROM sigutp_prava;";
        $res = $this->executeQuery($q);
        $res = $this->resultObjectToArray($res);
        return array_reverse($res); // pole otocim
    }
    
    /**
     *  Vraci vsechny informace o uzivateli.
     *  @param string $login    Login uzivatele.
     *  @return array           Pole s informacemi o konkretnim uzivateli nebo null.
     */
    public function allUserInfo($login){
        $q = "SELECT * FROM sigutp_uzivatele, sigutp_prava
                WHERE sigutp_uzivatele.login = '$login'
                  AND sigutp_prava.idprava = sigutp_uzivatele.idprava;";
        $res = $this->executeQuery($q);
        $res = $this->resultObjectToArray($res);
        //print_r($res);
        if($res != null && count($res)>0){
            // vracim pouze prvni radek, ve kterem je uzivatel
            return $res[0];
        } else {
            return null;
        }
    }
    
    /**
     *  Vraci vsechny informace o vsech uzivatelich.
     *  @return array           Pole s informacemi o konkretnim uzivateli nebo null.
     */
    public function allUsersInfo(){
        $q = "SELECT * FROM sigutp_uzivatele, sigutp_prava
                WHERE sigutp_prava.idprava = sigutp_uzivatele.idprava;";
        $res = $this->executeQuery($q);
        $res = $this->resultObjectToArray($res);
        //print_r($res);
        if($res != null && count($res)>0){
            // vracim vse
            return $res;
        } else {
            return null;
        }
    }
    
    /**
     *  Overi, zda dany uzivatel ma dane heslo.
     *  @param string $login  Login uzivatele.
     *  @param string $pass     Heslo uzivatele.
     *  @return boolean         Jsou hesla stejna?
     */
    public function isPasswordCorrect($login, $pass){
        $usr = $this->allUserInfo($login);
        if($usr==null){ // uzivatel neni v DB
            return false;
        }
        return $usr["heslo"]==$pass; // je heslo stejne?
    }
    
    /**
     *  Overi heslo uzivatele a pokud je spravne, tak uzivatele prihlasi.
     *  @param string $login    Login uzivatele.
     *  @param string $pass     Heslo uzivatele.
     *  @return boolean         Podarilo se prihlasit.
     */
    public function userLogin($login, $pass){
        if(!$this->isPasswordCorrect($login,$pass)){// neni heslo spatne?
            return false; // spatne heslo
        }
        // ulozim uzivatele do session
        $_SESSION["user"] = $this->allUserInfo($login);
        return true;
    }
    
    /**
     *  Odhlasi uzivatele.
     */
    public function userLogout(){
        // odstranim session
        if (isset($_SESSION["user"])){
            session_unset($_SESSION["user"]);
        }
    }
    
    /**
     *  Je uzivatel prihlasen?
     */
    public function isUserLogged(){
        return isset($_SESSION["user"]);
    }
    
    /**
     *  Vytvori v databazi noveho uzivatele.
     *
     *  @return boolean         Podarilo se uzivatele vytvorit
     */
    public function addNewUser($login,$jmeno, $heslo, $email, $idPrava){
        $q = "INSERT INTO sigutp_uzivatele(login,jmeno,heslo,email,idprava)
                VALUES ('$login','$jmeno','$heslo','$email',$idPrava)";
        $res = $this->executeQuery($q);
        if($res == null){
            return false;
        } else {
            return true;
        }
    }
    
    /**
     *  Upravi informace o danem uzivateli.
     *  ... vse potrebne ...
     *  @return boolean         Podarilo se data upravit?
     */
    public function updateUserInfo($userId, $jmeno, $heslo, $email, $idPrava){
        $q = "UPDATE sigutp_uzivatele
                SET jmeno='$jmeno', heslo='$heslo', email='$email', idprava=$idPrava 
                WHERE iduzivatel=$userId";
        $res = $this->executeQuery($q);
        if($res == null){
            return false;
        } else {
            return true;
        }
    }
    
    /**
     *  Smaze daneho uzivatele z databaze.
     *  @param integer $userId  ID uzivatele.
     *  @return boolean         Podarilo se?
     */
    public function deleteUser($userId){
        $q = "DELETE FROM sigutp_uzivatele
                WHERE iduzivatel=$userId";
        $res = $this->executeQuery($q);
        if($res == null){
            return false;
        } else {
            return true;
        }
    }
    
    //////////////// --- ARTICLE --- ////////////////
    
    public function addArticle($name, $authors, $abstract, $fileName){
        $user_id = $_SESSION["user"]["iduzivatel"];
        $time = date("Y-m-d H:i:s", time());
        $q = "INSERT INTO sigutp_articles(name,authors,abstract,fileName,user_id,time)
                VALUES ('$name','$authors','$abstract','$fileName','$user_id', '$time')";
        $res = $this->executeQuery($q);
        if($res == null){
            return false;
        } else {
            return true;
        }
    }
    
    public function deleteArticle($article_id){       
        $databaseEntry = $this->fetchArticle($article_id);
         
        $q = "DELETE FROM sigutp_articles WHERE id = $article_id";
        $this->executeQuery($q);
        
        return $databaseEntry['fileName'];
    }
    
    public function updateArticle($article_id, $name, $authors, $abstract, $fileName){        
        $q = "UPDATE sigutp_articles SET name = '$name', authors = '$authors', abstract = '$abstract', fileName = '$fileName' WHERE id = $article_id";
        $this->executeQuery($q);           
    }
    
    public function updateFileName($article_id, $fileName){
        $q = "UPDATE sigutp_articles SET fileName = '$fileName' WHERE id = $article_id";
        $this->executeQuery($q);   
    }
    
    public function getArticles(){
        $user_id = $_SESSION["user"]["iduzivatel"];
        $q = "SELECT * FROM sigutp_articles WHERE user_id = $user_id";
        return $this->executeQuery($q);
    }
    
    public function getAllArticles(){        
        $q = "SELECT * FROM sigutp_articles";
        return $this->executeQuery($q);
    }
    
    public function getAllArticlesAuthor(){
        $q = "SELECT * FROM sigutp_articles, sigutp_uzivatele WHERE sigutp_articles.user_id = sigutp_uzivatele.iduzivatel;";
        return $this->executeQuery($q);
    }
    
    public function fetchArticle($article_id){
        $q = "SELECT * FROM sigutp_articles WHERE id = $article_id";
        $databaseEntry = $this->executeQuery($q);
        return $databaseEntry->fetch();
    }
    
    //////////////// --- REVIEW --- ////////////////
    
    public function getReviewers(){
        $q = "SELECT * FROM sigutp_uzivatele WHERE idprava = 2;";
        return $this->executeQuery($q);
    }    
    
    public function getAllArticlesWithReviews(){        
        $q = "SELECT authors.login AS author_name, sigutp_articles.name, sigutp_articles.authors, sigutp_articles.time, reviewers.login AS reviewer_name, sigutp_reviews.state, sigutp_reviews.merit, sigutp_reviews.accuracy, sigutp_reviews.language, sigutp_reviews.review_id AS id
        FROM sigutp_articles, sigutp_reviews, sigutp_uzivatele AS authors, sigutp_uzivatele AS reviewers WHERE sigutp_articles.id = sigutp_reviews.article_id AND sigutp_articles.user_id = authors.iduzivatel AND sigutp_reviews.reviewer_id = reviewers.iduzivatel ORDER BY sigutp_articles.id;";
        
        return $this->executeQuery($q);
    }
        
    public function assignReview($article_id, $reviewer_id){
        $q = "INSERT INTO sigutp_reviews(article_id, reviewer_id, state, merit, accuracy, language)
                VALUES ('$article_id', '$reviewer_id', 'working', '0', '0', '0')";
        $this->executeQuery($q);
    }
}



?>