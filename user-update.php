<?php 
    // nacteni hlavicky stranky
    include("zaklad.php");
    head("Úprava osobních údajů uživatele");
?>

<?php 
    // načtení souboru s funkcemi
    //include("database.class.php");
    //$PDOObj = new Database();
?>

<?php
   // zpracovani odeslanych formularu
    if(isset($_POST['potvrzeni'])){ // mam vstup
        if($PDOObj->isPasswordCorrect($_SESSION["user"]["login"], $_POST["heslo-puvodni"])){ // odpovida aktualni heslo heslu v DB?
            if($_POST["heslo"]==$_POST["heslo2"]){ // odpovidaji si odeslana hesla
                //print_r($_POST);
                if($_POST["heslo"]==""){ // pokud neni zadano heslo, tak zustava puvodni
                    $heslo = $_SESSION["user"]["heslo"]; // puvodni
                } else { 
                    $heslo = $_POST["heslo"]; // nove
                }
                $PDOObj->updateUserInfo($_SESSION["user"]["iduzivatel"], $_POST["jmeno"], $heslo, $_POST["email"], $_POST["pravo"]);                                                
                // mohlo se zmenit heslo, tak radeji znovu prihlasim
                $PDOObj->userLogin($_SESSION["user"]["login"],$heslo);    
                echo "<b>Osobní údaje byly změněny.</b><br><br>";
            } else {
                echo "<b>Vámi zadaná hesla nejsou stejná!</b><br><br>";
            }            
        } else {
            echo "<b>Vámi zadané současné heslo není správné!</b><br><br>";
        }
    }
        
    if(!$PDOObj->isUserLogged()){ // neni prihlasen
   ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
        <b>Osobní údaje mohou měnit pouze přihlášení uživatelé.</b>

<?php
   ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else { // je prihlasen
   ///////////// PRO PRIHLASENE UZIVATELE ///////////////                
?>
        <b>Osobní údaje</b>
        <form action="" method="POST" oninput="x.value=(pas1.value==pas2.value)?'OK':'Nestejná hesla'">
            <table>
                <tr><td>Současné heslo:</td><td><input type="password" name="heslo-puvodni" required></td></tr>
                <tr><td>Login:</td><td><?php echo $_SESSION["user"]["login"]; ?></td></tr>
                <tr><td>Heslo 1:</td><td><input type="password" name="heslo" id="pas1"></td></tr>
                <tr><td>Heslo 2:</td><td><input type="password" name="heslo2" id="pas2"></td></tr>
                <tr><td>Ověření hesla:</td><td><output name="x" for="pas1 pas2"></output></td></tr>
                <tr><td>Jméno:</td><td><input type="text" name="jmeno" value="<?php echo $_SESSION["user"]["jmeno"]; ?>" required></td></tr>
                <tr><td>E-mail:</td><td><input type="email" name="email" value="<?php echo $_SESSION["user"]["email"]; ?>" required></td></tr>
                <tr><td>Právo:</td>
                    <td><?php echo createSelectBox($PDOObj->allRights(),$_SESSION["user"]["idprava"]); ?></td>
                </tr>
            </table>
            
            <input type="submit" name="potvrzeni" value="Upravit osobní údaje">
        </form>
        
<?php
   ///////////// KONEC: PRO PRIHLASENE UZIVATELE ///////////////                
    }
?>

<?php
    // paticka
    foot();
?>
             