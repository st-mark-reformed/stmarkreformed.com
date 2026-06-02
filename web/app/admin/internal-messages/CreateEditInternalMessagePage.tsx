import React, { useActionState, useRef } from 'react';
import { CreateEditInternalMessageValues } from './CreateEditInternalMessageValues';
import { CreateEditInternalMessageSubmitActionState } from './CreateEditInternalMessageSubmitActionState';
import EditInternalMessageSubmitFormAction from './edit/[internalMessageId]/EditInternalMessageSubmitFormAction';
import CreateNewInternalMessageSubmitFormAction from './new/CreateNewInternalMessageSubmitFormAction';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import Toggle from '../Forms/Toggle';
import FormButtons from '../Forms/FormButtons';
import ProfileSelector from '../Forms/ProfileSelector';
import InternalSeriesSelector from '../Forms/InternalSeriesSelector';
import SingleFileUploader from '../Forms/FileUploads/SingleFileUploader';

export default function CreateEditInternalMessagePage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            isEnabled: true,
            date: '',
            title: '',
            speakerId: '',
            passage: '',
            seriesId: '',
            audioPath: '',
        },
        internalMessageId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditInternalMessageValues;
        internalMessageId?: string;
    },
) {
    const initialState: CreateEditInternalMessageSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditInternalMessageSubmitFormAction
            : CreateNewInternalMessageSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/internal-messages',
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
                            content: 'Internal Messages',
                            href: '/admin/internal-messages',
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
                <input type="hidden" name="internalMessageId" value={internalMessageId} />
                <TextInput
                    label="Title"
                    name="title"
                    defaultValue={state.values.title}
                    error={state.ok ? undefined : state.errors.title}
                />
                <TextInput
                    label="Date"
                    name="date"
                    type="datetime-local"
                    defaultValue={state.values.date}
                    error={state.ok ? undefined : state.errors.date}
                />
                <TextInput
                    label="Passage"
                    name="passage"
                    defaultValue={state.values.passage}
                    error={state.ok ? undefined : state.errors.passage}
                />
                <InternalSeriesSelector
                    label="Series"
                    name="seriesId"
                    defaultValue={state.values.seriesId}
                    error={state.ok ? undefined : state.errors.seriesId}
                />
                <ProfileSelector
                    label="Speaker"
                    name="speakerId"
                    defaultValue={state.values.speakerId}
                    error={state.ok ? undefined : state.errors.speakerId}
                />
                <Toggle
                    label="Is Enabled?"
                    name="isEnabled"
                    defaultValue={state.values.isEnabled}
                    error={state.ok ? undefined : state.errors.isEnabled}
                />
                <SingleFileUploader
                    label="Audio"
                    name="audioPath"
                    defaultValue={state.values.audioPath}
                    error={state.ok ? undefined : state.errors.audioPath}
                />
                <FormButtons secondaryLinkHref="/admin/internal-messages" isPending={isPending} />
            </Form>
        </>
    );
}
