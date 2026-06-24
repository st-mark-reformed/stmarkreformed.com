'use client';

import React from 'react';
import ChunkedMultiFileField from '../Forms/FileUploads/ChunkedMultiFileField';
import { CreateEditHymnOfTheMonthPracticeTrackValue } from './CreateEditHymnOfTheMonthValues';

export default function HymnPracticeTracksField (
    {
        initialTracks,
        onUploadingChange = undefined,
    }: {
        initialTracks: CreateEditHymnOfTheMonthPracticeTrackValue[];
        onUploadingChange?: ((isUploading: boolean) => void) | undefined;
    },
) {
    return (
        <ChunkedMultiFileField
            heading="Practice Tracks"
            addLabel="Add Practice Track"
            removeAriaLabel="Remove track"
            fileFieldName="practiceTrackFile"
            fileAccept=".mp3"
            fileLabel="Audio file (MP3)"
            acceptType="mp3"
            metaField={{ kind: 'editableTitle', name: 'practiceTrackTitle', label: 'Title' }}
            initialRows={initialTracks.map((track) => ({
                title: track.title,
                filename: '',
                file: track.file,
            }))}
            onUploadingChange={onUploadingChange}
        />
    );
}
