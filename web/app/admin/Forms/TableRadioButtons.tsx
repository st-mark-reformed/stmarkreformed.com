import React from 'react';
import InputWrapper from './InputWrapper';

interface Option {
    name: string;
    label: string;
    defaultChecked?: boolean;
}

export default function TableRadioButtons (
    {
        label,
        name,
        colSpan = undefined,
        options,
    }: {
        label: string;
        name: string;
        colSpan?: number | 'full' | undefined;
        options: Option[];
    },
) {
    return (
        <InputWrapper label={label} name={name} colSpan={colSpan}>
            <fieldset
                aria-label={label}
                className="relative -space-y-px rounded-md bg-white dark:bg-gray-800/50"
            >
                {options.map((option) => (
                    <label
                        key={option.name}
                        aria-label={option.label}
                        aria-description=""
                        className="group flex flex-col border border-gray-200 p-4 first:rounded-tl-md first:rounded-tr-md last:rounded-br-md last:rounded-bl-md focus:outline-hidden has-checked:relative has-checked:border-crimson/15 has-checked:bg-crimson/10 md:grid md:grid-cols-1 md:pr-6 md:pl-4 dark:border-gray-700 dark:has-checked:border-crimson/25 dark:has-checked:bg-crimson/15"
                    >
                        <span className="flex items-center gap-3 text-sm">
                            <input
                                defaultValue={option.name}
                                defaultChecked={option.defaultChecked}
                                name={name}
                                type="radio"
                                className="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-crimson checked:bg-crimson focus-visible:outline-2 focus-visible:outline-offset-2 focus:outline-crimson focus-visible:outline-crimson disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 dark:border-white/10 dark:bg-white/5 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:before:bg-white/20 forced-colors:appearance-auto forced-colors:before:hidden"
                            />
                            <span className="font-medium text-gray-900 dark:text-white">
                                {option.label}
                            </span>
                        </span>
                    </label>
                ))}
            </fieldset>
        </InputWrapper>
    );
}
