import React, { ReactNode } from 'react';
import HeadingH2 from './HeadingH2';

export default function SectionWithH2Heading (
    {
        heading,
        children,
    }: {
        heading: string;
        children: ReactNode;
    },
) {
    return (
        <>
            <HeadingH2 content={heading} />
            {children}
        </>
    );
}
