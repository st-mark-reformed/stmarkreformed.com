// Make sure FAB is defined
window.FAB = window.FAB || {};

function runContactForm(F) {
    'use strict';

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runContactForm(F);
        }, 10);
        return;
    }

    F.controller.make('ContactForm', {
        events: {
            submit: function(e) {
                e.preventDefault();
                this.submitForm();
            }
        },

        init: function() {
            var self = this;

            self.$el.attr('novalidate', 'novalidate');
            self.$el.find('#site').add('#mailing_address')
                .attr('required', false)
                .attr('tabindex', '-1')
                .attr('autocomplete', 'off')
                .val('');
        },

        submitForm: function() {
            var self = this;
            var $submitButton = self.$el.find('.JSContactForm__SubmitButton');

            $submitButton.attr('disabled', true)
                .text($submitButton.data('working'));

            $.ajax({
                url: window.location.href,
                data: self.$el.serialize(),
                method: 'POST',
                success: function(json) {
                    if (! json.success) {
                        $submitButton.attr('disabled', false)
                            .text($submitButton.data('value'));

                        self.parseErrors(json);

                        return;
                    }

                    window.location = json.redirect;
                }
            });
        },

        parseErrors: function(json) {
            var self = this;
            var $errorMessageWrapper = self.$el.siblings(
                '.JSContactForm__ErrorMessage'
            );

            self.$el.find('.JSContactForm__InputWrapper').removeClass(
                'ContactForm__InputWrapper--HasError'
            );

            self.$el.find('.ContactForm__InputErrorMessage').remove();

            self.$el.find(':input').off('change.validate').off('keyup.validate');

            $errorMessageWrapper.html(
                '<div class="ContactForm__ErrorMessage">' +
                    json.message +
                '</div>'
            );

            for (var inputName in json.inputErrors) {
                (function(inputName, errorMessage) {
                    var $input = self.$el.find('[name="' + inputName + '"]');
                    var $parent = $input.closest('.JSContactForm__InputWrapper');

                    $parent.addClass('ContactForm__InputWrapper--HasError');

                    $parent.append(
                        '<div class="ContactForm__InputErrorMessage">' +
                            errorMessage +
                        '</div>'
                    );

                    $input.on('change.validate keyup.validate', function() {
                        $parent.removeClass('ContactForm__InputWrapper--HasError');
                        $parent.find('.ContactForm__InputErrorMessage').remove();
                    });
                })(inputName, json.inputErrors[inputName]);
            }
        }
    });
}

runContactForm(window.FAB);
