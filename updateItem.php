<?php
//INCLUDES
include_once('header.php');

//FORM DATA COLLECTION AND PARSING
$values=array();
$values['itemId'] = (int) $_GET['itemId'];
$values['type']=$_POST['type']{0};
$values['title'] = mysql_real_escape_string($_POST['title']);
$values['description'] = mysql_real_escape_string($_POST['description']);
$values['desiredOutcome']=mysql_real_escape_string($_POST['DesiredOutcome']);
$values['categoryId']=(int) $_POST['categoryId'];
$values['contextId'] = (int) $_POST['contextId'];
$values['timeframeId'] = (int) $_POST['timeframeId'];
$parents = $_POST['parentId']; //array
$values['deadline'] = $_POST['deadline'];
$values['repeat'] = (int) $_POST['repeat'];
$values['suppress'] = $_POST['suppress']{0};
$values['suppressUntil'] = (int) $_POST['suppressUntil'];
$values['nextAction']=$_POST['nextAction']{0};
$values['dateCompleted'] = $_POST['dateCompleted'];
$values['delete'] = $_POST['delete']{0};
if ($_POST['isSomeday']{0}=='y') $values['isSomeday']='y';
else $values['isSomeday']='n';

if ($values['suppress']!="y") $values['suppress']="n";
if ($values['nextaction']!="y") $values['nextaction']="n";
if (!isset($values['title'])) die ("No title. Item NOT updated.");

//SQL CODE AREA
if($values['delete']=="y"){

    query("deleteitemstatus",$config,$values);
    query("deleteitemattributes",$config,$values);
    query("deleteitem",$config,$values);
    query("deletelookup",$config,$values);

    echo '<META HTTP-EQUIV="Refresh" CONTENT="10; url=itemReport.php?itemId='.$parents[0].'" />';
    if ($values['nextAction']=='y') query("deletenextaction",$config,$values);
    }

else {
    query("updateitemstatus",$config,$values);
    query("updateitemattributes",$config,$values);
    query("updateitem",$config,$values);
    query("deletelookup",$config,$values); //remove all parents before adding current ones
    foreach ($parents as $values['parentId']) $result = query("updateparent",$config,$values);

    if ($values['nextAction']=='y' && ($values['dateCompleted']==NULL || $values['dateCompleted']=="0000-00-00")) foreach ($parents as $values['parentId']) $result = query("updatenextaction",$config,$values);
    else $result = query("deletenextaction",$config,$values);


    echo '<META HTTP-EQUIV="Refresh" CONTENT="`0; url=itemReport.php?itemId='.$values['itemId'].'" />';
    }

include_once('footer.php');
?>
