import React, { useEffect, useState } from 'react';
import { ExclamationTriangleIcon } from '@heroicons/react/24/outline';
import Select from 'react-select/base';
import { Options } from '../../../profiles/Repository/GetSelectProfileOptions';
import GetSeriesSelectOptions from '../series-manager/Repository/GetSeriesSelectOptions';
import InputLoading from '../../../inputs/InputLoading';

export default function SelectSeries (
    {
        label = 'Series',
        name = 'seriesId',
        value,
        setValue,
    }: {
        label?: string;
        name?: string;
        value: string;
        setValue: (val: string) => void;
    },
) {
    const [isOpen, setIsOpen] = useState(false);

    const [searchValue, setSearchValue] = useState('');

    const [options, setOptions] = useState<Options | null | 'loading'>(
        'loading',
    );

    useEffect(() => {
        GetSeriesSelectOptions()
            .then((loadedOptions) => {
                setOptions(loadedOptions);
            })
            .catch(() => {
                setOptions(null);
            });
    }, []);

    if (options === 'loading') {
        return <InputLoading label={label} />;
    }

    if (!Array.isArray(options)) {
        return (
            <InputLoading
                label={label}
                loadingText={(
                    <span className="text-red-500 text-base not-italic">
                        <ExclamationTriangleIcon className="h-4 w-4 inline-block align-middle mb-0.5 mr-1" />
                        {label} could not be loaded!
                    </span>
                )}
            />
        );
    }

    return (
        <div>
            <label
                htmlFor={name}
                className="block text-sm font-semibold leading-6 text-gray-900"
            >
                {label}
            </label>
            <div className="mt-2">
                <Select
                    classNames={{
                        container: () => ('react-select-control'),
                    }}
                    onMenuOpen={() => {
                        setIsOpen(true);
                    }}
                    onMenuClose={() => {
                        setIsOpen(false);
                        setSearchValue('');
                    }}
                    menuIsOpen={isOpen}
                    onInputChange={(val) => {
                        setSearchValue(val);
                    }}
                    // @ts-expect-error TS2322
                    onChange={(val: Option) => {
                        setValue(val?.value || '');
                    }}
                    options={options}
                    // @ts-expect-error TS2322
                    defaultValue={options.filter((option) => option.value === value)[0]}
                    inputValue={searchValue}
                    value={options.filter((option) => option.value === value)[0]}
                    isClearable
                />
            </div>
        </div>
    );
}
