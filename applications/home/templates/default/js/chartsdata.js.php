<!-- home/chartsdata.js.php v.1.2.0. 16/01/2020 -->
<script language="javascript">
$(function() {

   
    
     Morris.Bar({
        element: 'invoices-bilancio-fiscale-12-chart',
        data: [{{ App.invoices_bilancio_fiscale_12 }}],
        xkey: 'y',
        ykeys: ['v','a','u'],
        labels: ['{{ LocalStrings['vendite']|capitalize }}','{{ LocalStrings['acquisti']|capitalize }}','{{ LocalStrings['utile']|capitalize }}'],
        barColors: ['#428bca', '#d9534f','#5cb85c'],
        hideHover: 'auto',
        resize: true
    });
    
    
    Morris.Bar({
        element: 'invoices-fiscale-anno-precedente-chart',
        data: [{{ App.ricaviannoprecedentechartsdata }}],
        xkey: 'y',
        ykeys: ['r','ril','rin','t','u'],
        labels: [
        	'Ricavi',
        	'Imponibile lordo {{ App.coefficienteRedditivita|number_format(2, '.', ',') }}% CFR',
        	'Imponibile netto {{ App.impostaInps|number_format(2, '.', ',') }}% INPS',       	
        	'{{ LocalStrings['tasse']|capitalize }}',
        	'{{ LocalStrings['utile']|capitalize }}'
        	],
        barColors: ['#428bca','#f0ad4e','#5cb85c','#d9534f','#5cb85c'],
        hideHover: 'auto',
        
        resize: true
    });
   
    Morris.Bar({
        element: 'invoices-fiscale-anno-corrente-chart',
        data: [{{ App.ricaviannocorrentechartsdata }}],
        xkey: 'y',
        ykeys: ['r','ril','rin','t','u'],
        labels: [
        	'Ricavi',
        	'Imponibile lordo {{ App.coefficienteRedditivita|number_format(2, '.', ',') }}% CFR',
        	'Imponibile netto {{ App.impostaInps|number_format(2, '.', ',') }}% INPS',       	
        	'{{ LocalStrings['tasse']|capitalize }}',
        	'{{ LocalStrings['utile']|capitalize }}'
        	],
        barColors: ['#428bca','#f0ad4e','#5cb85c','#d9534f','#5cb85c'],
        hideHover: 'auto',
        resize: true
    });
   
    
});
</script>