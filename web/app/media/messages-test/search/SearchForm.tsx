'use client';

import React, { useState } from 'react';
import { MagnifyingGlassIcon } from '@heroicons/react/24/solid';
import SearchFormData from './SearchFormData';
import ToggleWithLabel from '../../../components/Toggle/ToggleWithLabel';
import useMessagesSearchParams from './useMessagesSearchParams';
import { ByOptions } from '../repository/FindAllByOptions';

export default function SearchForm (
    {
        byOptions,
        seriesOptions,
    }: {
        byOptions: ByOptions;
        seriesOptions: Record<string, string>;
    },
) {
    const { hasAnyParams } = useMessagesSearchParams();

    const [formIsVisible, setFormIsVisible] = useState(false);

    return (
        <>
            {(() => {
                if (hasAnyParams) {
                    return null;
                }

                return (
                    <div className="flex justify-center w-full mb-4">
                        <ToggleWithLabel
                            enabled={formIsVisible}
                            setEnabled={setFormIsVisible}
                            label={(
                                <>
                                    {(() => {
                                        if (formIsVisible) {
                                            return 'Close Search';
                                        }

                                        return 'Open Search';
                                    })()}
                                    <span className="inline-block w-4 h-4 mb-1 ml-1 align-middle">
                                        <MagnifyingGlassIcon />
                                    </span>
                                </>
                            )}
                        />
                    </div>
                );
            })()}
            <SearchFormData
                formIsShown={formIsVisible || hasAnyParams}
                setFormIsShown={setFormIsVisible}
                byOptions={byOptions}
                seriesOptions={seriesOptions}
            />
        </>
    );
}
