<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * core/password.php v.1.2.0. 13/08/2020
*/

//Sql::setDebugMode(1);

/* variabili ambiente */
$App->codeVersion = ' 1.2.0.';
$App->pageTitle = ucfirst($_lang['password']);
$App->pageSubTitle = preg_replace('/%ITEM%/', $_lang['password'], $_lang['modifica la %ITEM%']);
//$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.preg_replace('/%ITEM%/', $_lang['password'], $_lang['modifica %ITEM%']).'</li>';
$App->templateApp = Core::$request->action.'.html';
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);
$App->coreModule = true;

switch(Core::$request->method) {
	case 'update':
		if ($_POST) {			
			$password = (isset($_POST['password']) && $_POST['password'] != "") ? SanitizeStrings::stripMagic($_POST['password']) : '';
			$passwordCK = (isset($_POST['passwordCK']) && $_POST['passwordCK'] != "") ? SanitizeStrings::stripMagic($_POST['passwordCK']) : '';
			if ($password != '') {
				if ($password === $passwordCK) {
					$password = password_hash($password, PASSWORD_DEFAULT);
				} else {
					Core::$resultOp->error = 1;
					Core::$resultOp->message = $_lang['Le due password non corrispondono!'];
				}				
			} else {
				Core::$resultOp->error = 1;
				Core::$resultOp->message = $_lang['Devi inserire la password!'];			
			}
				
			if (Core::$resultOp->error == 0) {	
				/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
				Sql::initQuery(DB_TABLE_PREFIX.'users',array('password'),array($password,$App->id),"id = ?");	

				// commentare nella demo phprojekt.altervista

				Sql::updateRecord();


				if(Core::$resultOp->error == 0) {
					Core::$resultOp->message = $_lang['Password modificata correttamente! Sarà effettiva al prossimo login.'];
				}	
			$App->id	 = $_POST['id'];					         	
			}			
		} else {
			Core::$resultOp->error = 1;
			Core::$resultOp->message = $_lang['Devi inserire tutti i campi richiesti!'];
		}
	
	default:
		if ($App->id > 0) {	
			/* recupera i dati memorizzati */
			$App->item = new stdClass;	
			/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
			Sql::initQuery(Sql::getTablePrefix().'users',array('username','password'),array($App->id),"id = ?");	
			$App->item = Sql::getRecord();
			$App->defaultJavascript = "messages['Le due password non corrispondono!'] = '".addslashes($_lang['Le due password non corrispondono!'])."'";
			} else {
				ToolsStrings::redirect(URL_SITE_ADMIN."home");
				die();						
			}
	break;	
	}
	
$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplicationsCore.'/templates/'.$App->templateUser.'/js/password.js" type="text/javascript"></script>';
?>