import React, { useEffect, useState } from 'react';
import SearchableDropdown, { Option } from './SearchableDropdown';
import PartialPageLoading from '../../PartialPageLoading';

export default function SeriesSelector (
    {
        label,
        name,
        defaultValue = undefined,
        error = undefined,
    }: {
        label: string;
        name: string;
        defaultValue?: string | undefined;
        error?: string | undefined;
    },
) {
    const [
        seriesOptions,
        setSeriesOptions,
    ] = useState<Option[] | null>(null);

    const [loadingError, setLoadingError] = useState<string | null>(null);

    useEffect(() => {
        let cancelled = false;

        async function loadData () {
            try {
                const response = await fetch(
                    '/admin/messages/series/dropdown-list',
                    {
                        cache: 'no-store',
                    },
                );

                if (!response.ok) {
                    throw new Error('Failed to load series positions');
                }

                const data = await response.json() as Option[];

                if (cancelled) {
                    return;
                }

                setSeriesOptions(data);
            } catch (error_) {
                if (cancelled) {
                    return;
                }

                setLoadingError(
                    error_ instanceof Error
                        ? error_.message
                        : 'Something went wrong',
                );
            }
        }

        loadData();

        return () => {
            cancelled = true;
        };
    });

    if (loadingError) {
        return <p className="text-sm text-red-600">{loadingError}</p>;
    }

    if (!seriesOptions) {
        return <PartialPageLoading />;
    }

    return (
        <SearchableDropdown
            label={label}
            name={name}
            options={seriesOptions}
            defaultValue={defaultValue}
            error={error}
        />
    );
}
