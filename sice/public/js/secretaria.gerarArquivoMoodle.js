$("#form").submit(function () {
    window.open(this.action + "?" + $(this).serialize(), 'download');

    location.href = baseUrl + "/index.php/secretaria/turma/limpar";

    return false;
});

$("#notificarCursistas").click(function () {
    var link = baseUrl + "/index.php/secretaria/gerararquivomoodle/enviar-email/NU_SEQ_TURMA/" + $("#NU_SEQ_TURMA").val();
    location.href = link;
    return false;
});