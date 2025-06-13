import React from 'react';
import { PhotoIcon } from '@heroicons/react/24/solid';
import { DocumentIcon, MicrophoneIcon } from '@heroicons/react/24/outline';

export default function DndFileDisplay (
    {
        icon,
        text,
    }: {
        icon: 'photo-placeholder' | 'audio-file' | 'generic-file' | string;
        text: string;
    },
) {
    return (
        <div className="mb-3">
            {(() => {
                if (icon === 'photo-placeholder') {
                    return (
                        <PhotoIcon
                            className="mx-auto h-24 w-24 text-gray-300 -mb-2"
                            aria-hidden="true"
                        />
                    );
                }

                if (icon === 'audio-file') {
                    return (
                        <div className="relative">
                            <DocumentIcon
                                className="mx-auto h-18 w-18 text-gray-300"
                                aria-hidden="true"
                            />
                            <div className="absolute inset-0 flex items-center justify-center">
                                <MicrophoneIcon
                                    className="h-7 w-7 text-gray-300 mt-3"
                                    aria-hidden="true"
                                />
                            </div>
                        </div>
                    );
                }

                if (icon === 'generic-file') {
                    return (
                        <DocumentIcon
                            className="mx-auto h-18 w-18 text-gray-300"
                            aria-hidden="true"
                        />
                    );
                }

                return (
                    <img
                        src={icon}
                        alt=""
                        className="h-24 mx-auto object-contain mb-2"
                    />
                );
            })()}
            <span className="text-gray-500 text-sm">
                {text}
            </span>
        </div>
    );
}
