'use client';

import React from 'react';
import FullPageError, { ErrorParams } from './FullPageError';

export default function Error (
    {
        error,
    }: {
        error: Error;
    },
) {
    const errorParts = error.message.split('|');

    if (errorParts[0] === 'access_denied' && errorParts.length === 2) {
        const errorParams = JSON.parse(errorParts[1]) as ErrorParams;

        return (
            <FullPageError {...errorParams} />
        );
    }

    return <FullPageError />;
}
