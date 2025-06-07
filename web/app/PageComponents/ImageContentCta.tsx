// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React, { ReactNode } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { ArrowTopRightOnSquareIcon } from '@heroicons/react/16/solid';
import { Url } from '../types/Url';
import typography from '../typography/typography';

export enum ImageContentCtaBackgroundColor {
    crimson,
    goldenrod,
    saddleBrown,
    bronze,
}

export enum ImageContentDisposition {
    imageLeftContentRight,
    contentLeftImageRight,
}

export interface ImageContentCtaConfig {
    backgroundColor?: ImageContentCtaBackgroundColor;
    contentDisposition?: ImageContentDisposition;
    image: string;
    showTealOverlayOnImage?: boolean;
    preHeading?: string | ReactNode;
    heading?: string | ReactNode;
    content: string | ReactNode;
    cta?: Url;
}

export default function ImageContentCta (
    {
        backgroundColor = ImageContentCtaBackgroundColor.crimson,
        contentDisposition = ImageContentDisposition.imageLeftContentRight,
        image,
        showTealOverlayOnImage,
        preHeading,
        heading,
        content,
        cta,
    }: ImageContentCtaConfig,
) {
    return (
        <div
            className={(() => {
                const classes = ['relative'];

                switch (backgroundColor) {
                    case ImageContentCtaBackgroundColor.goldenrod:
                        classes.push('bg-goldenrod');
                        break;
                    case ImageContentCtaBackgroundColor.saddleBrown:
                        classes.push('bg-saddle-brown');
                        break;
                    case ImageContentCtaBackgroundColor.bronze:
                        classes.push('bg-bronze');
                        break;
                    case ImageContentCtaBackgroundColor.crimson:
                    default:
                        classes.push('bg-crimson-dark');
                        break;
                }

                return classes.join(' ');
            })()}
        >
            <div
                className="relative h-56 sm:h-72 md:absolute md:left-0 md:h-full md:w-1/2"
                style={(() => {
                    if (contentDisposition === ImageContentDisposition.contentLeftImageRight) {
                        return { marginLeft: '50%' };
                    }

                    return {};
                })()}
            >
                <Image
                    className="w-full h-full object-cover"
                    src={image}
                    alt=""
                    width={3641}
                    height={1684}
                />
                {(() => {
                    if (!showTealOverlayOnImage) {
                        return null;
                    }

                    return (
                        <div
                            aria-hidden
                            className="absolute inset-0 bg-gradient-to-r from-teal-500 to-cyan-600 mix-blend-multiply"
                        />
                    );
                })()}
                {(() => {
                    if (!cta?.linkData) {
                        return null;
                    }

                    return (
                        <Link
                            href={cta.linkData}
                            className="block absolute inset-0 z-50"
                            {...cta.newWindow ? { target: '_blank' } : {}}
                        />
                    );
                })()}
            </div>
            <div className="relative mx-auto max-w-md px-4 py-12 sm:max-w-7xl sm:px-6 sm:py-20 md:py-28 lg:px-8 lg:py-32">
                <div
                    className={(() => {
                        const classes = ['md:w-1/2'];

                        if (contentDisposition === ImageContentDisposition.contentLeftImageRight) {
                            classes.push('md:mr-auto md:pr-10');
                        } else {
                            classes.push('md:ml-auto md:pl-10');
                        }

                        return classes.join(' ');
                    })()}
                >
                    {(() => {
                        if (!preHeading) {
                            return null;
                        }

                        const classes = ['text-base font-semibold uppercase tracking-wider text-gray-100'];

                        if (typeof preHeading === 'string') {
                            return (
                                <h3
                                    className={classes.join(' ')}
                                    dangerouslySetInnerHTML={{
                                        __html: typography(preHeading),
                                    }}
                                />
                            );
                        }

                        return <h3 className={classes.join(' ')}>{preHeading}</h3>;
                    })()}
                    {(() => {
                        if (!heading) {
                            return null;
                        }

                        const classes = ['mt-2 text-white text-3xl font-extrabold tracking-tight sm:text-4xl'];

                        if (typeof heading === 'string') {
                            return (
                                <h2
                                    className={classes.join(' ')}
                                    dangerouslySetInnerHTML={{
                                        __html: typography(heading),
                                    }}
                                />
                            );
                        }

                        return <h2 className={classes.join(' ')}>{preHeading}</h2>;
                    })()}
                    {(() => {
                        if (!content) {
                            return null;
                        }

                        const classes = ['mt-3 text-lg text-gray-100 prose prose-over-dark'];

                        if (typeof content === 'string') {
                            return (
                                <div
                                    className={classes.join(' ')}
                                    dangerouslySetInnerHTML={{
                                        __html: typography(content),
                                    }}
                                />
                            );
                        }

                        return <div className={classes.join(' ')}>{content}</div>;
                    })()}
                    {(() => {
                        if (!cta?.linkData || !cta.linkText) {
                            return null;
                        }

                        return (
                            <div className="mt-8">
                                <div className="inline-flex rounded-md shadow">
                                    <Link
                                        href={cta.linkData}
                                        className="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                        {...cta.newWindow ? { target: '_blank' } : {}}
                                    >
                                        <span
                                            dangerouslySetInnerHTML={{
                                                __html: typography(cta.linkText),
                                            }}
                                        />
                                        {(() => {
                                            if (!cta.newWindow) {
                                                return null;
                                            }

                                            return (
                                                <ArrowTopRightOnSquareIcon
                                                    className="-mr-1 ml-3 h-5 w-5 text-gray-400"
                                                />
                                            );
                                        })()}
                                    </Link>
                                </div>
                            </div>
                        );
                    })()}
                </div>
            </div>
        </div>
    );
}
