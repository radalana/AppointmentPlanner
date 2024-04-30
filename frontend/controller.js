$(() => {   
        $.ajax({
            type: "GET",
            url: "../backend/serviceHandler.php",
            data: {method: "queryAppointments"},
            dataType: "json",
            cache: false,
            success: function (response) {
                /*
                $('#appointments').load('appointment.html', function() {
                   //console.log(response);
                    //console.log($('#appointments').html());
                    displayAppointments(response);
                    
                });
                */
               displayAppointments(response);
            },
            error: function() {
                console.error("Error in ajax!");
                
            }            
        });
});
/**return String */
function renderDatesOptions(dateOptions) {
    const datesStringParts = dateOptions.map((dateOptString) => {
        //console.log(dateOptString);
        const date = new Date(dateOptString);
        const month= date.toLocaleDateString('eng-US', { month: 'short' });
        
        const weekDay = date.toLocaleDateString('eng-US', {weekday: 'short'});
        const day = date.getDate();
        const hours = date.getHours();
        const min = date.getMinutes();
        const dateCeilString = 
        `<th class="data"><div class="d-flex flex-column justify-content-center">
        <span class="month">${month}</span>
        <div class="day">${day}</div>
        <div class="week">${weekDay}</div>
        <div class="time">${hours}:${min.toString().padStart(2, '0')}</div>
        </div></th>`;
        return dateCeilString;
    });
    const result = datesStringParts.join(" ");
    //console.log(result);
    return result;//datesStringParts.join();
}
function renderApp(objApp) {
    const numberParticipants = 4;
    const {id, title, descr, location, duration, creator, dateOptions} = objApp;
    const numberOfDateOptions = dateOptions.length;
    const datesOptions = renderDatesOptions(dateOptions);
    const checkBoxTemplate = `
    <td>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="">
            <label class="form-check-label" for="">
            </label>
        </div>                                  
    </td>`;
    const templateTable = `
    <div class="row"  data-id="${id}">
    <form method="post">
        <div class="table-responsive">
    <table class="table table-hover table-bordered table caption-top">
    <caption class="h3">${title}</caption>
            <thead>
            <tr>
            <th>${creator}</th>
            ${datesOptions}
            </tr>
            <tr>
            <th>${numberParticipants}</th>
            </tr>
        </thead>
        <tbody>
        <tr>
        <td>
            <div class = "d-flex justify-content-center">
                <i class="bi bi-person-circle text-white"></i>
                <label><input type="text" placeholder="Your name" name=user[name]></label>
            </div>
        </td>
        </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="comment" class="form-label">Kommentare</label>
                <textarea class="form-control" id="comment" rows="3" placeholder="Kommentar hinzufügen"></textarea>
            </div>
        </div>
    </form></div>`;
   $('#appointments').append(templateTable);
   for (i = 0; i < numberOfDateOptions; i++){
    $(`[data-id="${id}"] tbody tr`).append(checkBoxTemplate);
   }
   
};

function displayAppointments(appointments) {
   appointments.forEach(objApp => {renderApp(objApp)});
};
