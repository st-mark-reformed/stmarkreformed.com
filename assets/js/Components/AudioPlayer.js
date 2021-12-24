class AudioPlayer {
    constructor (model) {
        const self = this;

        const firstPlayEventListener = () => {
            self.setDuration();
        };

        self.model = model;

        self.durationHasBeenSet = false;

        self.player = model.el.querySelector('[ref="audioPlayer"]');

        self.player.addEventListener('play', firstPlayEventListener);

        self.player.addEventListener('timeupdate', () => {
            self.audioTimeUpdate();
        });

        self.progressBar = model.el.querySelector('[ref="progressBar"]');

        self.scrubber = model.el.querySelector('[ref="scrubber"]');

        self.currentTime = model.el.querySelector('[ref="currentTime"]');

        self.scrubberTime = model.el.querySelector('[ref="scrubberTime"]');

        model.watch('data.isPlaying', (val) => {
            if (val) {
                self.playAudio();
            } else {
                self.pauseAudio();
            }
        });

        model.watch('data.rollBack', () => {
            self.rollBack();
        });

        model.watch('data.rollForward', () => {
            self.rollForward();
        });

        self.playBar = model.el.querySelector('[ref="playBar"]');

        self.playBar.addEventListener('click', (event) => {
            self.playBarClick(event);
        });

        self.playBar.addEventListener('mousemove', (event) => {
            self.playBarMouseMove(event);
        });

        self.playBar.addEventListener('mouseout', (event) => {
            self.playBarMouseOut(event);
        });

        model.data.playerIsInitialized = true;
    }

    playAudio () {
        const self = this;

        self.player.play();

        self.player.volume = 1;
    }

    pauseAudio () {
        const self = this;

        self.player.pause();

        self.player.volume = 1;
    }

    audioTimeUpdate () {
        const self = this;

        const { player } = self;

        const percentPlayed = (player.currentTime / player.duration) * 100;

        self.progressBar.style.width = `${percentPlayed}%`;

        const time = new Date(null);

        let timeText = '';

        time.setSeconds(player.currentTime);

        if (time.getUTCHours()) {
            timeText += `${time.getUTCHours()}:`;

            if (time.getUTCMinutes() < 10) {
                timeText += '0';
            }
        }

        timeText += `${time.getUTCMinutes()}:`;

        if (time.getUTCSeconds() < 10) {
            timeText += '0';
        }

        timeText += time.getUTCSeconds();

        self.currentTime.innerHTML = timeText;
    }

    setDuration () {
        const self = this;

        if (self.durationHasBeenSet) {
            return;
        }

        const { player } = self;

        if (Number.isNaN(player.duration)) {
            setTimeout(() => {
                self.setDuration();
            }, 50);

            return;
        }

        const durTime = new Date(null);

        let durTimeText = '';

        durTime.setSeconds(player.duration);

        if (durTime.getUTCHours()) {
            durTimeText += `${durTime.getUTCHours()}:`;

            if (durTime.getUTCMinutes() < 10) {
                durTimeText += '0';
            }
        }

        durTimeText += `${durTime.getUTCMinutes()}:`;

        if (durTime.getUTCSeconds() < 10) {
            durTimeText += '0';
        }

        durTimeText += durTime.getUTCSeconds();

        self.model.el.querySelector('[ref="duration"]').innerHTML = durTimeText;
    }

    rollBack () {
        const self = this;
        const { player } = self;

        if (!player.readyState) {
            return;
        }

        let skipBack = player.currentTime - 30;

        // Make sure it's not less than 0
        skipBack = skipBack > 0 ? skipBack : 0;

        player.currentTime = skipBack;
    }

    rollForward () {
        const self = this;
        const { player } = self;

        if (!player.readyState) {
            return;
        }

        let skipForward = player.currentTime + 30;

        // Make sure it's not greater than the duration
        skipForward = skipForward < player.duration
            ? skipForward
            : player.duration;

        player.currentTime = skipForward;
    }

    playBarClick (event) {
        const self = this;
        const { player } = self;

        if (!player.readyState) {
            self.model.data.isPlaying = true;

            setTimeout(() => {
                self.playBarClick(event);
            }, 50);

            return;
        }

        const barWidth = self.playBar.offsetWidth;
        const percent = (event.offsetX / barWidth) * 100;

        // Calculate the time to set audio to based on percentage
        player.currentTime = (player.duration * percent) / 100;
    }

    playBarMouseMove (event) {
        const self = this;
        const { player } = self;

        if (!player.readyState) {
            return;
        }

        const barWidth = self.playBar.offsetWidth;
        const percent = (event.offsetX / barWidth) * 100;

        // Calculate the time based on percentage
        const seconds = (player.duration * percent) / 100;

        const time = new Date(null);
        let timeText = '';

        time.setSeconds(seconds);

        if (time.getUTCHours()) {
            timeText += `${time.getUTCHours()}:`;

            if (time.getUTCMinutes() < 10) {
                timeText += '0';
            }
        }

        timeText += `${time.getUTCMinutes()}:`;

        if (time.getUTCSeconds() < 10) {
            timeText += '0';
        }

        timeText += time.getUTCSeconds();

        self.scrubberTime.innerHTML = timeText;

        const scrubberW = self.scrubber.offsetWidth;

        self.scrubber.style.left = `calc(${percent}% - ${scrubberW * 0.5}px)`;

        self.model.data.isScrubbing = true;
    }

    playBarMouseOut () {
        const self = this;

        self.model.data.isScrubbing = false;
    }
}

export default (model) => {
    // eslint-disable-next-line no-new
    new AudioPlayer(model);
};
