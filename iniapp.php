<?php
// carica i dati modulo
foreach(Core::$globalSettings['module sections'] AS $key=>$value) {
  Sql::initQuery(Config::$DatabaseTables['modules']['name'],array('*'),array($key),'active = 1 AND section = ?','ordering ASC');
  Config::$modules[$key] = Sql::getRecords();
  if (Core::$resultOp->error == 1) die('Errore db livello utenti!');
}
?>