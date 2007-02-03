<?php
//Configuration settings
$config = array(

    //connection information
        "host"                      => 'localhost', //the hostname of your database server
        "db"                        => '', //the name of your database
        "prefix"					=> 'gtd_', // the GTD table prefix for your installation
        "user"                      => '', //username for database access
        "pass"                      => '', //database password
    //database information
        "dbtype"                    => 'mysql',  //database type: currently only mysql is valid.  DO NOT CHANGE!

    //user preferences : MOVE TO DATABASE
        "title"                     => 'GTD-PHP', // site name (appears at the top of each page)
		"datemask"                  => 'Y-m-d D', // date format - required
        "debug"                     => 'false',  // false | true
        "theme"                     => 'default', //default | menu_sidebar
        "contextsummary"            => 'all',  //all | nextaction (Show all actions on context report, or nextactions only?)
        "nextaction"                => 'multiple', //single | multiple (Allow single or multiple nextactions per project)
        		"afterCreate"				=> array (  // parent | item | list | another - default view after creating an item
        			'i'		=>	'another', // inbox preference
        			'a'		=>	'parent', // action preference
        			'w'		=>	'parent', // waiting-on preference
        			'r'		=>	'parent', // reference preference
        			'p'		=>	'list', // project preference
        			'm'		=>	'item', // value preference
        			'v'		=>	'item', // vision preference
        			'o'		=>	'item', // role preference
        			'g'		=>	'list' // goal preference
        		), 
        "title_suffix"				=> false // true | false - add filename to title tag
        );


//Default sort order for each query (can be easily overridden within each page...)
//Once all built, can be either (a) simplified for user-editing, (b) create an options page that alters the config file, or (c) placed in the database and options page employed [best option?]
//need to alter once sqlabstraction is done to turn $sort into a simple variable string in mysql.inc.php, and pass the correct sort via the php code-- default is in config file/database/admin page (defined by report --or sort order--, not query), and can be modified on the page as needed.
//simplify all options down to a few...

$sort = array(
    "projectssummary"       => "`" . $config['prefix'] . "projects`.`name` ASC",
    "spacecontextselectbox" => "`" . $config['prefix'] . "context`.`name` ASC",
    "categoryselectbox"     => "`" . $config['prefix'] . "categories`.`category` ASC",
    "checklistselectbox"    => "`" . $config['prefix'] . "checklist`.`title` ASC",
    "listselectbox"         => "`" . $config['prefix'] . "list`.`title` ASC",
    "parentselectbox"       => "`" . $config['prefix'] . "items`.`title` ASC",
    "timecontextselectbox"  => "`" . $config['prefix'] . "timeitems`.`timeframe` DESC",
    "getprojects"           => "`" . $config['prefix'] . "categories`.`category`, `" . $config['prefix'] . "projectattributes`.`deadline`, `" . $config['prefix'] . "projects`.`name` ASC",
    "getlistitems"          => "`" . $config['prefix'] . "listItems`.`item` ASC",
    "getcompleteditems"     => "`" . $config['prefix'] . "itemstatus`.`dateCompleted` DESC, `" . $config['prefix'] . "projects`.`name`, `" . $config['prefix'] . "items`.`title` ASC",
    "getitemsandparent"     => "ptitle ASC, pcatname ASC, type ASC, deadline ASC, title ASC, dateCreated DESC",
    "getorphaneditems"      => "`" . $config['prefix'] . "itemattributes`.`type` ASC, `" . $config['prefix'] . "items`.`title` ASC",
    "selectchecklist"       => "`" . $config['prefix'] . "checklist`.`title` ASC",
    "getchecklists"         => "`" . $config['prefix'] . "categories`.`category` ASC",
    "getlists"              => "`" . $config['prefix'] . "categories`.`category` ASC",
    "getchecklistitems"     => "`" . $config['prefix'] . "checklistItems`.`checked` DESC, `" . $config['prefix'] . "checklistItems`.`item` ASC",
    "getchildren"           => "`" . $config['prefix'] . "itemattributes`.`type` ASC",
    "getitems"              => "`" . $config['prefix'] . "categories`.`category`, `" . $config['prefix'] . "items`.`title` ASC ",
    "getnotes"              => "`" . $config['prefix'] . "tickler`.`date` DESC ",
    );

// Access keys defined.  Note IE only allows 26 access keys (a-z).
$acckey = array(
	"about.php"								=> "", // License
	"achivements.php"						=> "", // Achievements
	"credits.php"							=> "", // Credits
	"donate.php"							=> "", // Donate
	"item.php?type=a"						=> "", // add Action
	"item.php?type=a&nextonly=true"			=> "", // add Next Action
	"item.php?type=g"						=> "", // add Goal
	"item.php?type=i"						=> "i", // add Inbox item
	"item.php?type=m"						=> "", // add Value
	"item.php?type=o"						=> "", // add Role
	"item.php?type=p"						=> "p", // add Project
	"item.php?type=p&someday=true"			=> "", // add Someday/Maybe
	"item.php?type=r"						=> "", // add Reference
	"item.php?type=v"						=> "", // add Vision
	"item.php?type=w"						=> "", // add Waiting On
	"leadership.php"						=> "", // Leadership
	"listChecklist.php"						=> "c", // Checklists
	"listItems.php?type=a"					=> "a", // Actions
	"listItems.php?type=a&nextonly=true"	=> "n", // Next Actions
	"listItems.php?type=a&tickler=true"		=> "", // Tickler File
	"listItems.php?type=g"					=> "", // Goals
	"listItems.php?type=i"					=> "", // Inbox
	"listItems.php?type=m"					=> "", // Values
	"listItems.php?type=o"					=> "", // Roles
	"listItems.php?type=p"					=> "v", // Projects
	"listItems.php?type=p&someday=true"		=> "m", // Someday/Maybes
	"listItems.php?type=r"					=> "", // References
	"listItems.php?type=v"					=> "", // Visions
	"listItems.php?type=w"					=> "w", // Waiting On
	"listList.php"							=> "l", // Lists
	"management.php"						=> "", // Management
	"newCategory.php"						=> "", // new Categories
	"newChecklist.php"						=> "", // new Checklist
	"newContext.php"						=> "", // new Space Contexts
	"newList.php"							=> "", // new List
	"newTimeContext.php"					=> "", // new Time Contexts
	"note.php"								=> "o", // Note
	"orphans.php"							=> "", // Orphaned Items
	"preferences.php"						=> "", // User Preferences
	"reportCategory.php"					=> "g", // Categories
	"reportContext.php"						=> "x", // Space Contexts
	"reportTimeContext.php"					=> "t", // Time Contexts
	"summaryAlone.php"						=> "s", // Summary
	"weekly.php"							=> "r" // Weekly Review
);

// Entirely optional: add custom items to the weekly review.  
// Uncomment to use, add more fields to the array for more lines.

/*
$custom_review = array(
	"Play the Lottery" => "Before Saturday's drawing!",
	"Pay Allowances" => "I want the kids to let me move in after I retire.",
	"Check my Oil" => "Check the oil in the car.",
	"Send Update" => "Send Weekly Update to Tom"
);
*/