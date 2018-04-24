$("#GerarCertificadoTeste").on('click', function (e) {
    $('form').attr('action', $(this).data('url'));
    $('form').attr('target', '_blank');
    $('form').submit();
});

$("#confirmar").on('click', function (e) {
    $('form').attr('action', $(this).data('url'));
    $('form').removeAttr('target');
    $('form').submit();
});

$("#cancelar").on('click', function (){
    window.location = $(this).data('url');
});