'use client';

import React, { useActionState, useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import { useRouter } from 'next/navigation';
import PageHeader from '../../layout/PageHeader';
import TextInput from '../../inputs/TextInput';
import RadioPanel from '../../inputs/RadioPanel';
import PostFormData from './PostFormData';
import ButtonLoader from '../../inputs/ButtonLoader';
import Message from '../../messaging/Message';
import {
    LeadershipPosition,
    LeadershipPositionType,
    NewProfileFormData,
} from './NewProfileFormData';

export default function PageInner () {
    const router = useRouter();

    const [formData, setFormData] = useState<NewProfileFormData>({
        firstName: '',
        lastName: '',
        titleOrHonorific: '',
        email: '',
        leadershipPosition: '',
    });

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

    const [error, submitAction, isPending] = useActionState(
        async () => {
            const response = await PostFormData(formData);

            if (response.success) {
                router.push('/cms/profiles');

                return null;
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
                    title="Create New Profile"
                    buttons={[
                        {
                            id: 'newEntry',
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
            {error}
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
