import React from 'react';
import { Entry } from '../../audio/Entry';
import AudioListing from '../../audio/AudioListing';

export default function EntryDisplay (
    {
        baseUri,
        entry,
        showBorder = false,
        showPermalink = false,
        useInternalAudioUrlScheme = false,
    }: {
        baseUri: string;
        entry: Entry;
        showBorder?: boolean;
        showPermalink?: boolean;
        useInternalAudioUrlScheme?: boolean;
    },
) {
    let byUrl = null;

    if (entry.by?.slug) {
        byUrl = `${baseUri}/by/${entry.by.slug}`;
    }

    let seriesUrl = null;

    if (entry.series?.slug) {
        seriesUrl = `${baseUri}/series/${entry.series.slug}`;
    }

    let permalink = null;

    if (showPermalink) {
        permalink = `${baseUri}/${entry.slug}`;
    }

    let audioUrl = `/uploads/audio/${entry.audioFileName}`;

    if (useInternalAudioUrlScheme) {
        audioUrl = `${baseUri}/audio/${entry.slug}/${entry.audioFileName}`;
    }

    return (
        <AudioListing
            title={entry.title}
            by={entry.by?.title}
            byUrl={byUrl}
            date={entry.postDateDisplay}
            series={entry.series?.title}
            seriesUrl={seriesUrl}
            text={entry.text}
            permalink={permalink}
            audioUrl={audioUrl}
            showBorder={showBorder}
        />
    );
}
