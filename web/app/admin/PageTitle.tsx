import React, { ReactNode } from 'react';
import Link from 'next/link';
import { PencilIcon } from '@heroicons/react/24/solid';
import { CheckIcon, PlusIcon, XMarkIcon } from '@heroicons/react/16/solid';
import { EyeIcon, TrashIcon } from '@heroicons/react/24/outline';

export interface Button {
    content: string;
    href: string;
    onClick?: React.MouseEventHandler<HTMLButtonElement>;
    onClickButtonType?: 'submit' | 'reset' | 'button' | undefined;
    type: 'primary' | 'secondary' | 'pending';
    glyph?: 'pencil' | 'plus' | 'check' | 'eye' | 'trash' | 'x-mark';
}

export default function PageTitle (
    {
        children,
        buttons = [],
    }: {
        children: ReactNode;
        buttons?: Button[];
    },
) {
    return (
        <>
            <div className="my-4 md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight dark:text-white">
                        {children}
                    </h2>
                </div>
                {(() => {
                    if (buttons.length === 0) {
                        return null;
                    }

                    return (
                        <div className="mt-4 flex shrink-0 md:mt-0 md:ml-4">
                            {buttons.map((button) => {
                                const classes = ['ml-3 inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold shadow-xs select-none'];

                                if (button.type === 'primary') {
                                    classes.push('cursor-pointer bg-crimson text-white hover:bg-crimson-dark dark:bg-crimson/70 dark:shadow-none dark:hover:bg-crimson/80');
                                } else if (button.type === 'pending') {
                                    classes.push('cursor-default bg-gray-300 text-gray-500');
                                } else {
                                    classes.push('cursor-pointer bg-white text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20');
                                }

                                if (button.glyph) {
                                    classes.push('pr-4');
                                }

                                const iconClasses = 'size-5 mr-1 -ml-0.5';

                                const glyphRender = () => {
                                    if (button.glyph === 'pencil') {
                                        return <PencilIcon className={iconClasses} aria-hidden="true" />;
                                    }

                                    if (button.glyph === 'plus') {
                                        return <PlusIcon className={iconClasses} aria-hidden="true" />;
                                    }

                                    if (button.glyph === 'check') {
                                        return <CheckIcon className={iconClasses} aria-hidden="true" />;
                                    }

                                    if (button.glyph === 'eye') {
                                        return <EyeIcon className={iconClasses} aria-hidden="true" />;
                                    }

                                    if (button.glyph === 'trash') {
                                        return <TrashIcon className={iconClasses} aria-hidden="true" />;
                                    }

                                    if (button.glyph === 'x-mark') {
                                        return <XMarkIcon className={iconClasses} aria-hidden="true" />;
                                    }

                                    return null;
                                };

                                if (button.onClick) {
                                    return (
                                        <button
                                            // eslint-disable-next-line react/button-has-type
                                            type={button.onClickButtonType ?? 'button'}
                                            className={classes.join(' ')}
                                            key={button.href}
                                            onClick={button.onClick}
                                        >
                                            {glyphRender()}
                                            {button.content}
                                        </button>
                                    );
                                }

                                return (
                                    <Link
                                        className={classes.join(' ')}
                                        key={button.href}
                                        href={button.href}
                                    >
                                        {glyphRender()}
                                        {button.content}
                                    </Link>
                                );
                            })}
                        </div>
                    );
                })()}
            </div>
            <hr className="my-5 border-1 w-full border-gray-200 dark:border-gray-500" />
        </>
    );
}
