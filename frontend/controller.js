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
    return datesStringParts.join();
}
function renderApp(objApp) {
    const numberParticipants = 4;
    const {title, descr, location, duration, creator, dateOptions} = objApp;
    const datesOptions = renderDatesOptions(dateOptions);
    const templateTable = `
    <div class="row">
    <form>
        <div class="table-responsive">
    <table class="table table-hover table-bordered table caption-top">
    <caption id="title" class="h3">${title}</caption>
        <thead>
            <tr>
            <th></th>
            ${datesOptions}
            </tr>
            <tr>
            <th>${numberParticipants}</th>
            </tr>
        </thead>
        <tbody >
            
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
    /*
    $('#title').text(title);
    $('#descr').text(descr);
    $('#location').text(location);
    $('#duration').text(duration);
    $('#creator').text(creator);
    */
   $('#appointments').append(templateTable);
};

function displayAppointments(appointments) {
   
    appointments.forEach(objApp => {renderApp(objApp)});
    //renderApp(appointments[0])
};
