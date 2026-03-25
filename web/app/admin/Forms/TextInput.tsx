import React, { HTMLInputAutoCompleteAttribute, HTMLInputTypeAttribute } from 'react';
import InputWrapper from './InputWrapper';

export default function TextInput (
    {
        label,
        name,
        type = 'text',
        autoComplete = undefined,
        defaultValue = undefined,
        colSpan = undefined,
        error = undefined,
    }: {
        label: string;
        name: string;
        type?: HTMLInputTypeAttribute | undefined;
        autoComplete?: HTMLInputAutoCompleteAttribute | undefined;
        defaultValue?: string | readonly string[] | number | undefined;
        colSpan?: number | 'full' | undefined;
        error?: string | undefined;
    },
) {
    return (
        <InputWrapper label={label} name={name} colSpan={colSpan} error={error}>
            <input
                id={name}
                name={name}
                type={type}
                autoComplete={autoComplete}
                className={(() => {
                    const classes = ['block w-full rounded-md px-3 py-1.5 text-base text-gray-900 appearance-none border-0 outline-none ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6 dark:text-white dark:ring-white/10 dark:placeholder:text-gray-500'];

                    if (error) {
                        classes.push('bg-crimson/20 dark:bg-crimson/40');
                    } else {
                        classes.push('bg-white dark:bg-white/5');
                    }

                    return classes.join(' ');
                })()}
                defaultValue={defaultValue}
            />
        </InputWrapper>
    );
}
