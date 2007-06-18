<?php
//INCLUDES
include_once('header.php');
require_once('listItems.inc.php')
?>
<div id="filter">
    <form action="listItems.php" method="post">
        <div class="formrow">
            <label for='categoryId' class='left'>Category:</label>
            <select name="categoryId" id="categoryId" title="Filter items by parent category">
            <?php echo $cashtml; ?>
            </select>
            <input type="checkbox" id="notcategory" name="notcategory" title="Exclude category from list" value="true" <?php if ($filter['notcategory']=="true") echo 'checked="checked"'?> />
            <label for='notcategory' class='notfirst'>NOT</label>
            <label for='contextId' class='left'>Context:</label>
            <select name="contextId" id="contextId" title="Filter items by context">
            <?php echo $cshtml; ?>
            </select>
            <input type="checkbox" id="notspacecontext" name="notspacecontext" title="Exclude spatial context from list" value="true" <?php if ($filter['notspacecontext']=="true") echo 'checked="checked"'?> />
            <label for='notspacecontext' class='notfirst'>NOT</label>
            <label for='timeframeId' class='left'>Time:</label>
            <select name="timeframeId" id="timeframeId" title="Filter items by time context">
            <?php echo $tshtml; ?>
            </select>
            <input type="checkbox" name="nottimecontext" id="nottimecontext" title="Exclude time context from list" value="true" <?php if ($filter['nottimecontext']=="true") echo 'checked="checked"'?> />
            <label for='nottimecontext' class='notfirst'>NOT</label>
        </div>
        <div class="formrow">
            <label class='left'>Status:</label>
            <input type='radio' name='completed' id='pending' value='false' class="first" <?php if ($filter['completed']=="false") echo 'checked="checked"'?> title="Show incomplete <?php echo $typename ?>s" /><label for='pending' class='right' >Pending</label>
            <input type='radio' name='completed' id='completed' value='true' class="notfirst" <?php if ($filter['completed']=="true") echo 'checked="checked"'?> title="Show achievements" /><label for='completed' class='right'>Completed</label>
            <label class='left'>Tickler:</label>
            <input type='radio' name='tickler' id='notsuppressed' value='false' class="notfirst" <?php if ($filter['tickler']=="false") echo 'checked="checked"'?> title="Show active <?php echo $typename ?>s" /><label for='notsuppressed' class='right'>Active</label>
            <input type='radio' name='tickler' id='suppressed' value='true' class="notfirst" <?php if ($filter['tickler']=="true") echo 'checked="checked"'?> title="Show tickler <?php echo $typename ?>s" /><label for='suppressed' class='right'>Tickler</label>
            <label class='left'>Someday/Maybe:</label>
            <input type='radio' name='someday' id='notsomeday' value='false' class="notfirst" <?php if ($filter['someday']=="false") echo 'checked="checked"'?> title="Show active <?php echo $typename ?>s" /><label for='notsuppressed' class='right'>Active</label>
            <input type='radio' name='someday' id='someday' value='true' class="notfirst" <?php if ($filter['someday']=="true") echo 'checked="checked"'?> title="Show someday/maybe <?php echo $typename ?>s" /><label for='suppressed' class='right'>Someday</label>
        </div>
        <div class="formrow">
            <input type="checkbox" name="nextonly" id="nextonly" class="first" value="true" <?php if ($filter['nextonly']=="true") echo 'checked="checked"'?> title="Show only Next Actions" /><label for='nextonly' class='right'>Next Actions</label>
            <input type="checkbox" name="dueonly" id="dueonly" class="notfirst" value="true" <?php if ($filter['dueonly']=="true") echo 'checked="checked"'?> title="Show only <?php echo $typename ?>s with a due date" /><label for='dueonly' class='right'>Due</label>
            <input type="checkbox" name="repeatingonly" id="repeatingonly" class="notfirst" value="true" <?php if ($filter['repeatingonly']=="true") echo 'checked="checked"'?> title="Show only repeating <?php echo $typename ?>s" /><label for='repeatingonly' class='right'>Repeating</label>
            <select name="type" id="type" title="Filter items by type">
            <?php
                $types=array();
                $types=getTypes();
                foreach($types as $key=>$thistype) if ($key!=='s'){
                    echo "<option value='$key'"
                        ,($filter['type']==$key)?" selected='selected' ":''
                        ,">$thistype</option>\n";
                }
            ?>
            </select>
           <input type="checkbox" name="everything" id="everything" class="notfirst" value="true" <?php if ($filter['everything']=="true") echo 'checked="checked"'?> title="Show all <?php echo $typename ?>s, regardless of status or labels" /><label for='everything'>Show all&nbsp;<?php echo $typename ?>s</label>
        </div>
        <div class="formbuttons">
            <input type="submit" class="button" value="Filter" name="submit" title="Filter <?php echo $typename ?>s by selected criteria" />
        </div>
    </form>
</div>
<?php if (count($remindertable)) { ?>
    <h2><a href="note.php?&amp;type=<?php echo $values['type']; ?>&amp;referrer=t" title="Add new reminder">Reminder Notes</a></h2>
    <table class="datatable sortable" summary="table of reminders" id="remindertable">
        <thead>
            <tr>
                <th>Reminder</th>
                <th>Title</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($remindertable as $row) {
            echo "<tr>\n"
                ,"<td>",$row['date'],"</td>\n"
                ,"<td><a href='note.php?referrer=t&amp;noteId=",$row['id']
                ,'&amp;type=',$values['type']
                ,"' title='Edit {$row['title']}'>{$row['title']}</a></td>\n"
                ,"<td>{$row['note']}</td>\n"
                ,"</tr>";
        } ?>
        </tbody>
    </table>
<?php } ?>
<h2><?php echo $sectiontitle; ?></h2>
<?php if (count($maintable)) { ?>
    <form action="processItems.php" method="post">
    <table class="datatable sortable" summary="Table of actions" id="actiontable">
        <?php require('displayItems.inc.php'); ?>
    </table>
    <div>
    <?php
    if ($row['type']==='a')
        echo "<input type='hidden' name='wasNAonEntry' value='",implode(' ',$wasNAonEntry),"' />\n";
    if (($show['NA'] || $show['checkbox']) && count($maintable))
        echo "<input type='submit' class='button' value='Update marked {$typename}s' name='submit' />";
    ?>
    <input type="hidden" name="referrer" value="<?php echo $referrer; ?>" />
    <input type="hidden" name="type" value="<?php echo $values['type']; ?>" />
    <input type="hidden" name="multi" value="y" />
    <input type="hidden" name="action" value="complete" />
    </div>
    </form>
    <?php
}
if (isset($endmsg['header'])) echo "<h4>{$endmsg['header']}</h4>\n";
if (isset($endmsg['link'])) echo "<a href='{$endmsg['link']}'>{$endmsg['prompt']}</a>\n";
$_SESSION['afterCreate'.$values['type']]=$referrer;
include_once('footer.php');
?>
