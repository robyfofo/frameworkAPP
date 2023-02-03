<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * core/error.php v.1.3.0. 07/09/2020
*/


/* variabili ambiente */
$App->pageTitle = 'Error';
$App->templateApp = Core::$request->action.'.html';
$App->templateBase = 'struttura-error.html';
$App->coreModule = true;
$App->codeVersion = ' 1.3.0.';

switch(Core::$request->method) {

	case '404':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Error 404!'];
		$App->error_content = $_lang['testo errore 404'];
	break;

	case 'access':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Access Error!'];
		$App->error_content = $_lang['testo errore accesso'];
	break;

	case 'mail':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Mail Error!'];
		$App->error_content = $_lang['testo errore mail'];
	break;

	case 'db':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Database Error!'];
		$App->error_content = $_lang['testo errore database'];
		$App->error_contentAlt = (Core::$request->param != '' ? Core::$request->param : '');
	break;

	case 'module':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Module Error!'];
		$module = (Core::$request->param != '' ? Core::$request->param : '');
		$App->error_content = preg_replace('/%MODULE%/',$module,$_lang['Errore nel modulo %MODULE%!']);
		$App->error_contentAlt = (Core::$request->params[0] != '' ? Core::$request->params[0] : '');
	break;

	case 'nopm':
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Permissions Error!'];
		$App->error_content = $_lang['testo errore permessi'];
		$App->error_contentAlt = (Core::$request->param != '' ? Core::$request->param : '');
	break;

	default:
		$App->error_title = $_lang['Errore!'];
		$App->error_subtitle = $_lang['Internal Server Error!'];
		$App->error_content = $_lang['testo errore generico'];
	break;

}

$App->pageSubTitle = $App->error_subtitle;
?>
