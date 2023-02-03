<?php
/**
* Framework Siti HTML-PHP-MySQL
* PHP Version 7
* @author Roberto Mantovani (<me@robertomantovani.vr.it>
* @copyright 2009 Roberto Mantovani
* @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
* wscms/core/nousername.php v.1.3.1. 15/02/2023 
*/

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

//Core::setDebugMode(1);

$App->pageTitle = Config::$localStrings['titolo sezione richiesta username'];
$App->pageSubTitle = Config::$localStrings['titolo sezione richiesta username'];
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

	//Permissions::checkCsrftoken($returnurl= URL_SITE.Core::$request->action);

	if (!isset($_POST['email'])) {
		$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Config::$localStrings['indirizzo email'],Config::$localStrings['Devi inserire un %ITEM%!']);;
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	$email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
	
	// legge username dalla email	
	// (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false))
	Sql::initQuery(Config::$dbTablePrefix.'users',array('id','username'),array($email),"email = ? AND active = 1");
	$App->item = Sql::getRecord();
	if (Core::$resultOp->error > 0) {
		$_SESSION['message'] = '1|'.Core::$localStrings['Database Error!'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}

	if ( !isset($App->item->id) || (isset($App->item->id) && $App->item->id == 0) ) {
		$_SESSION['message'] = '1|'.Config::$localStrings['Indirizzo email inserito non esiste! Vi invitiamo a ripetere la procedura o contattare amministratore del sistema.'];
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	
	// crea l'email
	$subject = Config::$localStrings['titolo email sezione richiesta username'];
	$subject = preg_replace('/%SITENAME%/',Config::$globalSettings['site name'],$subject);
	$content = Config::$localStrings['testo email sezione richiesta username'];
	$content = preg_replace('/%SITENAME%/',Config::$globalSettings['site name'],$content);
	$content = preg_replace('/%EMAIL%/',$email,$content);
	$content = preg_replace('/%USERNAME%/',$App->item->username,$content);
	$content_plain = \Soundasleep\Html2Text::convert($content);
	
	$debuglog->info('Invio email per recupero username',['sezione'=>'nousername','email'=>$email]);
	$debuglog->info('Soggetto: '.$subject,['sezione'=>'nousername']);
	$debuglog->info('Contenuto: '.$content,['sezione'=>'nousername']);
	$debuglog->info('Content_plan: '.$content_plain,['sezione'=>'nousername']);

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
	$sendemail->to($email);
	
	if (Config::$globalSettings['send email debug'] == 1) {
		$sendemail->bcc(Config::$globalSettings['email debug']);
	};
	
	$sendemail->subject($subject);
	$sendemail->html($content);
	$sendemail->text($content_plain);
	
	try {
		$mailer->send($sendemail);
		Core::$resultOp->error = 0;
	} catch (TransportExceptionInterface $e) {
		Core::$resultOp->error = 1;
	}
	
	if (Core::$resultOp->error > 0) {
		$debuglog->info('Email per recupero username NON inviata!',['sezione'=>'nousername','email'=>$email]);
		$_SESSION['message'] = '1|'.Config::$localStrings['Errore invio della email! Vi invitiamo a ripetere la procedura o contattare amministratore.']; 
		ToolsStrings::redirect(URL_SITE.Core::$request->action);
	}
	
	$debuglog->error('Email per recupero username inviata!',['sezione'=>'nopassword','email'=>$email]);
	$_SESSION['message'] = '0|'.Config::$localStrings['Email inviata correttamente! Nel testo troverete il username!']; 
	ToolsStrings::redirect(URL_SITE.Core::$request->action);
}
?>