<?php 
// nacteni hlavicky stranky
include("zaklad.php");
head("Správa recenzí");
?>

<?php

if(!$PDOObj->isUserLogged() && ($_SESSION["user"]["idprava"] == 1)){ // neni prihlasen
    ///////////// PRO NEPRIHLASENE UZIVATELE ///////////////        
?>
    <b>Tato stránka je dostupná pouze přihlášeným uživatelům.</b>
    <?php
    ///////////// KONEC: PRO NEPRIHLASENE UZIVATELE ///////////////
} else {
    
$newArticles = $PDOObj->getAllArticlesAuthor();
$reviewedArticles = $PDOObj->getAllArticlesWithReviews();   
?> 

<div>
    <h2>Nové články</h2>
    <table>
    <tr>
        <th>Uživatel</th>
        <th>Název</th>
        <th>Autoři</th> 
        <th>Čas</th>
        <th>Recenzent</th>
    </tr>
        
           
<?php
while($newRow = $newArticles->fetch()){
    $reviewers = $PDOObj->getReviewers();    
 
    echo 
        "<tr>" .
            "<td>" . $newRow['login'] . "</td>" .
            "<td>" . $newRow['name'] . "</td>" .
            "<td>" . $newRow['authors'] . "</td>" . 
            "<td>" . $newRow['time'] . "</td>" .  
            "<form method='POST'>
                <td><select name='assignedReviewer'>";
                    
                    while($reviewer = $reviewers->fetch()){
                        $reviewer_id = $reviewer[iduzivatel];
                        $reviewer_login = $reviewer[login];
                        echo "<option value='$reviewer_id'>$reviewer_login</option>";
                    }
    
    echo        "</select></td>" . 
                "<input type='hidden' name='article_id' value=$newRow[id] />" .
                "<td><input type='submit' name='assignReview' value='Přiřadit' /></td>" .  
            "</form>" .            
        "</tr>";
}
?> 
        
    </table>
</div>

<div>
    <h2>Přidělené recenze</h2>
    <table>
        <tr>
            <th>Uživatel</th>
            <th>Název</th>
            <th>Autoři</th> 
            <th>Čas</th>
            <th>Recenzent</th>
            <th>Stav</th>
            <th>Hodnocení</th>
        </tr>
  
<?php
while($assignedRow = $reviewedArticles->fetch()){
    echo 
        "<tr>" .
            "<td>" . $assignedRow['author_name'] . "</td>" .
            "<td>" . $assignedRow['name'] . "</td>" .
            "<td>" . $assignedRow['authors'] . "</td>" . 
            "<td>" . $assignedRow['time'] . "</td>" .
            "<td>" . $assignedRow['reviewer_name'] . "</td>" .
            "<td>" . $assignedRow['state'] . "</td>" .
            "<td>" . (($assignedRow['merit'] + $assignedRow['accuracy'] + $assignedRow['language'])/3) . "</td>" .
            "<td>
                <form action='index.php?page=6' method='POST'>
                    <input type='hidden' name='article_id' value=$assignedRow[id] />
                    <input type='submit' name='editButton' value='Upravit' article_id='$assignedRow[id]'>                    
                </form>               
            </td>" .
            "<td>
                <form action='' method='POST'>
                    <input type='hidden' name='article_id' value=$assignedRow[id] />
                    <input type='submit' name='deleteButton' value='Odstranit'>                    
                </form>
            </td>" .
        "</tr>";
}
?>
    
    </table>
</div>






<?php
    
    if (isset($_POST['assignReview'])){
        $set_reviewer_id = $_POST['assignedReviewer'];
        $set_article_id = $_POST['article_id']; 
        
        $PDOObj->assignReview($set_article_id, $set_reviewer_id);        
    }   
    
}
?>