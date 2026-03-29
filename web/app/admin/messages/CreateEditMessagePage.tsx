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
import SearchableDropdown from '../Forms/SearchableDropdown';

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
                {/* TODO: Wrap the series dropdown to retrieve options */}
                <SearchableDropdown
                    label="Series"
                    name="seriesId"
                    options={[
                        { value: '019d2aa5-c11d-7321-851d-59de5c202e15', label: 'ASDF' },
                        { value: '019d2a96-c316-7079-9e55-69e57ce14672', label: 'Baz' },
                        { value: '019d2a96-e847-7112-9917-874315c42430', label: 'Foo' },
                        { value: '019d2d28-f3b0-70e2-aa30-46ce82031297', label: 'Thing' },
                        { value: '019d2d5b-29c5-717d-ac71-bd3dec86fc95', label: 'Starfleet' },
                        { value: '019d3256-8cdd-731d-9e53-f9c093ab44a5', label: 'QWERTY' },
                    ]}
                    defaultValue={state.values.seriesId}
                    error={state.ok ? undefined : state.errors.seriesId}
                />
                {/* TODO: Wrap the speaker dropdown to retrieve options */}
                <SearchableDropdown
                    label="Speaker"
                    name="speakerId"
                    options={[
                        { value: '019d22e9-38a3-71d3-a7cb-4c428325c4ad', label: 'Foo title Test Foo Bar bar last asdf' },
                        { value: '019d22e9-da14-7394-a63a-6a1495e3dff2', label: 'Rev. Joe Thacker' },
                        { value: '019d255f-4c4d-7060-8e2c-2bf57d0db33e', label: 'New Test Profile' },
                        { value: '019d2d36-9e4e-714a-b080-da3e6a781bed', label: 'Bar Baz Foo' },
                    ]}
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
                <FormButtons secondaryLinkHref="/admin/messages" isPending={isPending} />
            </Form>
        </>
    );
}
