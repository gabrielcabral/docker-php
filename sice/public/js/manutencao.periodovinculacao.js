//Script para fazer com que seja exibida mensagem de confirma��o ao se acionar o �cone de excluir
$(".icoExcluir").click(function(){
    var link = this;
    Dialog.confirm($(this).attr('mensagem'),'Confirma��o', function(r){
        if(r == true){
            location.href = link.href;
        }
    });
    return false;
});