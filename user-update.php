<!-- ZMĚNA ÚDAJŮ STÁVAJÍCÍHO UŽIVATELE -->

<?php 
    // nacteni hlavicky stranky
    include("zaklad.php");
    head("Správa osobních údajů");
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
        <h3>Osobní údaje</h3>
        <form action="" method="POST" oninput="x.value=(pas1.value==pas2.value)?'OK':'Nestejná hesla'">
            <div class="table-responsive">
            <table class="table">
                <tr><td class="col-md-1">Současné heslo:</td><td class="col-md-1"><input type="password" name="heslo-puvodni" required></td></tr>
                <tr><td class="col-md-1">Login:</td><td class="col-md-1"><?php echo $_SESSION["user"]["login"]; ?></td></tr>
                <tr><td class="col-md-1">Heslo 1:</td><td class="col-md-1"><input type="password" name="heslo" id="pas1"></td></tr>
                <tr><td class="col-md-1">Heslo 2:</td><td class="col-md-1"><input type="password" name="heslo2" id="pas2"></td></tr>
                <tr><td class="col-md-1">Ověření hesla:</td><td class="col-md-1"><output name="x" for="pas1 pas2"></output></td></tr>
                <tr><td class="col-md-1">Jméno:</td><td class="col-md-1"><input type="text" name="jmeno" value="<?php echo $_SESSION["user"]["jmeno"]; ?>" required></td></tr>
                <tr><td class="col-md-1">E-mail:</td><td class="col-md-1"><input type="email" name="email" value="<?php echo $_SESSION["user"]["email"]; ?>" required></td></tr>
                <tr><td class="col-md-1">Právo:</td>
                    <td class="col-md-1"><?php 
                            if ($_SESSION["user"]["idprava"] == 1){
                                echo "Administrátor"; 
                            } else if ($_SESSION["user"]["idprava"] == 2){
                                echo "Recenzent"; 
                            } else if ($_SESSION["user"]["idprava"] == 3){
                                echo "Autor"; 
                            }
                            
                        ?></td>
                </tr>
            </table>
            </div>
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
             