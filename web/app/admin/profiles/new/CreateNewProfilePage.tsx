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
import FormButtons from '../../Forms/FormButtons';

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
            <Form ref={formRef} action={formAction}>
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
                <FormButtons secondaryLinkHref="/admin/profiles" />
            </Form>
        </>
    );
}
