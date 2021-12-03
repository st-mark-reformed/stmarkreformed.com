/* eslint-disable no-new */

import AudioPlayer from './Components/AudioPlayer.js';
import Flatpickr from './Components/Flatpickr.js';
import Selects from './Components/Selects.js';

window.Methods.AudioPlayer = AudioPlayer;

// Flatpickr
const flatpickrEls = document.querySelectorAll(
    'input[type="date"], input[type="datetime-local"]',
);
if (flatpickrEls.length > 0) {
    new Flatpickr(flatpickrEls);
}

// Selects
const selectEls = document.querySelectorAll('[ref="select"]');
if (selectEls.length > 0) {
    new Selects(selectEls);
}
