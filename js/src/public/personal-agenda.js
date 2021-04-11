document.addEventListener('DOMContentLoaded', () => {

    const schedules = document.querySelectorAll('.sympose-schedule.event');
    let savedSessions = [];

    // Get current saved sessions
    manage_agenda().then((result) => {
        if (result.status === 200) {
            savedSessions = result.data;

            // Check if anything is stored.
            let localSavedSessions = localStorage.getItem('_sympose_saved_sessions');

            if (localSavedSessions !== null) {
                localSavedSessions = JSON.parse(localSavedSessions);
                if (localSavedSessions.length > 0) {
                    localSavedSessions.map((local_event) => {
                        savedSessions.map((event) => {
                            if (event.id === local_event.id) {
                                local_event.sessions.map((sessionID) => {
                                    if (!event.sessions.includes(sessionID)) {
                                        event.sessions.push(sessionID);
                                    }
                                })
                            }
                        })
                    });
                }
            }

            localStorage.setItem('_sympose_saved_sessions', JSON.stringify(savedSessions));
            schedules.forEach((schedule) => {
                setFavorites(schedule, savedSessions);
            });
        }
    });

    schedules.forEach((schedule) => {

        // Show stars
        savedSessions = localStorage.getItem('_sympose_saved_sessions');

        if (savedSessions === null || savedSessions === '') {
            savedSessions = [];
        } else {
            savedSessions = JSON.parse(savedSessions);
            if (savedSessions === null || savedSessions === '') {
                savedSessions = [];
            }
        }

        // Check local storage, add session favorites.
        setFavorites(schedule, savedSessions);

        schedule.addEventListener('click', (e) => {

            // Maybe trigger show/hide
            if (e.target.closest('.saved-sessions-control')) {
                // Check if has content
                if (schedule.querySelectorAll('.session-row[data-state="on"]').length > 0) {
                    schedule.dataset.showFavorites = (schedule.dataset.showFavorites === 'true' ? 'false' : 'true');
                }
            } else {

                if (schedule.closest('.sympose-schedule').classList.contains('updating')) {
                    return;
                }

                // Save session
                let target = e.target.closest('.session-saved');
                if (target !== null) {

                    let eventID = target.closest('table').dataset.id;
                    let newState = 'off';
                    if (!target.parentNode.dataset.state || target.parentNode.dataset.state === 'off') {
                        newState = 'on';
                    }
                    let sessionID = target.parentNode.dataset.id;

                    let savedSessions = localStorage.getItem('_sympose_saved_sessions');

                    if (savedSessions !== null) {
                        savedSessions = JSON.parse(savedSessions);
                    } else {
                        savedSessions = [];
                    }

                    let eventExists = false

                    savedSessions.map((event) => {
                        if (event.id === eventID) {
                            eventExists = true;
                            if (!event.sessions.includes(sessionID)) {
                                event.sessions.push(sessionID);
                            } else {
                                event.sessions.pop(sessionID);
                            }
                        }
                    });

                    if (eventExists === false) {
                        savedSessions.push({
                            'id': eventID,
                            'sessions': [sessionID]
                        })
                    }


                    localStorage.setItem('_sympose_saved_sessions', JSON.stringify(savedSessions));

                    schedule.closest('.sympose-schedule').classList.add('updating');

                    manage_agenda({
                        'saved_sessions': savedSessions
                    }).then(() => {
                        schedule.closest('.sympose-schedule').classList.remove('updating');
                        target.parentNode.dataset.state = newState;
                    })
                }
            }
        });
    });




});

function manage_agenda(body = {}) {
    let headers = {
        'X-WP-Nonce': wpApiSettings.nonce,
    };

    return fetch(wpApiSettings.root + 'sympose/v1/update_agenda_sessions', {
        method: "POST",
        headers: headers,
        body: JSON.stringify(body)
    })
        .then((response) => {
            return response.json();
        })
        .then((result) => {
            return result;
        });
}

function setFavorites(schedule, savedSessions) {
    savedSessions.map((event) => {
        if (schedule.dataset.id === event.id) {
            event.sessions.map((session_id) => {
                let session = schedule.querySelector('.session-row[data-id="' + session_id + '"]');
                if (session !== null) {
                    let state = session.querySelector('.session-saved');
                    state.parentNode.dataset.state = 'on';
                }
            });
        }
    });
}