<?php
/**
 * Framework App HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * modules/index.php v.1.3.0. 18/09/2020
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".Config::$localStrings['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
//$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

switch(substr(Core::$request->method,-4,4)) {	
	default:
		$Module = new Module(Core::$request->action,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/modules.php");
	break;							
}	
?>
