'use client';

import React, { useState } from 'react';
import SearchFormData from './SearchFormData';
import ToggleWithLabel from '../../../components/Toggle/ToggleWithLabel';
import useMessagesSearchParams from './useMessagesSearchParams';

export default function SearchForm () {
    const messagesSearchParams = useMessagesSearchParams();

    const [formIsVisible, setFormIsVisible] = useState(
        messagesSearchParams.hasAnyParams,
    );

    return (
        <>
            <div className="flex justify-center w-full mb-4">
                <ToggleWithLabel
                    enabled={formIsVisible}
                    setEnabled={setFormIsVisible}
                    label={(() => {
                        if (formIsVisible) {
                            return 'Close Search';
                        }

                        return 'Open Search';
                    })()}
                />
            </div>
            <SearchFormData formIsShown={formIsVisible} />
        </>
    );
}
