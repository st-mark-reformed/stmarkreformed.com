// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/naming-convention */
import React, {
    Dispatch,
    SetStateAction,
    useState,
} from 'react';
import { MagnifyingGlassIcon } from '@heroicons/react/24/solid';
import { XMarkIcon } from '@heroicons/react/16/solid';
import { useRouter } from 'next/navigation';
import Select from 'react-select';
import useMessagesSearchParams from './useMessagesSearchParams';
import { ByOptions } from '../repository/FindAllByOptions';
import { MessagesSearchParams } from './MessagesSearchParams';
import RenderOnMount from '../../../RenderOnMount';

export default function SearchFormData (
    {
        formIsShown,
        setFormIsShown,
        byOptions,
        seriesOptions,
    }: {
        formIsShown: boolean;
        setFormIsShown: (enabled: boolean) => void | Dispatch<SetStateAction<boolean>>;
        byOptions: ByOptions;
        seriesOptions: Record<string, string>;
    },
) {
    const router = useRouter();

    const { hasAnyParams, params } = useMessagesSearchParams();

    const [applyButtonDisabled, setApplyButtonDisabled] = useState(
        true,
    );

    const [formInputs, setFormInputs] = useState<MessagesSearchParams>(params);

    return (
        <form
            onSubmit={(e) => {
                e.preventDefault();

                const url = new URL(window.location.href.split('?')[0]);

                formInputs.by.forEach((byItem) => {
                    url.searchParams.append('by[]', byItem);
                });

                formInputs.series.forEach((seriesItem) => {
                    url.searchParams.append('by[]', seriesItem);
                });

                if (formInputs.scripture_reference) {
                    url.searchParams.append(
                        'scripture_reference',
                        formInputs.scripture_reference,
                    );
                }

                if (formInputs.title) {
                    url.searchParams.append('title', formInputs.title);
                }

                if (formInputs.date_range_start) {
                    url.searchParams.append(
                        'date_range_start',
                        formInputs.date_range_start,
                    );
                }

                if (formInputs.date_range_end) {
                    url.searchParams.append(
                        'date_range_end',
                        formInputs.date_range_end,
                    );
                }

                setApplyButtonDisabled(true);

                router.push(url.toString());
            }}
            onChange={() => setApplyButtonDisabled(false)}
            className={(() => {
                const classes = [
                    'max-w-6xl mx-auto space-y-4 transition-all duration-400 ease-in-out',
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
                        <RenderOnMount>
                            <Select
                                classNames={{
                                    container: () => ('react-select-control shadow-sm'),
                                }}
                                options={[
                                    {
                                        label: 'St. Mark Leadership',
                                        options: Object.entries(byOptions.leadership).map(([slug, name]) => ({
                                            value: slug,
                                            label: name,
                                        })),
                                    },
                                    {
                                        label: 'Other Speakers',
                                        options: Object.entries(byOptions.others).map(([slug, name]) => ({
                                            value: slug,
                                            label: name,
                                        })),
                                    },
                                ]}
                                value={(() => {
                                    // @ts-expect-error TS7034
                                    const selected = [];

                                    const all = Object.entries(byOptions.leadership).concat(Object.entries(byOptions.others));

                                    all.forEach(([slug, name]) => {
                                        if (!formInputs.by.includes(slug)) {
                                            return;
                                        }

                                        selected.push({
                                            value: slug,
                                            label: name,
                                        });
                                    });

                                    // @ts-expect-error TS7005
                                    return selected;
                                })()}
                                onChange={(values) => {
                                    setApplyButtonDisabled(false);

                                    setFormInputs({
                                        ...formInputs,
                                        by: values.map((val) => val.value),
                                    });
                                }}
                                isMulti
                            />
                        </RenderOnMount>
                    </div>
                </div>
                {/* Series */}
                <div className="sm:col-span-2">
                    <label htmlFor="by" className="block text-sm font-medium text-gray-700">
                        Series
                    </label>
                    <div className="mt-1">
                        <RenderOnMount>
                            <Select
                                classNames={{
                                    container: () => ('react-select-control shadow-sm'),
                                }}
                                options={Object.entries(seriesOptions).map(([slug, name]) => ({
                                    value: slug,
                                    label: name,
                                }))}
                                value={(() => {
                                    // @ts-expect-error TS7034
                                    const selected = [];

                                    Object.entries(seriesOptions).forEach(([slug, name]) => {
                                        if (!formInputs.series.includes(slug)) {
                                            return;
                                        }

                                        selected.push({
                                            value: slug,
                                            label: name,
                                        });
                                    });

                                    // @ts-expect-error TS7005
                                    return selected;
                                })()}
                                onChange={(values) => {
                                    setApplyButtonDisabled(false);

                                    setFormInputs({
                                        ...formInputs,
                                        series: values.map((val) => val.value),
                                    });
                                }}
                                isMulti
                            />
                        </RenderOnMount>
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
                            value={formInputs.scripture_reference}
                            onChange={(e) => {
                                setFormInputs({
                                    ...formInputs,
                                    scripture_reference: e.currentTarget.value,
                                });
                            }}
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
                            value={formInputs.title}
                            onChange={(e) => {
                                setFormInputs({
                                    ...formInputs,
                                    title: e.currentTarget.value,
                                });
                            }}
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
                            value={formInputs.date_range_start}
                            onChange={(e) => {
                                setFormInputs({
                                    ...formInputs,
                                    date_range_start: e.currentTarget.value,
                                });
                            }}
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
                            value={formInputs.date_range_end}
                            onChange={(e) => {
                                setFormInputs({
                                    ...formInputs,
                                    date_range_end: e.currentTarget.value,
                                });
                            }}
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
                        <button
                            type="button"
                            className="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crimson cursor-pointer"
                            onClick={() => {
                                setFormIsShown(false);

                                setFormInputs({
                                    by: [],
                                    series: [],
                                    scripture_reference: '',
                                    title: '',
                                    date_range_start: '',
                                    date_range_end: '',
                                });

                                router.push(
                                    window.location.href.split('?')[0],
                                );
                            }}
                        >
                            Clear Search
                            <span className="inline-block w-5 h-5 mb-0.5 ml-1 align-middle">
                                <XMarkIcon />
                            </span>
                        </button>
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
