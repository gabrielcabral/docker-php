$('#filter_todos').click(function(){
    var grupo = $('form :checkbox');
    var i = 0;

    while (i < grupo.length) {
        grupo[i].checked = this.checked;
        i++;
    }
});