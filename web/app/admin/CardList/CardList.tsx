'use client';

import React, {
    ComponentPropsWithoutRef,
    forwardRef,
    ReactNode,
    useEffect,
    useState,
} from 'react';
import Link from 'next/link';
import { PencilIcon } from '@heroicons/react/24/solid';
import { ChevronDoubleRightIcon } from '@heroicons/react/16/solid';

type CardButton = {
    content: string;
    href: string;
    type: 'primary' | 'secondary';
    rightGlyph?: 'chevron-double-left' | 'pencil';
};

type CardButtons = CardButton[];

type ObjectColumn = {
    id: string;
    line1: string | ReactNode;
    line2?: string | ReactNode;
};

export type CardColumn = string | ReactNode | ObjectColumn | CardButtons;

export interface CardItem {
    id: string;
    columns?: CardColumn[];
}

export type CardItems = CardItem[];

type Props = {
    items?: CardItems;
    noItemsFoundMessage?: string;
    formAction?: ComponentPropsWithoutRef<'form'>['action'];
    showCheckBoxes?: boolean;
    onCheckedChange?: (hasCheckedItems: boolean, checkedIds: string[]) => void;
};

function isObjectColumn (
    column: CardColumn,
): column is ObjectColumn {
    return typeof column === 'object' && column !== null && 'id' in column;
}

function isCardButtons (
    column: CardColumn,
): column is CardButtons {
    return Array.isArray(column)
        && column.every((item) => (
            typeof item === 'object'
            && item !== null
            && 'content' in item
            && 'href' in item
            && 'type' in item
        ));
}

const CardList = forwardRef<HTMLFormElement, Props>((
    {
        items = [],
        noItemsFoundMessage = 'No items found.',
        formAction = undefined,
        showCheckBoxes = false,
        onCheckedChange,
    },
    ref,
) => {
    const [checkedIds, setCheckedIds] = useState<string[]>([]);

    useEffect(() => {
        onCheckedChange?.(checkedIds.length > 0, checkedIds);
    }, [checkedIds, onCheckedChange]);

    const handleCheckboxChange = (itemId: string, checked: boolean) => {
        setCheckedIds((currentCheckedIds) => (
            checked
                ? Array.from(new Set([...currentCheckedIds, itemId]))
                : currentCheckedIds.filter((id) => id !== itemId)
        ));
    };

    if (items.length === 0) {
        return (
            <div className="text-center">
                <p className="text-sm/6 text-gray-500 dark:text-gray-400">
                    {noItemsFoundMessage}
                </p>
            </div>
        );
    }

    return (
        <form ref={ref} action={formAction}>
            <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-xs outline-1 outline-gray-900/5 sm:rounded-xl dark:divide-white/5 dark:bg-gray-800/50 dark:shadow-none dark:outline-white/10 dark:sm:-outline-offset-1 relative">
                {items.map((item) => (
                    <li
                        key={item.id}
                        className={(() => {
                            const classes = ['relative grid gap-x-6 px-4 py-5 grid-cols-1'];

                            if (checkedIds.includes(item.id)) {
                                classes.push('bg-crimson/10 dark:bg-gray-700/60');
                            }

                            if (item.columns?.length === 2) {
                                classes.push('sm:grid-cols-2');
                            } else if (item.columns?.length === 3) {
                                classes.push('sm:grid-cols-3');
                            } else if (item.columns?.length === 4) {
                                classes.push('sm:grid-cols-4');
                            } else if (item.columns?.length === 5) {
                                classes.push('sm:grid-cols-5');
                            } else if (item.columns?.length === 6) {
                                classes.push('sm:grid-cols-6');
                            } else if (item.columns?.length === 7) {
                                classes.push('sm:grid-cols-7');
                            } else if (item.columns?.length === 8) {
                                classes.push('sm:grid-cols-8');
                            }

                            if (showCheckBoxes) {
                                classes.push('pr-12');
                            }

                            return classes.join(' ');
                        })()}
                    >
                        {item.columns?.map((column: CardColumn, index) => {
                            // @ts-expect-error TS18048
                            let isLastColumn = index === item.columns.length - 1;

                            // @ts-expect-error TS18048
                            if (item.columns.length < 2) {
                                isLastColumn = false;
                            }

                            const isFirstColumn = index === 0;

                            const wrapperClasses = ['flex min-w-0 gap-x-4 self-center'];

                            if (isLastColumn) {
                                wrapperClasses.push('sm:justify-self-end sm:text-right');
                            }

                            if (isObjectColumn(column)) {
                                return (
                                    <div
                                        key={column.id}
                                        className={wrapperClasses.join(' ')}
                                    >
                                        <div className="min-w-0">
                                            <p
                                                className={(() => {
                                                    const classes = ['text-sm/6 text-gray-900 dark:text-white'];

                                                    if (isFirstColumn) {
                                                        classes.push('font-semibold');
                                                    }

                                                    return classes.join(' ');
                                                })()}
                                            >
                                                {column.line1}
                                            </p>
                                            {(() => {
                                                if (!column.line2) {
                                                    return null;
                                                }

                                                return (
                                                    <p className="mt-1 text-xs/5 text-gray-500 dark:text-gray-40">
                                                        {column.line2}
                                                    </p>
                                                );
                                            })()}
                                        </div>
                                    </div>
                                );
                            }

                            if (isCardButtons(column)) {
                                return (
                                    <div
                                        // eslint-disable-next-line react/no-array-index-key
                                        key={`column-${index}`}
                                        className={wrapperClasses.join(' ')}
                                    >
                                        {column.map((button) => {
                                            const classes = ['inline-flex items-center rounded-sm px-2 py-1 text-xs font-semibold text-gray-900 shadow-xs'];

                                            if (button.type === 'primary') {
                                                classes.push('cursor-pointer bg-crimson text-white hover:bg-crimson-dark dark:bg-crimson/70 dark:shadow-none dark:hover:bg-crimson/80');
                                            } else {
                                                classes.push('cursor-pointer bg-white text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20');
                                            }

                                            if (button.rightGlyph) {
                                                classes.push('pl-2');
                                            }

                                            const iconClasses = 'size-3 ml-1.5';

                                            const glyphRender = () => {
                                                if (button.rightGlyph === 'pencil') {
                                                    return <PencilIcon className={iconClasses} aria-hidden="true" />;
                                                }
                                                if (button.rightGlyph === 'chevron-double-left') {
                                                    return <ChevronDoubleRightIcon className={iconClasses} aria-hidden="true" />;
                                                }

                                                return null;
                                            };

                                            return (
                                                <Link
                                                    key={button.href}
                                                    href={button.href}
                                                    className={classes.join(' ')}
                                                >
                                                    {button.content}
                                                    {glyphRender()}
                                                </Link>
                                            );
                                        })}
                                    </div>
                                );
                            }

                            return (
                                <div
                                    // eslint-disable-next-line react/no-array-index-key
                                    key={`column-${index}`}
                                    className={wrapperClasses.join(' ')}
                                >
                                    {column}
                                </div>
                            );
                        })}
                        {(() => {
                            if (!showCheckBoxes) {
                                return null;
                            }

                            return (
                                <div className="absolute right-4 top-1/2 -translate-y-1/2">
                                    <input
                                        name="comments"
                                        type="checkbox"
                                        checked={checkedIds.includes(item.id)}
                                        onChange={(event) => {
                                            handleCheckboxChange(item.id, event.currentTarget.checked);
                                        }}
                                        className="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-crimson checked:bg-crimson indeterminate:border-crimson indeterminate:bg-crimson focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-crimson disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto focus:outline-2 focus:outline-crimson dark:bg-gray-500"
                                    />
                                </div>
                            );
                        })()}
                    </li>
                ))}
            </ul>
        </form>
    );
});

export default CardList;
