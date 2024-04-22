$(() => {   
        $.ajax({
            type: "GET",
            url: "../backend/serviceHandler.php",
            data: {method: "queryAppointments"},
            dataType: "json",
            cache: false,
            success: function (response) {
                console.log(response);
            },
            error: function() {
                console.error("Error in ajax!");
            }            
        });

});

