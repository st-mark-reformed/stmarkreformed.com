import React from 'react';
import { Label, Radio, RadioGroup } from '@headlessui/react';

function classNames (...classes: Array<string>) {
    return classes.filter(Boolean).join(' ');
}

export default function RadioPanel (
    {
        label,
        name,
        value,
        setValue,
        options,
        maxWidth = '',
    }: {
        label: string;
        name: string;
        value: string;
        setValue: (key: string, val: string) => void;
        options: Array<{
            name: string;
            val: string;
        }>;
        maxWidth?: '' | 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    },
) {
    let maxWidthClass = '';

    if (maxWidth === 'xs') {
        maxWidthClass = 'max-w-xs';
    } else if (maxWidth === 'sm') {
        maxWidthClass = 'max-w-sm';
    } else if (maxWidth === 'md') {
        maxWidthClass = 'max-w-md';
    } else if (maxWidth === 'lg') {
        maxWidthClass = 'max-w-lg';
    } else if (maxWidth === 'xl') {
        maxWidthClass = 'max-w-xl';
    }

    return (
        <div className={maxWidthClass}>
            <label
                htmlFor={name}
                className="block text-sm font-semibold leading-6 text-gray-900"
            >
                {label}
            </label>
            <div className="mt-2">
                <RadioGroup
                    value={value}
                    onChange={(val) => {
                        setValue(name, val);
                    }}
                >
                    <Label className="sr-only">
                        {label}
                    </Label>
                    <div className="relative -space-y-px rounded-md bg-white">
                        {options.map((option, planIdx) => (
                            <Radio
                                key={option.name}
                                value={option.val}
                                className={({ checked }) => classNames(
                                    planIdx === 0 ? 'rounded-tl-md rounded-tr-md' : '',
                                    planIdx === options.length - 1 ? 'rounded-bl-md rounded-br-md' : '',
                                    checked ? 'z-10 border-cyan-600 bg-cyan-50' : 'border-gray-200',
                                    'relative flex cursor-pointer flex-col border p-4 focus:outline-none md:pl-4 md:pr-6',
                                )}
                            >
                                {({ checked }) => (
                                    <>
                                        <span className="flex items-center text-sm">
                                            <span
                                                className={classNames(
                                                    checked ? 'bg-cyan-600 border-transparent' : 'bg-white border-gray-300',
                                                    'h-4 w-4 rounded-full border flex items-center justify-center',
                                                )}
                                                aria-hidden="true"
                                            >
                                                <span className="rounded-full bg-white w-1.5 h-1.5" />
                                            </span>
                                            <Label
                                                as="span"
                                                className={classNames(checked ? 'text-cyan-600' : 'text-gray-900', 'ml-3 font-medium')}
                                            >
                                                {option.name}
                                            </Label>
                                        </span>
                                    </>
                                )}
                            </Radio>
                        ))}
                    </div>
                </RadioGroup>
            </div>
        </div>
    );
}
