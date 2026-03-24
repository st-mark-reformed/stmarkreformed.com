import React, { ReactNode } from 'react';

export default function InputWrapper (
    {
        children,
        label,
        name,
        colSpan = undefined,
    }: {
        children: ReactNode;
        label: string;
        name: string;
        colSpan?: number | 'full' | undefined;
    },
) {
    return (
        <div
            className={(() => {
                switch (colSpan) {
                    case 'full':
                        return 'col-span-full';
                    case 1:
                        return 'col-span-1';
                    case 2:
                        return 'col-span-2';
                    case 3:
                        return 'col-span-3';
                    case 4:
                        return 'col-span-4';
                    case 5:
                        return 'col-span-5';
                    case 6:
                        return 'col-span-6';
                    case 7:
                        return 'col-span-7';
                    case 8:
                        return 'col-span-8';
                    case 9:
                        return 'col-span-9';
                    case 10:
                        return 'col-span-10';
                    default:
                        return undefined;
                }
            })()}
        >
            <label
                htmlFor={name}
                className="block text-sm/6 font-medium text-gray-900 dark:text-white"
            >
                {label}
            </label>
            <div className="mt-2">
                {children}
            </div>
        </div>
    );
}
