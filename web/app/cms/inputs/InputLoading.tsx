import React, { ReactNode } from 'react';

export default function InputLoading (
    {
        label,
        labelParenthetical,
        loadingText = 'loading…',
    }: {
        label: string;
        labelParenthetical?: string;
        loadingText?: string | ReactNode;
    },
) {
    return (
        <div>
            {/* eslint-disable-next-line jsx-a11y/label-has-for */}
            <label className="block text-sm font-semibold leading-6 text-gray-900">
                <span className="inline-block align-middle">{label}</span>
                {(() => {
                    if (!labelParenthetical) {
                        return null;
                    }

                    return (
                        <>
                            {' '}
                            <span className="text-xxs font-normal inline-block align-middle text-gray-600">
                                ({labelParenthetical})
                            </span>
                        </>
                    );
                })()}
            </label>
            <div className="mt-2 italic text-gray-500 text-xs">
                {loadingText}
            </div>
        </div>
    );
}
