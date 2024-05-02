$(() => {   
        $.ajax({
            type: "GET",
            url: "../backend/serviceHandler.php",
            data: {method: "queryAppointments"},
            dataType: "json",
            cache: false,
            success: function (response) {
                displayAppointments(response, () =>{
                    handleForms();
                });

               // Hier warten wir darauf, dass die displayAppointments-Funktion vollständig ausgeführt wird,
               // da sie asynchron ist, bevor wir die Formulare abrufen und bearbeiten.
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
function createAppoinmentTemplate(id, title, creator, numberParticipants, dateOptions, votesResult="") {
   //ass input type="hidden" value="vote[appointment]"
    const templateTable = `
    <div class="row my-4" >
    <form method="post" data-appointment-id="${id}">
        <div class="table-responsive">
    <table class="table table-hover table-bordered table caption-top">
    <caption class="h3">${title}</caption>
            <thead>
            <tr>
            <th>${creator}</th>
            ${dateOptions}
            </tr>
            <tr>
            <th>${numberParticipants}</th>
            ${votesResult}
            </tr>
        </thead>
        <tbody>
        <tr>
        <td>
            <div class = "d-flex justify-content-center">
                <i class="bi bi-person-circle text-white"></i>
                <label><input type="text" placeholder="Your name" required name=vote[user]></label>
            </div>
        </td>
        </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea class="form-control" id="comment" name="vote[comment]" rows="3" placeholder="Add comment"></textarea>
            </div>
        </div>
        <div class="row">
        <div class="col">
        <button type="submit" class="btn btn-primary">Send</button>
        </div>
        </div>
        </form>
    </form></div>`;
    return templateTable;

};
function createCheckBoxesTemplate(optionNumber){
    //value="${date_option_id}"
    return `
    <td>
        <div class="form-check">
            <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="vote[${optionNumber}]" value="true">
            </label>
        </div>                                  
    </td>`;
};
function renderApp(objApp) {
    const numberParticipants = 4;
    const {id, title, descr, location, duration, creator, dateOptions} = objApp;
    const numberOfDateOptions = dateOptions.length;
    const datesOptionsTemplate = renderDatesOptions(dateOptions);
    const templateTable = createAppoinmentTemplate(id, title, creator, numberParticipants, datesOptionsTemplate);
   $('#appointments').append(templateTable);
   for (i = 1; i <= numberOfDateOptions; i++){
    $(`[data-appointment-id="${id}"] tbody tr`).append(createCheckBoxesTemplate(i));
   }
   
};

function displayAppointments(appointments, callback) {
   appointments.forEach(objApp => {renderApp(objApp)});
    callback();
   
};

function handleForms() {
    $('form').submit((e)=>{
        e.preventDefault();
        const target = e.target;
        const appointmentId = target.dataset.appointmentId;
        const formData = new FormData(e.target);
        formData.set("vote[appId]", appointmentId);
        const dataObject = Object.fromEntries(formData);
        //console.log(dataObject);
        $.ajax({
            type: "POST",
            url: "../backend/serviceHandler.php?method=sendVote",
            data: dataObject,
            //contentType: "application/json", // Specify JSON content type
            success: function(response) {
                console.log("Success:", response);
                // Additional actions upon successful form submission
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error in submitting form!", textStatus, errorThrown);
            }
        });
    });
}


