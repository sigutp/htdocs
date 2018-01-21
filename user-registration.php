<?php 
    // nacteni hlavicky stranky
    include("zaklad.php");
    head("Registrace nového uživatele");
?>


<?php 
    // načtení souboru s funkcemi
    //include("database.class.php");
    //$PDOObj = new Database();
?>

<?php
   // zpracovani odeslanych formularu
    if(isset($_POST['potvrzeni'])){ // nova registrace
        if($_POST["heslo"]==$_POST["heslo2"]){
            if($PDOObj->allUserInfo($_POST["login"])!=null){ // tento uzivatel uz existuje
                echo "<b>Tento login už existuje. Zvolte si prosím jiný.</b><br><br>";
            } else {
                $PDOObj->addNewUser($_POST["jmeno"], $_POST["login"], $_POST["heslo"], $_POST["email"], $_POST["pravo"]);    
                $PDOObj->userLogin($_POST["login"],$_POST["heslo"]);                
            }
        } else {
            echo "<b>Hesla nejsou stejná!</b><br><br>";
        }
        
    }
    
   // je uzivatel aktualne prihlasen
    if(!$PDOObj->isUserLogged()){ // neni prihlasen
   ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
        <b>Registrační formulář</b>
        <form action="" method="POST" oninput="x.value=(pas1.value==pas2.value)?'OK':'Nestejná hesla'">
            <table>
                <tr><td>Login:</td><td><input type="text" name="login" value="<?php echo @$_POST["login"]; ?>" required></td></tr>
                <tr><td>Heslo 1:</td><td><input type="password" name="heslo" id="pas1" required></td></tr>
                <tr><td>Heslo 2:</td><td><input type="password" name="heslo2" id="pas2" required></td></tr>
                <tr><td>Ověření hesla:</td><td><output name="x" for="pas1 pas2"></output></td></tr>
                <tr><td>Jméno:</td><td><input type="text" name="jmeno" value="<?php echo @$_POST["jmeno"]; ?>" required></td></tr>
                <tr><td>E-mail:</td><td><input type="email" name="email" value="<?php echo @$_POST["email"]; ?>" required></td></tr>
                <tr><td>Právo:</td>
                    <td><?php echo createSelectBox($PDOObj->allRights(),null); ?></td>
                </tr>
            </table>
            
            <input type="submit" name="potvrzeni" value="Registrovat">
        </form>

<?php
   ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else { // je prihlasen
   ///////////// PRO PRIHLASENE UZIVATELE ///////////////                
?>
        <b>Přihlášený uživatel se nemůže znovu registrovat.</b>
        
<?php
   ///////////// KONEC: PRO PRIHLASENE UZIVATELE ///////////////                
    }
?>

<?php
    // paticka
    foot();
?>
             