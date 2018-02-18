// Make sure FAB is defined
window.FAB = window.FAB || {};

function runMain(F) {
    'use strict';

    if (! window.jQuery ||
        ! F.controller ||
        ! F.model
    ) {
        setTimeout(function() {
            runMain(F);
        }, 10);
        return;
    }

    var GlobalModelConstructor = F.model.make({
        googleApiKey: 'string',
        googleMapsApiLoaded: 'bool'
    });

    F.GlobalModel = new GlobalModelConstructor($('body').data('vars'));

    var delay = 0;

    F.controller.construct('MobileMenu', {
        el: 'body'
    });

    $('.JSSiteNav__HasSubMenu').each(function() {
        F.controller.construct('SubNav', {
            el: this
        });
    });

    $('.JSGalleryShowcaseBackground').each(function() {
        var el = this;
        var rand = Math.round(Math.random() * (3000 - 500)) + 500;

        setTimeout(function() {
            F.controller.construct('GalleryShowcaseBackground', {
                el: el
            });
        }, delay);

        delay += rand;
    });

    $('.JSGoogleMap').each(function() {
        F.controller.construct('GoogleMap', {
            el: this
        });
    });

    $('.JSPageSubNav').each(function() {
        F.controller.construct('PageSubNavMobile', {
            el: this
        });
    });

    $('.JSAudioPlayer').each(function() {
        F.controller.construct('AudioPlayer', {
            el: this
        });
    });

    $('.JSGallery').each(function() {
        F.controller.construct('Gallery', {
            el: this
        });
    });

    $('.JSContactForm').each(function() {
        F.controller.construct('ContactForm', {
            el: this
        });
    });
}

runMain(window.FAB);
