$(function () {

    $("#searchResult").hide();

    $("#btn_Search").click(function (e) {
       loaddata($("#searchfield").val());
    });


    let data = null;
    
    function loaddata(searchterm) {
    
        console.log("Sending Ajax Request");
        data = null;    
    
        // see: https://api.jquery.com/jQuery.ajax/        
        $.ajax({
            type: "GET",
            url: "../serviceHandler.php",
            cache: false,
            data: {method: "queryPersonByName", param: searchterm},
            dataType: "json",
            success: function (response) {
                
                console.log("Ajax-Success", response);
                data = response;
    
                $.each(data, function(i, v) {
                    console.log(v.firstname + " " + v.lastname + ", " + v.email);
                });
    
                $("#noOfentries").val(response.length);
                $("#searchResult").show(1000).delay(1000).hide(1000);
            },
            error: function() {
                console.error("Error!");
            }            
        });
    
        // $.ajax is an asynchronous call ... these line are logged usually *before* "Ajax-Success"
        console.log("loaddata finished");
        console.log("Data (still null ... AJAX call not yet finished)", data);
    }

});

