"use strict";

document.addEventListener('DOMContentLoaded', function () {
  var schedules = document.querySelectorAll('.sympose-schedule.event');
  var savedSessions = []; // Get current saved sessions

  manage_agenda().then(function (result) {
    if (result.status === 200) {
      savedSessions = result.data; // Check if anything is stored.

      var localSavedSessions = localStorage.getItem('_sympose_saved_sessions');

      if (localSavedSessions !== null) {
        localSavedSessions = JSON.parse(localSavedSessions);

        if (localSavedSessions.length > 0) {
          localSavedSessions.map(function (local_event) {
            savedSessions.map(function (event) {
              if (event.id === local_event.id) {
                local_event.sessions.map(function (sessionID) {
                  if (!event.sessions.includes(sessionID)) {
                    event.sessions.push(sessionID);
                  }
                });
              }
            });
          });
        }
      }

      localStorage.setItem('_sympose_saved_sessions', JSON.stringify(savedSessions));
      schedules.forEach(function (schedule) {
        setFavorites(schedule, savedSessions);
      });
    }
  });
  schedules.forEach(function (schedule) {
    // Show stars
    savedSessions = localStorage.getItem('_sympose_saved_sessions');

    if (savedSessions === null || savedSessions === '') {
      savedSessions = [];
    } else {
      savedSessions = JSON.parse(savedSessions);

      if (savedSessions === null || savedSessions === '') {
        savedSessions = [];
      }
    } // Check local storage, add session favorites.


    setFavorites(schedule, savedSessions);
    schedule.addEventListener('click', function (e) {
      // Maybe trigger show/hide
      if (e.target.closest('.saved-sessions-control')) {
        // Check if has content
        if (schedule.querySelectorAll('.session-row[data-state="on"]').length > 0) {
          schedule.dataset.showFavorites = schedule.dataset.showFavorites === 'true' ? 'false' : 'true';
        }
      } else {
        if (schedule.closest('.sympose-schedule').classList.contains('updating')) {
          return;
        } // Save session


        var target = e.target.closest('.session-saved');

        if (target !== null) {
          var eventID = target.closest('table').dataset.id;
          var newState = 'off';

          if (!target.parentNode.dataset.state || target.parentNode.dataset.state === 'off') {
            newState = 'on';
          }

          var sessionID = target.parentNode.dataset.id;

          var _savedSessions = localStorage.getItem('_sympose_saved_sessions');

          if (_savedSessions !== null) {
            _savedSessions = JSON.parse(_savedSessions);
          } else {
            _savedSessions = [];
          }

          var eventExists = false;

          _savedSessions.map(function (event) {
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
            _savedSessions.push({
              'id': eventID,
              'sessions': [sessionID]
            });
          }

          localStorage.setItem('_sympose_saved_sessions', JSON.stringify(_savedSessions));
          schedule.closest('.sympose-schedule').classList.add('updating');
          manage_agenda({
            'saved_sessions': _savedSessions
          }).then(function () {
            schedule.closest('.sympose-schedule').classList.remove('updating');
            target.parentNode.dataset.state = newState;
          });
        }
      }
    });
  });
});

function manage_agenda() {
  var body = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var headers = {
    'X-WP-Nonce': wpApiSettings.nonce
  };
  return fetch(wpApiSettings.root + 'sympose/v1/update_agenda_sessions', {
    method: "POST",
    headers: headers,
    body: JSON.stringify(body)
  }).then(function (response) {
    return response.json();
  }).then(function (result) {
    return result;
  });
}

function setFavorites(schedule, savedSessions) {
  savedSessions.map(function (event) {
    if (schedule.dataset.id === event.id) {
      event.sessions.map(function (session_id) {
        var session = schedule.querySelector('.session-row[data-id="' + session_id + '"]');

        if (session !== null) {
          var state = session.querySelector('.session-saved');
          state.parentNode.dataset.state = 'on';
        }
      });
    }
  });
}
"use strict";