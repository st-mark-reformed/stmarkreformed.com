// Make sure FAB is defined
window.FAB = window.FAB || {};

function runMobileMenu(F, W) {
    'use strict';

    var desktopBreakPoint = 1000;

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runMobileMenu(F, W);
        }, 10);
        return;
    }

    F.controller.make('MobileMenu', {
        model: {
            isActive: 'bool'
        },

        isMobile: true,

        events: {
            'click .JSMobileMenuActivator': function(e) {
                var self = this;

                e.preventDefault();

                self.model.set('isActive', ! self.model.get('isActive'));
            },
            'click .JSMobileMenuCloseAction': function() {
                var self = this;

                self.model.set('isActive', false);
            }
        },

        init: function() {
            var self = this;

            self.model.onChange('isActive', function() {
                self.menuResponder();
            });

            self.resizeResponder();

            $(W).on('resize.MobileMenuWatch', function() {
                self.resizeResponder();
            });
        },

        resizeResponder: function() {
            var self = this;
            var windowIsDesktop = W.innerWidth >= desktopBreakPoint;
            var modeIsMobile = self.isMobile;

            if (windowIsDesktop && modeIsMobile) {
                self.model.set('isActive', false);
                self.deactivateMenu();
            }

            self.isMobile = ! windowIsDesktop;
        },

        menuResponder: function() {
            var self = this;
            var state = self.model.get('isActive');

            if (W.innerWidth >= desktopBreakPoint) {
                if (self.model.get('isActive')) {
                    self.deactivateMenu();
                }

                return;
            }

            if (state) {
                self.activateMenu();
                return;
            }

            self.deactivateMenu();
        },

        activateMenu: function() {
            var self = this;
            var $siteHeader = $('.JSSiteHeader');

            $siteHeader.addClass($siteHeader.data('mobileMenuOpenClass'));

            $('.JSNavList').slideDown(150);

            $('.JSMobileMenuActivator').each(function() {
                var $el = $(this);
                var activeClass = $el.data('activeClass');

                if (! activeClass) {
                    return;
                }

                $el.addClass(activeClass);
            });

            $('body').on('click.MobileMenuOff', function(e) {
                if ($(e.target).closest('.JSNavList').length) {
                    return;
                }

                self.model.set('isActive', false);
            });
        },

        deactivateMenu: function() {
            var $navList = $('.JSNavList');
            var $siteHeader = $('.JSSiteHeader');

            $siteHeader.removeClass($siteHeader.data('mobileMenuOpenClass'));

            $navList.slideUp(150, null, function() {
                $navList.attr('style', null);
            });

            $('.JSMobileMenuActivator').each(function() {
                var $el = $(this);
                var activeClass = $el.data('activeClass');

                if (! activeClass) {
                    return;
                }

                $el.removeClass(activeClass);
            });

            $('body').off('click.MobileMenuOff');
        }
    });
}

runMobileMenu(window.FAB, window);
