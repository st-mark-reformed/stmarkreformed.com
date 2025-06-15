'use client';

import { useRouter } from 'next/navigation';
import React, { useActionState, useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import { LeadershipPosition, LeadershipPositionType, ProfileFormData } from './ProfileFormData';
import PostFormData from '../new/PostFormData';
import Message from '../../messaging/Message';
import PutFormData from '../[id]/PutFormData';
import PageHeader from '../../layout/PageHeader';
import ButtonLoader from '../../inputs/ButtonLoader';
import TextInput from '../../inputs/TextInput';
import RadioPanel from '../../inputs/RadioPanel';

export default function EditProfileForm (
    {
        id,
        initialFormData = {
            firstName: '',
            lastName: '',
            titleOrHonorific: '',
            email: '',
            leadershipPosition: '',
        },
    }: {
        id?: string;
        initialFormData?: ProfileFormData;
    },
) {
    const router = useRouter();

    const [formData, setFormData] = useState<ProfileFormData>(initialFormData);

    const setFirstName = (val: string) => {
        setFormData({ ...formData, firstName: val });
    };

    const setLastName = (val: string) => {
        setFormData({ ...formData, lastName: val });
    };

    const setTitleOrHonorific = (val: string) => {
        setFormData({ ...formData, titleOrHonorific: val });
    };

    const setEmail = (val: string) => {
        setFormData({ ...formData, email: val });
    };

    const setLeadershipPosition = (val: LeadershipPositionType) => {
        setFormData({ ...formData, leadershipPosition: val });
    };

    const [message, submitAction, isPending] = useActionState(
        async () => {
            const response = id
                ? await PutFormData(id, formData)
                : await PostFormData(formData);

            if (response.success) {
                if (!id) {
                    router.push('/cms/profiles');

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
                    title={id ? 'Edit Profile' : 'Create New Profile'}
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
                        label="First Name"
                        name="firstName"
                        value={formData.firstName}
                        setValue={(key, val) => {
                            setFirstName(val);
                        }}
                    />
                    <TextInput
                        label="Last Name"
                        name="lastName"
                        value={formData.lastName}
                        setValue={(key, val) => {
                            setLastName(val);
                        }}
                    />
                    <TextInput
                        label="Title or Honorific"
                        name="titleOrHonorific"
                        value={formData.titleOrHonorific}
                        setValue={(key, val) => {
                            setTitleOrHonorific(val);
                        }}
                    />
                    <TextInput
                        label="Email"
                        name="email"
                        type="email"
                        value={formData.email}
                        setValue={(key, val) => {
                            setEmail(val);
                        }}
                    />
                    <RadioPanel
                        label="Leadership Position"
                        name="leadershipPosition"
                        value={formData.leadershipPosition}
                        setValue={(key, val) => {
                            setLeadershipPosition(val);
                        }}
                        options={[
                            {
                                name: 'None',
                                val: '',
                            },
                            ...Object.entries(LeadershipPosition).map(([key, value]) => ({
                                name: value,
                                val: key,
                            })),
                        ]}
                    />
                </div>
            </div>
        </form>
    );
}
