<?php
// timecard
$DatabaseTablesFields['timecard_predefinite']['name'] = self::$dbTablePrefix.'timecard_predefinite';
$DatabaseTables['timecard_predefinite']['ordering'] = 'DESC';
$DatabaseTablesFields['timecard_predefinite'] = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>0),
	'title'=>array('label'=>Config::$localStrings['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'content'=>array('label'=>Config::$localStrings['contenuto'],'searchTable'=>true,'required'=>false,'type'=>'text'),
	'starttime'=>array('label'=>Config::$localStrings['ora inizio'],'searchTable'=>false,'required'=>false,'type'=>'time','validate'=>'timepicker'),
	'endtime'=>array('label'=>Config::$localStrings['ora fine'],'searchTable'=>false,'required'=>false,'type'=>'time','validate'=>'timepicker'),
	'worktime'=>array('label'=>Config::$localStrings['ore lavoro'],'searchTable'=>false,'required'=>false,'type'=>'time','defValue'=>'00:00:00','validate'=>'time'),
   'access_read'										=> array(
		'label'											=> Config::$localStrings['accesso lettura'],
		'searchTable'									=> false,
		'required'										=> false,
		'type'											=> 'text',
		'defValue'										=> 'none'
	),
	'access_write'										=> array(
		'label'											=> Config::$localStrings['accesso scrittura'],
		'searchTable'									=> false,
		'required'										=> false,
		'type'											=> 'text',
		'defValue'										=> 'none'
	),
   'created'											=> array(
		'label'											=> Config::$localStrings['creazione'],
		'searchTable'									=> false,
		'required'										=> false,
		'type'											=> 'datatime',
		'defValue'										=> Config::$nowDateTime,
		'validate'										=> 'datetimeiso',
		'forcedValue'                   				=> Config::$nowDateTime
	),
	'active'											=> array(
		'label'											=> Config::$localStrings['attiva'],
		'required'										=> false,
		'type'											=> 'int|1',
		'validate'										=> 'int',
		'defValue'										=> '0',
		'forcedValue'      								=> 1
	)
);

$DatabaseTables['timecard']['name'] = self::$dbTablePrefix.'timecard';

// company
$DatabaseTables['company']['name'] = self::$dbTablePrefix.'company';
$DatabaseTablesFields['company'] = array (
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'ragione_sociale'                          		        => array(
		'label'																							=> Config::$localStrings['ragione sociale'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|255',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['ragione sociale']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['ragione sociale']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	'name'                                    		        => array(
		'label'																							=> Config::$localStrings['nome'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
 	'surname'                                         		=> array(
		'label'																							=> Config::$localStrings['cognome'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['cognome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['cognome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	'telephone'                                       		=> array(
		'label'                                        			=> Core::$localStrings['telefono'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|20',
		'validate'									    										=> 'telephonenumber|maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['numero di telefono']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['telefono']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	'email'                                           		=> array(
		'label'                                        			=> Core::$localStrings['email'],
		'searchTable'                                  			=> true,
		'required'                                     			=> true,
		'type'                                         			=> 'varchar|255',
		'defValue'                                     			=> '',
		'validate'                                     			=> 'isemail|maxchars',
		'sanitize'																					=> 'email',
		'error message'                                			=> preg_replace('/%ITEM%/',Core::$localStrings['email'],Config::$localStrings['Devi inserire una %ITEM%!']),
		'error validation message'                     			=> preg_replace('/%ITEM%/',Core::$localStrings['email'],Config::$localStrings['Il valore per il campo %ITEM% non è stato validato!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['email']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
 	),
 	'mobile'                                          		=> array(
		'label'                                        			=> Core::$localStrings['mobile'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|20',
		'validate'									    										=> 'telephonenumber|maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['mobile']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['mobile']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['mobile']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
 	'fax'                                             		=> array(
		'label'                                        			=> Core::$localStrings['fax'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|20',
		'validate'									    										=> 'telephonenumber|maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['fax']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['fax']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['fax']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	
	'partita_iva'			                                    => array(
		'label'                                        			=> Core::$localStrings['partita IVA'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['partita IVA']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['partita IVA']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'codice_fiscale'                                      => array(
		'label'                                        			=> Core::$localStrings['codice fiscale'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice fiscale']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice fiscale']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	
	'gestione_iva'											       						=> array(
	  'label'									       											=> Config::$localStrings['gestione iva'],
	  'required'								        									=> false,
	  'type'									        										=> 'int|1',
	  'validate'			    				        								=> 'int',
		'sanitize'																					=> 'int',
	  'defValue'								        									=> '0',
	  'forcedValue'              				        					=> 1
  ),
	'iva'                                          				=> array(
		'label'                                        			=> Core::$localStrings['iva'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'int|2',
		'validate'								        									=> 'int',
		'sanitize'																					=> 'int',
  ),
	'text_noiva'																					=> array(
		'label'																							=> Core::$localStrings['testo no IVA'],
		'searchTable'																				=> true,
		'type'																							=> 'varchar|255',
		'validate'                             							=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['testo no IVA']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['testo no IVA']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																					=> ''
	),	
	'gestione_rivalsa'									       						=> array(
	  'label'									       											=> Config::$localStrings['gestione rivalsa'],
	  'required'								        									=> false,
	  'type'									        										=> 'int|1',
	  'validate'			    				        								=> 'int',
		'sanitize'																					=> 'int',
	  'defValue'								        									=> '0',
	  'forcedValue'              				        					=> 1
  ),
	'rivalsa'                                          		=> array(
		'label'                                        			=> Core::$localStrings['rivalsa'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'int|2',
		'validate'								        									=> 'int',
		'sanitize'																					=> 'int',
  ),
	'text_rivalsa'																				=> array(
		'label'																							=> Core::$localStrings['testo rivalsa'],
		'searchTable'																				=> true,
		'type'																							=> 'varchar|255',
		'validate'                             							=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['testo rivalsa']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['testo rivalsa']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																					=> ''
	),
	
	'banca'                                          			=> array(
		'label'                                        			=> Core::$localStrings['banca'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|100',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['banca']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['banca']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'intestatario'                                        => array(
		'label'                                        			=> Core::$localStrings['intestatario'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|100',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['intestatario']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['intestatario']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'iban'                                           			=> array(
		'label'                                        			=> Core::$localStrings['iban'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|40',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['iban']),40),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['iban']),40),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'bic_swift'                                           => array(
		'label'                                        			=> Core::$localStrings['bic_swift'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|20',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['bic_swift']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['bic_swift']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),

	'zip_code'                                      		  => array(
		'label'                                     			  => Core::$localStrings['c.a.p.'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',10,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|10',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'int',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['c.a.p.']),10),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['c.a.p.']),10),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	'street'                                        		  => array(
		'label'																							=> Config::$localStrings['indirizzo'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|100',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['indirizzo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['indirizzo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	 ),
	'location_comuni_id'                          		    => array(
		'label'                          								    => Core::$localStrings['comune'],
		'searchTable'                          			        => false,
		'required'                             			        => false, 
		'type'                                    			    => 'int|10',
		'defValue'																			    => 0
 	),
 	'comune_alt'                                      		=> array(
		'label'                                       			=> Core::$localStrings['altro comune'],
		'searchTable'                                  			=> false,
		'required'                                     			=> false,
		'sanitize'																					=> 'string',
		'type'                                         			=> 'varchar|255',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altro comune']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altro comune']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
 	'location_province_id'                            		=> array(
		'label'                                        			=> Core::$localStrings['provincia'],
		'searchTable'                                  			=> false,
		'required'                                     			=> true,
		'type'                                         			=> 'int|10',
		'defValue'                                     			=> 0
	),
	'provincia_alt'                                   		=> array(
		'label'                                      				=> Core::$localStrings['altra provincia'],
		'searchTable'                                  			=> true,
		'required'                                     			=> false,
		'type'                                         			=> 'varchar|255',
		'sanitize'																					=> 'string',
		'defValue'                                     			=> '',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altra provincia']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altra provincia']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'location_nations_id'                         			  => array(
		'label'                                        			=> Core::$localStrings['nazione'],
		'searchTable'                                  			=> false,
		'required'                                     			=> false,
		'type'                                         			=> 'int|10',
		'defValue'                                   				=> 0
	)
);

// todo
$DatabaseTables['todo']['ordering'] = 'DESC';
$DatabaseTables['todo']['name'] = self::$dbTablePrefix.'todo';
$DatabaseTablesFields['todo'] 													= array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	),
	'id_project'																					=> array(
		'label'																							=> Config::$localStrings['progetto'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	),
	'title'                                           		=> array(
		'label'                                        			=> Core::$localStrings['titolo'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|100',
	),
	'content'																							=> array(
		'label'																							=> Core::$localStrings['contenuto'],
		'searchTable'																				=> true,
		'type'																							=> 'text|65535',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																					=> ''
	),
	'status'																							=> array(
		'label'																							=> Config::$localStrings['status'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 0
	),	
	'access_type'																					=> array(
		'label'																							=> Config::$localStrings['tipo accesso'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'sanitize'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 0
	),
	'access_read'																					=> array(
		'label'																							=> Config::$localStrings['accesso lettura'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'text|65535',
		'defValue'																					=> 'none'
	),
	'access_write'																				=> array(
		'label'																							=> Config::$localStrings['accesso scrittura'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'text|65535',
		'defValue'																					=> 'none'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)
);

// projects
$DatabaseTables['projects']['name'] = self::$dbTablePrefix.'projects';
$DatabaseTables['projects']['ordering'] = 'DESC';
$DatabaseTablesFields['projects'] = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'id_contact'																					=> array(
		'label'																							=> Config::$localStrings['contatto'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	),
	'title'                                           		=> array(
		'label'                                        			=> Core::$localStrings['titolo'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|100',
		'error message'																			=>  preg_replace('/%ITEM%/',Core::$localStrings['titolo'],Config::$localStrings['Devi inserire un %ITEM%!']),
	),
	'content'																							=> array(
		'label'																							=> Core::$localStrings['contenuto'],
		'searchTable'																				=> true,
		'type'																							=> 'text|65535',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																					=> ''
	),
	'status'																							=> array(
		'label'																							=> Config::$localStrings['status'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 0
	),
	'costo_orario'																				=> array(
		'label'																							=> Config::$localStrings['costo orario'].' (es: 200.30)',
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'forcedValue'  																			=> '0.00',
		'validate'																					=> 'float',
		'error message'																			=>  preg_replace('/%ITEM%/',Core::$localStrings['costo orario'],Config::$localStrings['Devi inserire un %ITEM%!']),
	),
	'completato'																					=> array(
		'label'																							=> Config::$localStrings['completato'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|1',
		'defValue'																					=> 0,
		'validate'																					=> 'minmax',
		'valuesRif'																					=> array('min'=>0,'max'=>100),
		'forcedValue'  																			=> '0'
	),
	'timecard'																						=> array(
		'label'																							=> Config::$localStrings['timecard'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|1',
		'defValue'																					=> 0,
		'validate'																					=> 'int'
	),
	'current'																							=> array(
		'label'																							=> Config::$localStrings['selezionato'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|1',
		'defValue'																					=> 0,
		'validate'																					=> 'int'
	),
	'ore_preventivo'																			=> array(
		'label'																							=> 'Ore Preventivo',
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|5',
		'defValue'																					=> 0,
		'forcedValue'  																			=> '0'
	),
	'access_type'																					=> array(
		'label'																							=> Config::$localStrings['tipo accesso'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 0
	),
	'access_read'																					=> array(
		'label'																							=> Config::$localStrings['accesso lettura'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'text|65535',
		'defValue'																					=> 'none'
	),
	'access_write'																				=> array(
		'label'																							=> Config::$localStrings['accesso scrittura'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'text|65535',
		'defValue'																					=> 'none'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);

// contacts
//$DatabaseTables['contacts']['name'] = self::$dbTablePrefix.'contacts';

// thirdparty
$DatabaseTables['thirdparty']['name'] = self::$dbTablePrefix.'thirdparty';
$DatabaseTables['thirdparty']['ordering'] = 'DESC';
$DatabaseTablesFields['thirdparty']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'requiredd'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'requiredd'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'categories_id'																				=>array (
		'label'																							=> 'ID '.Config::$localStrings['categoria'],
		'requiredd'																					=> true,
		'type'																							=>'int|8',
		'defValue'																					=> '0',
		'validate'																					=> 'int'
	),
	'id_type'																							=> array(
		'label'																							=> Config::$localStrings['tipo'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'defValue'																					=> '0',
		'validate'																					=> 'int'
	),
	'ragione_sociale'                          		        => array(
		'label'																							=> Config::$localStrings['ragione sociale'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|255',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['ragione sociale']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['ragione sociale']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	 'name'                                    		        => array(
		'label'																							=> Config::$localStrings['nome'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
 	'surname'                                         		=> array(
		'label'																							=> Config::$localStrings['cognome'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['cognome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['cognome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	 'telephone'                                       		=> array(
		'label'                                        			=> Core::$localStrings['telefono'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|20',
		'validate'									    										=> 'telephonenumber|maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['numero di telefono']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['telefono']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	'email'                                           		=> array(
		'label'                                        			=> Core::$localStrings['email'],
		'searchTable'                                  			=> true,
		'requiredd'                                     			=> true,
		'type'                                         			=> 'varchar|255',
		'defValue'                                     			=> '',
		'validate'                                     			=> 'isemail|maxchars',
		'sanitize'																					=> 'email',
		'error message'                                			=> preg_replace('/%ITEM%/',Core::$localStrings['email'],Config::$localStrings['Devi inserire una %ITEM%!']),
		'error validation message'                     			=> preg_replace('/%ITEM%/',Core::$localStrings['email'],Config::$localStrings['Il valore per il campo %ITEM% non è stato validato!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['email']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
 	),
	 'mobile'                                          		=> array(
		'label'                                        			=> Core::$localStrings['mobile'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> false,
		'type'																							=> 'varchar|20',
		'validate'									    										=> 'telephonenumber|maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['mobile']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['mobile']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['mobile']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
 	'fax'                                             		=> array(
		'label'                                        			=> Core::$localStrings['fax'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> false,
		'type'																							=> 'varchar|20',
		'validate'									    										=> 'telephonenumber|maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['fax']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['fax']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['fax']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	 'partita_iva'			                                    => array(
		'label'                                        			=> Core::$localStrings['partita IVA'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['partita IVA']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['partita IVA']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'codice_fiscale'                                      => array(
		'label'                                        			=> Core::$localStrings['codice fiscale'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|50',
		'validate'								        									=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice fiscale']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice fiscale']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'zip_code'                                      		  => array(
		'label'                                     			  => Core::$localStrings['c.a.p.'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',10,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|10',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'int',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['c.a.p.']),10),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['c.a.p.']),10),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
	'street'                                        		  => array(
		'label'																							=> Config::$localStrings['indirizzo'],
		'labelsubtext'																			=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'																				=> true,
		'requiredd'																					=> true,
		'type'																							=> 'varchar|100',
		'validate'																					=> 'maxchars',
		'sanitize'																					=> 'string',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['indirizzo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['indirizzo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	 ),
	 'location_comuni_id'                          		    => array(
		'label'                          								    => Core::$localStrings['comune'],
		'searchTable'                          			        => false,
		'requiredd'                             			        => false, 
		'type'                                    			    => 'int|10',
		'defValue'																			    => 0
 	),
 	'comune_alt'                                      		=> array(
		'label'                                       			=> Core::$localStrings['altro comune'],
		'searchTable'                                  			=> false,
		'requiredd'                                     			=> false,
		'sanitize'																					=> 'string',
		'type'                                         			=> 'varchar|255',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altro comune']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altro comune']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
 	),
 	'location_province_id'                            		=> array(
		'label'                                        			=> Core::$localStrings['provincia'],
		'searchTable'                                  			=> false,
		'requiredd'                                     			=> true,
		'type'                                         			=> 'int|10',
		'defValue'                                     			=> 0
	),
	'provincia_alt'                                   		=> array(
		'label'                                      				=> Core::$localStrings['altra provincia'],
		'searchTable'                                  			=> true,
		'requiredd'                                     			=> false,
		'type'                                         			=> 'varchar|255',
		'sanitize'																					=> 'string',
		'defValue'                                     			=> '',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altra provincia']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['altra provincia']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'location_nations_id'                         			  => array(
		'label'                                        			=> Core::$localStrings['nazione'],
		'searchTable'                                  			=> false,
		'requiredd'                                     			=> false,
		'type'                                         			=> 'int|10',
		'defValue'                                   				=> 0
	),
	'pec'																									=> array (
		'label'																							=> 'PEC',
		'searchTable'																				=> true,
		'requiredd'																					=> false,
		'type'																							=> 'varchar|255'
	),
	'sid'																									=> array(
		'label'																							=> 'SID',
		'searchTable'																				=> true,
		'requiredd'																					=> false,
		'type'																							=> 'varchar|50'
	),
	'stampa_quantita'																			=> array(
		'label'																							=>'Stampa quantità',
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'stampa_unita'																				=> array(
		'label'																							=>'Stampa unità',
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),		
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);	
	
// thirdparty categories
$DatabaseTables['thirdparty_categories']['name'] = self::$dbTablePrefix.'thirdparty_categories';
$DatabaseTables['thirdparty_categories']['ordering'] = 'DESC';
$DatabaseTablesFields['thirdparty_categories']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'parent'																							=> array(
		'label'																							=> 'Parent',
		'required'																					=> false,
		'type'																							=>'int|8',
		'defValue'																					=> '0',
		'validate'																					=> 'int',
		'forcedValue'                   										=> '0'
	),
	'title'																								=> array(
		'label'																							=> Config::$localStrings['titolo'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|255'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'required'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);	
	
// thirdparty types
$DatabaseTables['thirdparty_types']['name'] = self::$dbTablePrefix.'thirdparty_types';

// estimates
$DatabaseTables['estimates']['name'] = self::$dbTablePrefix.'estimates';
$DatabaseTables['estimates']['ordering'] = 'DESC';
$DatabaseTablesFields['estimates']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
	),
	'thirdparty_id'																				=> array(
		'label'																							=> Config::$localStrings['cliente'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'customer'																						=> array(
		'label'																							=> Config::$localStrings['cliente'],
		'required'																					=> false,
		'type'																							=> 'text|65535',
		'defValue'																					=> '',
		'forcedValue'  																			=> ''
	),
	'dateins'																							=> array(
		'label'																							=> Config::$localStrings['data'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'date',
		'defValue'																					=> Config::$nowDate,
		'validate'																					=> 'datepicker',
		'convert'																						=> 'datepicker'
	),
	'datesca'																							=> array(
		'label'																							=> Config::$localStrings['data scadenza'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'date',
		'defValue'																					=> Config::$nowDate,
		'validate'																					=> 'datepicker',
		'convert'																						=> 'datepicker'
	),
	'note'																								=> array(
		'label'																							=> Config::$localStrings['note'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|255',
		'defValue'																					=> '')
	,
	'content'																							=> array(
		'label'																							=> Core::$localStrings['contenuto'],
		'searchTable'																				=> true,
		'type'																							=> 'longtext|65535',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																					=> ''
	),
	'tax'																									=> array(
		'label'																							=> Config::$localStrings['tassa'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|5',
		'defValue'																					=> '0'
	),
	'rivalsa'																							=> array(
		'label'																							=> 'Rivalsa',
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|2',
		'defValue'																					=> '0',
		'validate'																					=> 'int',
		'forcedValue'  																			=> '0'
	),
	'estimates_status_id'																	=> array(
		'label'																							=> Config::$localStrings['stato'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|4',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'estimates_result_status_id'													=> array(
		'label'																							=> Config::$localStrings['stato'].' '.Config::$localStrings['risultato'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|4',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'result_text'																					=> array(
		'label'																							=> Config::$localStrings['risultato'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'varchar|1000',
		'defValue'																					=> ''
	),
	'gain'																								=> array(
		'label'																							=> Config::$localStrings['guadagno'],
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'forcedValue'  																			=> '0.00',
		'validate'																					=> 'float'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);

// estimates articles
$DatabaseTables['estimates_articles']['name'] = self::$dbTablePrefix.'estimates_articles';
$DatabaseTables['estimates_articles']['ordering'] = 'DESC';
$DatabaseTablesFields['estimates_articles']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'requiredd'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'requiredd'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> '0'
	),
	'estimates_id'																				=> array(
		'label'																							=> Config::$localStrings['preventivo'],
		'searchTable'																				=> false,
		'required'																					=> true,
		'type'																							=> 'int|8',
		'defValue'																					=> '0',
		'validate'                                          => 'int',
		'forcedValue'  																			=> '0'
	),
	'content'																							=> array(
		'label'																							=> Core::$localStrings['contenuto'],
		'searchTable'																				=> true,
		'type'																							=> 'text|65535',
		'error message'																			=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'													=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																					=> ''
	),
	'price_unity'																					=> array(
		'label'																							=> Config::$localStrings['prezzo unitario'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'forcedValue'  																			=> '0.00',
		'validate'																					=> 'float',
		'error message'																			=>  preg_replace('/%ITEM%/',Core::$localStrings['prezzo unitario'],Config::$localStrings['Devi inserire un %ITEM%!']),	
	),
	'price_tax'																						=> array(
		'label'																							=> Config::$localStrings['imponibile'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'forcedValue'  																			=> '0.00',
		'validate'																					=> 'float',
		'error message'																			=>  preg_replace('/%ITEM%/',Core::$localStrings['imponibile'],Config::$localStrings['Devi inserire un %ITEM%!']),
	),
	'price_total'																					=> array(
		'label'																							=> Config::$localStrings['prezzo totale'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'forcedValue'  																			=> '0.00',
		'validate'																					=> 'float',
		'error message'																			=>  preg_replace('/%ITEM%/',Core::$localStrings['prezzo totale'],Config::$localStrings['Devi inserire un %ITEM%!']),
	),
	'quantity'																						=> array(
		'label'																							=> Config::$localStrings['quantità'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'int',
		'defValue'																					=> '1',
		'validate'																					=> 'int|5'
	),
	'tax'																									=> array(
		'label'																							=> Config::$localStrings['tassa'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|5',
		'defValue'																					=> '0'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);


// estimates status
$DatabaseTables['estimates_status']['name'] = self::$dbTablePrefix.'estimates_status';

// estimates result status
$DatabaseTables['estimates_result_status']['name'] = self::$dbTablePrefix.'estimates_result_status';

// orders
$DatabaseTables['orders']['name'] = self::$dbTablePrefix.'orders';
$DatabaseTables['orders']['ordering'] = 'DESC';
$DatabaseTablesFields['orders']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
	),
	'dateins'																							=> array(
		'label'																							=> Config::$localStrings['data'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'date',
		'defValue'																					=> Config::$nowDate,
		'validate'																					=> 'datepicker',
		'convert'																						=> 'datepicker'
	),
	'number'																							=> array(
		'label'																							=> Config::$localStrings['numero'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|20',
		'defValue'																					=> ''
	),
	'number_year'																					=> array(
		'label'																							=> Config::$localStrings['anno'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|4',
		'defValue'																					=> '',
		'validate'																					=> 'int'
	),
	'note'																								=> array(
		'label'																							=> Config::$localStrings['Note (visibili in fattura)'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|255',
		'defValue'																				  =>''
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);
	
// order articles
$DatabaseTables['orders_articles']['name'] = self::$dbTablePrefix.'orders_articles';
$DatabaseTables['orders_articles']['ordering'] = 'DESC';
$DatabaseTablesFields['orders_articles']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
	),
	'orders_id'																						=> array(
		'label'																							=> 'ID '.Config::$localStrings['ordine'],
		'searchTable'																				=> false,
		'required'																					=> true,
		'type'																							=> 'int',
		'defValue'																					=> '0',
		'validate'																					=> 'int'
	),
	'content'																							=> array(
		'label'																							=> Config::$localStrings['contenuto'],
		'searchTable'																				=> false,
		'required'																					=> true,
		'type'																							=> 'text',
		'defValue'																					=> ''
	),
	'price_unity'																					=> array(
		'label'																							=> Config::$localStrings['prezzo unitario'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'validate'																					=> 'float'
	),
	'price_tax'																						=> array(
		'label'																							=> Config::$localStrings['imponibile'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'float',
		'defValue'																					=> '0.00',
		'validate'																					=> 'float'
	),
	'price_total'																					=> array(
		'label'																							=> Config::$localStrings['prezzo totale'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'float',
		'defValue'																					=> '0.00',
		'validate'																					=> 'float'
	),
	'quantity'																						=> array(
		'label'																							=> Config::$localStrings['quantità'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'int',
		'defValue'																					=> '22',
		'validate'																					=>'float|4,1'
	),	
	'tax'																									=> array(
		'label'																							=> Config::$localStrings['tassa'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar',
		'defValue'																					=> '22'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);

// warehouse configurzione
$DatabaseTables['warehouse_configuration']['name'] = self::$dbTablePrefix.'warehouse_configuration';
$DatabaseTables['warehouse_configuration']['ordering'] = 'DESC';
$DatabaseTablesFields['warehouse_configuration']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	)
);

// categorie
$DatabaseTables['warehouse_categories']['name'] = self::$dbTablePrefix.'warehouse_categories';
$DatabaseTables['warehouse_categories']['ordering'] = 'DESC';
$DatabaseTablesFields['warehouse_categories']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
	),
	'parent'																							=> array(
		'label'																							=> 'Parent',
		'searchTable'																				=> false,
		'required'																					=> false,
		'type'																							=> 'int|8',
		'defValue'																					=> 0
	),
	'title'																								=> array(
		'label'																							=> Config::$localStrings['titolo'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|255'
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)	
);

// prodotti
$DatabaseTables['warehouse_products']['name'] = self::$dbTablePrefix.'warehouse_products';
$DatabaseTables['warehouse_products']['ordering'] = 'DESC';
$DatabaseTablesFields['warehouse_products']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
	),
	'categories_id'																				=> array(
		'label'																							=> 'ID Cat',
		'required'																					=> false,
		'type'																							=>'int|8'
	),
	'price_unity'																					=> array(
		'label'																							=> Config::$localStrings['prezzo unitario'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'validate'																					=> 'float'
	),
	'tax'																									=> array(
		'label'																							=> Config::$localStrings['iva'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'float|10,2',
		'defValue'																					=> '0.00',
		'validate'																					=> 'float'
	),
	'filename'																						=> array(
		'label'																						  => Config::$localStrings['immagine'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|255'
	),
	'org_filename'																				=> array(
		'label'																							=> Config::$localStrings['nome file originale'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'varchar|255'
	),
	'title' 																							=> array(
		'label'																							=> Config::$localStrings['titolo'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|255',
		'defValue'																					=> ''
	),
	'content'																							=> array(
		'label'																							=> Config::$localStrings['descrizione'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'mediumtext',
		'defValue'																					=> ''
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)
);

/*
// attributi prodotto
$App->params->tables['proatypes'] = $App->params->tableRif.'products_attribute_types';

$App->params->tables['proa'] = $App->params->tableRif.'product_attributes';
$App->params->fields['proa'] = array (
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
'products_id'=>array('label'=>'ID Prodotto','required'=>false,'searchTable'=>false,'type'=>'int|8'),
'products_attribute_types_id'=>array('label'=>'ID Tipo Attributo','required'=>false,'searchTable'=>false,'type'=>'int|8'),
'code'=>array('label'=>Config::$localStrings['codice'],'required'=>false,'searchTable'=>false,'type'=>'varchar|100','defValue'=>''),
'value_string'=>array('label'=>Config::$localStrings['valore stringa'],'required'=>false,'searchTable'=>true,'type'=>'varchar|100','defValue'=>''),
'value_int'=>array('label'=>Config::$localStrings['valore intero'],'required'=>false,'searchTable'=>true,'type'=>'int|8','validate'=>'int','defValue'=>'0'),
'value_float'=>array('label'=>Config::$localStrings['valore float'],'required'=>false,'searchTable'=>true,'type'=>'float|10,2','validate'=>'float','defValue'=>'0.00'),
'value_type'=>array('label'=>Config::$localStrings['valore tipo'],'required'=>false,'searchTable'=>true,'type'=>'varchar|10','defValue'=>''),
'quantity'=>array('label'=>Config::$localStrings['quantità'],'required'=>false,'searchTable'=>true,'type'=>'int|8','validate'=>'int','defValue'=>'0'),
'active'																							=> array(
	'label'																							=> Config::$localStrings['attiva'],
	'requiredd'																					=> false,
	'type'																							=> 'int|1',
	'validate'																					=> 'int',
	'defValue'																					=> '0',
	'forcedValue'  																			=> 1
)	
);
*/

// calendar
$DatabaseTables['calendar']['name'] = self::$dbTablePrefix.'calendar';
$DatabaseTables['calendar']['ordering'] = 'DESC';
$DatabaseTablesFields['calendar']  = array(
	'id'																									=> array(
		'label'																							=> 'ID',
		'required'																					=> false,
		'type'																							=> 'int|8',
		'autoinc'																						=> true,
		'nodb'																							=> true,
		'primary'																						=> true
	),
	'users_id'																						=> array(
		'label'																							=> Config::$localStrings['proprietario'],
		'required'																					=> false,
		'type'																							=> 'int|8',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
	),

	'datetimeins'																					=> array(
		'label'																							=> Config::$localStrings['data'],
		'searchTable'																				=> false,
		'required'																					=> true,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimepicker',
		'convert'																						=> 'datetimepicker',
		'error message'																			=> Config::$localStrings['Devi inserire una data valida!']
	),

	'title' 																							=> array(
		'label'																							=> Config::$localStrings['titolo'],
		'searchTable'																				=> true,
		'required'																					=> true,
		'type'																							=> 'varchar|255',
		'defValue'																					=> ''
	),
	'content'																							=> array(
		'label'																							=> Config::$localStrings['descrizione'],
		'searchTable'																				=> true,
		'required'																					=> false,
		'type'																							=> 'mediumtext|16777215',
		'defValue'																					=> ''
	),
	'created'																							=> array(
		'label'																							=> Config::$localStrings['creazione'],
		'searchTable'																				=> false,
		'requiredd'																					=> false,
		'type'																							=> 'datatime',
		'defValue'																					=> Config::$nowDateTime,
		'validate'																					=> 'datetimeiso',
		'forcedValue'                   										=> Config::$nowDateTime
	),
	'active'																							=> array(
		'label'																							=> Config::$localStrings['attiva'],
		'requiredd'																					=> false,
		'type'																							=> 'int|1',
		'validate'																					=> 'int',
		'defValue'																					=> '0',
		'forcedValue'  																			=> 1
	)
);


?>