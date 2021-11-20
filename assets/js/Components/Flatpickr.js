/**
 * @see https://flatpickr.js.org/
 */

import Loader from '../Helpers/Loader.js';

class Flatpickr {
    /**
     * @param {NodeList} els
     */
    constructor (els) {
        const cdn = 'https://cdn.jsdelivr.net/npm';
        const css = `${cdn}/flatpickr/dist/themes/material_red.min.css`;
        const js = `${cdn}/flatpickr`;

        // noinspection JSIgnoredPromiseFromCall
        Loader.loadCss(css);

        Loader.loadJs(js).then(() => {
            els.forEach((el) => {
                const value = el.getAttribute('value');

                let options = {
                    enableTime: true,
                    dateFormat: 'Y-m-d h:i K',
                };

                if (el.getAttribute('type') === 'date') {
                    options = {
                        enableTime: false,
                        dateFormat: 'Y-m-d',
                    };
                }

                if (value) {
                    options.defaultDate = value;
                }

                window.flatpickr(el, options);
            });
        });
    }
}

export default Flatpickr;
