// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React, { ReactNode } from 'react';
import typography from '../../typography/typography';

export default function Profile (
    {
        image,
        name,
        children = null,
    }: {
        image: {
            href1x: string;
            href2x: string;
        };
        name: string;
        children?: ReactNode;
    },
) {
    return (
        <div className="my-10">
            <img
                src={image.href1x}
                srcSet={`${image.href1x} 1x, ${image.href2x} 2x`}
                alt={name}
                className="float-left w-32 mt-2 mr-4 mb-2"
                loading="lazy"
            />
            <h3
                className="text-lg font-semibold tracking-wider text-gray-900 mb-2"
                dangerouslySetInnerHTML={{
                    __html: typography(name),
                }}
            />
            <div className="text-base">
                {children}
            </div>
            <span className="block clear-both" />
        </div>
    );
}
