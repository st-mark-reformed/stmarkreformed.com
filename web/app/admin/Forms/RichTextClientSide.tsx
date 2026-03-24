'use client';

import React, { useState } from 'react';
import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Bold,
    Underline,
    Strikethrough,
    Subscript,
    Superscript,
    HorizontalLine,
    List,
    Link,
    SourceEditing,
    Italic,
} from 'ckeditor5';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import 'ckeditor5/ckeditor5.css';

export default function RichTextClientSide (
    {
        name,
        defaultValue = '',
    }: {
        name: string;
        defaultValue?: string;
    },
) {
    const [value, setValue] = useState(defaultValue);

    return (
        <div className="prose max-w-none">
            <input
                type="hidden"
                name={name}
                value={value}
            />
            <CKEditor
                editor={ClassicEditor}
                data={value}
                onChange={(_, editor) => {
                    setValue(editor.getData());
                }}
                config={{
                    licenseKey: 'GPL',
                    plugins: [
                        Essentials,
                        Paragraph,
                        Bold,
                        Underline,
                        Strikethrough,
                        Subscript,
                        Superscript,
                        HorizontalLine,
                        List,
                        Link,
                        SourceEditing,
                        Italic,
                    ],
                    toolbar: [
                        'sourceEditing',
                        'bold',
                        'italic',
                        'underline',
                        'strikethrough',
                        'subscript',
                        'superscript',
                        'horizontalLine',
                        'numberedList',
                        'bulletedList',
                        'link',
                    ],
                }}
            />
        </div>
    );
}
