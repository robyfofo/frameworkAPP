<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/company/index.php v.1.3.0. 22/09/2020
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb .= $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);
	
switch(substr(Core::$request->method,-4,4)) {
	default:
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','id_cat'=>0));
		$Module = new Module(Core::$request->action,$App->params->tables['item']);

		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>';

		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/css/ajax-bootstrap-select.min.css" rel="stylesheet">';
		
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/js/ajax-bootstrap-select.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/js/locale/ajax-bootstrap-select.'.$_lang['charset'].'.min.js"></script>';
		include_once(PATH.$App->pathApplications.Core::$request->action."/company.php");	
		break;
	}
?>