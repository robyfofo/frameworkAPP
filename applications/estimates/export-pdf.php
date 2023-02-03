<?php
/*
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/estimate/export-pdf.php v.1.3.0. 28/09/2020
*/

//ini_set('display_errors',1);
//print_r(Core::$request);
//Core::setDebugMode(1);

use Dompdf\Dompdf;
use Dompdf\Options;

switch(Core::$request->method) {

	case 'estimateExpPdf':

		$App->item = new stdClass;
		$App->item_articoli = new stdClass;
		$App->customer = new stdClass;
		$id_estimate = intval(Core::$request->param);

		if ($id_estimate > 0) {

			// preleva estimate
			Sql::initQuery($App->params->tables['item'],array('*'),array($id_estimate),'id = ?');
			$App->item = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }

			// preleva articoli
			Sql::initQuery($App->params->tables['arts'],array('*'),array($id_estimate),'estimates_id = ?');
			$App->item_articoli = Sql::getRecords();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			//print_r($App->item_articoli);die();

			// preleva customer
			Sql::initQuery($App->params->tables['cust'],array('*'),array($App->item->thirdparty_id),'id = ?');
			$App->item_customer = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			//print_r($App->item_customer);die();

			// totali
			$articlesTotal = 0;
			$articlesTaxTotal = 0;
		

			$html = '<html>
			<head>
			
			<style>
			<!--
			body {
				padding-right: 20px;
				padding-left: 20px;
				font-size: 16px;
				font-family: "Helvetica";
			}

			div.content {
				padding: 20px;
				margin-bottom:20px;
				width: 100%;
				clear: both;
			}
			div.company {
				width: 39%;
				float: right;
				text-align: right;
				padding-right: 10px;

			}

			div.customer {
				width: 59%;
				float: left;
			}

			div.note {
				padding-top: 40px;
				font-size: 18px;
			}

			div.data {
				width: 49%;
				float: left;
			}

			div.scadenza {
				width: 49%;
				float: right;
			}

			div.notepagamento {
				margin-top: 50px;
			}


			div.pagamento {
				width: 49%;
				float: left;
				text-align: center;
			}

			div.firma {
				width: 49%;
				float: right;
				text-align: center;
			}

			div.firma > i{
				font-size: 80%;
			}

			h1 {
				font-size: 20px;
			}

			h2 {
				font-size: 18px;
			}

			h3 {
				font-size: 17px;
				text-align: center;
			}

			table {
				border: 1px solid #000;
				padding: 10px;
			}
			
			table {
				width: 90%;
				margin-left: auto;
				margin-right: auto;
				margin-top: 20px;
			}

			table td {
				padding: 5px 10px 5px 10px;
			}

			caption {
				font-size: 19px;
				font-weight: bold;
				margin-top: 20px;
				margin-bottom: 10px;
			}


			.td-totale {
				max-width: 120px;
				width: 120px;
				text-align: right;
			}

			.td-lab-totale {
				text-align: right;
				font-size: 18px;
				padding-top: 40px;
			}

			.td-tot-totale {
				max-width: 120px;
				width: 120px;
				text-align: right;
				font-size: 19px;
				font-weight: bold;
				padding-top: 40px;
			}
			-->
			</style>
			
			</head>
			<body>';

			$html  .= '<div class="content">';

			// azienda
			$html  .= '<div class="company">';
			$html  .= '<h2>'.$App->company->ragione_sociale.'</h2>';
			$html  .= $App->company->street.' - '.$App->company->zip_code.' - '.$App->company->city.' ('.$App->company->provincia.')<br />';
			$html  .= $_lang['P. IVA'].' '.$App->company->partita_iva.' - '.$_lang['C. Fiscale'].' '.$App->company->codice_fiscale.'<br />';
			$html  .= '</div>';

			// cliente
			$html  .= '<div class="customer">';
			$html  .=  '<h1>'.ucfirst($_lang['destinatario']).'</h1>';
			if ($App->item->thirdparty_id > 0) {	
				$html  .=  '<strong>'.$_lang['P.IVA'].'</strong> '.$App->item_customer->partita_iva.'<br />';
				$html  .=  '<strong>'.$_lang['C.F.'].'</strong> '.$App->item_customer->codice_fiscale.'<br />';
				$html  .=  '<strong>'.$App->item_customer->ragione_sociale.'</strong><br />';
				$html  .=  '<strong>'.strtoupper($_lang['email']).'</strong> '.$App->item_customer->email.'<br />';
				$html  .=  $App->item_customer->street.'<br />';
				$html  .=   $App->item_customer->zip_code.' '.$App->item_customer->city.' ('.$App->item_customer->provincia.')<br />';
			} else {
				$html  .=   $App->item->customer;
			}
			$html  .= '</div>';

			$html  .= '</div>';

			// note
			$html  .= '<div class="content note">';
			$html  .= $App->item->note;
			$html  .= '</div>';	

			// date
			$html  .= '<div class="content">
				<div class="data">
					<strong>'.strtoupper($_lang['voce']).'</strong> '.$_lang['del'].' <strong>'.DateFormat::convertDateFormats($App->item->dateins,'Y-m-d',$_lang['data format'],$App->nowDate).'</strong>
				</div>
				<div class="scadenza">
					<strong>'.strtoupper($_lang['scadenza']).'</strong>: '.DateFormat::convertDateFormats($App->item->datesca,'Y-m-d',$_lang['data format'],$App->nowDate).
				'</div>
				</div>';

			// contenuto
			if ($App->item->content != '') {
				$html .= '<div class="content">'.$App->item->content.'</div>';
			}
			
			// articoli
			$articlesTotal = 0;
			$articlesTaxTotal = 0;
			$z = 0;

			// articolo
			if (is_array($App->item_articoli) && count($App->item_articoli) > 0) {
				$html .= '<div class="content"><h3>'.ucfirst($_lang['elenco lavorazioni da fare']).'</h3>';
				$html .= '<table>';
				//<caption>'.ucfirst($_lang['elenco lavorazioni da fare']).'</caption>
				$html .= '<thead><tr><th>'.ucfirst($_lang['contenuto']).'</th><th class="th-totale">'.ucfirst($_lang['totale']).'</th></tr></thead><tbody>';
				foreach ($App->item_articoli AS $key=>$value) {

					$articleTotal = (float)$value->price_total + $value->price_tax;

					// aggiunge tassa aggiuntiva
					$articleAddTax = 0;
					if ($App->item->tax > 0) {					
						$articleAddTax = ( $value->price_total * $App->item->tax ) / 100;
					} 

					// aggiunge rivalsa
					$articleRivalsa = '0';
					if ($App->item->rivalsa > 0) {
						$articleRivalsa = ($value->price_total * $App->item->rivalsa) / 100;
					}

					$articleTotal = (float)$articleTotal + $articleAddTax + $articleRivalsa;

					$articlesTotal = (float)$articlesTotal + $articleTotal;

					$html .= '<td>'.$value->content.'</td>';
					$html .= '<td class="td-totale">'.'€ '.number_format($articleTotal,2,',','.').'</td>';
				}
				$html .= '</tbody>';
			}

			$html .= '<tfoot>';
			$html .=  '<tr><td class="td-lab-totale">'.ucfirst($_lang['totale']).' '.$_lang['preventivo'].'</td><td class="td-tot-totale">€ '.number_format($articlesTotal,2,',','.').'</td></tr>';
			$html .= '</tfoot></table></div>';
			


			// note pagamento e firma

			$html  .= '<div class="content notepagamento">
				<div class="pagamento">
					<strong>'.ucfirst($_lang['pagamento']).':</b> '.$_lang['tipo pagamento'].'</strong>
				</div>
				<div class="firma">
					<strong>'.mb_strtoupper($_lang['accetto preventivo'],'utf-8').'</strong>
					<br /><br /><i>'.$_lang['firma cliente'].'</i>
				</div>
				</div>';			

			$html .= '</body></html>';
			

			//die($html);

			// instantiate and use the dompdf class
			$options = new Options();
			$options->setIsHtml5ParserEnabled(true);

			$dompdf = new Dompdf($options);
			$dompdf->loadHtml($html);

			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4', 'portait');

			// Render the HTML as PDF
			$dompdf->render();

			// Output the generated PDF to Browser
			$filename = ucfirst($_lang['voce']).'-'.$App->item->dateins.".pdf";
			$dompdf->stream($filename);
		}

	break;
}
die();
?>
