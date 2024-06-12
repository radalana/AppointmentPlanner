$(() => {
    // Abrufen der Termine über AJAX beim Laden der Seite
        $.ajax({
            type: "GET",
            url: "../backend/serviceHandler.php",
            data: {method: "queryAppointments"},
            dataType: "json",
            cache: false,
            success: function (response) {
            // Anzeigen der Termine und Einrichten der Formularbehandlung nach erfolgreichem Abrufen
                displayAppointments(response, () => {
                    handleForms();
                    
                });
            },
            error: function() {
                // Konsolenausgabe bei Fehler beim Abrufen der Termine
                console.error("Fehler beim Abrufen von Terminen!");
                
            }            
        });
        
});

/** Gibt ein Datum als String zurück */
function renderDatesOptions(dateOptions) {
    // Funktion zur Erstellung von Datums-HTML-Vorlagen für die Anzeige
    const datesStringParts = dateOptions.map((dateOptString) => {
        // Datum in ein lesbares Format konvertieren
        const date = new Date(dateOptString);
        const month= date.toLocaleDateString('eng-US', { month: 'short' });
        const weekDay = date.toLocaleDateString('eng-US', {weekday: 'short'});
        const day = date.getDate();
        const hours = date.getHours();
        const min = date.getMinutes();
        // HTML-Formatierung des Datums
        const dateCeilString = 
        `<th class="data"><div class="d-flex flex-column justify-content-center">
        <span class="month">${month}</span>
        <div class="day">${day}</div>
        <div class="week">${weekDay}</div>
        <div class="time">${hours}:${min.toString().padStart(2, '0')}</div>
        </div></th>`;
        return dateCeilString;
    });
     // Zusammenführen der Datums-HTML-Teile zu einem String
    const result = datesStringParts.join(" ");
    return result;
}

/** Erstellt die Vorlage für einen Termin */
function createAppoinmentTemplate(id, title, creator, dateOptions) {
    const templateTable = `
    <div class="row my-4">
    <form method="post" data-appointment-id="${id}">
        <div class="table-responsive-md">
    <table class="table table-hover table-bordered table caption-top">
    <caption class="h3">${title}</caption>
            <thead>
            <tr>
            <th>${creator}</th>
            ${dateOptions}
            </tr>
        </thead>
        <tbody>
        <tr>
        <td>
            <span class ="d-inline-flex justify-content-space-evenly">
                <i class="bi bi-person-circle text-white"></i>
                <label><input type="text" placeholder="Your name" required name=vote[user]></label>
            </span>
        </td>
        </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col">
            <div class="mb-3">
                <label class="form-label">Comment </label>
                <textarea class="form-control" name="vote[comment]" rows="3" placeholder="Add comment"></textarea>
                
            </div>
        </div>
        <div class="row">
        <div class="col">
        <button type="submit" class="btn">Send</button>
        </div>
        </div>
        </form>
    </form></div>`;
    return templateTable;

};

/** Erstellt die Vorlage für Checkboxen */
function createCheckBoxesTemplate(optionNumber){
    return `
    <td>
        <div class="form-check">
            <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="vote[${optionNumber}]" value="true">
            </label>
        </div>                                  
    </td>`;
};

/** Rendert die Abstimmungsergebnisse für Termine */
function renderAppointmentsVotionResult(usersVotes, numberOfDateOptions) {
    const usersVotesRows = usersVotes.map((userVote) =>{
        const nickname = userVote['user'];
        // HTML-Vorlage für die Anzeige der Abstimmungsergebnisse jedes Benutzers erstellen
        const userVoteResultsElement = $('<tr></tr>');
        userVoteResultsElement.append(`<td>${nickname}</td>`);
        for (i = 1; i <= numberOfDateOptions; i++) {
            const optionVote = userVote[`choice${i}`];
            const icon = optionVote == 1 ? "bi-check2" : "";
            const iconEl = $('<i class="bi"></i>').addClass(icon);
            userVoteResultsElement.append($(`<td></td>`).append(iconEl));
        }
        return userVoteResultsElement;
    });
    return usersVotesRows;
}

/** Deaktiviert abgelaufene Termine */
function disableExpiredApp(idApp, expireDateString){
    const currentDateTime = new Date();
    const expireDateTime = new Date(expireDateString);
    let activeClassCaption = "text-primary";
    let activeClassTr = "table-primary";
    let sendButtonClass = "btn-primary";
    const appEl = $(`[data-appointment-id="${idApp}"]`);
    if (currentDateTime > expireDateTime) {
         // Deaktivieren der Eingabefelder und Schaltflächen für abgelaufene Termine
        appEl.find('input, textarea, button').prop("disabled", "true");
        activeClassCaption = "text-muted";
        activeClassTr = "table-secondary";
        sendButtonClass = "btn-secondary";
    }
     // Hinzufügen von Klassen zur visuellen Kennzeichnung abgelaufener Termine
    appEl.find("caption").addClass(activeClassCaption);
    appEl.find("tbody tr:first").addClass(activeClassTr);
    appEl.find("button").addClass(sendButtonClass);
};

/** Rendert einen Termin */
function renderApp(objApp) {
    const {id, title, descr, location, duration, creator, dateOptions, expireDate, results} = objApp;
    // Datumsauswahl rendern
    const datesOptionsTemplate = renderDatesOptions(dateOptions);
    // Vorlage für den Termin erstellen
    const templateTable = createAppoinmentTemplate(id, title, creator, datesOptionsTemplate);
    const numberOfDateOptions = dateOptions.length;
    // Termin-Tabelle zur HTML hinzufügen
    $('#appointments').append(templateTable);
    // Checkboxen für jeden Termin-Tag hinzufügen
    for (i = 1; i <= numberOfDateOptions; i++){
    $(`[data-appointment-id="${id}"] tbody tr`).append(createCheckBoxesTemplate(i));
    }
    // Abgelaufene Termine deaktivieren
    disableExpiredApp(id, expireDate);
    // Abstimmungsergebnisse rendern
   const voteResultsRows = renderAppointmentsVotionResult(results, numberOfDateOptions);
   const tbody = $(`[data-appointment-id="${id}"] tbody`);
   voteResultsRows.forEach((row) => tbody.append(row);)
};

/** Anzeige von Terminen */
function displayAppointments(appointments, callback) {
   appointments.forEach(objApp => {renderApp(objApp)});
    callback();
   
};


/** Behandlung von Formularen */
function handleForms() {
    $('form').submit((e)=>{
        e.preventDefault();
        const target = e.target;
        const appointmentId = target.dataset.appointmentId;
        const formData = new FormData(e.target);
        formData.set("vote[appId]", appointmentId);
        const dataObject = Object.fromEntries(formData);
        // AJAX-Anfrage zum Senden von Stimmen
        $.ajax({
            type: "POST",
            url: "../backend/serviceHandler.php?method=sendVote",
            data: dataObject,
            success: function(response) {
                console.log("Success:", response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error in submitting form!", textStatus, errorThrown);
            }
        });
    });
}
