
$(document).ready(function(){ 
    $("td.sinal").click(function(){ 
          var t = $(this).parent().find('td.sinal').text(); 
    
          if(t == '[-]') {
    		  $(this).text('[+]');
    		  $(this).parent().parent().find('tr.accordion').slideToggle("slow");  
    	  }
    	  else {
    		  $(this).text('[-]');
    		  $(this).parent().parent().find('tr.accordion').slideToggle("slow");  
    	  }
    }); 
}); 


