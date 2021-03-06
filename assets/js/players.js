let requestHeaders = new Headers();
requestHeaders.append("X-Requested-With", "XMLHttpRequest");

$("#playGame").on('show.bs.modal', function () {
    // present players
    let event_id = $('.event-id').text();
    let registredPlayers = parseInt($('#registred-players').text());
    fetch('/manager/event/' + event_id + '/present', { method: "POST", headers: requestHeaders })
        .then(res => res.text())
        .then(result => {
            let presentPlayers = parseInt(result);
            $('#present-players').text(result);
            // alert if players are not all present
            $('#number-of-players-not-reached').show();
            if (registredPlayers === presentPlayers) {
                $('#number-of-players-not-reached').hide();
            }
        })
});