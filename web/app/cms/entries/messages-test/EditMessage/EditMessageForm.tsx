'use client';

import React, { useActionState, useEffect, useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import { useRouter } from 'next/navigation';
import { ExclamationTriangleIcon } from '@heroicons/react/24/outline';
import PageHeader from '../../../layout/PageHeader';
import Toggle from '../../../inputs/Toggle';
import CustomDateTimePicker from '../../../inputs/CustomDateTimePicker';
import TextInput from '../../../inputs/TextInput';
import SingleFileUploader from '../../../inputs/SingleFileUploader';
import { AudioUploadFileTypes } from '../../../inputs/AudioUploadFileTypes';
import { MessageFormData } from './MessageFormData';
import Message from '../../../messaging/Message';
import ButtonLoader from '../../../inputs/ButtonLoader';
import SelectProfile from '../../../inputs/SelectProfile';
import SelectSeries from './SelectSeries';

export default function EditMessageForm (
    {
        id,
        initialFormData = {
            published: true,
            date: new Date(),
            title: '',
            text: '',
            speakerId: '',
            seriesId: '',
            audioFile: '',
        },
    }: {
        id?: string;
        initialFormData?: MessageFormData;
    },
) {
    const router = useRouter();

    const [formData, setFormData] = useState<MessageFormData>(initialFormData);

    const setPublished = (val: boolean) => {
        setFormData({ ...formData, published: val });
    };

    const setDate = (val: Date | null) => {
        setFormData({ ...formData, date: val });
    };

    const setTitle = (val: string) => {
        setFormData({ ...formData, title: val });
    };

    const setText = (val: string) => {
        setFormData({ ...formData, text: val });
    };

    const setSpeakerId = (val: string) => {
        setFormData({ ...formData, speakerId: val });
    };

    const setSeriesId = (val: string) => {
        setFormData({ ...formData, seriesId: val });
    };

    const setAudioFile = (val: string) => {
        setFormData({ ...formData, audioFile: val });
    };

    const [message, submitAction, isPending] = useActionState(
        async () => {
            const response = {
                success: false,
                messages: ['TODO: Implement submitAction'],
            };
            // const response = id
            //     ? await PutFormData(id, formData)
            //     : await PostFormData(formData);

            if (response.success) {
                if (!id) {
                    router.push('/cms/entries/messages-test');

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
                    title={id ? 'Edit Message' : 'Create New Message'}
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
                <div className="align-top grid gap-4 sm:grid-cols-4">
                    <Toggle
                        label="Publish Status"
                        name="published"
                        value={formData.published}
                        setValue={(key, val) => {
                            setPublished(val);
                        }}
                    />
                    <div className="sm:col-span-3">
                        <CustomDateTimePicker
                            label="Date"
                            name="date"
                            value={formData.date}
                            setValue={(val) => {
                                setDate(val);
                            }}
                        />
                    </div>
                </div>
                <div className="align-top grid gap-4 sm:grid-cols-2">
                    <TextInput
                        label="Title"
                        name="title"
                        value={formData.title}
                        setValue={(key, val) => {
                            setTitle(val);
                        }}
                    />
                    <TextInput
                        label="Text"
                        labelParenthetical="Scripture Reference"
                        name="text"
                        value={formData.text}
                        setValue={(key, val) => {
                            setText(val);
                        }}
                    />
                    <SelectProfile
                        label="Speaker"
                        name="speaker"
                        value={formData.speakerId}
                        setValue={setSpeakerId}
                    />
                    <SelectSeries
                        value={formData.seriesId}
                        setValue={setSeriesId}
                    />
                </div>
                <SingleFileUploader
                    label="Audio File"
                    name="audio_file"
                    value={formData.audioFile}
                    fileTypes={AudioUploadFileTypes}
                    setValue={(key: string, val: string) => {
                        setAudioFile(val);
                    }}
                />
            </div>
        </form>
    );
}
