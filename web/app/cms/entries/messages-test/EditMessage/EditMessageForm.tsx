'use client';

import React, { useActionState, useEffect, useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import { useRouter } from 'next/navigation';
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
import PutFormData from '../[id]/PutFormData';
import PostFormData from '../new/PostFormData';
import GetSelectFileNames from '../file-manager/Repository/GetSelectFileNames';

export default function EditMessageForm (
    {
        id,
        initialFormData = {
            published: true,
            date: new Date(),
            title: '',
            slug: '',
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

    const setSlug = (val: string) => {
        setFormData({ ...formData, slug: val });
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
            const response = id
                ? await PutFormData(id, formData)
                : await PostFormData(formData);

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

    const [existingFiles, setExistingFiles] = useState<Array<string>>([]);

    useEffect(() => {
        GetSelectFileNames().then((fileNames) => {
            if (fileNames === null) {
                return;
            }

            setExistingFiles(fileNames);
        });
    }, []);

    useEffect(() => {
        if (initialFormData.slug && !formData.slug) {
            setSlug(initialFormData.slug);
        }
    }, [formData.slug, initialFormData.slug, setSlug]);

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
                        label="Slug"
                        name="slug"
                        labelParenthetical="Leave blank to auto-generate from title on submit"
                        value={formData.slug}
                        setValue={(key, val) => {
                            setSlug(val);
                        }}
                    />
                </div>
                <div className="align-top grid gap-4 sm:grid-cols-3">
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
                    filePickerFileNames={existingFiles}
                    setValue={(key: string, val: string) => {
                        setAudioFile(val);
                    }}
                />
            </div>
        </form>
    );
}
