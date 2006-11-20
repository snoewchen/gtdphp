<?php
//INCLUDES
include_once('header.php');

//RETRIEVE URL VARIABLES
$values=array();

$result = query("getorphaneditems",$config,$values,$options,$sort);

$tablehtml="";

foreach ($result as $row) {
    switch ($row['type']) {
        case "v" : $typename="visions";
        case "o" : $typename="roles";
        case "g" : $typename="goals";
        case "p" : $typename="projects";
        case "a" : $typename="actions";
        case "w" : $typename="waiting";
        case "r" : $typename="references";
        }
    
                                $tablehtml .= " <tr>\n";
                                $tablehtml .= '         <td><a href = "listItems.php?type='.$row['type'].'"title="List '.$typename.'">'.$typename."</a></td>\n";
                                $tablehtml .= '            <td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.stripslashes($row['title']).'</td>';
                                $tablehtml .= '         <td>'.nl2br(substr(stripslashes($row['description']),0,72))."</td>\n";
                                $tablehtml .= " </tr>\n";
    }

//PAGE DISPLAY CODE
        echo "<h2>Orphaned Items</h2>\n";


if ($tablehtml!="") {
        echo "<table class='datatable'>\n";
        echo "  <thead>\n";
        echo "          <td>Type</td>\n";
        echo "          <td>Title</td>\n";
        echo "          <td>Description</td>\n";
        echo "  </thead>\n";
        echo $tablehtml;
        echo "</table>\n";
} else {
        $message="Nothing was found.";
        nothingFound($message);
}

include_once('footer.php');
?>