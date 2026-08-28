import DocumentService from '@typo3/core/document-service.js';
import AjaxRequest from '@typo3/core/ajax/ajax-request.js';
import Notification from '@typo3/backend/notification.js';

/**
 * Submits changed labels of the translation module via AJAX.
 */
class L10nTranslator {
    constructor() {
        this.initialize();
    }

    async initialize() {
        await DocumentService.ready();
        document.addEventListener('submit', (event) => {
            const form = event.target.closest('.l10n-translation-translation');
            if (form === null) {
                return;
            }
            event.preventDefault();

            const data = {
                language: form.querySelector('input[name=language]').value,
                path: form.querySelector('input[name=path]').value,
                target: form.querySelector('[name=target]').value,
                key: form.querySelector('input[name=key]').value,
            };

            new AjaxRequest(TYPO3.settings.ajaxUrls['L10nTranslator_update'])
                .post(data)
                .then(async (response) => {
                    const body = await response.resolve();
                    Notification.showMessage(
                        body.flashMessage.title,
                        body.flashMessage.message,
                        body.flashMessage.severity,
                        5
                    );
                })
                .catch((error) => {
                    Notification.error('Error', String(error.response?.status ?? error), 5);
                });
        });
    }
}

export default new L10nTranslator();
