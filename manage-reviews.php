<!-- SPRÁVA RECENZÍ ADMINISTRÁTOREM -->

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
            <h3>Nové články</h3>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>Uživatel</th>
                        <th>Název</th>
                        <th>Autoři</th>
                        <th>Čas</th>
                        <th>Počet recenzí</th>
                        <th colspan=2>Recenzent</th>
                        <th>Akce</th>
                    </tr>


                    <?php
while($newRow = $newArticles->fetch()){
    if($newRow['article_state'] != 'fresh'){
        continue;
    }
    $reviewers = $PDOObj->getReviewers();
    $numberOfReviews = $PDOObj->countReviews($newRow['id']);
    
    echo 
        "<tr>" .
            "<td class='col-md-1'>" . $newRow['login'] . "</td>" .
            "<td class='col-md-1'>" . $newRow['name'] . "</td>" .
            "<td class='col-md-1'>" . $newRow['authors'] . "</td>" . 
            "<td class='col-md-2'>" . $newRow['time'] . "</td>" .
            "<td class='col-md-1'>$numberOfReviews</td>" .
            "<form method='POST'>
                <td class='col-md-1 buttonField'><select name='assignedReviewer'>";
                    
                    while($reviewer = $reviewers->fetch()){
                        $reviewer_id = $reviewer[iduzivatel];
                        $reviewer_login = $reviewer[login];
                        echo "<option value='$reviewer_id'>$reviewer_login</option>";
                    }
    
    echo        "</select></td>" . 
                "<input type='hidden' name='article_id' value=$newRow[id] />" .                
                "<td class='col-md-1 buttonField'><input type='submit' name='assignReview' value='Přiřadit' /></td>";
    if ($numberOfReviews >= 3){
        echo    "<td class='col-md-1 buttonField'><input type='submit' name='acceptArticle' value='Přijmout' /></td>" .
                "<td class='col-md-1 buttonField'><input type='submit' name='refuseArticle' value='Odmítnout' /></td>";
    } else {
        echo    "<td class='col-md-1 buttonField' colspan='2'>Alespoň 3 recenze</td>";
    }
    echo    "</form>" .            
        "</tr>";
}
?>

                </table>
            </div>
        </div>

        <div>
            <h3>Přidělené recenze</h3>
            <div class="table-responsive">
                <table class="table">
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
    if($assignedRow['article_state'] != 'fresh'){
        continue;
    }
    echo 
        "<tr>" .
            "<td class='col-md-1'>" . $assignedRow['author_name'] . "</td>" .
            "<td class='col-md-1'>" . $assignedRow['name'] . "</td>" .
            "<td class='col-md-1'>" . $assignedRow['authors'] . "</td>" . 
            "<td class='col-md-2'>" . $assignedRow['time'] . "</td>" .
            "<td class='col-md-1'>" . $assignedRow['reviewer_name'] . "</td>" .
            "<td class='col-md-1'>" . $assignedRow['state'] . "</td>" .
            "<td class='col-md-1'>" . round((($assignedRow['merit'] + $assignedRow['accuracy'] + $assignedRow['language'])/3), 2) . "</td>" .            
        "</tr>";
}
?>

                </table>
            </div>
        </div>






        <?php
    
    if (isset($_POST['assignReview'])){
        $set_reviewer_id = $_POST['assignedReviewer'];
        $set_article_id = $_POST['article_id']; 
        
        $PDOObj->assignReview($set_article_id, $set_reviewer_id);        
    }   
    
    if (isset($_POST['acceptArticle'])){
        $PDOObj->acceptArticle($_POST['article_id']);
    }
    
    if (isset($_POST['refuseArticle'])){
        $PDOObj->refuseArticle($_POST['article_id']);
    }
}
?>

            <?php
    foot();
?>
