<?php
$DatabaseTables = array();
$DatabaseTablesFields = array();


// nazioni
$DatabaseTables['location_nations']['name'] = self::$dbTablePrefix.'location_nations';
$DatabaseTables['location_nations']['ordering'] = 'ASC';
$DatabaseTablesFields['location_nations'] = array(
	'id'			=> array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'targa'                                   => array(
		'label'											=> Config::$localStrings['targa'],
		'labelsubtext'									=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'									=> true,
		'required'										=> true,
		'type'											=> 'varchar|5',
		'validate'										=> 'maxchars',
		'sanitize'										=> 'string',
		'error message'								=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['targa']),5),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'					=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['targa']),5),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'active'												=> array(
		'label'										   => Config::$localStrings['attiva'],
		'required'								      => false,
		'type'									      => 'int|1',
		'validate'			    				      => 'int',
		'defValue'								      => '0',
		'forcedValue'              				        => 1
	)
);		
foreach(Config::$globalSettings['languages'] AS $lang) {
	$required = ($lang == Config::$localStrings['user'] ? true : false);
	$DatabaseTablesFields['location_nations']['title_'.$lang] = array(

		'label'											=> Config::$localStrings['titolo'].' '.$lang,
		'labelsubtext'									=> preg_replace('/%NUMBER%/',2,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'									=> true,
		'required'										=> $required,
		'type'											=> 'varchar|255',
		'validate'										=> 'maxchars',
		'sanitize'										=> 'string',
		'error message'								=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['titolo'].' '.$lang),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'					=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['titolo'].' '.$lang),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	);
}

// province
$DatabaseTables['location_province']['name'] = self::$dbTablePrefix.'location_province';
$DatabaseTables['location_province']['ordering'] = 'ASC';
$DatabaseTablesFields['location_province'] = array(
	'id'			=> array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'nome'                                   => array(
		'label'											=> Config::$localStrings['nome'],
		'labelsubtext'									=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'									=> true,
		'required'										=> true,
		'type'											=> 'varchar|255',
		'validate'										=> 'maxchars',
		'sanitize'										=> 'string',
		'error message'								=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['targa']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'					=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['targa']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'targa'                                   => array(
		'label'											=> Config::$localStrings['targa'],
		'labelsubtext'									=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		'searchTable'									=> true,
		'required'										=> true,
		'type'											=> 'varchar|5',
		'validate'										=> 'maxchars',
		'sanitize'										=> 'string',
		'error message'								=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['targa']),5),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'					=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['targa']),5),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
  ),
	'active'									        => array(
		'label'									        => Config::$localStrings['attiva'],
		'required'								        => false,
		'type'									        => 'int|1',
		'validate'			    				        => 'int',
		'defValue'								        => '0',
		'forcedValue'              				        => 1
	)
);

// comuni
$DatabaseTables['location_comuni']['ordering'] = 'ASC';
$DatabaseTables['location_comuni']['name'] = self::$dbTablePrefix .'location_comuni';
$DatabaseTablesFields['location_comuni'] = array(
	'id'					=> array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'nome'					=> array(
        'label'=>Config::$localStrings['nome'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255','defValue'=>''),
	'cap'					=> array('label'=>Config::$localStrings['cap'],'searchTable'=>true,'required'=>true,'type'=>'varchar|50','defValue'=>''),

	'provincia'				=> array('label'=>Config::$localStrings['provincia'],'searchTable'=>false,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_province_id'	=> array('label'=>Config::$localStrings['provincia'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

	'nation'				=> array('label'=>Config::$localStrings['nazione'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_nations_id'	=> array('label'=>Config::$localStrings['nazione'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

	'active'									        								=> array(
		'label'									        								=> Config::$localStrings['attiva'],
		'required'								        							=> false,
		'type'									        								=> 'int|1',
		'validate'			    				        						=> 'int',
		'defValue'								       								=> '0',
		'forcedValue'              				        			=> 1
	)
);

// modules
$DatabaseTables['modules']['name'] = self::$dbTablePrefix . 'modules';
$DatabaseTables['modules']['ordering'] = 'ASC';
$DatabaseTablesFields['modules'] = array(
	'id'                                      				=> array(
		'label'                                					=> 'ID',
		'required'                             					=> false,
		'type'                                 					=> 'int|8',
		'autoinc'                            					  => true,
		'primary'                              					=> true,
  	),
	'name'																						=> array(
		'label'																					=> Core::$localStrings['nome'],
		'required'																			=> true,
		'searchTable'																		=> true,
		'type'																					=> 'varchar|255',
		'validate'                             					=> 'maxchars',
		'sanitize'																			=> 'string',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''
	),
	'label'																						=> array(
		'label'																					=> Core::$localStrings['etichetta'],
		'required'																			=> true,
		'searchTable'																		=> true,
		'type'																					=> 'varchar|255',
		'validate'                            					=> 'maxchars',
		'sanitize'																			=> 'string',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['etichetta']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['etichetta']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''
	),
	'alias'																						=> array(
		'label'																					=> Core::$localStrings['alias'],
		'required'																			=> true,
		'searchTable'																		=> true,
		'type'																					=> 'varchar|100',
		'validate'                             					=> 'maxchars',
		'sanitize'																			=> 'string',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['alias']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['alias']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''
	),
	'content'																					=> array(
		'label'																					=> Core::$localStrings['contenuto'],
		'searchTable'																		=> true,
		'type'																					=> 'text|65535',
		'validate'                             					=> 'maxchars',
		'sanitize'																			=> 'string',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['contenuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''
	),
	'code_menu'                               				=> array(
        'label'                              				=> Core::$localStrings['codice menu'],
        'searchTable'                        				=> true,
        'type'                               				=> 'text|65535',
        'validate'                           				=> 'json|maxchars',
		  'sanitize'																		=> 'json',
		  'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice menu']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
        'error validation message'									=> preg_replace('/%FIELD%/',ucfirst(Config::$localStrings['codice menu']),'il campo %FIELD% deve essere in formato json valido!').'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice menu']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		  'defValue'																		=> '{"name":"%NAME%","icon":"<i class=\"fa fa-cog\" ><\/i>","label":"%LABEL%"}'  
    ),
	'ordering'																				=> array(
		'label'																					=> Core::$localStrings['ordine'],
		'searchTable'																		=> false,
		'type'																					=> 'int|8',
		'defValue'																			=> '0'
	),
	'section'                                    			=> array(
		'label'																					=> Core::$localStrings['sezione'],
		'searchTable'																		=> false,
		'type'																					=> 'int|1'
	),
	'help_small'																			=> array(
		'label'																					=> Core::$localStrings['aiuto breve'],
		'searchTable'                             			=> false,
		'type'                                    			=> 'varchar|255',
		'validate'                             					=> 'maxchars',
		'sanitize'																			=> 'string',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['aiuto breve']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['codice menu']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''  
	),
	'help'																						=> array(
		'label'																					=> Core::$localStrings['aiuto'],
		'searchTable'																		=> false,
		'type'																					=> 'text|65535',
		'validate'                             					=> 'maxchars',
		'sanitize'																			=> 'editorhtml',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['aiuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['aiuto']),65535),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''  

	),	
	'active'																					=> array(
		'label'																					=> Config::$localStrings['attiva'],
		'required'																			=> false,
		'type'																					=> 'int|1',
		'validate'																			=> 'int',
		'defValue'																			=> '0',
		'forcedValue'  																	=> 1
	)
);

// levels
$DatabaseTables['levels']['name'] = self::$dbTablePrefix . 'levels';
$DatabaseTables['levels']['ordering'] = 'ASC';
$DatabaseTablesFields['levels'] = array(
	'id'                                      				=> array(
		'label'                                					=> 'ID',
		'required'                             					=> false,
		'type'                                 					=> 'int|8',
		'autoinc'                              					=> true,
		'primary'                              					=> true,
  	),
	'title'																						=> array(
		'label'																					=> Core::$localStrings['titolo'],
		'required'																			=> true,
		'searchTable'																		=> true,
		'type'																					=> 'varchar|100',
		'validate'                             					=> 'maxchars',
		'sanitize'																			=> 'string',
		'error message'																	=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['titolo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['titolo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		'defValue'																			=> ''
	),
	'active'																					=> array(
		'label'																					=> Config::$localStrings['attiva'],
		'required'																			=> false,
		'type'																					=> 'int|1',
		'validate'																			=> 'int',
		'defValue'																			=> '0',
		'forcedValue'  																	=> 1
	)
);

$DatabaseTables['modules_levels_access']['name'] = Config::$dbTablePrefix.'modules_levels_access';

// users
$DatabaseTables['users']['name'] = self::$dbTablePrefix.'users';
$DatabaseTables['users']['ordering'] = 'DESC';
$DatabaseTablesFields['users'] = array(
	'id'																							=> array(
		 'label'                                        => 'ID',
		 'required'                                     => false,
		 'type'                                         => 'autoinc',
		 'primary'                                      => true
	),
	'username'                                        => array(
		 'label'                                        => Config::$localStrings['nome utente'],
		 'searchTable'                                  => true, 
		 'required'                                     => true,
		 'type'                                         => 'varchar|255',
		 'validate'                                     => 'username|maxchars',
		 'error message'                                => preg_replace('/%ITEM%/',Core::$localStrings['nome utente'],Config::$localStrings['Devi inserire una %ITEM%!']),
		 'error validation message'                     => preg_replace('/%ITEM%/',Core::$localStrings['nome utente'],Config::$localStrings['Il valore per il campo %ITEM% non è stato validato!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['telefono']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'password'                             		        => array(
		 'label'                                       	=> self::$localStrings['password'],
		 'searchTable'                                 	=> false,
		 'required'                                    	=> true,
		 'type'                                        	=> 'password'
	),
	'name'                                            => array(
		 'label'																				=> Config::$localStrings['nome'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> true,
		 'type'																					=> 'varchar|50',
		 'validate'																			=> 'maxchars',
		 'valueRif'																			=> 50,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'surname'                                         => array(
		 'label'																				=> Config::$localStrings['cognome'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',50,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> true,
		 'type'																					=> 'varchar|50',
		 'validate'																			=> 'maxchars',
		 'valueRif'																			=> 50,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['cognome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['cognome']),50),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'street'                                          => array(
		 'label'																				=> Config::$localStrings['indirizzo'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> true,
		 'type'																					=> 'varchar|100',
		 'validate'																			=> 'maxchars',
		 'valueRif'																			=> 100,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['indirizzo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['indirizzo']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])

	),
	'location_comuni_id'                              => array(
		 'label'                                        => Core::$localStrings['comune'],
		 'searchTable'                                  => false,
		 'required'                                     => false, 
		 'type'                                         => 'int|10',
		 'defValue'                                     => 0
	),
	'comune_alt'                                      => array(
		 'label'                                        => Core::$localStrings['altro comune'],
		 'searchTable'                                  => false,
		 'required'                                     => false, 
		 'type'                                         => 'varchar|255'
	),
	'zip_code'                                        => array(
		 'label'                                        => Core::$localStrings['c.a.p.'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',10,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> true,
		 'type'																					=> 'varchar|10',
		 'validate'																			=> 'maxchars',
		 'valueRif'																			=> 10,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['c.a.p.']),10),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['c.a.p.']),10),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'location_province_id'                            => array(
		 'label'                                        => Core::$localStrings['provincia'],
		 'searchTable'                                  => false,
		 'required'                                     => true,
		 'type'                                         => 'int|10',
		 'defValue'                                     => 0
	),
	'provincia_alt'                                   => array(
		 'label'                                        => Core::$localStrings['altra provincia'],
		 'searchTable'                                  => true,
		 'required'                                     => false,
		 'type'                                         => 'varchar|255',
		 'defValue'                                     => ''
	),
	'location_nations_id'                             => array(
		 'label'                                        => Core::$localStrings['nazione'],
		 'searchTable'                                  => false,
		 'required'                                     => false,
		 'type'                                         => 'int|10',
		 'defValue'                                     => 0
	),
	'telephone'                                       => array(
		 'label'                                        => Core::$localStrings['telefono'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> true,
		 'type'																					=> 'varchar|20',
		 'validate'									    								=> 'telephonenumber|maxchars',
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['numero di telefono']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['telefono']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'email'                                           => array(
		 'label'                                        => Core::$localStrings['email'],
		 'searchTable'                                  => true,
		 'required'                                     => true,
		 'type'                                         => 'varchar|255',
		 'defValue'                                     => '',
		 'validate'                                     => 'isemail|maxchars',
		 'valueRif'																			=> 255,
		 'error message'                                => preg_replace('/%ITEM%/',Core::$localStrings['email'],Config::$localStrings['Devi inserire una %ITEM%!']),
		 'error validation message'                     => preg_replace('/%ITEM%/',Core::$localStrings['email'],Config::$localStrings['Il valore per il campo %ITEM% non è stato validato!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['email']),255),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
	),
	'mobile'                                          => array(
		 'label'                                        => Core::$localStrings['mobile'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> false,
		 'type'																					=> 'varchar|20',
		 'validate'									    								=> 'telephonenumber|maxchars',
		 'valueRif'																			=> 20,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['mobile']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['mobile']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['mobile']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'fax'                                             => array(
		 'label'                                        => Core::$localStrings['fax'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',20,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> false,
		 'type'																					=> 'varchar|20',
		 'validate'									    								=> 'telephonenumber|maxchars',
		 'valueRif'																			=> 20,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['fax']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace('/%ITEM%/',ucfirst(Config::$localStrings['fax']),Config::$localStrings['%ITEM% non valido!']).'|'.preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['fax']),20),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])

	),
	'skype'                                           => array(
		 'label'                                        => Core::$localStrings['skype'],
		 'labelsubtext'																	=> preg_replace('/%NUMBER%/',100,Config::$localStrings['massimo %NUMBER% caratteri']),
		 'searchTable'																	=> true,
		 'required'																			=> false,
		 'type'																					=> 'varchar|100',
		 'validate'								        							=> 'maxchars',
		 'valueRif'																			=> 100,
		 'error message'																=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!']),
		 'error validation message'											=> preg_replace(array('/%FIELD%/','/%NUMBER%/'),array(ucfirst(Config::$localStrings['nome']),100),Config::$localStrings['Il campo %FIELD% ha superato i %NUMBER% caratteri!'])
	),
	'avatar'                                          => array(
		 'label'                                        => Core::$localStrings['avatar'],
		 'searchTable'                                  => false,
		 'type'                                         => 'blob'
	),
	'avatar_info'                                     => array(
		 'label'                                        => Core::$localStrings['avatar info'],
		 'searchTable'                                  => false,
		 'type'                                         => 'varchar|255'
	),
	'levels_id'                                       => array(
		 'label'                                        => Core::$localStrings['livello'],
		 'searchTable'                                  => false,
		 'type'                                         => 'int|1'
	),
	'is_root'                                         => array(
		 'label'                                        => 'Root',
		 'searchTable'                                  => false,
		 'type'                                         => 'varchar',
		 'defValue'                                     => 0
	),
	'template'                                        => array(
		 'label'                                        => 'template',
		 'searchTable'                                  => false,
		 'type'                                         => 'varchar|100'
	),
	'hash'                      	                    => array(
		 'label'                                        => 'Hash',
		 'searchTable'                                  => false,
		 'type'                                         => 'varchar'
	),
  'created'																	        => array(
	  'label'									        								=> Config::$localStrings['creazione'],
	  'searchTable'							        							=> false,
	  'required'								        							=> false,
	  'type'									        								=> 'datatime',
	  'defValue'								        							=> Config::$nowDateTime,
	  'validate'								        							=> 'datetimeiso',
	  'forcedValue'              				        			=> self::$nowDateTime
  ),
  'active'									       									=> array(
	  'label'									       									=> Config::$localStrings['attiva'],
	  'required'								        							=> false,
	  'type'									        								=> 'int|1',
	  'validate'			    				        						=> 'int',
	  'defValue'								        							=> '0',
	  'forcedValue'              				        			=> 1
  )
);

?>