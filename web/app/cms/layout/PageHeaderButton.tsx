import React, { MouseEvent, ReactElement } from 'react';
import Link from 'next/link';

export interface PageHeaderButtonConfig {
    id: string;
    type?: 'primary' | 'secondary';
    content: string | ReactElement;
    href?: string;
    onClick?: (e: MouseEvent<HTMLButtonElement>) => void;
    disabled?: boolean;
}

export function PageHeaderButton (
    {
        buttonConfig,
        useMarginLeft,
    }: {
        buttonConfig: PageHeaderButtonConfig | undefined;
        useMarginLeft: boolean;
    },
) {
    if (!buttonConfig) {
        return null;
    }

    buttonConfig.type = buttonConfig.type || 'primary';

    const classes = [
        'inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm cursor-pointer',
    ];

    if (useMarginLeft) {
        classes.push('ml-3');
    }

    if (buttonConfig.disabled) {
        classes.push('bg-gray-400');
    } else if (buttonConfig.type === 'primary') {
        classes.push('bg-cyan-600 hover:bg-cyan-500');
    } else if (buttonConfig.type === 'secondary') {
        classes.push('bg-white/10 hover:bg-white/20');
    }

    if (buttonConfig.onClick) {
        return (
            <button
                type="button"
                onClick={buttonConfig.onClick}
                className={classes.join(' ')}
                disabled={buttonConfig.disabled}
            >
                {buttonConfig.content}
            </button>
        );
    }

    if (buttonConfig.href) {
        return (
            <Link
                href={buttonConfig.href}
                className={classes.join(' ')}
            >
                {buttonConfig.content}
            </Link>
        );
    }

    return (
        <button
            type="submit"
            className={classes.join(' ')}
            disabled={buttonConfig.disabled}
        >
            {buttonConfig.content}
        </button>
    );
}
