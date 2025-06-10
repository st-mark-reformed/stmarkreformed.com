import Link from 'next/link';
import React from 'react';
import { PrimaryOrSecondaryLink } from './PrimaryOrSecondaryLink';

function RenderButton (
    {
        link,
        classes,
    }: {
        link: PrimaryOrSecondaryLink | undefined;
        classes: Array<string>;
    },
) {
    if (!link) {
        return null;
    }

    if (link.onClick) {
        return (
            <button
                type="button"
                onClick={link.onClick}
                className={classes.join(' ')}
            >
                {link.content}
            </button>
        );
    }

    if (link.href) {
        return (
            <Link
                href={link.href}
                className={classes.join(' ')}
            >
                {link.content}
            </Link>
        );
    }

    return null;
}

export default function PageHeader (
    {
        title,
        subTitle,
        primaryLink,
        secondaryLink,
        RenderCustomButton,
    }: {
        title: string;
        subTitle?: string;
        primaryLink?: PrimaryOrSecondaryLink;
        secondaryLink?: PrimaryOrSecondaryLink;
        RenderCustomButton?: () => React.ReactElement;
    },
) {
    const primaryButtonMargin = secondaryLink && (secondaryLink.href || secondaryLink.onClick) ? 'ml-3' : '';

    return (
        <div className="bg-amber-950 px-4 py-5 sm:px-6 rounded-xl">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-white sm:truncate sm:text-3xl sm:tracking-tight pb-1.5">
                        {title}
                    </h2>
                    {(() => {
                        if (!subTitle) {
                            return null;
                        }

                        return (
                            <h3 className="leading-7 text-white sm:truncate sm:tracking-tight">
                                {subTitle}
                            </h3>
                        );
                    })()}
                </div>
                <div className="mt-4 flex md:ml-4 md:mt-0">
                    <RenderButton
                        link={secondaryLink}
                        classes={[
                            'inline-flex',
                            'items-center',
                            'rounded-md',
                            'bg-white/10',
                            'px-3',
                            'py-2',
                            'text-sm',
                            'font-semibold',
                            'text-white',
                            'shadow-sm',
                            'hover:bg-white/20',
                        ]}
                    />
                    <RenderButton
                        link={primaryLink}
                        classes={[
                            primaryButtonMargin,
                            'inline-flex',
                            'items-center',
                            'rounded-md',
                            'bg-cyan-600',
                            'px-3',
                            'py-2',
                            'text-sm',
                            'font-semibold',
                            'text-white',
                            'shadow-sm',
                            'hover:bg-cyan-500',
                            'focus-visible:outline',
                            'focus-visible:outline-2',
                            'focus-visible:outline-offset-2',
                            'focus-visible:outline-cyan-600',
                        ]}
                    />
                    {(() => {
                        if (!RenderCustomButton) {
                            return null;
                        }

                        return <RenderCustomButton />;
                    })()}
                </div>
            </div>
        </div>
    );
}
