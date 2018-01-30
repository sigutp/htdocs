<!-- ROZCESTNÍK -->

<?php
// soubor obsahujici zakladni nastaveni

global $db_server, $db_name, $db_user, $db_pass;
global $web_pagesExtension, $web_pages;

// databaze
    $db_server = "localhost";
    $db_name = "sigutp";
    $db_user = "root";
    $db_pass = "";
    

// stranky webu (ostatni nebudou dostupne)
    $web_pagesExtension = ".php";
    $web_pages[0] = "login";
    $web_pages[1] = "user-registration";
    $web_pages[2] = "user-update";
    $web_pages[3] = "user-management";
    $web_pages[4] = "new-article";
    $web_pages[5] = "articles";
    $web_pages[6] = "edit-article";
    $web_pages[7] = "manage-reviews";
    $web_pages[8] = "my-reviews";
    $web_pages[9] = "new-review";
    $web_pages[10] = "home";
?>
