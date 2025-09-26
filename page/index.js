$(document).ready(function(){
    $("#inputuser").focus();
});

$("#formlogin").on("submit", function(e) {
    e.preventDefault();  // 🚫 Detiene el envío normal del form
    login();             // ✅ Llama a tu función login
});

function login() {

    let formData = new FormData($("#formlogin")[0]);
    const campos = [
      "#inputuser",
      "#inputpassword"
    ];

    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            getMessage("warning", "Complete todos los datos")

            $(campos[i]).focus();
            return false;
        }
    }

    $.ajax({
         url: "controller/usuario.php?op=login",
         type: "POST",
         data: formData,
         contentType: false,
         processData: false,
         success: function (datos) {
            console.log(datos); 
            if (datos.success == 1) {
                window.location='view/Dashboard/';
            } else {
                getMessage("error", datos.mensaje || "Error desconocido")
            }
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
        title:  pSrtText,
    });
}