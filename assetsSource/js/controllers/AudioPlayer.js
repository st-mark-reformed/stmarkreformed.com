// Make sure FAB is defined
window.FAB = window.FAB || {};

function runAudioPlayer(F, W) {
    'use strict';

    var audioPlayerTemplate;
    var GlobalAudioModelConstructor;
    var globalAudioModel;

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runAudioPlayer(F, W);
        }, 10);
        return;
    }

    audioPlayerTemplate = $('#JSTemplate__AudioPlayer').html();

    GlobalAudioModelConstructor = F.model.make({
        playAction: 'int'
    });

    globalAudioModel = new GlobalAudioModelConstructor();

    F.controller.make('AudioPlayer', {
        $audioPlayerTemplate: null,
        $playPause: null,
        $barPlayed: null,
        $barScrubber: null,
        $currentTime: null,
        $rollBack: null,
        $rollForward: null,
        $playerSpeed: null,
        $scrubArea: null,
        $scrubHover: null,
        miniClass: 'AudioPlayer--Mini',
        playStoppedClass: 'AudioPlayer__PlayPauseButton--Stopped',
        playPlayingClass: 'AudioPlayer__PlayPauseButton--Playing',

        model: {
            isMini: 'bool',
            isPlaying: 'bool',
            duration: 'string'
        },

        init: function() {
            var self = this;

            self.setElements();
            self.setWatchers();
            self.checkMini();
        },

        setElements: function() {
            var self = this;

            self.$audioPlayerTemplate = $(audioPlayerTemplate);

            self.$playPause = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__PlayPause'
            );

            self.$barPlayed = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__BarPlayed'
            );

            self.$barScrubber = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__BarScrubber'
            );

            self.$currentTime = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__CurrentTime'
            );

            self.$rollBack = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__RollBack'
            );

            self.$rollForward = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__RollForward'
            );

            self.$playerSpeed = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__PlayerSpeed'
            );

            self.$scrubArea = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__ScrubArea'
            );

            self.$scrubHover = self.$audioPlayerTemplate.find(
                '.JSAudioPlayerTemplate__ScrubHover'
            );

            self.$audioPlayerTemplate.insertAfter(self.$el);

            self.$audioPlayerTemplate.prepend(self.$el);

            self.$el.hide();
        },

        setWatchers: function() {
            var self = this;

            self.model.onChange('isMini', function() {
                self.setMini();
            });

            $(window).on('resize.audioPlayer', function() {
                self.checkMini();
            });

            globalAudioModel.onChange('playAction', function() {
                self.model.set('isPlaying', false);
            });

            self.model.onChange('isPlaying', function(val) {
                if (val) {
                    self.playAudio();
                } else {
                    self.pauseAudio();
                }
            });

            self.$playPause.on('click', function() {
                var shouldPlay = ! self.model.get('isPlaying');

                globalAudioModel.set(
                    'playAction',
                    globalAudioModel.get('playAction') + 1
                );

                self.model.set('isPlaying', shouldPlay);
            });

            self.el.addEventListener('play', function() {
                self.audioElPlayRespond();
            });

            self.$el.on('play.firstPlay', function() {
                self.$el.off('play.firstPlay');
                self.setDuration();
            });

            self.el.addEventListener('pause', function() {
                self.audioElPauseRespond();
            });

            self.el.addEventListener('timeupdate', function() {
                self.audioTimeUpdate();
            });

            self.$rollBack.on('click', function() {
                self.rollBack();
            });

            self.$rollForward.on('click', function() {
                self.rollForward();
            });

            self.$playerSpeed.on('click', function() {
                self.playerSpeedUpdate();
            });

            self.$scrubArea.on('mousemove', function(e) {
                self.scrubAreaMouseMove(e);
            });

            self.$scrubArea.on('mouseout', function() {
                self.scrubAreaMouseOut();
            });

            self.$scrubArea.on('click', function(e) {
                self.scrubAreaClick(e);
            });
        },

        checkMini: function() {
            var self = this;
            self.model.set('isMini', self.$audioPlayerTemplate.width() < 451);
        },

        setMini: function() {
            var self = this;

            if (self.model.get('isMini')) {
                self.$audioPlayerTemplate.addClass(self.miniClass);
                return;
            }

            self.$audioPlayerTemplate.removeClass(self.miniClass);
        },

        setDuration: function() {
            var self = this;

            var durTime;
            var durTimeText = '';

            if (isNaN(self.el.duration)) {
                setTimeout(function() {
                    self.setDuration();
                }, 50);

                return;
            }

            durTime = new Date(null);
            durTime.setSeconds(self.el.duration);

            if (durTime.getUTCHours()) {
                durTimeText += durTime.getUTCHours() + ':';

                if (durTime.getUTCMinutes() < 10) {
                    durTimeText += '0';
                }
            }

            durTimeText += durTime.getUTCMinutes() + ':';

            if (durTime.getUTCSeconds() < 10) {
                durTimeText += '0';
            }

            durTimeText += durTime.getUTCSeconds();

            self.$audioPlayerTemplate.find('.JSAudioPlayerTemplate__TotalTime')
                .text(durTimeText);
        },

        playAudio: function() {
            this.el.play();
        },

        pauseAudio: function() {
            this.el.pause();
        },

        audioElPlayRespond: function() {
            var self = this;

            self.$playPause.removeClass(self.playStoppedClass)
                .addClass(self.playPlayingClass);

            /**
             * Make sure volume is all the way up.
             * Users control volume of their computer speakers, why would we
             * want to control volume here?
             */
            self.el.volume = 1;
        },

        audioElPauseRespond: function() {
            var self = this;

            self.$playPause.addClass(self.playStoppedClass)
                .removeClass(self.playPlayingClass);
        },

        audioTimeUpdate: function() {
            var self = this;

            // Get the percentage played
            var per = (self.el.currentTime / self.el.duration) * 100;

            var time = new Date(null);
            var timeText = '';

            // Set the bar width to the appropriate percentage
            self.$barPlayed.css('width', per + '%');

            // Set the scrubber position to the appropriate percentage
            self.$barScrubber.css('left', per + '%');

            time.setSeconds(self.el.currentTime);

            if (time.getUTCHours()) {
                timeText += time.getUTCHours() + ':';

                if (time.getUTCMinutes() < 10) {
                    timeText += '0';
                }
            }

            timeText += time.getUTCMinutes() + ':';

            if (time.getUTCSeconds() < 10) {
                timeText += '0';
            }

            timeText += time.getUTCSeconds();

            self.$currentTime.text(timeText);
        },

        rollBack: function() {
            var self = this;
            var skipBack;

            if (! self.el.readyState) {
                return;
            }

            skipBack = self.el.currentTime - parseFloat(
                self.$rollBack.data('amount')
            );

            // Make sure it's not less than 0
            skipBack = skipBack > 0 ? skipBack : 0;

            self.el.currentTime = skipBack;
        },

        rollForward: function() {
            var self = this;
            var skipForward;

            if (! self.el.readyState) {
                return;
            }

            skipForward = self.el.currentTime + parseFloat(
                self.$rollForward.data('amount')
            );

            // Make sure it's not greater than the duration
            skipForward = skipForward < self.el.duration ?
                skipForward :
                self.el.duration;

            self.el.currentTime = skipForward;
        },

        playerSpeedUpdate: function() {
            var self = this;
            var rate;

            if (! self.el.readyState) {
                return;
            }

            if (self.el.playbackRate >= 2) {
                rate = 0.5;
            } else {
                rate = self.el.playbackRate + 0.25;
            }

            self.el.playbackRate = rate;

            self.$playerSpeed.text(rate + 'x');
        },

        scrubAreaMouseMove: function(e) {
            var self = this;
            var barWidth = self.$scrubArea.width();

            // Get percentage of the clicked area
            var per = (e.offsetX / barWidth) * 100;

            // Calculate the time based on percentage
            var seconds = (self.el.duration * per) / 100;

            var time = new Date(null);
            var timeText = '';

            if (! self.el.readyState) {
                return;
            }

            self.$scrubHover.css('left', per + '%');

            time.setSeconds(seconds);

            if (time.getUTCHours()) {
                timeText += time.getUTCHours() + ':';

                if (time.getUTCMinutes() < 10) {
                    timeText += '0';
                }
            }

            timeText += time.getUTCMinutes() + ':';

            if (time.getUTCSeconds() < 10) {
                timeText += '0';
            }

            timeText += time.getUTCSeconds();

            self.$scrubHover.text(timeText);

            self.$scrubHover.css(
                'margin-left',
                '-' + ((self.$scrubHover.width() / 2) + 4) + 'px'
            );

            if (! self.$scrubHover.is(':visible')) {
                self.$scrubHover.fadeIn(400);
            }
        },

        scrubAreaMouseOut: function() {
            this.$scrubHover.fadeOut(400);
        },

        scrubAreaClick: function(e) {
            var self = this;
            var barWidth = self.$scrubArea.width();

            // Get percentage of the clicked area
            var per = (e.offsetX / barWidth) * 100;

            if (! self.el.readyState) {
                return;
            }

            // Calculate the time to set audio to based on percentage
            // and set the audio time
            self.el.currentTime = (self.el.duration * per) / 100;
        }
    });
}

runAudioPlayer(window.FAB, window);
