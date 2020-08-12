document.addEventListener('DOMContentLoaded', () => {
    const setupWizard = document.querySelector('.sympose-setup-wizard');

    if (setupWizard !== null) {

        resizeSetupWizard();

        // Show
        setupWizard.style.display = 'flex';
        const buttons = setupWizard.querySelectorAll('ul.buttons li a');
        const notices = setupWizard.querySelector('.sympose-setup-wizard-notices');

        buttons.forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();

                let action = e.target.dataset.action;

                let headers = {
                    'X-WP-Nonce': wpApiSettings.nonce,
                };

                let url = '';

                let data = '';

                // Sample Data

                if (action === 'setup-sample-data') {
                    button.parentNode.querySelector('.spinner').style.display = 'block';
                    button.parentNode.querySelector('.spinner').classList.add('is-active');
                    url = wpApiSettings.root + 'sympose/v1/generate_sample_data';
                }

                if (action === 'skip-step') {
                    url = wpApiSettings.root + 'sympose/v1/quick_start_event';
                    data = {
                        action: action
                    }
                    setupWizard.style.display = 'none';
                }

                if (action === 'quick-start') {
                    setupWizard.querySelector('.wizard-content[data-type=quick-start]').style.display = 'block';
                    setupWizard.querySelector('.wizard-content[data-type=introduction]').style.display = 'none';
                }

                fetch(url, {
                    method: "POST",
                    headers: headers,
                    body: JSON.stringify(data)
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (result) {
                        if (result.status === 200) {
                            const spinner = button.parentNode.querySelector('.spinner');

                            if (spinner !== null) {
                                button.parentNode.querySelector('.spinner').classList.remove('is-active');
                                button.parentNode.querySelector('.spinner').style.display = 'none';
                            }

                            notices.innerHTML = '<div class="notice notice-success is-dismissible"> <p>' + result.message + '</p> </div>';
                        }
                    });

            })
        })

        const url = new URL(window.location.href);
        const step = url.searchParams.get("step");

        if (step) {
            setupWizard.querySelector('.wizard-content[data-type=quick-start]').style.display = 'block';
            setupWizard.querySelector('.wizard-content[data-type=introduction]').style.display = 'none';
        }
    }

});

const resizeSetupWizard = () => {
    const setupWizard = document.querySelector('.sympose-setup-wizard');
    const wpContent = document.querySelector('#wpcontent');

    if (setupWizard !== null) {
        // Resize
        setupWizard.style.marginLeft = wpContent.offsetLeft + 'px';
    }
}

window.addEventListener('resize', resizeSetupWizard);
