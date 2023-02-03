<?php
/**
* Framework Siti HTML-PHP-MySQL
* PHP Version 7
* @author Roberto Mantovani (<me@robertomantovani.vr.it>
* @copyright 2009 Roberto Mantovani
* @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
* core/nopassword.php v.1.3.1. 15/02/2023 
*/

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

//Core::setDebugMode(1);

$App->pageTitle = Config::$localStrings['titolo sezione richiesta password'];
$App->pageSubTitle = Config::$localStrings['titolo sezione richiesta password'];
$App->pathApplications = 'application/core/';

$App->templateApp = Core::$request->action.'.html';
$App->action = '';
$App->item = new stdClass;
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

$App->templateBase = 'struttura-login.html';

$section = preg_replace('/%ITEM%/',Config::$localStrings['login'],Config::$localStrings['torna al %ITEM%']);
$section1 = '<a href="'.URL_SITE.'" title="'.ucfirst($section).'">'.ucfirst(Config::$localStrings['login']).'</a>';
$App->returnlink = ucfirst(preg_replace('/%ITEM%/',$section1,Config::$localStrings['torna al %ITEM%']));

if (isset($_POST['submit'])) {

	Permissions::checkCsrftoken($returnurl= URL_SITE.Core::$request->action);
	
	if (!isset($_POST['username'])) {
		$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Config::$localStrings['nome utente'],Config::$localStrings['Devi inserire un %ITEM%!']);
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	$username = filter_input(INPUT_POST,'username',FILTER_SANITIZE_SPECIAL_CHARS);
	
	// guardo se esiste l' username
	$App->item = new stdClass;				
	Sql::initQuery(Config::$DatabaseTables['users']['name'],array('id','username','email'),array($username),"username = ? AND active = 1");
	$App->item = Sql::getRecord();	
	
	if (Core::$resultOp->error > 0) {
		$_SESSION['message'] = '1|'.Config::$localStrings['Errore accesso db!'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	
	if ( !isset($App->item->id) || (isset($App->item->id) && $App->item->id == 0) ) {
		$_SESSION['message'] = '1|'.Config::$localStrings['Il nome utente inserito non esiste! Vi invitiamo a ripetere la procedura o contattare amministratore del sistema.'];	
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}

	// crea la nuova password
	$passw = ToolsStrings::setNewPassword('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890',8);
	//$passw = 'master'; // per test
	
	$criptPassw = password_hash($passw, PASSWORD_DEFAULT);			
	$subject = Config::$localStrings['titolo email sezione richiesta password'];
	$subject = preg_replace('/%SITENAME%/',Config::$globalSettings['site name'],$subject);
	$content = Config::$localStrings['testo email sezione richiesta password'];
	$content = preg_replace('/%SITENAME%/',Config::$globalSettings['site name'],$content);
	$content = preg_replace('/%PASSWORD%/',$passw,$content);
	$content = preg_replace('/%CLEANPASSWORD%/',$passw,$content);
	$content = preg_replace('/%USERNAME%/',$App->item->username,$content);
	$content_plain = \Soundasleep\Html2Text::convert($content);

	$debuglog->info('Invio email per recupero password',['sezione'=>'nopassword','usernaame'=>$username]);
	$debuglog->info('Soggetto: '.$subject,['sezione'=>'nopassword']);
	$debuglog->info('Contenuto: '.$content,['sezione'=>'nopassword']);
	$debuglog->info('Content_plan: '.$content_plain,['sezione'=>'nopassword']);
	
	// Create a Transport object
	if (Config::$globalSettings['use class mail for send email'] == 1) {
		$transport = Transport::fromDsn('sendmail://default?command=/usr/bin/sendmail%20-t');
	}
	if (Config::$globalSettings['use gmail for send email'] == 1) {
		$transport = new GmailSmtpTransport(Config::$globalSettings['gmail username'], Config::$globalSettings['gmail password']);
	};
	$mailer = new Mailer($transport); 
	$sendemail = (new Email());
	
	$sendemail->from( new Address(Config::$globalSettings['default email'], Config::$globalSettings['default email label']) );
	$sendemail->to($App->item->email);
	
	if (Config::$globalSettings['send email debug'] == 1) {
		$sendemail->bcc(Config::$globalSettings['email debug']);
	};
	
	$sendemail->subject($subject);
	$sendemail->html($content);
	$sendemail->text($content_plain);
	try {
		$debuglog->info('Email per recupero password inviata!',['sezione'=>'nopassword','username'=>$username]);
		$mailer->send($sendemail);
		Core::$resultOp->error = 0;
	} catch (TransportExceptionInterface $e) {
		$debuglog->error('Email per recupero password NON inviata!',['sezione'=>'nopassword','username'=>$username]);
		$_SESSION['message'] = '1|'.Config::$localStrings['Errore invio della email! Vi invitiamo a ripetere la procedura o contattare amministratore.'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	
	// aggiorno la password nel db 			
	// (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false))
	Sql::initQuery(Config::$DatabaseTables['users']['name'],array('password'),array($criptPassw,$App->item->id),"id = ?");
	Sql::updateRecord();
	if (Core::$resultOp->error > 0) {
		$_SESSION['message'] = '1|'.Config::$localStrings['testo errore generico'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	$debuglog->info('Memorizzata nuova password!',['sezione'=>'nopassword','username'=>$username]);

	$_SESSION['message'] = '0|'.Config::$localStrings['La nuova password vi è stata inviata con email indirizzo associato ed è stata memorizzata nel sistema!'];
	ToolsStrings::redirect(URL_SITE.Core::$request->action);
}
?>