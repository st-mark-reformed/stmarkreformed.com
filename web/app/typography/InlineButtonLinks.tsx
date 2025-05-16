// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import typography from './typography';

export default function InlineButtonLinks (
    {
        buttons,
    }: {
        buttons: Array<{
            content: string;
            href: string;
            useDownloadAttribute?: boolean;
        }>;
    },
) {
    const totalButtons = buttons.length;

    return (
        <div className="text-left">
            <div>
                {buttons.map((button, i) => {
                    const wrapperClasses = [
                        'inline-block',
                        'mt-2',
                    ];

                    if ((i + 1) < totalButtons) {
                        wrapperClasses.push('mr-2');
                    }

                    return (
                        <div
                            key={`${button.href}-${button.content}`}
                            className={wrapperClasses.join(' ')}
                        >
                            <div className="inline-flex rounded-md shadow">
                                <Link
                                    href={button.href}
                                    className="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-100 bg-crimson hover:bg-crimson-dark not-prose"
                                    dangerouslySetInnerHTML={{
                                        __html: typography(button.content),
                                    }}
                                    download={button.useDownloadAttribute === true}
                                />
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
