// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import typography from '../../typography/typography';

export default function Links (
    {
        links,
    }: {
        links: Array<{
            content: string;
            href: string;
        }>;
    },
) {
    return (
        <>
            {links.map((link) => (
                <p>
                    <Link
                        className="font-bold"
                        href={link.href}
                        target="_blank"
                        rel="noreferrer noopener"
                        dangerouslySetInnerHTML={{
                            __html: typography(link.content),
                        }}
                    />
                </p>
            ))}
        </>
    );
}
