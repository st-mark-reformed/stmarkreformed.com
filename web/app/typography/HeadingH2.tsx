// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import typography from './typography';

export default function HeadingH2 (
    {
        content,
    }: {
        content: string;
    },
) {
    return (
        <h2
            className="mt-2 text-black text-3xl font-extrabold tracking-tight sm:text-4xl"
            dangerouslySetInnerHTML={{
                __html: typography(content),
            }}
        />
    );
}
