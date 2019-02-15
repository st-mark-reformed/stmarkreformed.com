// Make sure FAB is defined
window.FAB = window.FAB || {};

function runRequireAnInput(F) {
    'use strict';

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runRequireAnInput(F);
        }, 10);
        return;
    }

    F.controller.make('RequireAnInput', {
        events: {
            submit: function(e) {
                var $form = $(e.currentTarget);
                var $inputs = $form.find('.JS-RequireAnInput__Input');
                var hasVal = false;

                $inputs.each(function() {
                    if (this.value) {
                        hasVal = true;
                    }
                });

                if (hasVal) {
                    return;
                }

                e.preventDefault();

                $form.find('.JS-RequireAnInput__Msg').show();
            }
        }
    });
}

runRequireAnInput(window.FAB);
