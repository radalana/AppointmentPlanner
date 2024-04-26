$(() => {   
        $.ajax({
            type: "GET",
            url: "../backend/serviceHandler.php",
            data: {method: "queryAppointments"},
            dataType: "json",
            cache: false,
            success: function (response) {
                $('#appointments').load('appointment.html', function() {
                   //console.log(response);
                    //console.log($('#appointments').html());
                    displayAppointments(response);
                    
                });
            },
            error: function() {
                console.error("Error in ajax!");
                
            }            
        });
});
function renderDates(dateOptions) {
    const dates = dateOptions.forEach((dateOptString) => {
        //console.log(dateOptString);
        const date = new Date(dateOptString);
        const month= date.toLocaleDateString('eng-US', { month: 'short' });
        
        const weekDay = date.toLocaleDateString('eng-US', {weekday: 'short'});
        const day = date.getDate();
        const hours = date.getHours();
        const min = date.getMinutes();
        //return {month, day, weekDay, hours, min};
        const dateCeil = $('<th class="data"></th>');
        dateCeil.html(`<div class="d-flex flex-column justify-content-center">
        <span class="month">${month}</span>
        <div class="day">${day}</div>
        <div class="week">${weekDay}</div>
        <div class="time">${hours}:${min.toString().padStart(2, '0')}</div>
        </div>`);
        console.log(dateCeil.html());
        $("thead tr:first").append(dateCeil);
    });
    //return dates;
}
function renderApp(objApp) {
    const {title, descr, location, duration, creator, dateOptions} = objApp;
    renderDates(dateOptions);
    
    $('#title').text(title);
    $('#descr').text(descr);
    $('#location').text(location);
    $('#duration').text(duration);
    $('#creator').text(creator);
};

function displayAppointments(appointments) {
    const template = `
    <table>
        <thead>
            <tr>
            </tr>
            <tr>
            </tr>
        </thead>
        <tbody>
            <tr><td>Title:</td><td>{{title}}</td></tr>
            <tr><td>Description:</td><td>{{descr}}</td></tr>
            <tr><td>Location:</td><td>{{location}}</td></tr>
            <tr><td>Duration:</td><td>{{duration}}</td></tr>
            <tr><td>Creator:</td><td>{{creator}}</td></tr>
        </tbody>
    </table>`;
    //appointments.forEach(objApp => {renderApp(objApp)});
    renderApp(appointments[0])
};
