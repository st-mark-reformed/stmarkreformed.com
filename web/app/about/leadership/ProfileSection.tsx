// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React, { ReactNode } from 'react';
import typography from '../../typography/typography';

export default function ProfileSection (
    {
        headline,
        children,
        isLast = false,
    }: {
        headline: string;
        children: ReactNode;
        isLast?: boolean;
    },
) {
    return (
        <>
            <h2
                className="mt-2 text-black text-3xl font-extrabold tracking-tight sm:text-4xl"
                dangerouslySetInnerHTML={{
                    __html: typography(headline),
                }}
            />
            {children}
            {(() => {
                if (isLast) {
                    return null;
                }

                return <hr className="w-full border border-gray-300 my-10" />;
            })()}
        </>
    );
}
