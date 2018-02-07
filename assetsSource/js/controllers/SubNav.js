// Make sure FAB is defined
window.FAB = window.FAB || {};

function runSubNav(F, W) {
    'use strict';

    var desktopBreakPoint = 1000;
    var menuAnimationTime = 150;

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runSubNav(F, W);
        }, 10);
        return;
    }

    F.controller.make('SubNav', {
        model: {
            subNavIsActive: 'bool',
            mode: 'string'
        },

        isMobile: true,
        mouseLeaveTimer: 0,

        events: {
            mouseenter: function() {
                var self = this;

                if (W.innerWidth < desktopBreakPoint) {
                    return;
                }

                clearTimeout(self.mouseLeaveTimer);

                self.model.set('subNavIsActive', true);
            },
            mouseleave: function() {
                var self = this;

                // Check if we're in mobile mode and stop if so
                if (W.innerWidth < desktopBreakPoint) {
                    return;
                }

                self.mouseLeaveTimer = setTimeout(function() {
                    self.model.set('subNavIsActive', false);
                }, 400);
            }
        },

        init: function() {
            var self = this;

            self.model.onChange('subNavIsActive', function() {
                self.openCloseLogic();
            });
        },

        openCloseLogic: function() {
            var self = this;

            var mobileMenuIsActive = self.model.get('subNavIsActive');

            if (mobileMenuIsActive) {
                self.openMobileMenu();
                return;
            }

            self.closeMobileMenu();
        },

        openMobileMenu: function() {
            var self = this;

            self.$el.addClass(self.$el.data('subNavOpenClass'));
            self.$el.find('.JSSiteNav__SubNavList').slideDown(menuAnimationTime);
        },

        closeMobileMenu: function() {
            var self = this;

            // Open the menu
            self.$el.removeClass(self.$el.data('subNavOpenClass'));
            self.$el.find('.JSSiteNav__SubNavList').slideUp(menuAnimationTime);
        }
    });
}

runSubNav(window.FAB, window);
