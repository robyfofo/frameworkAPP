<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * warehouse/index.php v.1.3.0. 11/09/2020
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

$moduleRedirect = substr(Core::$request->method,-4,4);

switch($moduleRedirect) {
	/*
	case 'Conf':
		$App->sessionName = 'config';
		$Module = new Module($App->params->tables['conf']);
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		include_once(PATH.$App->pathApplications.Core::$request->action."/configuration.php");
	break;
*/
	case 'Proa':
		$App->sessionName = $App->sessionName.'-products-attribute';
		if (!isset($_SESSION[$App->sessionName]['products_attribute_types_id'])) $_SESSION[$App->sessionName]['products_attribute_types_id'] = 0;
		if (!isset($_SESSION[$App->sessionName]['products_id'])) $_SESSION[$App->sessionName]['products_id'] = 0;

		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->sessionName,$App->params->tables['proa']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/product-attributes.php");
	break;

	case 'Cate':
		$App->sessionName = $App->sessionName.'-categories';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module($App->sessionName,$App->params->tables['cate']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/categories.php");
	break;
	
	case 'Prod':
	default:
		$Module = new Module($App->sessionName,$App->params->tables['prod']);
		if (!isset($_SESSION[$App->sessionName]['categories_id'])) $_SESSION[$App->sessionName]['categories_id'] = 0;
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		include_once(PATH.$App->pathApplications.Core::$request->action."/products.php");
	break;
}
?>
