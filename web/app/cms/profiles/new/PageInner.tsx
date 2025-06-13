'use client';

import React, { useState } from 'react';
import { CheckIcon } from '@heroicons/react/20/solid';
import PageHeader from '../../layout/PageHeader';
import TextInput from '../../inputs/TextInput';
import RadioPanel from '../../inputs/RadioPanel';

const LeadershipPosition = {
    pastor: 'Pastor',
    associatePastor: 'Associate Pastor',
    assistantPastor: 'Assistant Pastor',
    elder: 'Elder',
    rulingElder: 'Ruling Elder',
    deacon: 'Deacon',
};

type LeadershipPositionType = typeof LeadershipPosition[keyof typeof LeadershipPosition] | '';

interface FormData {
    firstName: string;
    lastName: string;
    titleOrHonorific: string;
    email: string;
    leadershipPosition: LeadershipPositionType;
}

export default function PageInner () {
    const [formData, setFormData] = useState<FormData>({
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

    return (
        <>
            <div className="mb-4 ">
                <PageHeader
                    title="Create New Profile"
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
        </>
    );
}
