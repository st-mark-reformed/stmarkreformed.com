// Make sure FAB is defined
window.FAB = window.FAB || {};

function runGalleryShowcaseBackground(F) {
    'use strict';

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runGalleryShowcaseBackground(F);
        }, 10);
        return;
    }

    F.controller.make('GalleryShowcaseBackground', {
        init: function() {
            var self = this;
            var $images = self.$el.find('.JSGalleryShowcaseBackground__Image');

            if ($images.length < 2) {
                return;
            }

            setInterval(function() {
                self.changeSlide();
            }, 8000);
        },

        changeSlide: function() {
            var self = this;
            var jsActiveClass = 'JSGalleryShowcaseBackground__Image--IsActive';
            var jsClass = 'JSGalleryShowcaseBackground__Image';
            var $active = self.$el.find('.' + jsActiveClass);
            var $next = $active.next();
            var activeActiveClass = $active.data('activeClass');
            var nextActiveClass;

            if ($next.length < 1) {
                $next = self.$el.find('.' + jsClass).first();
            }

            nextActiveClass = $next.data('activeClass');

            $active.removeClass(jsActiveClass);
            $active.removeClass(activeActiveClass);
            $next.addClass(nextActiveClass);
            $next.addClass(jsActiveClass);
        }
    });
}

runGalleryShowcaseBackground(window.FAB);
