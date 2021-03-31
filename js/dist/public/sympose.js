"use strict";

document.addEventListener('DOMContentLoaded', function () {
  var schedules = document.querySelectorAll('.sympose-schedule.event');
  var url = wpApiSettings.root + 'sympose/v1/update_agenda_sessions';
  schedules.forEach(function (schedule) {
    // Show stars
    var savedSessions = localStorage.getItem('_sympose_saved_sessions');

    if (savedSessions === null) {
      savedSessions = {};
    } else {
      savedSessions = JSON.parse(savedSessions);
    }

    if (savedSessions[schedule.dataset.id] !== undefined) {
      Object.keys(savedSessions[schedule.dataset.id]).map(function (key_id) {
        var session_id = savedSessions[schedule.dataset.id][key_id];
        var session = schedule.querySelector('.session-row[data-id="' + session_id + '"]');
        var state = session.querySelector('.session-saved');
        state.dataset.state = 'on';
        session.classList.add('is-favorite');
        schedule.dataset.starsHidden = false;
      });
    } // Save session


    schedule.addEventListener('click', function (e) {
      e.stopPropagation();

      if (e.target.closest('.saved-sessions-control')) {
        // Check if has content
        if (schedule.querySelectorAll('.session-row.is-favorite').length > 0) {
          schedule.dataset.showFavorites = schedule.dataset.showFavorites === 'true' ? 'false' : 'true';
        }
      }

      var target = e.target.closest('.session-saved');

      if (target !== null) {
        var eventID = target.closest('table').dataset.id;
        var newState = target.dataset.state === 'off' ? 'on' : 'off';
        var sessionID = target.parentNode.dataset.id;
        target.dataset.state = newState; // Mark session as favorite

        if (newState === 'on') {
          target.parentNode.classList.add('is-favorite');
        } else {
          target.parentNode.classList.remove('is-favorite');
        }

        var headers = {
          'X-WP-Nonce': wpApiSettings.nonce
        };

        var _savedSessions = localStorage.getItem('_sympose_saved_sessions');

        if (_savedSessions === null) {
          _savedSessions = [];
        } else {
          _savedSessions = JSON.parse(_savedSessions);
        }

        fetch(url, {
          method: "POST",
          headers: headers,
          body: JSON.stringify({
            'state': newState,
            'id': sessionID,
            'event_id': eventID,
            'saved_sessions': _savedSessions
          })
        }).then(function (response) {
          return response.json();
        }).then(function (result) {
          // Always update localStorage.
          localStorage.setItem('_sympose_saved_sessions', JSON.stringify(result.data));

          if (result.status === 200) {// that's fine.
          }
        });
      }
    });
  });
});
"use strict";