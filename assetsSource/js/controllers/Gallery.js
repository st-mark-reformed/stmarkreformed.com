// Make sure FAB is defined
window.FAB = window.FAB || {};

function runGallery(F, W) {
    'use strict';

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runGallery(F, W);
        }, 10);
        return;
    }

    F.controller.make('Gallery', {
        $galleryItems: null,
        $navItems: null,
        totalItems: 0,
        lastIndex: 0,

        model: {
            fullScreen: 'bool',
            activeIndex: 'int'
        },

        events: {
            'click .JSGallery__FullScreenToggle': function() {
                var self = this;
                self.model.set('fullScreen', ! self.model.get('fullScreen'));
            },
            'click .JSGallery__NavForward': function() {
                this.advanceIndex();
            },
            'click .JSGallery__NavBack': function() {
                this.backIndex();
            },
            'click .JSGallery__NavItem': function(e) {
                var self = this;
                var $el = $(e.currentTarget);
                var index = parseInt($el.data('galleryIndex'));

                self.model.set('activeIndex', index);
            }
        },

        init: function() {
            var self = this;

            self.$galleryItems = self.$el.find('.JSGallery__Item');

            self.$navItems = self.$el.find('.JSGallery__NavItem');

            self.model.set('activeIndex');

            self.totalItems = self.$galleryItems.length;

            self.lastIndex = self.totalItems - 1;

            self.model.onChange('fullScreen', function(val) {
                if (val) {
                    self.goFullScreen();
                    return;
                }

                self.endFullScreen();
            });

            self.model.onChange('activeIndex', function(val) {
                self.activateSlideIndex(val);
                self.ensureActiveThumbInView();
            });
        },

        goFullScreen: function() {
            var self = this;
            var theClass = self.$el.data('fullScreenClass');

            self.$el.addClass(theClass);

            $(W).on('keyup.galleryFullScreen', function(e) {
                var keyCode = e.keyCode;

                if (keyCode === 27) {
                    self.endFullScreen();
                } else if (keyCode === 39) {
                    self.advanceIndex();
                } else if (keyCode === 37) {
                    self.backIndex();
                }
            });
        },

        endFullScreen: function() {
            var self = this;
            var theClass = self.$el.data('fullScreenClass');

            self.$el.removeClass(theClass);

            $(W).off('keyup.galleryFullScreen');
        },

        advanceIndex: function() {
            var self = this;
            var currentIndex = self.model.get('activeIndex');
            var newIndex = currentIndex + 1;

            if (newIndex > self.lastIndex) {
                return;
            }

            self.model.set('activeIndex', newIndex);
        },

        backIndex: function() {
            var self = this;
            var currentIndex = self.model.get('activeIndex');
            var newIndex = currentIndex - 1;

            if (newIndex < 0) {
                return;
            }

            self.model.set('activeIndex', newIndex);
        },

        activateSlideIndex: function(index) {
            var self = this;
            var lastIndex = self.lastIndex;
            var $newActiveItem;
            var newActiveClass;

            index = index <= lastIndex ? index : lastIndex;
            index = index >= 0 ? index : 0;

            self.$galleryItems.add(self.$navItems).each(function() {
                var $el = $(this);
                var activeClass = $el.data('activeClass');

                $el.removeClass(activeClass);
            });

            $newActiveItem = self.$galleryItems.eq(index);
            newActiveClass = $newActiveItem.data('activeClass');
            $newActiveItem.addClass(newActiveClass);

            $newActiveItem = self.$navItems.eq(index);
            newActiveClass = $newActiveItem.data('activeClass');
            $newActiveItem.addClass(newActiveClass);

            self.$el.find('.JSGallery__NavBack').each(function() {
                var $el = $(this);
                var hiddenClass = $el.data('hiddenClass');

                if (index < 1) {
                    $el.addClass(hiddenClass);
                } else {
                    $el.removeClass(hiddenClass);
                }
            });

            self.$el.find('.JSGallery__NavForward').each(function() {
                var $el = $(this);
                var hiddenClass = $el.data('hiddenClass');

                if (index >= self.lastIndex) {
                    $el.addClass(hiddenClass);
                } else {
                    $el.removeClass(hiddenClass);
                }
            });
        },

        ensureActiveThumbInView: function() {
            var self = this;
            var activeIndex = self.model.get('activeIndex');
            var $thumb = self.$navItems.eq(activeIndex);
            var $nav = self.$el.find('.JSGallery__Nav');
            var $navWrapper = self.$el.find('.JSGallery__NavWrapper');
            var navWrapperWidth = $navWrapper.width();
            var navWrapperOffset = $navWrapper.offset().left;
            var navWrapperRightEdge = navWrapperOffset + navWrapperWidth;
            var thumbPos = $thumb.offset().left + $thumb.width();

            if (thumbPos > navWrapperRightEdge) {
                $nav.animate({
                    scrollLeft: ($thumb.offset().left - $thumb.width()) - $nav.offset().left + $nav.scrollLeft()
                });
            } else if (navWrapperOffset > thumbPos - $thumb.width()) {
                $nav.animate({
                    scrollLeft: (($thumb.offset().left) - $nav.offset().left + $nav.scrollLeft() - navWrapperWidth / 2)
                });
            }
        }
    });
}

runGallery(window.FAB, window);
