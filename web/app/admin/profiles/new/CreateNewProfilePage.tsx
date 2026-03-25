'use client';

import React, { useActionState, useRef } from 'react';
import Link from 'next/link';
import Breadcrumbs from '../../Breadcrumbs';
import PageTitle from '../../PageTitle';
import Form from '../../Forms/Form';
import TextInput from '../../Forms/TextInput';
import LeadershipPositionRadioButtons from './LeadershipPositionRadioButtons';
import RichText from '../../Forms/RichText';
import SubmitFormAction, { SubmitFormActionState } from './SubmitForm/SubmitFormAction';
import Alert from '../../../Alert';

const initialState: SubmitFormActionState = {
    ok: true,
    success: false,
    values: {
        titleOrHonorific: '',
        email: '',
        firstName: '',
        lastName: '',
        leadershipPosition: '',
        bio: '',
    },
};

export default function CreateNewProfilePage () {
    const [state, formAction] = useActionState(SubmitFormAction, initialState);

    const formRef = useRef<HTMLFormElement>(null);

    return (
        <>
            <Breadcrumbs crumbs={
                [
                    {
                        content: 'Profiles',
                        href: '/admin/profiles',
                    },
                ]
            }
            />
            <PageTitle
                buttons={[
                    {
                        content: 'Cancel',
                        href: '/admin/profiles',
                        type: 'secondary',
                    },
                    {
                        content: 'Submit',
                        href: 'title-submit-button',
                        type: 'primary',
                        onClick: () => {
                            formRef.current?.requestSubmit();
                        },
                    },
                ]}
            >
                Create New Profile
            </PageTitle>
            {(() => {
                if (state.ok) {
                    return null;
                }

                let headline;

                if (Object.keys(state.errors).length > 1) {
                    headline = 'There were errors with the submission';
                } else {
                    headline = 'There was an error with the submission';
                }

                return (
                    <Alert
                        headline={headline}
                        contentList={Object.values(state.errors)}
                        type="error"
                    />
                );
            })()}
            <Form ref={formRef} action={formAction}>
                <TextInput
                    label="Title/Honorific"
                    name="titleOrHonorific"
                    defaultValue={state.values.titleOrHonorific}
                    error={state.ok ? undefined : state.errors.titleOrHonorific}
                />
                <TextInput
                    label="Email"
                    name="email"
                    type="email"
                    defaultValue={state.values.email}
                    error={state.ok ? undefined : state.errors.email}
                />
                <TextInput
                    label="First Name"
                    name="firstName"
                    autoComplete="first-name"
                    defaultValue={state.values.firstName}
                    error={state.ok ? undefined : state.errors.firstName}
                />
                <TextInput
                    label="Last Name"
                    name="lastName"
                    autoComplete="last-name"
                    defaultValue={state.values.lastName}
                    error={state.ok ? undefined : state.errors.lastName}
                />
                <LeadershipPositionRadioButtons
                    defaultValue={state.values.leadershipPosition}
                    error={state.ok ? undefined : state.errors.leadershipPosition}
                />
                <RichText
                    label="Bio"
                    name="bio"
                    defaultValue={state.values.bio}
                    error={state.ok ? undefined : state.errors.bio}
                />
                <div className="mt-6 flex items-center justify-end gap-x-3 col-span-full border-t-2 border-gray-200 pt-6 dark:border-gray-500">
                    <Link
                        href="/admin/profiles"
                        className="cursor-pointer rounded-md px-3 py-2 text-sm font-semibold shadow-xs bg-white text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        className="cursor-pointer rounded-md bg-crimson px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-crimson-dark dark:bg-crimson/70 dark:shadow-none dark:hover:bg-crimson/80"
                    >
                        Submit
                    </button>
                </div>
            </Form>
        </>
    );
}
