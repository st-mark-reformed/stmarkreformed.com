// Make sure FAB is defined
window.FAB = window.FAB || {};

function runPageSubNavMobile(F, W) {
    'use strict';

    var desktopBreakPoint = 1200;

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runPageSubNavMobile(F, W);
        }, 10);
        return;
    }

    F.controller.make('PageSubNavMobile', {
        events: {
            'click .JSPageSubNav__MobileExpander': function() {
                var self = this;
                self.model.set('isOpen', ! self.model.get('isOpen'));
            },
            'click .JSPageSubNav__Link': function(e) {
                var self = this;
                var $el = $(e.currentTarget);
                self.$el.find('.JSPageSubNav__ActiveText')
                    .text($el.text().trim());
                self.model.set('isOpen', false);
            }
        },

        model: {
            isOpen: 'bool',
            mode: 'string'
        },

        init: function() {
            var self = this;

            self.model.onChange('isOpen', function(val) {
                val ? self.openMenu() : self.closeMenu();
            });

            self.model.onChange('mode', function(val) {
                val === 'desktop' ? self.reset() : null;
            });

            self.setMode();

            $(W).on('resize', function() {
                self.setMode();
            });
        },

        setMode: function() {
            var self = this;

            self.model.set(
                'mode',
                W.innerWidth >= desktopBreakPoint ? 'desktop' : 'mobile'
            );
        },

        reset: function() {
            var self = this;

            self.model.set('isOpen', false);

            self.closeMenu();
        },

        openMenu: function() {
            var self = this;

            if (self.model.get('mode') === 'desktop') {
                return;
            }

            self.$el.addClass(self.$el.data('openClass'));
            self.$el.find('.JSPageSubNav__List').slideDown(200);
        },

        closeMenu: function() {
            var self = this;
            var $list = self.$el.find('.JSPageSubNav__List');

            self.$el.removeClass(self.$el.data('openClass'));

            if (self.model.get('mode') === 'desktop') {
                $list.attr('style', null);
                return;
            }

            $list.slideUp(200);
        }
    });
}

runPageSubNavMobile(window.FAB, window);
