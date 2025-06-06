// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/naming-convention */
import React, {
    Dispatch, SetStateAction, useActionState, useState,
} from 'react';
import { MagnifyingGlassIcon } from '@heroicons/react/24/solid';
import { XMarkIcon } from '@heroicons/react/16/solid';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import useMessagesSearchParams from './useMessagesSearchParams';

export default function SearchFormData (
    {
        formIsShown,
        setFormIsShown,
    }: {
        formIsShown: boolean;
        setFormIsShown: (enabled: boolean) => void | Dispatch<SetStateAction<boolean>>;
    },
) {
    const router = useRouter();

    const { hasAnyParams, params } = useMessagesSearchParams();

    const [applyButtonDisabled, setApplyButtonDisabled] = useState(
        true,
    );

    const {
        by,
        series,
        scripture_reference,
        title,
        date_range_start,
        date_range_end,
    } = params;

    const [, formAction] = useActionState(
        (prevState: void, formData: FormData) => {
            const formBy = formData.getAll('by[]').filter(
                (byItem) => typeof byItem === 'string',
            );

            const formSeries = formData.getAll('series[]').filter(
                (seriesItem) => typeof seriesItem === 'string',
            );

            const formScripture = formData.get('scripture_reference');

            const formTitle = formData.get('title');

            const formDateStart = formData.get('date_range_start');

            const formDateEnd = formData.get('date_range_end');

            const url = new URL(window.location.href.split('?')[0]);

            formBy.forEach((byItem: string) => {
                url.searchParams.append('by[]', byItem);
            });

            formSeries.forEach((seriesItem) => {
                url.searchParams.append('series[]', seriesItem);
            });

            if (typeof formScripture === 'string' && formScripture) {
                url.searchParams.append(
                    'scripture_reference',
                    formScripture,
                );
            }

            if (typeof formTitle === 'string' && formTitle) {
                url.searchParams.append('title', formTitle);
            }

            if (typeof formDateStart === 'string' && formDateStart) {
                url.searchParams.append(
                    'date_range_start',
                    formDateStart,
                );
            }

            if (typeof formDateEnd === 'string' && formDateEnd) {
                url.searchParams.append(
                    'date_range_end',
                    formDateEnd,
                );
            }

            router.push(url.toString());

            return prevState;
        },
        undefined,
    );

    return (
        <form
            action={formAction}
            onChange={() => setApplyButtonDisabled(false)}
            className={(() => {
                const classes = [
                    'max-w-6xl mx-auto space-y-4 overflow-hidden transition-all duration-400 ease-in-out',
                ];

                if (formIsShown) {
                    classes.push('max-h-[800px] sm:max-h-[400px] opacity-100');
                } else {
                    classes.push('max-h-0 opacity-0');
                }

                return classes.join(' ');
            })()}
        >
            <div className="my-6 grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                {/* By */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        By
                    </label>
                    <div className="mt-1">
                        <select
                            id="by"
                            name="by[]"
                            className="block focus:ring-crimson focus:border-crimson w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            multiple
                        >
                            <optgroup label="TODO">
                                <option>Some Option</option>
                                <option>Another Option</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                {/* Series */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        Series
                    </label>
                    <div className="mt-1">
                        <select
                            id="series"
                            name="series[]"
                            className="block focus:ring-crimson focus:border-crimson w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            multiple
                        >
                            <optgroup label="TODO">
                                <option>Some Option</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                {/* Scripture Reference */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        Scripture Reference
                    </label>
                    <div className="mt-1">
                        <input
                            type="text"
                            id="scripture_reference"
                            name="scripture_reference"
                            className="shadow-sm focus:ring-crimson focus:border-crimson block w-full sm:text-sm border-gray-300 rounded-md"
                            defaultValue={scripture_reference}
                        />
                    </div>
                </div>
                {/* Title */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        Title
                    </label>
                    <div className="mt-1">
                        <input
                            type="text"
                            id="title"
                            name="title"
                            className="shadow-sm focus:ring-crimson focus:border-crimson block w-full sm:text-sm border-gray-300 rounded-md"
                            defaultValue={title}
                        />
                    </div>
                </div>
                {/* Date Range Start */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        Date Range Start
                    </label>
                    <div className="mt-1">
                        <input
                            type="date"
                            id="date_range_start"
                            name="date_range_start"
                            className="shadow-sm focus:ring-crimson focus:border-crimson block w-full sm:text-sm border-gray-300 rounded-md"
                            defaultValue={date_range_start}
                        />
                    </div>
                </div>
                {/* Date Range End */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        Date Range End
                    </label>
                    <div className="mt-1">
                        <input
                            type="date"
                            id="date_range_end"
                            name="date_range_end"
                            className="shadow-sm focus:ring-crimson focus:border-crimson block w-full sm:text-sm border-gray-300 rounded-md"
                            defaultValue={date_range_end}
                        />
                    </div>
                </div>
            </div>
            <div className="flex justify-end mb-2">
                {(() => {
                    if (!hasAnyParams) {
                        return null;
                    }

                    return (
                        <Link
                            href="/media/messages-test"
                            className="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crimson cursor-pointer"
                            onClick={() => {
                                setFormIsShown(false);
                            }}
                        >
                            Clear Search
                            <span className="inline-block w-5 h-5 mb-0.5 ml-1 align-middle">
                                <XMarkIcon />
                            </span>
                        </Link>
                    );
                })()}
                <button
                    type="submit"
                    className={(() => {
                        const classes = [
                            'ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md focus:outline-none',
                        ];

                        if (applyButtonDisabled) {
                            classes.push('bg-gray-300');
                        } else {
                            classes.push('text-white bg-crimson hover:bg-crimson-dark cursor-pointer');
                        }

                        return classes.join(' ');
                    })()}
                    disabled={applyButtonDisabled}
                >
                    Apply Search
                    <span className="inline-block w-4 h-4 mt-0.5 ml-1 align-middle">
                        <MagnifyingGlassIcon />
                    </span>
                </button>
            </div>
        </form>
    );
}
