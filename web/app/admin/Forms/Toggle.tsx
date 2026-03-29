import React from 'react';
import InputWrapper from './InputWrapper';

export default function Toggle (
    {
        label,
        name,
        defaultValue = false,
        colSpan = undefined,
        error = undefined,
    }: {
        label: string;
        name: string;
        defaultValue?: boolean;
        colSpan?: number | 'full' | undefined;
        error?: string | undefined;
    },
) {
    return (
        <InputWrapper label={label} name={name} colSpan={colSpan} error={error}>
            {/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
            <label className="relative inline-flex cursor-pointer items-center">
                <input
                    id={name}
                    name={name}
                    type="checkbox"
                    defaultChecked={defaultValue}
                    className="peer sr-only"
                />
                <span className="relative h-6 w-11 rounded-full bg-gray-300 transition-colors duration-200 ease-in-out peer-checked:bg-crimson dark:bg-white/10 dark:peer-checked:bg-crimson/70" />
                <span className="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm ring-1 ring-gray-900/5 transition-transform duration-200 ease-in-out peer-checked:translate-x-5" />
            </label>
        </InputWrapper>
    );
}
