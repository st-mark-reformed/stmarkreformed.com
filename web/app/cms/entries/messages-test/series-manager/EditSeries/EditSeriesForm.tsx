'use client';

import { useRouter } from 'next/navigation';
import React, { useActionState, useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import { SeriesFormData } from './SeriesFormData';
import Message from '../../../../messaging/Message';
import PostFormData from '../new/PostFormData';
import PutFormData from '../[id]/PutFormData';
import PageHeader from '../../../../layout/PageHeader';
import ButtonLoader from '../../../../inputs/ButtonLoader';
import TextInput from '../../../../inputs/TextInput';

export default function EditSeriesForm (
    {
        id,
        initialFormData = {
            title: '',
            slug: '',
        },
    }: {
        id?: string;
        initialFormData?: SeriesFormData;
    },
) {
    const router = useRouter();

    const [formData, setFormData] = useState<SeriesFormData>(initialFormData);

    const setTitle = (val: string) => {
        setFormData({ ...formData, title: val });
    };

    const setSlug = (val: string) => {
        setFormData({ ...formData, slug: val });
    };

    const [message, submitAction, isPending] = useActionState(
        async () => {
            const response = id
                ? await PutFormData(id, formData)
                : await PostFormData(formData);

            if (response.success) {
                if (!id) {
                    router.push(
                        '/cms/entries/messages-test/series-manager',
                    );

                    return null;
                }

                return (
                    <Message
                        type="success"
                        heading="Success!"
                        body={['Your edit was submitted successfully!']}
                        padBottom
                    />
                );
            }

            return (
                <Message
                    type="error"
                    heading="Your submission ran into errors"
                    body={response.messages}
                    padBottom
                />
            );
        },
        null,
    );

    return (
        <form action={submitAction}>
            <div className="mb-4 ">
                <PageHeader
                    title={id ? 'Edit Series' : 'Create New Series'}
                    buttons={[
                        {
                            id: 'submit',
                            type: 'primary',
                            disabled: isPending,
                            content: (
                                <>
                                    {isPending ? <ButtonLoader /> : <CheckIcon className="h-5 w-5 mr-1" />}
                                    Submit
                                </>
                            ),
                        },
                    ]}
                />
            </div>
            {message}
            <div className="space-y-8">
                <div className="align-top grid gap-4 sm:grid-cols-2 space-y-8">
                    <TextInput
                        label="Title"
                        name="title"
                        value={formData.title}
                        setValue={(key, val) => {
                            setTitle(val);
                        }}
                    />
                    <TextInput
                        label="Slug"
                        labelParenthetical="Leave blank to auto-generate from title on submit"
                        name="slug"
                        value={formData.slug}
                        setValue={(key, val) => {
                            setSlug(val);
                        }}
                    />
                </div>
            </div>
        </form>
    );
}
