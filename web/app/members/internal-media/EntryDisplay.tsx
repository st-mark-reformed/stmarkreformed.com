import React from 'react';
import { Entry } from './Entry';
import AudioListing from '../../audio/AudioListing';

export default function EntryDisplay (
    {
        entry,
        showBorder = false,
        showPermalink = false,
    }: {
        entry: Entry;
        showBorder?: boolean;
        showPermalink?: boolean;
    },
) {
    let byUrl = null;

    if (entry.by?.slug) {
        byUrl = `/members/internal-media/by/${entry.by.slug}`;
    }

    let seriesUrl = null;

    if (entry.series?.slug) {
        seriesUrl = `/members/internal-media/series/${entry.series.slug}`;
    }

    let permalink = null;

    if (showPermalink) {
        permalink = `/members/internal-media/${entry.slug}`;
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
            audioUrl={`/members/internal-media/audio/${entry.slug}/${entry.audioFileName}`}
            showBorder={showBorder}
        />
    );
}
