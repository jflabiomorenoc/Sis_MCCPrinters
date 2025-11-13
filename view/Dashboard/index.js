$(document).ready(function(){

    $.post("../../controller/dashboard.php?op=obtener_estadisticas_dashboard", function (data) {
        data = JSON.parse(data);
    
        // Asignar los valores a los elementos del DOM
        $('#total-contratos').html(data.total_contratos);
        $('#contratos-vigentes').html(data.contratos_vigentes);
        $('#contratos-finalizados').html(data.contratos_finalizados);
        $('#contratos-suspendidos').html(data.contratos_suspendidos);
        
        $('#total-tickets').html(data.total_tickets);
        $('#tickets-pendientes').html(data.tickets_pendientes);
        $('#tickets-en-proceso').text(data.tickets_en_proceso);
        $('#tickets-resueltos').html(data.tickets_resueltos);
    });
});