// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/naming-convention */
import React from 'react';
import useMessagesSearchParams from './useMessagesSearchParams';

export default function SearchFormData (
    {
        formIsShown,
    }: {
        formIsShown: boolean;
    },
) {
    const { params } = useMessagesSearchParams();

    const {
        scripture_reference,
        title,
        date_range_start,
        date_range_end,
    } = params;

    return (
        <form className={(() => {
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
                            name="date_range_start"
                            className="shadow-sm focus:ring-crimson focus:border-crimson block w-full sm:text-sm border-gray-300 rounded-md"
                            defaultValue={date_range_end}
                        />
                    </div>
                </div>
            </div>
            <div className="flex justify-end mb-2">
                {/* {% if params.hasSearchParams %} */}
                <button
                    type="button"
                    className="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crimson cursor-pointer"
                >
                    Reset
                </button>
                {/* {% endif %} */}
                <button
                    type="submit"
                    className="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-crimson hover:bg-crimson-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crimson-dark cursor-pointer"
                >
                    Apply Search
                </button>
            </div>
        </form>
    );
}
