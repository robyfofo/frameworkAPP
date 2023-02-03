<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/pdf.php v.1.0.0. 12/02/2018
*/
//ini_set('display_errors',0);
//print_r(Core::$request);

switch(Core::$request->method) {

	case 'invoicesExpPdf':
		$App->invoice = new stdClass;
		$App->invoice_articoli = new stdClass;
		$id_invoice = intval(Core::$request->param);
		
		$headerpdf = array();
		$notepdf = array();
		$articolipdf = array();
		$totalipdf = array();
		
		if ($id_invoice > 0) {
			
			

				/* preleva invoice */
				Sql::initQuery($App->params->tables['InvSal'],array('*'),array($id_invoice),'id = ?');
				$App->invoice = Sql::getRecord();
				
				$App->invoice->number_complete = $App->invoice->id_customer.'-'.$App->invoice->number.'-'.$App->invoice->number_year;
				
				//print_r($App->invoice); die();
				
				if (Core::$resultOp->error == 0) {
					
					/* preleva articoli */
					Sql::initQuery($App->params->tables['ArtSal'],array('*'),array($id_invoice),'id_invoice = ? AND active = 1');
					$App->invoice_articoli = Sql::getRecords();
					if (Core::$resultOp->error == 0) {
						
										
							/* settings */	
							$App->company->gestione_iva = 0;
							$App->company->gestione_rivalsa = 1;
							
													
							/* totali */
							$articlesTotal = 0;
							$articlesTaxTotal = 0;																			
							$z = 0;							
							/* calcola inponibile */
							if (is_array($App->invoice_articoli) && count($App->invoice_articoli) > 0) {
								foreach ($App->invoice_articoli AS $key=>$value) {
									$articlesTotal = (float)$articlesTotal + $value->price_total;
									$articlesTaxTotal = (float)$articlesTaxTotal + $value->price_tax;									
									/* dati pdf */
									/* se iva */
									if ($App->company->gestione_iva == 1) {
										$articolipdf[$z]['tax'] = $value->tax.'% ';
										$articolipdf[$z]['importotax'] = '€ '.number_format($value->price_tax,2,',','.').' ';
										}
									$articolipdf[$z]['descrizione'] = $value->content;
									if ($App->invoice->stampa_quantita == 1) {
										$articolipdf[$z]['quantità'] = number_format($value->quantity,2,',','.');
									}
									if ($App->invoice->stampa_unita == 1) {
										$articolipdf[$z]['prezzounitario'] = '€ '.number_format($value->price_unity,2,',','.').' ';
									}
									$articolipdf[$z]['importo'] = '€ '.number_format($value->price_unity * $value->quantity,2,',','.').' ';
									$z++;									 
									}
								}
								
								/* calcola tassa aggiuntiva */
								$invoiceTotalTax = '0.00';
								if ($App->invoice->tax > 0) $invoiceTotalTax = ($articlesTotal * $App->invoice->tax) / 100;
								
								/* calcola rivalsa */
								$invoiceTotalRivalsa = '0.00';
								if ($App->invoice->rivalsa > 0) $invoiceTotalRivalsa = ($articlesTotal * $App->invoice->rivalsa) / 100;
								
								$invoiceTotal = (float)$articlesTotal + $articlesTaxTotal + $invoiceTotalTax + $invoiceTotalRivalsa;
								
								// Initialize a ROS PDF class object using DIN-A4, with background color gray
								$pdf = new Cezpdf('a4','portrait','color',array(255,255,255));
								// Set pdf Bleedbox
								$pdf->ezSetMargins("50","50","70","70");
								// Use one of the pdf core fonts
								$mainFont = 'Helvetica';
								// Select the font
								$pdf->selectFont($mainFont);
								// Define the font size
								$size=12;
								// Modified to use the local file if it can
								$pdf->openHere('Fit');
								
								$datacreator = array (
									'Title'=>ucfirst($_lang['fattura']).' '.$App->invoice->number,
									'Author'=>SITE_OWNER,
									'Subject'=>ucfirst($_lang['fattura']).' '.$App->invoice->number,
									'Creator'=>SITE_NAME,
									'Producer'=>URL_SITE
									);
								$pdf->addInfo($datacreator);
								
								/* HEADER */
								$pdf->ezSetY(750);
								$colsheadpdf = array('titolo'=>''); 
								$headpdf[0]['titolo'] = $App->company->ragione_sociale;
								$headpdf[1]['titolo'] = $App->company->street.' - '.$App->company->zip_code.' - '.$App->company->city.' ('.$App->company->province.')';
								$headpdf[2]['titolo'] = $_lang['P. IVA'].' '.$App->company->partita_iva.' - '.$_lang['C. Fiscale'].' '.$App->company->codice_fiscale;
								$headpdf[3]['titolo'] = '<strong>'.strtoupper($_lang['fattura']).'</strong> '.$_lang['nr.'].' <b>'.$App->invoice->number_complete.'</b> '.$_lang['del'].' <b>'.DateFormat::convertDateFormats($App->invoice->dateins,'Y-m-d',$_lang['data format'],$App->nowDate).'</b>';	
	
								
															
								$headpdf[4]['titolo'] = $App->invoice->note;
								$col = $pdf->ezTable($headpdf,$colsheadpdf,'',array('showHeadings'=> 0,'gridlines'=>EZ_GRIDLINE_DEFAULT,'showLines'=>0,'fontSize'=>10,'shaded'=>0,'rowGap'=>2,'colGap'=>20,'xPos'=>270,'xOrientation'=>'right','cols'=>array('titolo'=>array('showLines'=>1),'testo'=>array('showLines'=>0))));
								$pdf->setColor('0.8','0.8','0.8');
								$pdf->setStrokeColor('0.8','0.8','0.8');
								$pdf->setLineStyle(1);
								$pdf->line(30,$col-10,560,$col-10);
								
								$pdf->ezSetDy(-20);
								$tablecols = array('titolo'=>'','testo'=>'Cliente'); 
								$tabledata = array();
								$tabledata[0]['titolo'] = '<strong>'.$_lang['P.IVA'].'</strong> '.$App->invoice->customer_partita_iva;
								$tabledata[0]['testo'] = ucfirst($_lang['destinatario']);
								$tabledata[1]['titolo'] = '<strong>'.$_lang['C.F.'].'</strong> '.$App->invoice->customer_codice_fiscale;
								$tabledata[1]['testo'] = '<strong>'.$App->invoice->customer_ragione_sociale.'</strong>';
								$tabledata[2]['titolo'] = '<strong>'.strtoupper($_lang['email']).'</strong> '.$App->invoice->customer_email;
								$tabledata[2]['testo'] = $App->invoice->customer_street;
								$tabledata[3]['titolo'] = '';
								$tabledata[3]['testo'] = $App->invoice->customer_zip_code.' '.$App->invoice->customer_city.' ('.$App->invoice->customer_province.')';
								$col = $pdf->ezTable($tabledata,$tablecols,'',array('showHeadings'=>0,'gridlines'=>EZ_GRIDLINE_DEFAULT,'showLines'=>0,'fontSize'=>11,'width'=>500,'shaded'=>0,'rowGap' => 2, 'colGap' => 2, 'lineCol'=>array(0.7,0.7,0.7), 'cols'=> array( 'titolo'=>array( ), 'testo'=>array( 'showLines' => 0 ), ) ) );
								/* FINE HEADER */
								
								/* ARTICOLI */
								$pdf->setColor('0.8','0.8','0.8');
								$pdf->setStrokeColor('0.8','0.8','0.8');
								$pdf->setLineStyle(1);
								$pdf->line(30,$col-10,560,$col-10);
								$pdf->ezSetDy(-30);
				
								$colsArticolipdf['descrizione'] = '<b>'.ucfirst($_lang['descrizione']).'</b>';
								
								if ($App->invoice->stampa_quantita == 1) {
									$colsArticolipdf['quantità'] = '<b>'.ucfirst($_lang['quantità']).'</b>';
								}
								
								if ($App->invoice->stampa_unita == 1) {
									$colsArticolipdf['prezzounitario'] = '<b>'.ucwords($_lang['prezzo unità']).'</b>';
								}
								
								$colsArticolipdf['importo'] = '<b>'.ucwords($_lang['importo']).'</b>';
								/* se iva */
								if ($App->company->gestione_iva == 1) {
									$colsArticolipdf['tax'] = '<b>'.ucwords($_lang['iva']).'</b>';
									}
								$opt = array( 'showHeadings' => 1, 'gridlines'=> EZ_GRIDLINE_DEFAULT,'fontSize'=>10,'width'=>500,'shaded'=>0,'rowGap' =>5,'colGap'=>7,'showLines'=>1,'lineCol'=>array(0.7,0.7,0.7),'cols'=> array('quantità'=>array('width'=>90,'justification'=>'center'),'prezzounitario'=>array('width'=>90,'justification' =>'right'),'importo'=>array('width'=>80,'justification' =>'right' )));							
								if ($App->company->gestione_iva = 1) {
									$opt['cols']['tax'] = array('width'=>30);
									}
								$y = $pdf->ezTable($articolipdf, $colsArticolipdf,'',$opt);
								/* FINE ARTICOLI */							
								
								
						
								
								/* NOTE */
								if ($y > 270) $y = 270;
								
								$pdf->ezSetY($y);
																
								$colspdf = array('titolo'=>mb_strtoupper($_lang['modalità pagamento'],'UTF-8'),'testo'=>strtoupper($_lang['scadenze'])); 
								$datapdf = array();
								$datapdf[1]['titolo'] = '<b>'.ucwords($_lang['bonifico bancario']).'</b>';
								$datapdf[1]['testo'] = '<b>'.DateFormat::convertDateFormats($App->invoice->datesca,'Y-m-d',$_lang['data format'],$App->nowDate).'</b>';
								$datapdf[2]['titolo'] = 'IBAN: <b>'.$App->company->iban.'</b>';
								$datapdf[2]['testo'] = '';
								$datapdf[3]['titolo'] = $App->company->intestatario;
								$datapdf[3]['testo'] = '';								
								$pdf->ezTable($datapdf, $colspdf,'',array('showHeadings' => 1,'gridlines'=>EZ_GRIDLINE_DEFAULT,'fontSize'=>9,'width'=>500,'shaded'=> 0,'rowGap'=>3,'colGap'=>10,'showLines'=>1,'lineCol'=>array(0.7,0.7,0.7),'cols'=>array('quantità'=>array('width'=>60,'justification' =>'center'),'prezzounitario'=>array('width'=>80,'justification'=>'right'),'importo'=>array('width'=>130,'justification'=>'right'))));
								/* FINE NOTE */	
								
								/* NOTE */
								$pdf->ezSetDy(-10);
								$colspdf = array('riepilogo'=>strtoupper($_lang['riepilogo iva']),'importo'=>strtoupper($_lang['importo lordo']),'imposte'=>strtoupper($_lang['imposte'])); 
								$datapdf = array();								
								$datapdf[0]['riepilogo'] = $App->company->text_noiva;
								$datapdf[0]['importo'] = '€ '.number_format($articlesTotal,2,',','.');
								$datapdf[0]['imposte'] = '€ '.number_format($articlesTaxTotal + $invoiceTotalTax,2,',','.');
								$opt = array('showHeadings' =>1,'gridlines'=>EZ_GRIDLINE_DEFAULT,'fontSize'=>9,'width'=>500,'shaded'=>0,'rowGap'=>3,'colGap'=>10,'showLines'=>1,'lineCol'=>array(0.7,0.7,0.7),'cols'=>array('imposte'=>array('justification'=>'right'),'importo'=>array('justification'=>'right')));
								$pdf->ezTable($datapdf, $colspdf,'',$opt);
								/* FINE NOTE */			
								
								/* TOTALI */
								$pdf->ezSetDy(-10);
								$colspdf = array('titolo'=>'titolo','testo'=>'testo'); 
								$datapdf = array();
								$z = 0;
								$datapdf[$z]['titolo'] = strtoupper($_lang['totale onorario']);
								$datapdf[$z]['testo'] = '€ '.number_format($articlesTotal,2,',','.');
				
								
								if ($App->invoice->tax > 0) {
									$z++;
									$datapdf[$z]['titolo'] = strtoupper($_lang['tassa aggiuntiva']);
									$datapdf[$z]['testo'] = '€ '.number_format($invoiceTotalTax,2,',','.');
									$z++;
									}
								
								if ($App->invoice->rivalsa > 0) {
									$z++;
									$datapdf[$z]['titolo'] = $App->company->text_rivalsa;
									$datapdf[$z]['testo'] = '€ '.number_format($invoiceTotalRivalsa,2,',','.');
									$z++;
									}
								$opt = array('showHeadings'=>0,'gridlines'=>EZ_GRIDLINE_DEFAULT,'fontSize'=>10,'width'=>500,'shaded'=>0,'rowGap'=>2,'colGap' =>10,'showLines' =>0,'lineCol'=>array(0.7,0.7,0.7),'cols'=>array('titolo'=>array('justification' =>'right'),'testo'=>array('width'=>110,'justification'=>'right')));
								$pdf->ezTable($datapdf, $colspdf,'',$opt);
								/* FINE TOTALI */
								
								/* TOTALI */
								$pdf->ezSetDy(-5);
								$cols = array('titolo'=>'titolo','testo'=>'testo'); 
								$data[0]['titolo'] = '<strong>'.strtoupper($_lang['totale']).'</strong>';
								$data[0]['testo'] = '€ <strong>'.number_format($invoiceTotal,2,',','.').'</strong>';
								$pdf->ezTable($data, $cols,'',array('showHeadings'=>0,'gridlines'=>EZ_GRIDLINE_DEFAULT,'fontSize' =>12,'width'=>500,'shaded'=>0,'rowGap'=>5,'colGap'=>10,'showLines'=>0,'lineCol'=>array(0.7,0.7,0.7),'cols'=>array('titolo'=>array('justification'=>'right'),'testo'=>array('width'=>110,'justification'=>'right'))));
								/* FINE TOTALI */
								
								//Output the pdf as stream, but uncompress
								$namefile = ucfirst($_lang['fattura']).'-'.$App->invoice->number_complete.".pdf";
								$applicationtype = "application/pdf";   
								header("Content-type: $applicationtype");
								header("Content-Disposition: attachment; filename=".basename($namefile).";");
								$pdf->ezStream(array('compress'=>0,'download'=>1,'Content-Disposition'=>$namefile));	
							
						}			
					}
					
			}	
	break;
	}
die();
?>