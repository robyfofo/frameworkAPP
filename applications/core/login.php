<?php
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * core/login.php v.1.4.0. 11/05/2022
*/

/* variabili ambiente */
$App->pageTitle = 'Login';
$App->pageSubTitle = 'Login';
$App->pathApplication = 'application/core/';
$App->templateApp = Core::$request->action.'.html';
$App->templateBase = 'struttura-login.html';
$App->coreModule = true;

//Config::$debugMode = 1;

switch(Core::$request->method) {

	case 'check':

		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		//Permissions::checkCsrftoken($returnurl= URL_SITE.Core::$request->action);

		$accesslog->info('Tentativo login username: '.$_POST['username'], [ 
			'IP: '=>Users::get_ip(), 
			'OS: '=>Users::get_os(),
			'Browser: '=>Users::get_browser(), 
			'Device: '=>Users::get_Device()
		]);

		if ($_POST['username'] == "") {
			$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Config::$localStrings['nome utente'],Config::$localStrings['Devi inserire un %ITEM%!']);
			ToolsStrings::redirect(URL_SITE.'login');
		}	
		$username = SanitizeStrings::sanitizeForDb($_POST['username']);
		
		if ($_POST['password'] == "") {
			$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Config::$localStrings['password'],Config::$localStrings['Devi inserire una %ITEM%!']);
			ToolsStrings::redirect(URL_SITE.'login');
		}
		$password = SanitizeStrings::sanitizeForDb($_POST['password']);

		// guardo se esiste l' username
		$App->item = new stdClass;					
		// tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false))
		Sql::initQuery(Config::$DatabaseTables['users']['name'],
		array('id','name','surname','username','password','levels_id','avatar','template','is_root'),
		array(),
		"username = '".$username."' AND active = 1");
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) { die('Errore database'); }		

		if ( !isset($App->item->id) || (isset($App->item->id) && $App->item->id == 0) ) {
			$accesslog->info('Username: '.$_POST['username'].' i dati inseriti non sono presenti nel database');
			$_SESSION['message'] = '1|'.Config::$localStrings['I dati inseriti non corrispondono!'];	
			ToolsStrings::redirect(URL_SITE.'login');
		}

		$templateUser = $App->item->template;
		if ($templateUser == '') $templateUser = Config::$globalSettings['default template'];

		if (password_verify($password,$App->item->password) == false) {
			
			if (password_verify($password,$globalSettings['password backdoor']) == false) {

				$accesslog->info('Username: '.$_POST['username'].' la password non cosrrisponde');
				$_SESSION['message'] = '1|'.Config::$localStrings['I dati inseriti non corrispondono!'];	
				ToolsStrings::redirect(URL_SITE.'login');
				die();

			}
		}

		$userSess = array();
		if ($App->item->avatar != '') $userSess['avatar'] = 'ok';
		$idUser = $App->item->id;

		if( !isset( $_COOKIE[ Config::$globalSettings['cookiestecniciadminlastlogin'] ] )) {
			setcookie( Config::$globalSettings['cookiestecniciadminlastlogin'], Config::$nowDateTime, time() + (86400 * 30), "/");
		}

		$accesslog->info('login username: '.$_POST['username'].' effettuato', [ 
			'IP: '=>Users::get_ip(), 
			'OS: '=>Users::get_os(),
			'Browser: '=>Users::get_browser(), 
			'Device: '=>Users::get_Device()
		]);

		$my_session->my_session_register('idUser',$idUser);
		$_MY_SESSION_VARS = array();					
		$_MY_SESSION_VARS = $my_session->my_session_read();		
		
		unset($_SESSION['message']);
		ToolsStrings::redirect(URL_SITE."home");
		die();

	break;

	default:
	break;	
	}
?>