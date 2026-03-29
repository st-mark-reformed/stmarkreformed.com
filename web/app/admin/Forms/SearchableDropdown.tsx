import React, {
    useEffect, useMemo, useRef, useState,
} from 'react';
import { ChevronDownIcon } from '@heroicons/react/24/solid';
import InputWrapper from './InputWrapper';

export type Option = {
    value: string;
    label: string;
};

export default function SearchableDropdown (
    {
        label,
        name,
        options,
        defaultValue = '',
        colSpan = undefined,
        error = undefined,
        placeholder = 'Search...',
        noOptionsText = 'No matching options',
    }: {
        label: string;
        name: string;
        options: Option[];
        defaultValue?: string;
        colSpan?: number | 'full' | undefined;
        error?: string | undefined;
        placeholder?: string;
        noOptionsText?: string;
    },
) {
    const selectedOption = options.find((option) => option.value === defaultValue) ?? null;
    const [query, setQuery] = useState(selectedOption?.label ?? '');
    const [isOpen, setIsOpen] = useState(false);
    const [selectedValue, setSelectedValue] = useState(defaultValue);
    const [activeIndex, setActiveIndex] = useState(-1);
    const containerRef = useRef<HTMLDivElement>(null);

    const filteredOptions = useMemo(() => {
        const normalizedQuery = query.trim().toLowerCase();

        if (!normalizedQuery) {
            return options;
        }

        return options.filter((option) => option.label.toLowerCase().includes(normalizedQuery));
    }, [options, query]);

    const selectedLabel = options.find((option) => option.value === selectedValue)?.label ?? '';

    useEffect(() => {
        if (!isOpen) {
            setActiveIndex(-1);

            return;
        }

        if (filteredOptions.length === 0) {
            setActiveIndex(-1);

            return;
        }

        setActiveIndex((currentActiveIndex) => {
            if (currentActiveIndex >= 0 && currentActiveIndex < filteredOptions.length) {
                return currentActiveIndex;
            }

            return 0;
        });
    }, [filteredOptions, isOpen]);

    return (
        <InputWrapper label={label} name={name} colSpan={colSpan} error={error}>
            <div
                ref={containerRef}
                className="relative"
                onBlur={(event) => {
                    if (containerRef.current?.contains(event.relatedTarget as Node)) {
                        return;
                    }

                    setIsOpen(false);
                    setQuery(selectedLabel);
                }}
            >
                <input
                    id={`${name}-search`}
                    type="text"
                    value={query}
                    placeholder={placeholder}
                    onChange={(event) => {
                        setQuery(event.target.value);
                        setIsOpen(true);
                        setSelectedValue('');
                    }}
                    onFocus={() => {
                        setIsOpen(true);
                    }}
                    onKeyDown={(event) => {
                        if (!isOpen) {
                            return;
                        }

                        if (filteredOptions.length === 0) {
                            return;
                        }

                        if (event.key === 'ArrowDown') {
                            event.preventDefault();
                            setActiveIndex((currentActiveIndex) => {
                                if (currentActiveIndex < 0) {
                                    return 0;
                                }

                                return (currentActiveIndex + 1) % filteredOptions.length;
                            });

                            return;
                        }

                        if (event.key === 'ArrowUp') {
                            event.preventDefault();
                            setActiveIndex((currentActiveIndex) => {
                                if (currentActiveIndex < 0) {
                                    return filteredOptions.length - 1;
                                }

                                return (currentActiveIndex - 1 + filteredOptions.length) % filteredOptions.length;
                            });

                            return;
                        }

                        if (event.key === 'Enter') {
                            const activeOption = filteredOptions[activeIndex];

                            if (activeOption && activeOption.value !== selectedValue) {
                                event.preventDefault();
                                setSelectedValue(activeOption.value);
                                setQuery(activeOption.label);
                                setIsOpen(false);
                            }

                            return;
                        }

                        if (event.key === 'Escape') {
                            event.preventDefault();
                            setIsOpen(false);
                            setQuery(selectedLabel);
                        }
                    }}
                    className={(() => {
                        const classes = [
                            'block w-full rounded-md px-3 py-1.5 pr-10 text-base appearance-none border-0 outline-none',
                            'ring-1 ring-inset ring-gray-300 dark:ring-white/10',
                            'placeholder:text-gray-400',
                            'focus:outline-none focus:ring-2 focus:ring-inset focus:ring-crimson-dark/50 sm:text-sm/6',
                            'text-gray-900 dark:text-white',
                        ];

                        if (error) {
                            classes.push('bg-crimson/20 dark:bg-crimson/40');
                        } else {
                            classes.push('bg-white dark:bg-white/5');
                        }

                        return classes.join(' ');
                    })()}
                    autoComplete="off"
                    aria-autocomplete="list"
                    aria-controls={`${name}-options`}
                    aria-activedescendant={isOpen && activeIndex >= 0 ? `${name}-option-${activeIndex}` : undefined}
                />

                <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <ChevronDownIcon
                        aria-hidden="true"
                        className={[
                            'h-4 w-4 text-gray-400 dark:text-gray-500 transition-transform duration-200',
                            isOpen ? '-rotate-180' : 'rotate-0',
                        ].join(' ')}
                    />
                </div>

                <input type="hidden" name={name} value={selectedValue} />

                {isOpen ? (
                    <div
                        id={`${name}-options`}
                        role="listbox"
                        className="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg dark:border-white/10 dark:bg-gray-900"
                    >
                        {filteredOptions.length > 0 ? (
                            filteredOptions.map((option, index) => (
                                <button
                                    key={option.value}
                                    id={`${name}-option-${index}`}
                                    type="button"
                                    role="option"
                                    aria-selected={option.value === selectedValue}
                                    className={(() => {
                                        const classes = [
                                            'block w-full px-3 py-2 text-left text-sm',
                                            'text-gray-900 hover:bg-gray-100',
                                            'dark:text-gray-100 dark:hover:bg-white/10',
                                        ];

                                        if (option.value === selectedValue) {
                                            classes.push('bg-gray-100 font-medium dark:bg-white/10');
                                        }

                                        if (index === activeIndex) {
                                            classes.push('bg-gray-100 dark:bg-white/10');
                                        }

                                        return classes.join(' ');
                                    })()}
                                    onMouseDown={(event) => {
                                        event.preventDefault();
                                    }}
                                    onMouseEnter={() => {
                                        setActiveIndex(index);
                                    }}
                                    onClick={() => {
                                        setSelectedValue(option.value);
                                        setQuery(option.label);
                                        setIsOpen(false);
                                    }}
                                >
                                    {option.label}
                                </button>
                            ))
                        ) : (
                            <div className="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                {noOptionsText}
                            </div>
                        )}
                    </div>
                ) : null}
            </div>
        </InputWrapper>
    );
}
