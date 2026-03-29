import React, { useActionState, useRef } from 'react';
import { CreateEditProfileValues } from './CreateEditProfileValues';
import { CreateEditProfileSubmitActionState } from './CreateEditProfileSubmitActionState';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import LeadershipPositionRadioButtons from './new/LeadershipPositionRadioButtons';
import RichText from '../Forms/RichText';
import FormButtons from '../Forms/FormButtons';
import Form from '../Forms/Form';
import CreateNewProfileSubmitFormAction from './new/CreateNewProfileSubmitFormAction';
import EditProfileSubmitFormAction from './edit/[profileId]/EditProfileSubmitFormAction';

export default function CreateEditProfilePage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            titleOrHonorific: '',
            email: '',
            firstName: '',
            lastName: '',
            leadershipPosition: '',
            bio: '',
        },
        profileId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditProfileValues;
        profileId?: string;
    },
) {
    const initialState: CreateEditProfileSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditProfileSubmitFormAction
            : CreateNewProfileSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/profiles',
            type: 'secondary',
        },
    ];

    if (isPending) {
        buttons.push({
            content: 'Submitting…',
            glyph: 'check',
            href: 'submit-button',
            type: 'pending',
            onClick: () => {},
        });
    } else {
        buttons.push({
            content: 'Submit',
            glyph: 'check',
            href: 'submit-button',
            type: 'primary',
            onClick: () => {
                formRef.current?.requestSubmit();
            },
        });
    }

    return (
        <>
            <Breadcrumbs
                crumbs={
                    [
                        {
                            content: 'Profiles',
                            href: '/admin/profiles',
                        },
                    ]
                }
            />
            <PageTitle buttons={buttons}>
                {pageTitle}
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
                <input type="hidden" name="profileId" value={profileId} />
                <TextInput
                    label="Email"
                    name="email"
                    type="email"
                    defaultValue={state.values.email}
                    error={state.ok ? undefined : state.errors.email}
                />
                <TextInput
                    label="Title/Honorific"
                    name="titleOrHonorific"
                    defaultValue={state.values.titleOrHonorific}
                    error={state.ok ? undefined : state.errors.titleOrHonorific}
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
                <FormButtons secondaryLinkHref="/admin/profiles" isPending={isPending} />
            </Form>
        </>
    );
}
