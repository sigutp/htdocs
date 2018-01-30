<!-- DOMOVSKÁ STRÁNKA S PUBLIKOVANÝMI ČLÁNKY -->

<?php 
// nacteni hlavicky stranky
include("zaklad.php");
head("Publikované články");

$acceptedArticles = $PDOObj->getAcceptedArticles();

?>

<div class="parent-container">


    <?php
while($row = $acceptedArticles->fetch()){
    $authorName = $PDOObj->getUserLogin($row['user_id']);
    $fileName = $row['fileName'];
    $fileName = str_replace(" ", "%20", $fileName);
    
    echo "
        <div class='container'>
            <h3><b>$row[authors]:</b> $row[name]</h3>
            <div>
                $row[abstract]
            </div>
            <div class='downloadArticle'>
                <a target='_blank' href=articles/$authorName/$fileName>Stáhnout</a>
            </div>
        </div>    
    ";
}
?>

</div>

<?php
    foot();
?>
