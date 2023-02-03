<?php

if (Permissions::checkAccessUserModule('invoices',$App->userLoggedData,$App->user_modules_active) == true && in_array(DB_TABLE_PREFIX.'invoices_sales',$App->tablesOfDatabase) && file_exists(PATH.$App->pathApplications."invoices/index.php") == true) {

// INVOICES RIEPILOGHI

// fiscale bilancio

$App->impostaInps = 25.72;
$App->codiceAteco = 62;
$App->coefficienteRedditivita = 67;
//$App->coefficienteRedditivita = 78;
$App->impostaIrpef = 15;

// get data for charts
$invoices_bilancio_fiscale_12 = array();
$date = DateTime::createFromFormat('Y-m-d',$App->nowDate);
$date->modify('-12 month');
for ($x=1;$x<=12;$x++) {
	$date->modify('+1 month');
	$d = $date->format('Y-m');
	$dini = $d . '-01';
	$dend = $d . '-31';	
	$ricavi = 0;
	$acquisti = 0;
	// trova le fatture aquisti del mese
	$table = DB_TABLE_PREFIX."invoices_purchases AS i";
	$fields = array("i.id,i.datesca,(SELECT SUM(price_total) FROM ".DB_TABLE_PREFIX."invoices_purchases_articles AS a WHERE i.id = a.id_invoice) AS total");
	$fieldsVals = array($dini,$dend);
	$where = "i.dateins BETWEEN ? AND ?";			
	Sql::initQuery($table,$fields,$fieldsVals,$where,'','','',false);
	$obj = Sql::getRecords();
	Core::setDebugMode(0);
	if (is_array($obj) && count($obj) > 0) {
		foreach ($obj AS $value) {
			if (isset($value->total) && $value->total > 0)  $acquisti += $value->total;			
		}
	}
	// trova le fatture vendite del mese
	$table = DB_TABLE_PREFIX."invoices_sales AS i";
	$fields = array("i.id,i.datesca,(SELECT SUM(a.price_total) + ((SUM(a.price_total) * i.tax) / 100) + ((SUM(a.price_total) * i.rivalsa) / 100) FROM ".DB_TABLE_PREFIX."invoices_sales_articles AS a WHERE i.id = a.id_invoice) AS total");
	$fieldsVals = array($dini,$dend);
	$where = "i.datesca BETWEEN ? AND ?";	
	Sql::initQuery($table,$fields,$fieldsVals,$where,'','','',false);
	$obj = Sql::getRecords();
	if (is_array($obj) && count($obj) > 0) {
		foreach ($obj AS $value) {
			if (isset($value->total) && $value->total > 0)  $ricavi += $value->total;			
		}
	}
	$contributiInps = ($ricavi * $App->impostaInps) / 100;
	$rettidoimponibilelordo = ($ricavi * $App->coefficienteRedditivita) / 100;	
	$rettidoimponibilenetto = $rettidoimponibilelordo - $contributiInps;
	$impostasostitutiva = ($rettidoimponibilenetto * $App->impostaIrpef) / 100;	
	$tassazione = $contributiInps + $impostasostitutiva;
	$utile = $ricavi - $tassazione;		
	$invoices_bilancio_fiscale_12[$d] = "{ y: '".$d."', v: ".$ricavi.", a: ".$acquisti.",u: ".number_format($utile,2, '.','' )." }";	
}		
$App->invoices_bilancio_fiscale_12 = implode(',',$invoices_bilancio_fiscale_12);	



// fiscale anno attuale e precedente

$date = DateTime::createFromFormat('Y-m-d',$App->nowDate);
$App->annocorrente = $date->format('Y');
$date->modify('-1 year');
$App->annoprecedente = $date->format('Y');

$dini = $App->annoprecedente . '-01-01';
$dend = $App->annoprecedente . '-12-31';
// trova le fatture vendite del mese
$table = DB_TABLE_PREFIX."invoices_sales AS i";
$fields = array("i.id,i.datesca,(SELECT SUM(a.price_total) + ((SUM(a.price_total) * i.tax) / 100) + ((SUM(a.price_total) * i.rivalsa) / 100) FROM ".DB_TABLE_PREFIX."invoices_sales_articles AS a WHERE i.id = a.id_invoice) AS total");
$fieldsVals = array($dini,$dend);
$where = "i.dateins BETWEEN ? AND ?";	
Sql::initQuery($table,$fields,$fieldsVals,$where,'','','',false);
$obj = Sql::getRecords();
$ricavi = 0;
if (is_array($obj) && count($obj) > 0) {
	foreach ($obj AS $value) {
		if (isset($value->total) && $value->total > 0)  $ricavi += $value->total;			
	}
		
	$contributiInps = ($ricavi * $App->impostaInps) / 100;
	$rettidoimponibilelordo = ($ricavi * $App->coefficienteRedditivita) / 100;	
	$rettidoimponibilenetto = $rettidoimponibilelordo - $contributiInps;
	$impostasostitutiva = ($rettidoimponibilenetto * $App->impostaIrpef) / 100;	
	$tassazione = $contributiInps + $impostasostitutiva;
	$utile = $ricavi - $tassazione;	
	$App->ricaviannoprecedentechartsdata = "{ 
		y: ".$App->annoprecedente.",
		r: ".number_format($ricavi,2, '.','' ).", 
		ril: ".number_format($rettidoimponibilelordo,2, '.', '').",
		rin: ".number_format($rettidoimponibilenetto,2, '.', '').",
		t: ".number_format($tassazione,2, '.','' ).",
		u: ".number_format($utile,2, '.','' )."}";
		 
}
// echo $App->ricaviannoprecedente;

/*
$App->ricavi = 13000.00;
$App->contributiInps = 2700.00;
echo '<br>ricavi :'.$App->ricavi;
echo '<br>inps :'.$App->contributiInps;
echo '<br>rettido imponibile lordo :'.$App->rettidoimponibilelordo;
echo '<br>rettido imponibile netto :'.$App->rettidoimponibilenetto;
echo '<br>imposta sostitutiva :'.$App->impostasostitutiva;

echo '<br>rettido imponibile netto :'.$App->rettidoimponibilenetto;
echo '<br>tassazione :'.$App->tassazione;
echo '<br>utile :'.$App->utile;
*/

$dini = $App->annocorrente . '-01-01';
$dend = $App->annocorrente . '-12-31';
// trova le fatture vendite del mese
$table = DB_TABLE_PREFIX."invoices_sales AS i";
$fields = array("i.id,i.datesca,(SELECT SUM(a.price_total) + ((SUM(a.price_total) * i.tax) / 100) + ((SUM(a.price_total) * i.rivalsa) / 100) FROM ".DB_TABLE_PREFIX."invoices_sales_articles AS a WHERE i.id = a.id_invoice) AS total");
$fieldsVals = array($dini,$dend);
$where = "i.dateins BETWEEN ? AND ?";	
Sql::initQuery($table,$fields,$fieldsVals,$where,'','','',false);
$obj = Sql::getRecords();
$ricavi = 0;
if (is_array($obj) && count($obj) > 0) {
	foreach ($obj AS $value) {
		if (isset($value->total) && $value->total > 0)  $ricavi += $value->total;			
	}
	
	$contributiInps = ($ricavi * $App->impostaInps) / 100;
	$rettidoimponibilelordo = ($ricavi * $App->coefficienteRedditivita) / 100;	
	$rettidoimponibilenetto = $rettidoimponibilelordo - $contributiInps;
	$impostasostitutiva = ($rettidoimponibilenetto * $App->impostaIrpef) / 100;	
	$tassazione = $contributiInps + $impostasostitutiva;
	$utile = $ricavi - $tassazione;	
	
	$App->ricaviannocorrentechartsdata = "{ 
		y: ".$App->annocorrente.",
		r: ".number_format($ricavi,2, '.','' ).", 
		ril: ".number_format($rettidoimponibilelordo,2, '.', '').",
		rin: ".number_format($rettidoimponibilenetto,2, '.', '').",
		t: ".number_format($tassazione,2, '.','' ).",
		u: ".number_format($utile,2, '.','' ).
		"}";
}
// echo $App->ricaviannocorrente;

}

// include jscript for charts
$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/vendor/morrisjs/morris.css" rel="stylesheet">';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/vendor/raphael/raphael.min.js"></script>';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/vendor/morrisjs/morris.min.js"></script>';			
$App->includeJscriptPHPBottom = Core::$request->action."/templates/".$App->templateUser."/js/chartsdata.js.php";
?>