// Make sure FAB is defined
window.FAB = window.FAB || {};

function runClearInput(F) {
    'use strict';

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runClearInput(F);
        }, 10);
        return;
    }

    F.controller.make('ClearInput', {
        events: {
            'change .JS-ClearInput__Input': function(e) {
                var $btn = this.$el.find('.JS-ClearInput__Button');

                if (! e.currentTarget.value) {
                    $btn.hide();
                    return;
                }

                $btn.show();
            },
            'click .JS-ClearInput__Button': function() {
                this.$el.find('.JS-ClearInput__Input').val('').trigger('change');
            }
        },

        init: function() {
            var self = this;
            var $btn = self.$el.find('.JS-ClearInput__Button');

            if (! self.$el.find('.JS-ClearInput__Input').val()) {
                $btn.hide();
                return;
            }

            $btn.show();
        }
    });
}

runClearInput(window.FAB);
