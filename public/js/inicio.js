$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


var modalBs = $('#modalBs');
var modalBsContent = $('#modalBs').find(".modal-content");

$(function(){
    handleAjaxModal();
    envttohs();

    $(document).on('mouseover','table a[data-modal]', function(){
        handleAjaxModal();
    })
});




function handleAjaxModal() {
    // Limpia los eventos asociados para elementos ya existentes, asi evita duplicación
    $("a[data-modal]").unbind("click");
    // Evita cachear las transaccione Ajax previas
    $.ajaxSetup({ cache: false });

    // Configura evento del link para aquellos para los que desean abrir popups
    $("a[data-modal]").on("click", function (e) {
        var dataModalValue = $(this).data("modal");

        modalBsContent.load(this.href, function (response, status, xhr) {
            switch (status) {
                case "success":
                    modalBs.modal({ backdrop: 'static', keyboard: false }, 'show');

                    if (dataModalValue == "modal-lg") {
                        modalBs.find(".modal-dialog").addClass("modal-lg");
                    } else {
                        modalBs.find(".modal-dialog").removeClass("modal-lg");
                    }

                    break;

                case "error":
                    var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                    if (xhr.status == 403) {
                        swal(
                            'Error!!',
                            'No tiene permisos para realizar esta acción',
                            'error'
                        )
                    }
                    else {
                        swal(
                            'Error!!',
                            message,
                            'error'
                        )
                    }
                    break;
            }

        });
        return false;
    });
}


function EventoFormularioModal(modal, onSuccess) {
    modal.find('form').submit(function () {
        $.ajax({
            url: this.action,
            type: this.method,
            data: $(this).serialize(),
            success: function (result) {
                onSuccess(result);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var message = "Error de ejecución: " + textStatus + " " + errorThrown;
                $.msgbox(message, { type: 'error' });
                console.log("Error: ");
            }
        });
        return false;
    });
}

jQuery.fn.reset = function () {
    $(this).each (function() { this.reset(); });
}

function validarNumeros($texto)
{
    var filter6=/^[0-9\s\xF1\xD1]+$/;
    if (filter6.test($texto)){
        return true;
    }
    else{
        return false;
    }
}
function validarTexto($texto){
    var filter6=/^[A-Za-z\ \s\xF1\xD1]+$/;
    if (filter6.test($texto)){
        return true;
    }
    else{
        return false;
    }
}

function justNumbers(e)
{
    var keynum = window.event ? window.event.keyCode : e.which;
    if (keynum == 8)
        return true;
    return /\d/.test(String.fromCharCode(keynum));
}

function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
    switch(event.type)
    {
        case "touchstart": type = "mousedown"; break;
        case "touchmove":  type="mousemove"; break;
        case "touchend":   type="mouseup"; break;
        default: return;
    }

    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1,
        first.screenX, first.screenY,
        first.clientX, first.clientY, false,
        false, false, false, 0/*left*/, null);
    first.target.dispatchEvent(simulatedEvent);
    // event.preventDefault();
}

function enmascarar(data) {
    valor = data.split('.');
    num = valor[0];
    if(valor[0].length>3){
        num = valor[0].split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
        num = num.split('').reverse().join('');
        pos = num.indexOf('.');
        if(pos==0){
            num = num.substring(1, num.length);
        }
    }
    if(valor[1]!= undefined){
        num = num+','+valor[1];
    }else{
        num = num+',00';
    }

    return num;
}

function envttohs()
{
    document.addEventListener("touchstart", touchHandler, true);
    document.addEventListener("touchmove", touchHandler, true);
    document.addEventListener("touchend", touchHandler, true);
    document.addEventListener("touchcancel", touchHandler, true);
}
