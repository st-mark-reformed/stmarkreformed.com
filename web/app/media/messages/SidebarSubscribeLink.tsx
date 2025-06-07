// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import typography from '../../typography/typography';

export default function SidebarSubscribeLink (
    {
        content,
        iconHref,
    }: {
        content: string;
        iconHref?: string;
    },
) {
    return (
        <>
            {(() => {
                if (!iconHref) {
                    return null;
                }

                return (
                    <span className="inline-block mr-2">
                        <img
                            className="h-7 w-7"
                            src={iconHref}
                            alt={content}
                            loading="lazy"
                        />
                    </span>
                );
            })()}
            <span
                className="inline-block"
                dangerouslySetInnerHTML={{
                    __html: typography(content),
                }}
            />
        </>
    );
}
