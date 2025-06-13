import React, { Dispatch, SetStateAction } from 'react';
import {
    CheckCircleIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
    XCircleIcon,
} from '@heroicons/react/20/solid';

type ClassesType = {
    backgroundClass: string;
    iconClass: string;
    headingClass: string;
    bodyClass: string;
    buttonClass: string;
};

export default function Message (
    {
        isVisible = true,
        setIsVisible,
        type = 'success',
        heading,
        body,
        padBottom,
    }: {
        isVisible?: boolean;
        setIsVisible?: Dispatch<SetStateAction<boolean>>;
        type?: 'success' | 'error' | 'note' | 'warning';
        heading?: string;
        body?: Array<string>;
        padBottom?: boolean;
    },
) {
    if (!isVisible) {
        return null;
    }

    let classes = {} as ClassesType;

    if (type === 'success') {
        classes = {
            backgroundClass: 'bg-green-50',
            iconClass: 'text-green-400',
            headingClass: 'text-green-800',
            bodyClass: 'text-green-700',
            buttonClass: [
                'bg-green-100',
                'text-green-800',
                'hover:bg-green-200',
                'focus:ring-green-600',
                'focus:ring-offset-green-50',
            ].join(' '),
        };
    } else if (type === 'error') {
        classes = {
            backgroundClass: 'bg-red-50',
            iconClass: 'text-red-400',
            headingClass: 'text-red-800',
            bodyClass: 'text-red-700',
            buttonClass: [
                'bg-red-100',
                'text-red-800',
                'hover:bg-red-200',
                'focus:ring-red-600',
                'focus:ring-offset-red-50',
            ].join(' '),
        };
    } else if (type === 'note') {
        classes = {
            backgroundClass: 'bg-cyan-50',
            iconClass: 'text-cyan-400',
            headingClass: 'text-cyan-800',
            bodyClass: 'text-cyan-700',
            buttonClass: [
                'bg-cyan-100',
                'text-cyan-800',
                'hover:bg-cyan-200',
                'focus:ring-cyan-600',
                'focus:ring-offset-cyan-50',
            ].join(' '),
        };
    } else if (type === 'warning') {
        classes = {
            backgroundClass: 'bg-yellow-50',
            iconClass: 'text-yellow-400',
            headingClass: 'text-yellow-800',
            bodyClass: 'text-yellow-700',
            buttonClass: [
                'bg-yellow-100',
                'text-yellow-800',
                'hover:bg-yellow-200',
                'focus:ring-yellow-600',
                'focus:ring-offset-yellow-50',
            ].join(' '),
        };
    }

    return (
        <div className={padBottom ? 'pb-4' : ''}>
            <div className={`rounded-md p-4 ${classes.backgroundClass}`}>
                <div className="flex">
                    <div className="flex-shrink-0">
                        {(() => {
                            if (type === 'success') {
                                return (
                                    <CheckCircleIcon
                                        className={`h-5 w-5 ${classes.iconClass}`}
                                        aria-hidden="true"
                                    />
                                );
                            }

                            if (type === 'error') {
                                return (
                                    <XCircleIcon
                                        className={`h-5 w-5 ${classes.iconClass}`}
                                        aria-hidden="true"
                                    />
                                );
                            }

                            if (type === 'note') {
                                return (
                                    <InformationCircleIcon
                                        className={`h-5 w-5 ${classes.iconClass}`}
                                        aria-hidden="true"
                                    />
                                );
                            }

                            if (type === 'warning') {
                                return (
                                    <ExclamationCircleIcon
                                        className={`h-5 w-5 ${classes.iconClass}`}
                                        aria-hidden="true"
                                    />
                                );
                            }

                            return null;
                        })()}
                    </div>
                    <div className="ml-3">
                        {(() => {
                            if (!heading) {
                                return null;
                            }

                            return (
                                <h3 className={`text-sm font-medium ${classes.headingClass}`}>
                                    {heading}
                                </h3>
                            );
                        })()}
                        {(() => {
                            if (!body) {
                                return null;
                            }

                            return (
                                <>
                                    {body.map((bodyItem) => (
                                        <div
                                            key={bodyItem}
                                            className={`mt-2 text-sm ${classes.bodyClass}`}
                                        >
                                            <p>
                                                {bodyItem}
                                            </p>
                                        </div>
                                    ))}
                                </>
                            );
                        })()}
                        {(() => {
                            if (!setIsVisible) {
                                return null;
                            }

                            return (
                                <div className="mt-4">
                                    <div className="-mx-2 -my-1.5 flex">
                                        <button
                                            type="button"
                                            className={`rounded-md px-2 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer ${classes.buttonClass}`}
                                            onClick={() => {
                                                setIsVisible(false);
                                            }}
                                        >
                                            Dismiss
                                        </button>
                                    </div>
                                </div>
                            );
                        })()}
                    </div>
                </div>
            </div>
        </div>
    );
}
