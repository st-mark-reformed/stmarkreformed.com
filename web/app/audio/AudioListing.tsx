// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import Link from 'next/link';
import smartypants from 'smartypants';
import { ArrowDownTrayIcon } from '@heroicons/react/24/outline';
import CustomAudioPlayer from './CustomAudioPlayer';

export default function AudioListing (
    {
        title,
        date = null,
        by = null,
        byUrl = null,
        series = null,
        seriesUrl = null,
        text = null,
        audioUrl = null,
        showBorder = false,
        permalink = null,
    }: {
        title: string;
        date?: string | null;
        by?: string | null;
        byUrl?: string | null;
        series?: string | null;
        seriesUrl?: string | null;
        text?: string | null;
        audioUrl?: string | null;
        showBorder?: boolean;
        permalink?: string | null;
    },
) {
    const wrapperClasses = [
        'max-w-3xl',
        'mx-auto',
        'px-8',
        'py-14',
        'border-gray-200',
    ];

    if (showBorder) {
        wrapperClasses.push('border-b');
    }

    return (
        <div className={wrapperClasses.join(' ')}>
            <h1 className="text-4xl font-bold tracking-tight sm:text-4xl text-center mb-10">
                {(() => {
                    if (!permalink) {
                        return (
                            <span
                                dangerouslySetInnerHTML={{
                                    __html: smartypants(title),
                                }}
                            />
                        );
                    }

                    return (
                        <Link
                            href={permalink}
                            className="text-crimson hover:text-crimson-dark inline-block align-middle not-prose"
                            dangerouslySetInnerHTML={{
                                __html: smartypants(title),
                            }}
                        />
                    );
                })()}
            </h1>
            <div className="prose max-w-none">
                {(() => {
                    if (!date) {
                        return null;
                    }

                    return (
                        <div className="mb-1">
                            <strong>Date</strong>: {date}
                        </div>
                    );
                })()}
                {(() => {
                    if (!by) {
                        return null;
                    }

                    if (!byUrl) {
                        return (
                            <div className="mb-1">
                                <strong>By</strong>: {by}
                            </div>
                        );
                    }

                    return (
                        <div className="mb-1">
                            <strong>By</strong>:
                            {' '}
                            <Link
                                className="text-crimson hover:text-crimson-dark underline"
                                href={byUrl}
                            >
                                {by}
                            </Link>
                        </div>
                    );
                })()}
                {(() => {
                    if (!series) {
                        return null;
                    }

                    if (!seriesUrl) {
                        return (
                            <div className="mb-1">
                                <strong>Series</strong>: {series}
                            </div>
                        );
                    }

                    return (
                        <div className="mb-1">
                            <strong>Series</strong>:
                            {' '}
                            <Link
                                className="text-crimson hover:text-crimson-dark underline"
                                href={seriesUrl}
                            >
                                {series}
                            </Link>
                        </div>
                    );
                })()}
                {(() => {
                    if (!text) {
                        return null;
                    }

                    return (
                        <div className="mb-1">
                            <strong>Text</strong>: {text}
                        </div>
                    );
                })()}
            </div>
            {(() => {
                if (!audioUrl) {
                    return null;
                }

                return (
                    <>
                        <div
                            className="py-6"
                            style={{
                                minHeight: '120px',
                            }}
                        >
                            <CustomAudioPlayer audioUrl={audioUrl} />
                        </div>
                        <a
                            href={audioUrl}
                            className="rounded-md bg-crimson px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-crimson-dark not-prose"
                            download
                        >
                            <ArrowDownTrayIcon className="h-4 w-4 -mt-1 mr-2 inline" />
                            Download MP3
                        </a>
                    </>
                );
            })()}
            {(() => {
                if (!permalink) {
                    return null;
                }

                return (
                    <div className="text-center mt-2">
                        <Link
                            className="text-tjd-red-500 hover:text-tjd-red-600"
                            href={permalink}
                        >
                            Permalink ›
                        </Link>
                    </div>
                );
            })()}
        </div>
    );
}
