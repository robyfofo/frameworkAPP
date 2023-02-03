<?php
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/core/password.php v.7.0.0. 04/02/2022
*/

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$changepasswordLog = new Logger('changepassword');
$changepasswordLog->pushHandler(new StreamHandler(PATH_SITE.'logs/changepassword.log', Logger::DEBUG));

/* variabili ambiente */
$App->codeVersion = ' 7.0.0.';
$App->pageTitle = ucfirst(Config::$localStrings['password']);
$App->pageSubTitle = preg_replace('/%ITEM%/', Config::$localStrings['password'], Config::$localStrings['modifica la %ITEM%']);
$App->breadcrumb = '<li class="active"><i class="icon-user"></i> '.preg_replace('/%ITEM%/', Config::$localStrings['password'], Config::$localStrings['modifica %ITEM%']).'</li>';
$App->templateApp = Core::$request->action.'.html';
if (isset($_POST['id'])) $App->id = intval($_POST['id']);
$App->coreModule = true;

$App->params = new stdClass();
$App->fieldsName = 'users';

$App->id = (isset($App->userLoggedData->id) && $App->userLoggedData->id > 0 ? $App->userLoggedData->id : 0);

switch(Core::$request->method) {
	case 'update':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); 	}
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		Permissions::checkCsrftoken();

		// prende i dati dell'utente
		Sql::initQuery(Config::$DatabaseTables['users']['name'],array('username','password'),array($App->id),"id = ?");	
		$user = Sql::getRecord();

		$password = (isset($_POST['password']) && $_POST['password'] != "") ? filter_var( $_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';
		$passwordCK = (isset($_POST['passwordCK']) && $_POST['passwordCK'] != "") ? filter_var( $_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';
		
		if ($password != '') {
			if ($password === $passwordCK) {
				$password = password_hash($password, PASSWORD_DEFAULT);
			} else {
				$_SESSION['message'] = '1|'.Core::$localStrings['Le due password non corrispondono!'];
				ToolsStrings::redirect(URL_SITE.Core::$request->action);
			}				
		} else {
			$_SESSION['message'] = '1|'.Core::$localStrings['Devi inserire la password!'];
			ToolsStrings::redirect(URL_SITE.Core::$request->action);		
		}

		Sql::initQuery(Config::$DatabaseTables['users']['name'],array('password'),array($password,$App->id),"id = ?");	
		Sql::updateRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$changepasswordLog->info('Utente '.$user->username.' ha cambiato la password');

		$_SESSION['message'] = '0|'.Core::$localStrings['Password modificata correttamente! Sarà effettiva al prossimo login.'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	break;
	
	default:
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		// recupera i dati memorizzati
		$App->item = new stdClass;	
		Sql::initQuery(Config::$DatabaseTables['users']['name'],array('username','password'),array($App->id),"id = ?");	
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		$App->defaultJavascript = "messages['Le due password non corrispondono!'] = '".addslashes(Config::$localStrings['Le due password non corrispondono!'])."'";		
	break;	
}

$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplicationsCore.'/templates/'.$App->templateUser.'/js/password.js" type="text/javascript"></script>';
?>