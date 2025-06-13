'use client';

import React, { useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import PageHeader from '../../../layout/PageHeader';
import Toggle from '../../../inputs/Toggle';
import CustomDateTimePicker from '../../../inputs/CustomDateTimePicker';
import TextInput from '../../../inputs/TextInput';
import SingleFileUploader from '../../../inputs/SingleFileUploader';
import { AudioUploadFileTypes } from '../../../inputs/AudioUploadFileTypes';

interface FormData {
    date: Date | null;
    published: boolean;
    title: string;
    text: string;
    audioFile: string;
}

export default function PageInner () {
    const [formData, setFormData] = useState<FormData>({
        published: true,
        date: new Date(),
        title: '',
        text: '',
        audioFile: '',
    });

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

    const setAudioFile = (val: string) => {
        setFormData({ ...formData, audioFile: val });
    };

    return (
        <>
            <div className="mb-4 ">
                <PageHeader
                    title="Create New Message"
                    buttons={[
                        {
                            id: 'newEntry',
                            type: 'primary',
                            content: (
                                <>
                                    <CheckIcon className="h-5 w-5 mr-1" />
                                    Submit
                                </>
                            ),
                            onClick: () => {},
                        },
                    ]}
                />
            </div>
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
                        label="Text (Scripture Reference)"
                        name="text"
                        value={formData.text}
                        setValue={(key, val) => {
                            setText(val);
                        }}
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
        </>
    );
}
