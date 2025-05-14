'use client';

import React, { useRef, useState, useEffect } from 'react';

export default function SubscribeButtonClientSide (
    {
        icsUrl,
    }: {
        icsUrl: string;
    },
) {
    const [isOpen, setIsOpen] = useState(false);

    const inputRef = useRef<HTMLInputElement>(null);

    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        function handleClickOutside (event: MouseEvent) {
            if (
                isOpen
                && containerRef.current
                && !containerRef.current.contains(event.target as Node)
            ) {
                setIsOpen(false);
            }
        }

        function handleEscKey (event: KeyboardEvent) {
            if (isOpen && event.key === 'Escape') {
                setIsOpen(false);
            }
        }

        document.addEventListener('mousedown', handleClickOutside);

        document.addEventListener('keydown', handleEscKey);

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);

            document.removeEventListener('keydown', handleEscKey);
        };
    }, [isOpen]);

    const selectUrl = () => {
        if (!inputRef?.current) {
            return;
        }

        inputRef.current.select();
    };

    const instructions = 'Copy and paste the link into the subscribe action of your calendar';

    return (
        <div className="relative" ref={containerRef}>
            <button
                type="button"
                className="flex items-center rounded-md border border-gray-300 bg-white py-2 px-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 select-none cursor-pointer"
                id="menu-button"
                title={instructions}
                onClick={() => {
                    selectUrl();

                    setIsOpen(!isOpen);
                }}
            >
                Subscribe
            </button>
            <div
                className={(() => {
                    const classes = [
                        'absolute',
                        'left-0',
                        'top-12',
                        'p-4',
                        'bg-white',
                        'shadow border',
                        'border-gray-100',
                        'rounded-2xl',
                        'sm:-left-50',
                    ];

                    if (!isOpen) {
                        classes.push('hidden');
                    }

                    return classes.join(' ');
                })()}
            >
                <div className="text-gray-600 text-sm mb-2">
                    {instructions}
                </div>
                <input
                    readOnly
                    name="url"
                    type="text"
                    ref={inputRef}
                    className="min-w-0 flex-auto rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 sm:text-sm/6 mb-2 max-w-md"
                    style={{
                        width: 'calc(100vw - 80px)',
                    }}
                    value={icsUrl}
                />
                <button
                    type="button"
                    className="rounded-md px-3 py-2 text-center text-xs font-semibold text-white bg-crimson hover:bg-crimson-dark cursor-pointer"
                    onClick={async () => {
                        selectUrl();
                        await navigator.clipboard.writeText(icsUrl);
                    }}
                >
                    Copy to Clipboard
                </button>
            </div>
        </div>
    );
}
