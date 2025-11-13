$(document).ready(function(){
    $("#inputuser").focus();
});

$("#formlogin").on("submit", function(e) {
    e.preventDefault();
    login();
});

function login() {
    let formData = new FormData($("#formlogin")[0]);
    const campos = [
        "#inputuser",
        "#inputpassword"
    ];

    // Validar campos vacíos
    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            getMessage("warning", "Complete todos los datos");
            $(campos[i]).focus();
            return false;
        }
    }

    // Deshabilitar botón submit para evitar múltiples envíos
    const $submitBtn = $("#formlogin input[type='submit']");
    $submitBtn.prop('disabled', true).val('Ingresando...');

    $.ajax({
        url: "controller/usuario.php?op=login",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',  // ✅ AGREGADO: Especificar que esperas JSON
        success: function (datos) {            
            if (datos.success == 1) {
                getMessage("success", "Bienvenido");
                
                // Redireccionar después de 500ms
                setTimeout(function() {
                    window.location.href = 'view/Dashboard/';
                }, 500);
            } else {
                getMessage("error", datos.mensaje || "Error al iniciar sesión");
                // Rehabilitar botón
                $submitBtn.prop('disabled', false).val('Ingresar');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Respuesta:', xhr.responseText);
            
            getMessage("error", "Error al conectar con el servidor");
            
            // Rehabilitar botón
            $submitBtn.prop('disabled', false).val('Ingresar');
        }
    });
}

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    }
});

function getMessage(pStrType, pSrtText){
    Toast.fire({
        icon: pStrType,
        title: pSrtText,
    });
}