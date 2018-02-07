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
}

runMain(window.FAB);
