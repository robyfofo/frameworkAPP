<!-- estimates/formItem.tpl.php v.1.0.0. 26/07/2018 -->
<div class="row">
	<div class="col-md-3 new">
 	</div>
	<div class="col-md-7 help-small-form">
		{% if App.params.help_small is defined %}{{ App.params.help_small }}{% endif %}
	</div>
	<div class="col-md-2 help">
	</div>
</div>
<div class="row">
	<div class="col-lg-12">	
		<form id="applicationForm" class="form-horizontal" role="form" action="{{ URLSITE }}{{ CoreRequest.action }}/{{ App.methodForm }}"  enctype="multipart/form-data" method="post">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs">
				<li class="{% if App.tabActive == 1 %}active{% endif %}"><a href="#datibase-tab" data-toggle="tab">{{ LocalStrings['dati base']|capitalize }}</a></li>
				<li class=""><a href="#content-tab" data-toggle="tab">{{ LocalStrings['contenuto']|capitalize }}</a></li>
				{% if App.id > 0 %}
				<li class="{% if App.tabActive == 2 %}active{% endif %}"><a href="#articles-tab" data-toggle="tab">{{ LocalStrings['articoli associati']|capitalize }}</a></li>
				{% endif %}
				<li class=""><a href="#options-tab" data-toggle="tab">{{ LocalStrings['opzioni']|capitalize }}</a></li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">		
				<div class="tab-pane{% if App.tabActive == 1 %} active{% endif %}" id="datibase-tab">			
					<fieldset>
						<div class="form-group">
							<label for="dateinsID" class="col-md-2 control-label">{{ LocalStrings['data']|capitalize }}</label>
							<div class="col-md-5 input-group date" id="dateinsDPID">
								<input required type="text" name="dateins" class="form-control" placeholder="{{ LocalStrings['inserisci una data']|capitalize }}" id="dateinsID" value="">
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
			               </span>		    	
					    	</div>
						</div>
						<div class="form-group">
							<label for="datescaID" class="col-md-2 control-label">{{ LocalStrings['data scadenza']|capitalize }}</label>
							<div class="col-md-5 input-group date" id="datescaDPID">
								<input required type="text" name="datesca" class="form-control" placeholder="{{ LocalStrings['inserisci una data di scadenza']|capitalize }}" id="datescaID" value="">
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
			               </span>		    	
					    	</div>
						</div>
						<hr>
						<div class="form-group">
							<label for="nameID" class="col-md-2 control-label">{{ LocalStrings['cliente']|capitalize }}</label>
							<div class="col-md-5">
								<textarea name="customer" class="form-control" id="customerID" rows="5">{{ App.item.customer|e('html') }}</textarea>							    	
							</div>
							
							<div class="col-md-4">
								<label for="nameID" class="col-md-2 control-label">{{ LocalStrings['oppure'] }}</label>
								<select name="id_customer" id="id_customerID" class="form-control">
									<option value="0">{{ LocalStrings['non è cliente']|capitalize }}</option>
									{% if App.customers is iterable and App.customers|length > 0 %}
										{% for key,value in App.customers %}
											<option value="{{ value.id }}"{% if App.item.id_customer == value.id %} selected="selected"{% endif %}>{{ value.ragione_sociale|e('html') }}</option>														
										{% endfor %}
									{% endif %}
								</select>								    	
							</div>
						</div>
						<hr>
						<div class="form-group">
							<label for="noteID" class="col-md-2 control-label">{{ LocalStrings['Note (visibili in fattura)']|capitalize }}</label>
							<div class="col-md-7">
								<textarea name="note" class="form-control" id="noteID" rows="3">{{ App.item.note|e('html') }}</textarea>
					    	</div>
						</div>	
					</fieldset>					
				</div>
				
				<div class="tab-pane" id="content-tab">			
					<fieldset>
						<div class="form-group">
							<label for="content" class="col-md-2 control-label">{{ LocalStrings['contenuto']|capitalize }}</label>
							<div class="col-md-7">
								<textarea name="content" class="form-control" id="contentID" rows="10">{{ App.item.content|e('html') }}</textarea>
							</div>
						</div>
					</fieldset>					
				</div>

{% if App.id > 0 %}
<!-- sezione movimenti -->
				<div class="tab-pane{% if App.tabActive == 2 %} active{% endif %}" id="articles-tab">
					{% if App.item_articles is iterable and App.item_articles|length > 0 %}	
						<table class="table table-striped table-bordered table-hover listData">
							<thead>
								<tr>
									<th class="text-center">{{ LocalStrings['contenuto']|capitalize }}</th>
									<th class="text-center">{{ LocalStrings['prezzo unitario']|capitalize }}</th>
									<th class="text-center">{{ LocalStrings['quantità']|capitalize }}</th>
									<th class="text-center">{{ LocalStrings['prezzo totale']|capitalize }}</th>	
									<th class="text-center">{{ LocalStrings['iva']|capitalize }}</th>
									<th class="text-center">{{ LocalStrings['imponibile']|capitalize }}</th>
									<th class="text-center">{{ LocalStrings['totale']|capitalize }}</th>								
									<th></th>
								</tr>
							</thead>
							<tbody>				
								{% if App.item_articles is iterable and App.item_articles|length > 0 %}
									{% for key,value in App.item_articles %}
										<tr>
											<td>{{ value.content }}</td>
											<td class="text-right">{{ value.price_unity_label }}</td>
											<td class="text-center">{{ value.quantity }}</td>
											<td class="text-right">{{ value.price_total_label }}</td>
											<td class="text-center">{{ value.tax }}</td>								
											<td class="text-right">{{ value.price_tax_label }}</td>								
											<td class="text-right">{{ value.total_label }}</td>	
											<td class="actions">
												<a class="btn btn-default btn-circle modifyArtItem" data-id="{{ value.id }}" href="javascript:void(0);" title="{{ LocalStrings['modifica %ITEM%']|replace({'%ITEM%': LocalStrings['articolo']})|capitalize }}"><i class="fa fa-edit"> </i></a>
												<a class="btn btn-default btn-circle confirm" href="{{ URLSITE }}{{ CoreRequest.action }}/deleteArtItem/{{ App.id }}/{{ value.id }}" title="{{ LocalStrings['cancella %ITEM%']|replace({'%ITEM%': LocalStrings['articolo']})|capitalize }}"><i class="fa fa-cut"> </i></a>
											</td>							
										</tr>	
									{% endfor %}
								{% endif %}
							</tbody>
							<tfoot>
								<tr>
									<td class="text-right" colspan="3">{{ LocalStrings['totali']|capitalize }}</td>
									<td class="text-right">{{ App.item.art_tot_price_total_label }}</td>
									<td></td>								
									<td class="text-right">{{ App.item.art_tot_price_tax_label }}</td>								
									<td class="text-right">{{ App.item.art_tot_total_label }}</td>
									<td></td>
								</tr>
								{% if App.item.tax > 0 %}
								<tr>						
									<td class="text-right" colspan="6" style="border:0px;">{{ LocalStrings['tassa aggiuntiva']|capitalize }}</td>								
									<td class="text-right"><big><b>{{ App.item.invoiceTotalTax_label }}</b></big></td>
									<td></td>
								</tr>		
								{% endif %}
								{% if App.item.rivalsa > 0 %}
								<tr>						
									<td class="text-right" colspan="6" style="border:0px;">{{ App.company.text_rivalsa }}</td>								
									<td class="text-right"><big><b>{{ App.item.invoiceTotalRivalsa_label }}</b></big></td>
									<td style="border:0px;"></td>
								</tr>		
								{% endif %}
								<tr>
									<td class="text-right" colspan="6" style="border:0px;">{{ LocalStrings['totale']|capitalize}}</td>								
									<td class="text-right"><big><b>{{ App.item.invoiceTotal_label }}</b></big></td>
									<td style="border:0px;"></td>
								</tr>																		
							</tfoot>
						</table>	
					{% endif %}
					<div class="row">
						<div class="col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
							<div class="panel panel-info" id="articlePanelID">
					  			<div class="panel-heading" id="articlePanelHeadingID">
					    			<h3 class="panel-title" id="articlePanelTitleID">{{ LocalStrings['inserisci %ITEM%']|replace({'%ITEM%': LocalStrings['articolo']})|capitalize }}</h3>
					  			</div>
					  			<div class="panel-body form-inline">
										<div class="row">	
											<div class="col-xs-12">
												<textarea required name="art_content" id="art_contentID" class="form-control form-content"  rows="3">{{ LocalStrings['inserisci testo articolo']|capitalize }}</textarea>						
											</div>
										</div>
										<div class="row">	
					  						<div class="form-group">
					    						<label for="price_unityID">{{ LocalStrings['prezzo']|capitalize }}</label>
					    						<input type="text" class="form-control form-amount" name="art_price_unity" id="art_price_unityID" value="0.00">
					  						</div>
					  						<div class="form-group">
					    						<label for="quantityID">{{ LocalStrings['q.tà']|capitalize }}</label>
					    						<input type="text" class="form-control form-tax" name="art_quantity" id="art_quantityID" value="1">
					  						</div>
					  						<div class="form-group">
												<label for="price_totalID">{{ LocalStrings['totale']|capitalize }}</label>
												<input readonly="readonly" type="text" class="form-control form-amount" name="art_price_total" id="art_price_totalID" value="0.00">	
											</div>
											<div class="form-group">
												<label for="taxID">{{ LocalStrings['tassa']|capitalize }}</label>
												<input type="text" class="form-control form-tax" name="art_tax" id="art_taxID" value="0">
											</div>
											<div class="form-group">
												<label for="totalID">{{ LocalStrings['totale']|capitalize }}</label>
												<input readonly="readonly" type="text" class="form-control form-amount" name="art_total" id="art_totalID" value="0.00">
											</div>
										</div>
					
										<div class="row modalaction">					
											<div class="col-md-12 text-center">							
												<input type="hidden" name="id_article" id="id_articleID" value="0">
												<input type="hidden" name="artFormMode" id="artFormModeID" value="ins">
												<button type="submit" name="submitArtForm" id="submitArtFormID" value="submitArt" class="btn btn-primary btn-sm">{{ LocalStrings['aggiungi']|capitalize }}</button>
												<button type="button" name="resetArtForm" id="resetArtFormID" value="reset" class="pull-right btn btn-info btn-sm">{{ LocalStrings['resetta']|capitalize }}</button>
											</div>
										</div>
									
					  			</div>
							</div>
						</div>
						<!-- /.col-md-12 -->
					</div>				
								
				</div>			
<!-- /sezione movimenti -->
{% endif %}

<!-- sezione opzioni --> 
				<div class="tab-pane" id="options-tab">
					<fieldset>
						<div class="form-group">
							<label for="rivalsaID" class="col-md-2 control-label">{{ LocalStrings['rivalsa']|capitalize }} %</label>
							<div class="col-md-3">
								<input type="text" name="rivalsa" class="form-control" id="rivalsaID" value="{{ App.item.rivalsa|e('html') }}">
					    	</div>
						</div>	
						<div class="form-group">
							<label for="ivaID" class="col-md-2 control-label">{{ LocalStrings['tassa aggiuntiva']|capitalize }} %</label>
							<div class="col-md-3">
								<input type="text" name="iva" class="form-control" id="ivaID" placeholder="{{ LocalStrings['inserisci una tassa aggiuntiva']|capitalize }}"  value="{{ App.item.tax|e('html') }}">
					    	</div>
						</div>				
						<hr>
						<div class="form-group">
							<label for="activeID" class="col-md-2 control-label">{{ LocalStrings['attiva']|capitalize }}</label>
							<div class="col-md-7">
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" name="active" id="activeID"{% if App.item.active == 1 %} checked="checked"{% endif %} value="1">
									</label>
        						</div>
      					</div>
				  		</div>
					</fieldset>					
				</div>
<!-- /sezione opzioni -->		 
			</div>
			<!--/Tab panes -->			
			<hr>
			<div class="form-group">
				<div class="col-md-offset-2 col-md-7 col-xs-offset-2 col-xs-4 actionsform">
					<input type="hidden" name="id" id="idID" value="{{ App.id }}">
					<input type="hidden" name="method" value="{{ App.methodForm }}">
					<button type="submit" name="submitForm" value="submit" class="btn btn-primary submittheform">{{ LocalStrings['invia']|capitalize }}</button>
					{% if App.id > 0 %}
						<button type="submit" name="applyForm" value="apply" class="btn btn-primary submittheform">{{ LocalStrings['applica']|capitalize }}</button>
					{% endif %}
				</div>
				<div class="col-md-3 col-xs-offset-0 col-xs-6 actionsform">					
					<a href="{{ URLSITE }}{{ CoreRequest.action }}/listItem" title="{{ LocalStrings['torna alla lista']|capitalize }}" class="btn btn-success">{{ LocalStrings['indietro']|capitalize }}</a>
				</div>
			</div>
		</form>
	</div>
</div>