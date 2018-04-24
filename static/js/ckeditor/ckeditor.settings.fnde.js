    jQuery(document).ready(function(){
        jQuery( 'textarea.editorHtml' ).ckeditor(function(){}, { toolbar :
                [
                ['Cut','Copy','Paste','PasteText','PasteFromWord'],
                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                ['Format','FontSize'],
                ['TextColor','BGColor'],
                ['NumberedList','BulletedList','-','Outdent','Indent'],
                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
                ['Maximize']
            ] , skin: "fnde"
        });
    });
