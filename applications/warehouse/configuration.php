<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/module-XXX/configuration.php v.4.5.1. 15/06/2020
*/

switch(Core::$request->method) {
	case 'updateConf':
	
		// preleva filename vecchio
		Sql::initQuery($App->params->tables['conf'],array('filename','org_filename'),array($App->id),'id = ?');
		$App->itemOld = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		
		//preleva il filename dal form
		ToolsUpload::setFilenameFormat($globalSettings['image type available']);	
		ToolsUpload::getFilenameFromForm();
   		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyConf/'.$App->id);
		}
		$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   	$_POST['org_filename'] = ToolsUpload::getOrgFilename(); 		   		   	
		$uploadFilename = $_POST['filename'];
		// imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)
		if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
		if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;	 
		// opzione cancella immagine
		if (isset($_POST['deleteFile']) && $_POST['deleteFile'] == 1) {
		   if (file_exists($App->params->uploadPaths['conf'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['conf'].$App->itemOld->filename);	
			}	
			$_POST['filename'] = '';
		   	$_POST['org_filename'] = ''; 	
		}

		// parsa i post in base ai campi
		Form::parsePostByFields($App->params->fields['conf'],Config::$localStrings,array());
		if (Core::$resultOp->error > 0) { 
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyConf/');
		}

		Sql::updateRawlyPost($App->params->fields['conf'],$App->params->tables['conf'],'id',1);
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/db'); }
		
		if ($uploadFilename != '') {
			move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['conf'].$uploadFilename) or die('Errore caricamento file');   			
		   	// cancella l'immagine vecchia
			if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['conf'].$App->itemOld->filename)) {			
				@unlink($App->params->uploadPaths['prod'].$App->itemOld->filename);			
			}
		}
		
		$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['configurazione'],Config::$localStrings['%ITEM% modificata']));
		ToolsStrings::redirect(URL_SITE_ADMIN.Core::$request->action.'/modifyConf/');
										
	break;
	
	default;	
		$App->pageSubTitle = Config::$localStrings['configurazione'];
		$App->viewMethod = 'form';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	default:
	case 'form':
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['conf'],array('*'),array(),' id = 1');
		$App->item = Sql::getRecord();
		
		$App->methodForm = 'updateConf';
		$App->templateApp = 'formConfiguration.html';
		$App->css[] = '<link href="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formConfiguration.css" rel="stylesheet">';		
		$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formConfiguration.js"></script>';			
	break;

	}	
?>