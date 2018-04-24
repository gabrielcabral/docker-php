window.onload = function(){
	dataTablesObj = $('#tbBolsistas');
	th = $(dataTablesObj).find('thead th:first');
	th.removeAttr('class');
	th.html($('<input />').attr('type','checkbox').bind('click',function(){
	               val = $(this).attr('checked');
	               $(this).parent().unbind();
	               $(dataTablesObj).find('tbody input[type=checkbox]').attr('checked',(val == "checked" || val == true ? "checked" : false));
	}));  
};