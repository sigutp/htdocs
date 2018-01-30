<?php
include_once("settings.inc.php");

class Database {
    
    private $db; // PDO objekt databaze
    
    public function __construct(){
        global $db_server, $db_name, $db_user, $db_pass;        
        // informace se berou ze settings
        $this->db = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
        $this->db->query("SET NAMES utf8");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
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
    
    //////////////// --- USERS --- ////////////////
    
    /**
    * Vrací přezdívku uživatele dle jeho ID.
    * @param userId ID uživatele
    * @return přezdívka uživatele
    */
    public function getUserLogin($userId){
        $q = "SELECT * FROM sigutp_uzivatele WHERE iduzivatel='$userId';";
        $databaseEntry = $this->executeQuery($q);
        $databaseEntry = $databaseEntry->fetch();
        $databaseEntry = $databaseEntry['login'];
        return $databaseEntry;
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
    
    /**
    * Zablokuje uživatele.
    * @param userId ID uživatele
    * @return úspěšnost operace
    */
    public function blockUser($userId){
        $q = "UPDATE sigutp_uzivatele SET blocked = 1 WHERE iduzivatel = $userId;";
        if ($this->executeQuery($q)){
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * Odblokuje uživatele.
    * @param userId ID uživatele
    * @return úspěšnost operace
    */
    public function unblockUser($userId){
        $q = "UPDATE sigutp_uzivatele SET blocked = 0 WHERE iduzivatel = $userId;";        
        if ($this->executeQuery($q)){
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * Zjistí, zda je uživatel zablokován
    * @param userId ID uživatele
    * @return zdali je uživatel zablokován, nebo ne
    */
    public function isUserBlocked($userName){
        $q = "SELECT * FROM sigutp_uzivatele WHERE login='$userName';";
        $databaseEntry = $this->executeQuery($q);
        $databaseEntry = $databaseEntry->fetch();
        if ($databaseEntry["blocked"] == 0){
            return false;
        } else {
            return true;
        }
    }
    
    /**
    * Změní práva uživatele.
    * @param userId ID uživatele
    * @param roleId ID práva
    * returns úspěšnost operace
    */
    public function changeRole($userId, $roleId){
        $q = "UPDATE sigutp_uzivatele SET idprava=$roleId WHERE iduzivatel=$userId";
        if ($this->executeQuery($q)){
            return true;
        } else {
            return false;
        }
    }
    
    //////////////// --- ARTICLE --- ////////////////
    
    /**
    * Přidá článek do databáze.
    * @param name název článku
    * @param authors autoři článku
    * @param abstract abstrakt
    * @param fileName název souboru
    * returns úspěšnost operace
    */
    public function addArticle($name, $authors, $abstract, $fileName){
        $user_id = $_SESSION["user"]["iduzivatel"];
        $time = date("Y-m-d H:i:s", time());
        $q = "INSERT INTO sigutp_articles(name,authors,abstract,fileName,user_id,time,article_state)
                VALUES ('$name','$authors','$abstract','$fileName','$user_id', '$time', 'fresh')";
        $res = $this->executeQuery($q);
        if($res == null){
            return false;
        } else {
            return true;
        }
    }
    
     /**
    * Odstraní článek z databáze.
    * @param article_id ID článku   
    * returns jméno starého souboru
    */
    public function deleteArticle($article_id){       
        $databaseEntry = $this->fetchArticle($article_id);
         
        $q = "DELETE FROM sigutp_articles WHERE id = $article_id";
        $this->executeQuery($q);
        
        return $databaseEntry['fileName'];
    }
    
    /**
    * Upraví informace o článku.
    * @param article_id ID článku
    * @param name název článku
    * @param authors autoři článku
    * @param abstract abstrakt
    * @param fileName název souboru
    */
    public function updateArticle($article_id, $name, $authors, $abstract, $fileName){        
        $q = "UPDATE sigutp_articles SET name = '$name', authors = '$authors', abstract = '$abstract', fileName = '$fileName' WHERE id = $article_id";
        $this->executeQuery($q);           
    }
    
    /**
    * Změní název souboru.
    * @param article_id ID článku
    * @param fileName nový název souboru 
    */
    public function updateFileName($article_id, $fileName){
        $q = "UPDATE sigutp_articles SET fileName = '$fileName' WHERE id = $article_id";
        $this->executeQuery($q);   
    }
    
    /**
    * Vrátí články napsané právě přihlášeným uživatelem.
    * @return články
    */
    public function getArticles(){
        $user_id = $_SESSION["user"]["iduzivatel"];
        $q = "SELECT * FROM sigutp_articles WHERE user_id = $user_id";
        return $this->executeQuery($q);
    }
    
    /**
    * Vrátí úplně všechny články.
    * @return články
    */
    public function getAllArticles(){        
        $q = "SELECT * FROM sigutp_articles";
        return $this->executeQuery($q);
    }
    
    /**
    * Vrátí všechny články spárované s informacemi o autorovi.
    * @return články
    */
    public function getAllArticlesAuthor(){
        $q = "SELECT * FROM sigutp_articles, sigutp_uzivatele WHERE sigutp_articles.user_id = sigutp_uzivatele.iduzivatel;";
        return $this->executeQuery($q);
    }
    
    /**
    * Vrátí konkrétní článek ve formě jednorozměrného pole (řádek v databázi).
    * @param article_id
    * @return článek ve formě pole
    */
    public function fetchArticle($article_id){
        $q = "SELECT * FROM sigutp_articles WHERE id = $article_id";
        $databaseEntry = $this->executeQuery($q);
        return $databaseEntry->fetch();
    }
    
    /**
    * Vrátí přijaté články (vhodné k publikaci)   
    * @return články
    */
    public function getAcceptedArticles(){
        $q = "SELECT * FROM sigutp_articles WHERE article_state='accepted';";
        return $this->executeQuery($q);
    }
    
    //////////////// --- REVIEW --- ////////////////
    
    /**
    * Vrátí všechny recenzenty.  
    * @return recenzenti
    */
    public function getReviewers(){
        $q = "SELECT * FROM sigutp_uzivatele WHERE idprava = 2;";
        return $this->executeQuery($q);
    }    
    
    /**
    * Vrátí všechny články spárované s informacemi o autorovi a jejich recenzemi.
    * Seřazené dle ID článku (seskupování)
    * @return články
    */
    public function getAllArticlesWithReviews(){        
        $q = "SELECT authors.login AS author_name, sigutp_articles.name, sigutp_articles.authors, sigutp_articles.time, sigutp_articles.article_state, reviewers.login AS reviewer_name, sigutp_reviews.state, sigutp_reviews.merit, sigutp_reviews.accuracy, sigutp_reviews.language, sigutp_reviews.review_id AS id
        FROM sigutp_articles, sigutp_reviews, sigutp_uzivatele AS authors, sigutp_uzivatele AS reviewers WHERE sigutp_articles.id = sigutp_reviews.article_id AND sigutp_articles.user_id = authors.iduzivatel AND sigutp_reviews.reviewer_id = reviewers.iduzivatel ORDER BY sigutp_articles.id;";
        
        return $this->executeQuery($q);
    }
        
    /**
    * Přiřadí recenzentovi článek k recenzi.
    * @param article_id ID článku
    * @param article_id ID recenzenta    
    */
    public function assignReview($article_id, $reviewer_id){
        $q = "INSERT INTO sigutp_reviews(article_id, reviewer_id, state, merit, accuracy, language)
                VALUES ('$article_id', '$reviewer_id', 'working', '0', '0', '0')";
        $this->executeQuery($q);
    }
    
    /**
    * Vrátí všechny nehotové recenze (tj. ty, na kterých se ještě pracuje).    
    * @return recenze
    */
    public function getWorkingReviews(){
        $user_id = $_SESSION['user']['iduzivatel'];
        $q = "SELECT * FROM sigutp_reviews, sigutp_articles WHERE sigutp_reviews.state='working' AND sigutp_reviews.article_id=sigutp_articles.id AND sigutp_reviews.reviewer_id=$user_id AND sigutp_articles.article_state='fresh';";
        return $this->executeQuery($q);
    }
    
    /**
    * Vrátí všechny dokončené recenze.
    * @return recenze
    */
    public function getFinishedReviews(){
        $q = "SELECT * FROM sigutp_reviews, sigutp_articles WHERE sigutp_reviews.state='finished' AND sigutp_reviews.article_id=sigutp_articles.id AND sigutp_reviews.reviewer_id='" . $_SESSION['user']['iduzivatel'] . "' AND sigutp_articles.article_state='fresh';";
        return $this->executeQuery($q);
    }
    
    /**
    * Vrátí jednu konkrétní recenzi ve formě jednorozměrného pole.  
    * @return recenze
    */
    public function fetchReview($review_id){
        $q = "SELECT * FROM sigutp_reviews WHERE id = $review_id";
        $databaseEntry = $this->executeQuery($q);
        return $databaseEntry->fetch();
    }
    
    /**
    * Vrátí jeden konkrétní článek spárovaný s informacemi o autorovi na základě jedné konkrétní recenze.
    * Kompletní informace o recenzi.
    * @param review_id ID recenze
    * @return recenze
    */
    public function fetchWholeReview($review_id){        
        $q = "SELECT authors.login AS author_name, sigutp_articles.name, sigutp_articles.authors, sigutp_articles.time, sigutp_reviews.state, sigutp_reviews.merit, sigutp_reviews.accuracy, sigutp_reviews.language, sigutp_reviews.comment
        FROM sigutp_articles, sigutp_reviews, sigutp_uzivatele AS authors WHERE sigutp_articles.id = sigutp_reviews.article_id AND sigutp_articles.user_id = authors.iduzivatel AND sigutp_reviews.review_id = $review_id;";
        
        $databaseEntry = $this->executeQuery($q);
        return $databaseEntry->fetch();
    }
    
    /**
    * Nahraje recenzi do databáze.    
    * @param review_id ID recenze
    * @param merit originalita článku
    * @param accuracy správnost článku
    * @param language jazyková správnost článku
    * @param comment komentář k článku
    */
    public function submitReview($review_id, $merit, $accuracy, $language, $comment){
        $q = "UPDATE sigutp_reviews SET state='finished', merit=$merit, accuracy=$accuracy, language=$language WHERE review_id=$review_id;";    
        $this->executeQuery($q);        
    }
    
    /**
    * Spočítá, kolik hotových recenzí je na jeden konkrétní článek.
    * @param article_id ID článku
    * @return počet recenzí
    */
    public function countReviews($article_id){
        $q = "SELECT COUNT(review_id) AS sum FROM sigutp_reviews WHERE article_id = $article_id AND state='finished';";
        $numberOfReviews = $this->executeQuery($q); 
        $numberOfReviews = $numberOfReviews->fetch();
        $numberOfReviews = $numberOfReviews['sum'];
        return $numberOfReviews;
    }
    
    /**
    * Přijme článek a připraví ho k publikaci.
    * @param article_id ID článku
    */
    public function acceptArticle($article_id){
        $q = "UPDATE sigutp_articles SET article_state='accepted' WHERE id=$article_id;";
        $this->executeQuery($q); 
    }
    
    /**
    * Odmítne článek.
    * @param article_id ID článku
    */
    public function refuseArticle($article_id){
        $q = "UPDATE sigutp_articles SET article_state='refused' WHERE id=$article_id;";
        $this->executeQuery($q); 
    }
    
    /**
    * Vrátí průměrné hodnocení článku ve všech třech kategoriích (originalita, faktická správnost, jazyková stránka).
    * @param article_id ID článku
    * @return průměrné hodnocení
    */
    public function averageRating($article_id){
        $q = "SELECT AVG(merit + accuracy + language)/3.0 AS average FROM (SELECT merit, accuracy, language FROM sigutp_reviews WHERE article_id='$article_id') AS reviews;";
        $averageRating = $this->executeQuery($q); 
        $averageRating = $averageRating->fetch();
        $averageRating = $averageRating['average'];
        return $averageRating;
    }
}



?>
