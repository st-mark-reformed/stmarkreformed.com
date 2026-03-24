'use client';

import React from 'react';
import 'ckeditor5/ckeditor5.css';
import dynamic from 'next/dynamic';
import InputWrapper from './InputWrapper';
import PartialPageLoading from '../../PartialPageLoading';

const RichTextClientSide = dynamic(
    () => import('./RichTextClientSide'),
    {
        ssr: false,
        loading: () => <PartialPageLoading />,
    },
);

export default function RichText (
    {
        label,
        name,
        colSpan = undefined,
        defaultValue = '',
    }: {
        label: string;
        name: string;
        colSpan?: number | 'full' | undefined;
        defaultValue?: string;
    },
) {
    return (
        <InputWrapper label={label} name={name} colSpan={colSpan}>
            <RichTextClientSide name={name} defaultValue={defaultValue} />
        </InputWrapper>
    );
}
