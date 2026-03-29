import React, { useActionState, useRef } from 'react';
import { CreateEditMessageValues } from './CreateEditMessageValues';
import { CreateEditMessageSubmitActionState } from './CreateEditMessageSubmitActionState';
import EditMessageSubmitFormAction from './edit/[messageId]/EditMessageSubmitFormAction';
import CreateNewMessageSubmitFormAction from './new/CreateNewMessageSubmitFormAction';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import Toggle from '../Forms/Toggle';
import FormButtons from '../Forms/FormButtons';

export default function CreateEditMessagePage (
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
        messageId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditMessageValues;
        messageId?: string;
    },
) {
    const initialState: CreateEditMessageSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditMessageSubmitFormAction
            : CreateNewMessageSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/messages',
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
                            content: 'Messages',
                            href: '/admin/messages',
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
                <input type="hidden" name="messageId" value={messageId} />
                <TextInput
                    label="Title"
                    name="title"
                    defaultValue={state.values.title}
                    error={state.ok ? undefined : state.errors.title}
                />
                {/* TODO: Datetime field */}
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
                {/* TODO: Series dropdown */}
                <TextInput
                    label="Series"
                    name="seriesId"
                    defaultValue={state.values.seriesId}
                    error={state.ok ? undefined : state.errors.seriesId}
                />
                {/* TODO: Speaker dropdown */}
                <TextInput
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
                {/* TODO: Audio upload */}
                <TextInput
                    label="Audio File"
                    name="audioPath"
                    colSpan="full"
                    defaultValue={state.values.audioPath}
                    error={state.ok ? undefined : state.errors.audioPath}
                />
                <FormButtons secondaryLinkHref="/admin/messages" />
            </Form>
        </>
    );
}
