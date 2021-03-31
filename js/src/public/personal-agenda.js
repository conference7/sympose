document.addEventListener('DOMContentLoaded', () => {

    const schedules = document.querySelectorAll('.sympose-schedule.event');

    const url = wpApiSettings.root + 'sympose/v1/update_agenda_sessions';

    schedules.forEach((schedule) => {

        // Show stars
        let savedSessions = localStorage.getItem('_sympose_saved_sessions');

        if (savedSessions === null) {
            savedSessions = {};
        } else {
            savedSessions = JSON.parse(savedSessions);
        }

        if (savedSessions[schedule.dataset.id] !== undefined) {
            Object.keys(savedSessions[schedule.dataset.id]).map((key_id) => {
                let session_id = savedSessions[schedule.dataset.id][key_id];
                let session = schedule.querySelector('.session-row[data-id="' + session_id + '"]');
                let state = session.querySelector('.session-saved');
                state.dataset.state = 'on';
                session.classList.add('is-favorite');
                schedule.dataset.starsHidden = false;
            })
        }

        // Save session
        schedule.addEventListener('click', (e) => {
            e.stopPropagation();

            if (e.target.closest('.saved-sessions-control')) {
                // Check if has content
                if (schedule.querySelectorAll('.session-row.is-favorite').length > 0) {
                    schedule.dataset.showFavorites = (schedule.dataset.showFavorites === 'true' ? 'false' : 'true');
                }
            }

            let target = e.target.closest('.session-saved');
            if (target !== null) {
                let eventID = target.closest('table').dataset.id;
                let newState = (target.dataset.state === 'off' ? 'on' : 'off');
                let sessionID = target.parentNode.dataset.id;

                target.dataset.state = newState;

                // Mark session as favorite
                if (newState === 'on') {
                    target.parentNode.classList.add('is-favorite');
                } else {
                    target.parentNode.classList.remove('is-favorite');
                }

                let headers = {
                    'X-WP-Nonce': wpApiSettings.nonce,
                };

                let savedSessions = localStorage.getItem('_sympose_saved_sessions');

                if (savedSessions === null) {
                    savedSessions = [];
                } else {
                    savedSessions = JSON.parse(savedSessions);
                }

                fetch(url, {
                    method: "POST",
                    headers: headers,
                    body: JSON.stringify({
                        'state': newState,
                        'id': sessionID,
                        'event_id': eventID,
                        'saved_sessions': savedSessions
                    })
                })
                    .then((response) => {
                        return response.json();
                    })
                    .then((result) => {

                        // Always update localStorage.
                        localStorage.setItem('_sympose_saved_sessions', JSON.stringify(result.data));

                        if (result.status === 200) {
                            // that's fine.
                        }
                    });
            }
        });
    });




});