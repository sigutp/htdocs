<?php 
    // nacteni hlavicky stranky
    include("zaklad.php");
    head("Správa uživatelů");
?>


<?php 
    // načtení souboru s funkcemi
    //include("database.class.php");
    //$PDOObj = new Database();
?>

<?php
           
    if(!$PDOObj->isUserLogged()){ // neni prihlasen
   ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
        <b>Tato stránka je dostupná pouze přihlášeným uživatelům.</b>
<?php
   ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
    } else { // je prihlasen
        //print_r($_SESSION["user"]);
        if($_SESSION["user"]["idprava"]!=1){ // neni admin
   ///////////// PRO PRIHLASENE UZIVATELE - NENI ADMIN ///////////////                
?>
        <b>Správu uživatelů mohou provádět pouze uživatelé s právem Administrátor.</b>
<?php
   ///////////// KONEC: PRO PRIHLASENE UZIVATELE - NENI ADMIN ///////////////                
        } else { // je admin
   ///////////// PRO PRIHLASENE UZIVATELE - JE ADMIN ///////////////                            
            // zpracovani odeslanych formularu
            if(isset($_POST["potvrzeni"]) && isset($_POST["user-id"])){
                // zadost o smazani uzivatele
                if($_POST["user-id"]!=""){
                    $res = $PDOObj->deleteUser($_POST["user-id"]);
                    if($res){
                        echo "<b>Uživatel s ID:".$_POST["user-id"]." byl smazán.</b><br><br>";
                    } else {
                        echo "<b>Uživatele s ID:".$_POST["user-id"]." se nepodařilo smazat!</b><br><br>";
                    }
                } else {
                    echo "<b>Neznámé ID uživatele. Mazání nebylo provedeno!</b><br><br>";
                }
            }

?>
        <b>Seznam uživatelů</b>
        <table border="1">
            <tr><th>ID</th><th>Login</th><th>Jméno</th><th>E-mail</th><th>Právo</th><th>Akce</th></tr>
            <?php  
                $users = $PDOObj->allUsersInfo(); // vsichni uzivatele
                foreach($users as $u){
                    if($u["iduzivatel"]!=$_SESSION["user"]["iduzivatel"]){ // aktualni uzivatele nevypisuju
                        echo "<tr><td>$u[iduzivatel]</td><td>$u[login]</td><td>$u[jmeno]</td><td>$u[email]</td><td>$u[nazev]</td>
                                <td>
                                    <form action='' method='POST'>
                                        <input type='hidden' name='user-id' value='$u[iduzivatel]'>
                                        <input type='submit' name='potvrzeni' value='Smazat'>
                                    </form>
                                </td>
                              </tr>";
                    }
                }
            ?>            
        </table>        
<?php

   ///////////// KONEC: PRO PRIHLASENE UZIVATELE - JE ADMIN ///////////////                
        }
    }
?>

<?php
    // paticka
    foot();
?>
             