<?php

//INCLUDES
include_once('header.php');

//page display options array--- can put defaults in preferences table/config/session and load into $show array as defaults...
$show=array();

//GET URL VARIABLES
$values = array();
$filter = array();

if (isset($_GET['type'])) $values['type']=$_GET["type"]{0};
else $values['type']="a";

if ($_GET['categoryId']>0) $values['categoryId']=(int) $_GET['categoryId'];
    else $values['categoryId']=(int) $_POST['categoryId'];
$filter['notcategory']=$_POST['notcategory'];

if ($_GET['contextId']>0) $values['contextId']=(int) $_GET['contextId'];
    else $values['contextId']=(int) $_POST['contextId'];
$filter['notspacecontext']=$_POST['notspacecontext'];

if ($_GET['timeId']>0) $values['timeframeId']=(int) $_GET['timeId'];
    else $values['timeframeId']=(int) $_POST['timeId'];
$filter['nottimecontext']=$_POST['nottimecontext'];

//suppressed (tickler file): true/false
if (isset($_GET['tickler'])) $filter['tickler']=$_GET['tickler'];
else $filter['tickler']=$_POST['tickler'];

//someday/maybe:true/empty
if (isset($_GET['someday'])) $filter['someday']=$_GET['someday'];
else $filter['someday']=$_POST['someday'];

//next actions only: true/empty 
if (isset($_GET['nextonly'])) $filter['nextonly']=$_GET['nextonly'];
else $filter['nextonly']=$_POST['nextonly'];

//status:pending/completed (empty)
if (isset($_GET['completed'])) $filter['completed']=$_GET['completed'];
else $filter['completed']=$_POST['completed'];

//has due date:true/empty
if (isset($_GET['dueonly'])) $filter['dueonly']=$_GET['dueonly'];
else $filter['dueonly']=$_POST['dueonly'];

//is repeating:true/empty
if (isset($_GET['repeatingonly'])) $filter['repeatingonly']=$_GET['repeatingonly'];
else $filter['repeatingonly']=$_POST['repeatingonly'];


//Check Session Variables
//If we have contextId from a new filter, change Session value
$contextId=$values['contextId'];
if ($contextId>=0) $_SESSION['contextId']=$contextId;
else $values['contextId']=$_SESSION['contextId'];

//If we have categoryId from a new filter, change Session value
$categoryId=$values['categoryId'];
if ($categoryId>=0) $_SESSION['categoryId']=$categoryId;
else $values['categoryId']=$_SESSION['categoryId'];


//SQL CODE

//create filters for selectboxes
if ($values['type']=="g") $values['timefilterquery'] = " WHERE ".sqlparts("timegoals",$config,$values);
else $values['timefilterquery'] = " WHERE ".sqlparts("timeitems",$config,$values);

//create filter selectboxes
$cashtml=categoryselectbox($config,$values,$options,$sort);
$cshtml=contextselectbox($config,$values,$options,$sort);
$tshtml=timecontextselectbox($config,$values,$options,$sort);

//select all nextactions for test
$result = query("getnextactions",$config,$values,$options,$sort);

$nextactions = array();
if ($result!="-1") {
    $i=0;
    foreach ($result as $row) {
        $nextactions[$i] = $row['nextaction'];
        $i++;
        }
    }

//Select notes
if ($filter['tickler']=="true") {
    $values['filterquery'] = "";
    $reminderresult = query("getnotes",$config,$values,$options,$sort);
    }

//Select items

//set default table column display options (kludge-- needs to be divided into multidimensional array for each table type and added to preferences table
$show['parent']=TRUE;
$show['title']=TRUE;
$show['description']=TRUE;
$show['desiredOutcome']=FALSE;
$show['isSomeday']=FALSE;
$show['suppress']=FALSE;
$show['suppressUntil']=FALSE;
$show['dateCreated']=FALSE;
$show['lastModified']=FALSE;
$show['category']=TRUE;
$show['context']=TRUE;
$show['timeframe']=TRUE;
$show['deadline']=TRUE;
$show['repeat']=TRUE;
$show['dateCompleted']=FALSE;
$show['checkbox']=TRUE;


//determine item and parent labels, set a few defaults
    switch ($values['type']) {
        case "m" : $typename="Values"; $parentname=""; $values['ptype']=""; $show['parent']=FALSE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; break;
        case "v" : $typename="Visions"; $parentname="Value"; $values['ptype']="m"; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; break;
        case "g" : $typename="Goals"; $parentname="Vision"; $values['ptype']="v"; $show['desiredOutcome']=TRUE; $show['context']=FALSE; break;
        case "o" : $typename="Roles"; $parentname="Goal"; $values['ptype']="g"; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; break;
        case "p" : $typename="Projects"; $parentname="Role"; $values['ptype']="o"; $show['context']=FALSE; $show['timeframe']=FALSE; break;
        case "a" : $typename="Actions"; $parentname="Project"; $values['ptype']="p"; $show['category']=FALSE; break;
        case "w" : $typename="Waiting On"; $parentname="Project"; $values['ptype']="p"; break;
        case "r" : $typename="References"; $parentname="Project"; $values['ptype']="p"; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; break;
        case "i" : $typename="Inbox Items"; $parentname=""; $values['ptype']=""; $show['parent']=FALSE; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $show['deadline']=FALSE; $show['dateCreated']=TRUE; $show['repeat']=FALSE; break;
        default  : $typename="Items"; $parentname=""; $values['ptype']="";
        }


if ($filter['someday']=="true") {
    $show['dateCreated']=TRUE;
    $show['context']=FALSE;
    $show['repeat']=FALSE;
    $show['deadline']=FALSE;
    $show['timeframe']=FALSE;
    }

if ($filter['tickler']=="true") $show['suppressUntil']=TRUE;

if ($filter['dueonly']=="true") $show['deadline']=TRUE;

if ($filter['repeatingonly']=="true") {
    $show['deadline']=TRUE;
    $show['repeat']=TRUE;
    }

if ($filter['completed']=="completed") {
    $show['suppress']=FALSE;
    $show['suppressUntil']=FALSE;
    $show['dateCreated']=TRUE;
    $show['deadline']=FALSE;
    $show['repeat']=FALSE;
    $show['dateCompleted']=TRUE;
    $show['checkbox']=FALSE;
}

//set query fragments based on filters
$values['childfilterquery'] = "";
$values['parentfilterquery'] = "";
$values['filterquery'] = "";

//type filter
$values['childfilterquery'] = " WHERE ".sqlparts("typefilter",$config,$values);

//filter box filters
if ($values['categoryId'] != NULL && $filter['notcategory']!="true") $values['filterquery'] .= " AND ".sqlparts("categoryfilter-parent",$config,$values);
if ($values['categoryId'] != NULL && $filter['notcategory']=="true") $values['filterquery'] .= " AND ".sqlparts("notcategoryfilter-parent",$config,$values);

if ($values['contextId'] != NULL && $filter['notspacecontext']!="true") $values['childfilterquery'] .= " AND ".sqlparts("contextfilter",$config,$values);
if ($values['contextId'] != NULL && $filter['notspacecontext']=="true") $values['childfilterquery'] .= " AND ".sqlparts("notcontextfilter",$config,$values);

if ($values['timeframeId'] != NULL && $filter['nottimecontext']!="true") $values['childfilterquery'] .= " AND ".sqlparts("timeframefilter",$config,$values);
if ($values['timeframeId'] != NULL && $filter['nottimecontext']=="true") $values['childfilterquery'] .= " AND ".sqlparts("nottimeframefilter",$config,$values);

if ($filter['completed']=="completed") $values['childfilterquery'] .= " AND ".sqlparts("completeditems",$config,$values);
else $values['childfilterquery'] .= " AND " .sqlparts("pendingitems",$config,$values);

//problem with project somedays vs actions...want an OR, but across subqueries;
if ($filter['someday']=="true") {
    $values['isSomeday']="y";
    $values['filterquery'] .= " WHERE " .sqlparts("issomeday-parent",$config,$values);
    }

else {
    $values['isSomeday']="n";
    $values['childfilterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
    $values['filterquery'] .= " WHERE " .sqlparts("issomeday-parent",$config,$values);
    }

//problem: need to get all items with suppressed parents(even if child is not marked suppressed), as well as all suppressed items
if ($filter['tickler']=="true") $values['childfilterquery'] .= " AND ".sqlparts("suppresseditems",$config,$values);

else {
    $values['childfilterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
    $values['filterquery'] .= " AND ".sqlparts("activeparents",$config,$values);
    }

if ($filter['repeatingonly']=="true") $values['childfilterquery'] .= " AND " .sqlparts("repeating",$config,$values);

if ($filter['dueonly']=="true") $values['childfilterquery'] .= " AND " .sqlparts("due",$config,$values);

/*
$filter['nextonly']
*/

//Get items for display
$result = query("getitemsandparent",$config,$values,$options,$sort);

//PAGE DISPLAY CODE
?>

<div id="filter">
    <form action="listItems.php?type=<?php echo $values['type']?>" method="post">
        <div class="formrow">
            <label for='categoryId' class='left'>Category:</label>
            <select name="categoryId" title="Filter items by parent category">
            <?php echo $cashtml ?>
            </select>
            <input type="checkbox" name="notcategory" title="Exclude category from list" value="true" <?php if ($filter['notcategory']=="true") echo 'CHECKED'?>>
            <label for='notcategory' class='notfirst'>NOT</label>
            <label for='contextId' class='left'>Context:</label>
            <select name="contextId" title="Filter items by context">
            <?php echo $cshtml ?>
            </select>
            <input type="checkbox" name="notspacecontext" title="Exclude spatial context from list" value="true" <?php if ($filter['notspacecontext']=="true") echo 'CHECKED'?>>
            <label for='notspacecontext' class='notfirst'>NOT</label>
            <label for='timeId' class='left'>Time:</label>
            <select name="timeId" title="Filter items by time context">
            <?php echo $tshtml ?>
            </select>
            <input type="checkbox" name="nottimecontext" title="Exclude time context from list" value="true" <?php if ($filter['nottimecontext']=="true") echo 'CHECKED'?>>
            <label for='nottimecontext' class='notfirst'>NOT</label>
        </div>
        <div class="formrow">
            <label class='left'>Status:</label>
            <input type='radio' name='completed' id='pending' value='pending' class="first" <?php if ($filter['completed']=="pending") echo 'CHECKED'?> title="Show incomplete <?php echo $typename ?>" /><label for='pending' class='right' >Pending</label>
            <input type='radio' name='completed' id='completed' value='completed' class="notfirst" <?php if ($filter['completed']=="completed") echo 'CHECKED'?> title="Show achivements" /><label for='completed' class='right'>Completed</label>
            <label class='left'>Tickler:</label>
            <input type='radio' name='tickler' id='notsuppressed' value='false' class="notfirst" <?php if ($filter['tickler']=="false") echo 'CHECKED'?> title="Show active <?php echo $typename ?>" /><label for='notsuppressed' class='right'>Active</label>
            <input type='radio' name='tickler' id='suppressed' value='true' class="notfirst" <?php if ($filter['tickler']=="true") echo 'CHECKED'?> title="Show tickler <?php echo $typename ?>" /><label for='suppressed' class='right'>Tickler</label>
            <label class='left'>Someday/Maybe:</label>
            <input type='radio' name='someday' id='notsomeday' value='false' class="notfirst" <?php if ($filter['someday']=="false") echo 'CHECKED'?> title="Show active <?php echo $typename ?>" /><label for='notsuppressed' class='right'>Active</label>
            <input type='radio' name='someday' id='someday' value='true' class="notfirst" <?php if ($filter['someday']=="true") echo 'CHECKED'?> title="Show someday/maybe <?php echo $typename ?>" /><label for='suppressed' class='right'>Someday</label>
        </div>
        <div class="formrow">
            <input type="checkbox" name="nextonly" id="nextonly" class="first" value="true" <?php if ($filter['nextonly']=="true") echo 'CHECKED'?> title="Show only Next Actions"><label for='nextonly' class='right'>Next Actions</label>
            <input type="checkbox" name="dueonly" id="dueonly" class="notfirst" value="true" <?php if ($filter['dueonly']=="true") echo 'CHECKED'?> title="Show only <?php echo $typename ?> with a due date" value="true"><label for='dueonly' class='right'>Due</label>
            <input type="checkbox" name="repeatingonly" id="repeatingonly" class="notfirst" value="true" <?php if ($filter['repeatingonly']=="true") echo 'CHECKED'?> title="Show only repeating <?php echo $typename ?>"><label for='repeatingonly' class='right'>Repeating</label>
            </div>
            <div class="formbuttons">
            <input type="submit" class="button" value="Filter" name="submit" title="Filter <?php echo $typename ?> by selected criteria">
        </div>
    </form>
</div>

<?php

//Tickler file header and notes section
if ($filter['tickler']=="true") {
    if ($reminderresult!="-1") {
            echo "<div class='notes'>\n";
            echo '<h2><a href="note.php?&type='.$values['type'].'&referrer=t" Title="Add new reminder">Reminder Notes</a></h2>';
            $tablehtml="";
            foreach ($reminderresult as $row) {
                    $tablehtml .= " <tr>\n";
                    $tablehtml .= "         <td>".$row['date']."</td>\n";
                    $tablehtml .= '         <td><a href = "note.php?noteId='.$row['ticklerId'].'&type='.$values['type'].'&referrer=t" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.htmlspecialchars(stripslashes($row['title']))."</td>\n";
                    $tablehtml .= '         <td>'.nl2br(htmlspecialchars(stripslashes($row['note'])))."</td>\n";
                    $tablehtml .= " </tr>\n";
            }

            echo "<table class='datatable'>\n";
            echo "  <thead>\n";
            echo "          <td>Reminder</td>\n";
            echo "          <td>Title</td>\n";
            echo "          <td>Note</td>\n";
            echo "  </thead>\n";
            echo $tablehtml;
            echo "</table>\n";
            echo "</div>\n";
        }
   }

        echo '<h2>';
        if ($filter['completed']=="completed") echo 'Completed&nbsp;';
        else echo '<a href="item.php?type='.$values['type'].'" title="Add new '.str_replace("s","",$typename).'">';
            if ($filter['repeatingonly']=="true") echo "Repeating&nbsp;";
            if ($filter['dueonly']=="true") echo "Due&nbsp;";
            if ($filter['someday']=="true") echo "Someday/Maybe&nbsp;";
            if ($filter['nextonly']=="true") echo "Next&nbsp;";
            echo $typename;
            if ($filter['tickler']=="true") echo ' in Tickler File';
            if ($filter['completed']!="true") echo "</a>";
            echo "</h2>\n";



	if ($result!="-1") {
                $tablehtml="";
                foreach ($result as $row) {
                    $showme="y";
                    //filter out all but nextactions if $filter['nextonly']==true
                    if (($filter['nextonly']=="true")  && !($key = array_search($row['itemId'],$nextactions))) $showme="n";
                    if($showme=="y") {
                        $tablehtml .= "	<tr>\n";

                        //parent title
                            if ($show['parent']!=FALSE) {
                                $tablehtml .= '		<td><a href = "itemReport.php?itemId='.$row['parentId'].'" title="Go to '.htmlspecialchars(stripslashes($row['ptitle'])).' '.$parentname.' report">';
                                if ($nonext=="true" && $filter['completed']!="completed") echo '<span class="noNextAction" title="No next action defined!">!</span>';
                                $tablehtml .= htmlspecialchars(stripslashes($row['ptitle']))."</a></td>\n";
                                }

                        //item title
                        if ($show['title']!=FALSE && ($row['type']=="a" || $row['type']=="r" || $row['type']=="w" || $row['type']=="i")) $tablehtml .= '         <td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">';

                        elseif ($show['title']!=FALSE) $tablehtml .= '         <td><a href = "itemReport.php?itemId='.$row['itemId'].'" title="Go to '.htmlspecialchars(stripslashes($row['title'])).' report">';

                        //if nextaction, add icon in front of action (* for now)
                        if ($key = array_search($row['itemId'],$nextactions) && ($show['title']!=FALSE)) $tablehtml .= '*&nbsp;';

                        if ($show['title']!=FALSE) $tablehtml .=htmlspecialchars(stripslashes($row['title']))."</td>\n";

                        //item description
                        if ($show['description']!=FALSE) $tablehtml .= '		<td>'.nl2br(substr(htmlspecialchars(stripslashes($row['description'])),0,72))."</td>\n";

                        //item desiredOutcome
                        if ($show['desiredOutcome']!=FALSE) $tablehtml .= '                <td>'.nl2br(substr(htmlspecialchars(stripslashes($row['desiredOutcome'])),0,72))."</td>\n";

                        //item category
                        if ($show['category']!=FALSE) $tablehtml .= '          <td><a href="reportCategory.php#'.$row['category'].'" title="Go to the  '.htmlspecialchars(stripslashes($row['category'])).' category">'.htmlspecialchars(stripslashes($row['category']))."</a></td>\n";

                        //item context name
                        if ($show['context']!=FALSE) $tablehtml .= '		<td><a href = "reportContext.php#'.$row['cname'].'" title="Go to the  '.htmlspecialchars(stripslashes($row['cname'])).' context report">'.htmlspecialchars(stripslashes($row['cname']))."</td>\n";
                        
                        //item timeframe name
                        if ($show['timeframe']!=FALSE) $tablehtml .= '         <td><a href = "reportTimeContext.php#'.$row['timeframe'].'" title="Go to '.htmlspecialchars(stripslashes($row['timeframe'])).' time context report">'.htmlspecialchars(stripslashes($row['timeframe']))."</td>\n";
                        
                        //item deadline
                        if ($show['deadline']!=FALSE) {
                            $tablehtml .= "		<td>";
                            if(($row['deadline']) == "0000-00-00" || $row['deadline'] ==NULL) $tablehtml .= "&nbsp;";
                            elseif(($row['deadline']) < date("Y-m-d")) $tablehtml .= '<font color="red"><strong title="Item overdue">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';  //highlight overdue actions
                            elseif(($row['deadline']) == date("Y-m-d")) $tablehtml .= '<font color="green"><strong title="Item due today">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>'; //highlight actions due today
                            else $tablehtml .= date("D M j, Y",strtotime($row['deadline']));
                            $tablehtml .= "</td>\n";
                            }

                        //item repeat
                        if ($show['repeat']!=FALSE) {
                            if ($row['repeat']=="0") $tablehtml .= "		<td></td>\n";
                            else $tablehtml .= "		<td>".$row['repeat']."</td>\n";
                            }

                        //tickler date
                        if ($show['suppressUntil']!=FALSE) {
                                    //Calculate reminder date as # suppress days prior to deadline
                                    if ($row['suppress']=="y") {
                                    $dm=(int)substr($row['deadline'],5,2);
                                    $dd=(int)substr($row['deadline'],8,2);
                                    $dy=(int)substr($row['deadline'],0,4);
                                    $remind=mktime(0,0,0,$dm,($dd-(int)$row['suppressUntil']),$dy);
                                    $reminddate=gmdate("Y-m-d", $remind);
                                    }
                                    else $reminddate="--";
                                    $tablehtml .= "         <td>".date("D M j, Y",strtotime($reminddate))."</td>\n";
                                    }
                                    
                        //item date Created
                        if ($show['dateCreated']!=FALSE) $tablehtml .= '              <td>'.nl2br(htmlspecialchars(stripslashes($row['dateCreated'])))."</td>\n";

                        //item last modified
                        if ($show['lastModified']!=FALSE) $tablehtml .= '              <td>'.nl2br(htmlspecialchars(stripslashes($row['lastModified'])))."</td>\n";

                        //item last modified
                        if ($show['dateCompleted']!=FALSE) $tablehtml .= '              <td>'.nl2br(htmlspecialchars(stripslashes($row['dateCompleted'])))."</td>\n";

                        //completion checkbox
                        if ($show['checkbox']!=FALSE) $tablehtml .= '		<td align="center"><input type="checkbox" align="center" title="Complete '.htmlspecialchars(stripslashes($row['title'])).'" name="completedNas[]" value="'.$row['itemId'].'" /></td>'."\n";
                        $tablehtml .= "	</tr>\n";
                        }
                    }

		if ($tablehtml!="") {
//                         if ($show['parent']!=FALSE) echo "<p>Click on ".$parentname." for individual report.</p>\n";
			echo '<form action="processItemUpdate.php" method="post">'."\n";
			echo "<table class='datatable'>\n";
			echo "	<thead>\n";
		        if ($show['parent']!=FALSE) echo "		<td>".$parentname."</td>\n";
			if ($show['title']!=FALSE) echo "		<td>".$typename."</td>\n";
			if ($show['description']!=FALSE) echo "		<td>Description</td>\n";
                        if ($show['desiredOutcome']!=FALSE) echo "         <td>Desired Outcome</td>\n";
                        if ($show['category']!=FALSE)echo "          <td>Category</td>\n";
                        if ($show['context']!=FALSE)echo "          <td>Space Context</td>\n";
			if ($show['timeframe']!=FALSE)echo "		<td>Time Context</td>\n";
			if ($show['deadline']!=FALSE)echo "		<td>Deadline</td>\n";
			if ($show['repeat']!=FALSE)echo "		<td>Repeat</td>\n";
                        if ($show['suppressUntil']!=FALSE) echo "            <td>Reminder Date</td>\n";
                        if ($show['dateCreated']!=FALSE)echo "               <td>Date Created</td>\n";
                        if ($show['lastModified']!=FALSE)echo "               <td>Last Modified</td>\n";
                        if ($show['dateCompleted']!=FALSE)echo "               <td>Date Completed</td>\n";
                        if ($show['checkbox']!=FALSE) echo "           <td>Completed</td>\n";
			echo "	</thead>\n";
			echo $tablehtml;
			echo "</table>\n";
			echo '<input type="hidden" name="type" value="'.$values['type'].'" />'."\n";
			echo '<input type="hidden" name="timeId" value="'.$values['timeframeId'].'" />'."\n";
                        echo '<input type="hidden" name="contextId" value="'.$values['contextId'].'" />'."\n";
                        echo '<input type="hidden" name="categoryId" value="'.$values['categoryId'].'" />'."\n";
			echo '<input type="hidden" name="referrer" value="i" />'."\n";
			echo '<input type="submit" class="button" value="Complete '.$typename.'" name="submit">'."\n";
			echo "</form>\n";
		}

        elseif($filter['completed']!="completed" && $values['type']!="t") {
                $message="You have no ".$typename." remaining.";
                $prompt="Would you like to create a new ".str_replace("s","",$typename)."?";
                $yeslink="item.php?type=".$values['type'];
                nothingFound($message,$prompt,$yeslink);
        }


        elseif($values['type']=="t") {
                $message="None";
                nothingFound($message);
        }

}

        elseif($filter['completed']!="completed" && $values['type']!="t") {
		$message="You have no ".$typename." remaining.";
		$prompt="Would you like to create a new ".str_replace("s","",$typename)."?";
		$yeslink="item.php?type=".$values['type'];
		nothingFound($message,$prompt,$yeslink);
	}


        elseif($values['type']=="t") {
                $message="None";
                nothingFound($message);
        }

	include_once('footer.php');
?>
