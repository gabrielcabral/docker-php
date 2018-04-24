//Script para fazer com que seja exibida mensagem de confirmação ao se acionar o ícone de excluir
$(".icoExcluir").click(function(){
    var link = this;
    Dialog.confirm($(this).attr('mensagem'),'Confirmação', function(r){
        if(r == true){
            location.href = link.href;
        }
    });
    return false;
});