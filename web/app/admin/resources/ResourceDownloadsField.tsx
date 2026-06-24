'use client';

import React from 'react';
import ChunkedMultiFileField from '../Forms/FileUploads/ChunkedMultiFileField';
import { CreateEditResourceDownloadValue } from './CreateEditResourceValues';

/**
 * Repeatable download rows. Each row is a single file; the filename shown to the
 * editor (and embedded in the public URL) is the original upload name. Existing
 * downloads are kept by `filename`; newly chosen files are uploaded resumably and
 * carry an `upload:{id}` handle the API claims into place.
 */
export default function ResourceDownloadsField (
    {
        initialDownloads,
        onUploadingChange = undefined,
    }: {
        initialDownloads: CreateEditResourceDownloadValue[];
        onUploadingChange?: ((isUploading: boolean) => void) | undefined;
    },
) {
    return (
        <ChunkedMultiFileField
            heading="Downloads"
            addLabel="Add Download"
            removeAriaLabel="Remove download"
            fileFieldName="downloadFile"
            acceptType="any"
            metaField={{ kind: 'fileName', name: 'downloadFilename' }}
            initialRows={initialDownloads.map((download) => ({
                title: '',
                filename: download.filename,
                file: download.file,
            }))}
            onUploadingChange={onUploadingChange}
        />
    );
}
