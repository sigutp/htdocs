<!-- SEZNAM ČLÁNKŮ PŘIDĚLENÝCH RECENZENTOVI K HODNOCENÍ -->

<?php 
// nacteni hlavicky stranky
include("zaklad.php");
head("Moje recenze");
?>

<?php

if(!$PDOObj->isUserLogged() && ($_SESSION["user"]["idprava"] == 2)){ // neni prihlasen
    ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <b>Tato stránka je dostupná pouze přihlášeným recenzentům.</b>
    <?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
} else {

$workingReviews = $PDOObj->getWorkingReviews();
$finishedReviews = $PDOObj->getFinishedReviews();
    
?>    
        
    <h3>Nové recenze</h3>
    <div class="table-responsive">
    <table class="table">
    <tr>        
        <th>Název</th>
        <th>Autoři</th> 
        <th>Čas</th>
        <th>Akce</th>
    </tr>
        
           
<?php
while($workingRow = $workingReviews->fetch()){    
    echo 
        "<tr>" .
            "<td class='col-md-1'>" . $workingRow['name'] . "</td>" .
            "<td class='col-md-1'>" . $workingRow['authors'] . "</td>" . 
            "<td class='col-md-1'>" . $workingRow['time'] . "</td>" .  
            "<form action='index.php?page=9' method='POST'>" .                
                "<input type='hidden' name='article_id' value=$workingRow[id] />" .
                "<input type='hidden' name='review_id' value=$workingRow[review_id] />" .
                "<td class='col-md-1 buttonField'><input type='submit' name='evaluate' value='Ohodnotit' /></td>" .  
            "</form>" .            
        "</tr>";
}
?> 
        
    </table>
</div>

<h3>Ohodnocené články</h3>
<div class="table-responsive">    
    <table class="table">
        <tr>            
            <th>Název</th>
            <th>Autoři</th> 
            <th>Čas</th>            
            <th>Hodnocení</th>
            <th>Akce</th>
        </tr>
  
<?php
while($finishedRow = $finishedReviews->fetch()){
    
    
    echo 
        "<tr>" .            
            "<td class='col-md-1'>" . $finishedRow['name'] . "</td>" .
            "<td class='col-md-1'>" . $finishedRow['authors'] . "</td>" . 
            "<td class='col-md-1'>" . $finishedRow['time'] . "</td>" .   
            "<td class='col-md-1'>" . (($finishedRow['merit'] + $finishedRow['accuracy'] + $finishedRow['language'])/3) . "</td>" .
            "<td class='col-md-1 buttonField'>
                <form action='index.php?page=9' method='POST'>
                    <input type='hidden' name='article_id' value=$finishedRow[id] />
                    <input type='hidden' name='review_id' value=$finishedRow[review_id] />
                    <input type='submit' name='editButton' value='Upravit' />                    
                </form>               
            </td>" .            
        "</tr>";
}
?>
    
    </table>
</div>

<?php
}
?>

<?php
    foot();
?>