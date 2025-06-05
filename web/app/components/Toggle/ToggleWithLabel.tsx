// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React, { Dispatch, ReactNode, SetStateAction } from 'react';

import {
    Description,
    Field,
    Label,
    Switch,
} from '@headlessui/react';
import typography from '../../typography/typography';

export default function ToggleWithLabel (
    {
        enabled,
        setEnabled,
        label,
        description,
    }: {
        enabled: boolean;
        setEnabled: (enabled: boolean) => void | Dispatch<SetStateAction<boolean>>;
        label: string | ReactNode;
        description?: string;
    },
) {
    return (
    // eslint-disable-next-line jsx-a11y/click-events-have-key-events,jsx-a11y/no-static-element-interactions
        <div
            className="inline-block cursor-pointer select-none"
            onClick={() => setEnabled(!enabled)}
        >
            <Field className="flex items-center justify-between gap-x-3">
                <span className="flex grow flex-col">
                    <Label as="span" passive className="text-sm/6 font-bold text-gray-700">
                        {(() => {
                            if (typeof label === 'string') {
                                return (
                                    <span
                                        dangerouslySetInnerHTML={{
                                            __html: typography(label),
                                        }}
                                    />
                                );
                            }

                            return <>{label}</>;
                        })()}
                    </Label>
                    {(() => {
                        if (!description) {
                            return null;
                        }

                        return (
                            <Description as="span" className="text-sm text-gray-500">
                                <span
                                    dangerouslySetInnerHTML={{
                                        __html: typography(description),
                                    }}
                                />
                            </Description>
                        );
                    })()}
                </span>
                <Switch
                    checked={enabled}
                    onChange={setEnabled}
                    className="group relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out data-checked:bg-crimson"
                >
                    <span
                        aria-hidden="true"
                        className="pointer-events-none inline-block size-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out group-data-checked:translate-x-5"
                    />
                </Switch>
            </Field>
        </div>
    );
}
