<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * core/logout.php v.1.2.0. 30/11/2019
*/

/* Istanziamo l'oggetto */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
/* Richiamiamo il metodo che distrugge la sessione */
$my_session->my_session_destroy();
/* Richiamiamo il metodo che pulire la tabella */
$my_session->my_session_gc();
/* cancello il cookie */
setcookie (SESSIONS_COOKIE_NAME, "", time()-1);
setcookie (Config::$globalSettings['cookiestecniciadminlastlogin'], "", time()-1);


session_destroy();
ToolsStrings::redirect(URL_SITE);
?>
