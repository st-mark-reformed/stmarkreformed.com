import React, { useEffect, useState } from 'react';
import SearchableDropdown, { Option } from './SearchableDropdown';
import PartialPageLoading from '../../PartialPageLoading';

export default function ProfileSelector (
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
        profiles,
        setProfiles,
    ] = useState<Option[] | null>(null);

    const [loadingError, setLoadingError] = useState<string | null>(null);

    useEffect(() => {
        let cancelled = false;

        async function loadData () {
            try {
                const response = await fetch(
                    '/admin/profiles/dropdown-list',
                    {
                        cache: 'no-store',
                    },
                );

                if (!response.ok) {
                    throw new Error('Failed to load leadership positions');
                }

                const data = await response.json() as Option[];

                if (cancelled) {
                    return;
                }

                setProfiles(data);
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

    if (!profiles) {
        return <PartialPageLoading />;
    }

    return (
        <SearchableDropdown
            label={label}
            name={name}
            options={profiles}
            defaultValue={defaultValue}
            error={error}
        />
    );
}
