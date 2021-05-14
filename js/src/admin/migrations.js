document.addEventListener('DOMContentLoaded', () => {
    const startMigrationButton = document.querySelector('a[data-action="sympose-start-migration"]');

    if (startMigrationButton !== null) {
        startMigrationButton.addEventListener('click', () => {
            startMigrationButton.nextElementSibling.classList.add('is-active');
            startMigrationButton.setAttribute('disabled', true);

            let url = wpApiSettings.root + 'sympose/v1/migrate';
            fetch(url, {
                method: "POST",
                body: JSON.stringify({
                    version: startMigrationButton.dataset.version
                })
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {
                    if (result.status === 200) {
                        startMigrationButton.nextElementSibling.classList.remove('is-active');
                        startMigrationButton.setAttribute('disabled', false);
                        startMigrationButton.innerHTML = 'Done!';
                    }
                });

        });
    }

});