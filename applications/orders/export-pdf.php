<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/orders/pdf.php v.1.3.0. 02/10/2020
*/
//ini_set('display_errors',0);
//print_r(Core::$request);

use Dompdf\Dompdf;
use Dompdf\Options;

switch(Core::$request->method) {

	case 'ordersExpPdf':
		

			$App->item = new stdClass;
			$App->item_articoli = new stdClass;
			$App->customer = new stdClass;
			$id_order = intval(Core::$request->param);
	
			if ($id_order > 0) {
	
				// preleva ordine
				Sql::initQuery($App->params->tables['orders'],array('*'),array($id_order),'id = ?');
				$App->item = Sql::getRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
				if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }
	
				// preleva articoli
				Sql::initQuery($App->params->tables['articles'],array('*'),array($id_order),'orders_id = ?');
				$App->item_articoli = Sql::getRecords();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
				//print_r($App->item_articoli);die();
	
				// preleva thirdparty
				Sql::initQuery($App->params->tables['orders_thirdparty'],array('*'),array($id_order),'orders_id = ?');
				$obj = Sql::getRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
				if (isset($obj) && is_object($obj) && count((array)$obj) > 0) {
					foreach ($obj as $attr => $value) {
						$App->item->{$attr} = $value;
					}
				}

				// preleva company
				Sql::initQuery($App->params->tables['orders_company'],array('*'),array($id_order),'orders_id = ?');
				$obj = Sql::getRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
				if (isset($obj) && is_object($obj) && count((array)$obj) > 0) {
					foreach ($obj as $attr => $value) {
						$App->item->{$attr} = $value;
					}
				}
				//print_r($App->item);
	
				// totali
				$articlesTotal = 0;
				$articlesTaxTotal = 0;
			
	
				$html = '<html>
				<head>
				
				<style>
				<!--
				body {
					font-size: 12px;
					font-family: "Helvetica";
				}
	
				div.content {
					padding: 0px;
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
					font-size: 14px;
				}
	
				h2 {
					font-size: 13px;
				}
	
				h3 {
					font-size: 12px;
					text-align: center;
				}
	
				table {
					border: 1px solid #000;
					padding: 0px;
				}
				
				table {
					width: 100%;
					margin: 20px auto auto 0px;
					border-collapse: collapse;
					font-size: 10px;
				}

				table tr {
					margin:0px;
					padding: 0px;
				}

				table th {
					text-transform: capitalize;
				}

				table td {
					padding: 5px 10px 5px 10px;
					border: 1px solid;
					margin: 0px;
					
				}
	
				caption {
					font-size: 19px;
					font-weight: bold;
					margin-top: 20px;
					margin-bottom: 10px;
				}
				
				td.align-center {
					text-align: center;
				}
				td.align-right {
					text-align: right;
				}
				.td-totale {
					max-width: 120px;
					width: 120px;
					text-align: right;
				}
	
				.td-lab-totale {
					text-align: right;
					font-size: 11px;
					padding-top: 40px;
				}
	
				.td-tot-totale {
					max-width: 120px;
					width: 120px;
					text-align: right;
					font-size: 11px;
					font-weight: bold;
					padding-top: 30px;
				}
				-->
				</style>
				
				</head>
				<body>';
	
				$html  .= '<div class="content">';
	
				// azienda
				$html  .= '<div class="company">';
				$html  .= '<h2>'.$App->item->company_ragione_sociale.'</h2>';
				$html  .= $App->item->company_street.' - '.$App->item->company_zip_code.' - '.$App->item->company_comune.' ('.$App->item->company_provincia.')<br />';
				$html  .= Config::$localStrings['P. IVA'].' '.$App->item->company_partita_iva.' - '.Config::$localStrings['C. Fiscale'].' '.$App->item->company_codice_fiscale.'<br />';
				$html  .= '</div>';
	
				// cliente
				$html  .= '<div class="customer">';
				$html  .=  '<h1>'.ucfirst(Config::$localStrings['destinatario']).'</h1>';
				$html  .=  '<strong>'.$App->item->thirdparty_ragione_sociale.'</strong><br />';
				$html  .=  $App->item->thirdparty_street.'<br />';
				$html  .=   $App->item->thirdparty_zip_code.' '.$App->item->thirdparty_comune.' ('.$App->item->thirdparty_provincia.')<br /><br />';
				$html  .=  '<strong>'.strtoupper(Config::$localStrings['email']).'</strong> '.$App->item->thirdparty_email.'<br />';
				$html  .=  '<strong>'.Config::$localStrings['P.IVA'].'</strong> '.$App->item->thirdparty_partita_iva.'<br />';
				$html  .=  '<strong>'.Config::$localStrings['C.F.'].'</strong> '.$App->item->thirdparty_codice_fiscale.'<br />';
				
				$html  .= '</div>';
	
				$html  .= '</div>';
	
				// note
				$html  .= '<div class="content note">';
				$html  .= $App->item->note;
				$html  .= '</div>';	
	
				// date
				$html  .= '<div class="content">
					<div class="data">
						<strong>'.strtoupper(Config::$localStrings['voce']).'</strong> '.Config::$localStrings['del'].' <strong>'.DateFormat::convertDateFormats($App->item->dateins,'Y-m-d',Config::$localStrings['data format'],$App->nowDate).'</strong>
					</div>';
	
			
				
				// articoli
				$articlesTotal = 0;
				$articlesTaxTotal = 0;
				$z = 0;
	
				// articolo
				if (is_array($App->item_articoli) && count($App->item_articoli) > 0) {
					$html .= '<div class="content"><h3>'.ucfirst(Config::$localStrings['articoli']).'</h3>';
					$html .= '<table>';
					//<caption>'.ucfirst(Config::$localStrings['elenco lavorazioni da fare']).'</caption>
					$html .= '<thead><tr><th class="text-center">'.Config::$localStrings['contenuto'].'</th>
					<th class="text-center">'.Config::$localStrings['prezzo unitario'].'</th>
					<th class="text-center">'.Config::$localStrings['quantità'].'</th>
					<th class="text-center">'.Config::$localStrings['prezzo totale'].'</th>	
					<th class="text-center">'.Config::$localStrings['iva'].'%</th>
					<th class="text-center">'.Config::$localStrings['imponibile'].'</th>
					<th class="text-center">'.Config::$localStrings['totale'].'</th></tr></thead>';
					foreach ($App->item_articoli AS $key=>$value) {
	
						$articleTotal = (float)$value->price_total + $value->price_tax;
	
	
						$articlesTotal = (float)$articlesTotal + $articleTotal;
	
						$html .= '<tr><td>'.$value->content.'</td>';
						$html .= '<td class="align-right">'.'€ '.number_format($value->price_unity,2,',','.').'</td>';
						$html .= '<td class="align-center">'.intval($value->quantity).'</td>';
						$html .= '<td class="align-right">'.'€ '.number_format($value->price_total,2,',','.').'</td>';
						$html .= '<td class="align-center">'.$value->tax.'</td>';
						$html .= '<td class="align-right">'.'€ '.number_format($value->price_tax,2,',','.').'</td>';
						$html .= '<td class="td-totale">'.'€ '.number_format($articleTotal,2,',','.').'</td></tr>';
					}
					$html .= '';
				}
	
				$html .= '<tfoot>';
				$html .=  '<tr><td colspan="6" class="td-lab-totale">'.ucfirst(Config::$localStrings['totale']).' '.Config::$localStrings['voce'].'</td><td class="td-tot-totale">€ '.number_format($articlesTotal,2,',','.').'</td></tr>';
				$html .= '</tfoot></table></div>';
				
	
	
				// note pagamento e firma
	
				
	
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
				$filename = ucfirst(Config::$localStrings['voce']).'-'.$App->item->dateins.".pdf";
				$dompdf->stream($filename);
			}
	
		break;
}
die();
?>