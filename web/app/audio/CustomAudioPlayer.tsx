'use client';

import React from 'react';
/** @see https://github.com/lhz516/react-h5-audio-player */
import AudioPlayer from 'react-h5-audio-player';
import 'react-h5-audio-player/lib/styles.css';
import './audio-player-style.css';
import {
    BackwardIcon,
    ForwardIcon,
    PlayIcon,
    PauseIcon,
} from '@heroicons/react/20/solid';
import RenderOnMount from '../RenderOnMount';

export default function CustomAudioPlayer (
    {
        audioUrl,
    }: {
        audioUrl: string;
    },
) {
    return (
        <RenderOnMount>
            <AudioPlayer
                volume={1}
                autoPlay={false}
                src={audioUrl}
                preload="metadata"
                layout="stacked-reverse"
                progressJumpSteps={{
                    backward: 30000,
                    forward: 30000,
                }}
                customIcons={{
                    play: <PlayIcon className="text-crimson hover:text-crimson-dark" />,
                    pause: <PauseIcon className="text-crimson hover:text-crimson-dark" />,
                    forward: (
                        <span className="block text-left text-crimson hover:text-crimson-dark">
                            <ForwardIcon className="h-5 w-5 inline-block align-middle" />
                            <span className="ml-0.5 inline-block align-middle text-xxs">
                                30s
                            </span>
                        </span>
                    ),
                    rewind: (
                        <span className="block text-right text-crimson hover:text-crimson-dark">
                            <span className="mr-0.5 inline-block align-middle text-xxs">
                                30s
                            </span>
                            <BackwardIcon className="h-5 w-5 inline-block align-middle" />
                        </span>
                    ),
                }}
                showJumpControls
                showDownloadProgress
                showFilledProgress
                showFilledVolume
            />
        </RenderOnMount>
    );
}
