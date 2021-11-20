import AudioPlayer from './Components/AudioPlayer.js';
import Flatpickr from './Components/Flatpickr.js';

window.Methods.AudioPlayer = AudioPlayer;

// Flatpickr
const flatpickrEls = document.querySelectorAll(
    'input[type="date"], input[type="datetime-local"]',
);
if (flatpickrEls.length > 0) {
    // eslint-disable-next-line no-new
    new Flatpickr(flatpickrEls);
}
