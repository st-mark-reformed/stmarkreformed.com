import React, { useActionState, useRef } from 'react';
import { CreateEditMailingListValues } from './CreateEditMailingListValues';
import { CreateEditMailingListSubmitActionState } from './CreateEditMailingListSubmitActionState';
import EditMailingListSubmitFormAction from './edit/[mailingListId]/EditMailingListSubmitFormAction';
import CreateNewMailingListSubmitFormAction from './new/CreateNewMailingListSubmitFormAction';
import SubscribersField from './SubscribersField';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import TableRadioButtons from '../Forms/TableRadioButtons';
import FormButtons from '../Forms/FormButtons';

export default function CreateEditMailingListPage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            listName: '',
            listAddress: '',
            imapServer: '',
            imapPort: '993',
            connectionType: 'ssl',
            username: '',
            password: '',
            subscribers: [],
        },
        mailingListId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditMailingListValues;
        mailingListId?: string;
    },
) {
    const initialState: CreateEditMailingListSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditMailingListSubmitFormAction
            : CreateNewMailingListSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/mailing-lists',
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

    const passwordLabel = submitFormAction === 'edit'
        ? 'Password (leave blank to keep current)'
        : 'Password';

    return (
        <>
            <Breadcrumbs
                crumbs={
                    [
                        {
                            content: 'Mailing Lists',
                            href: '/admin/mailing-lists',
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
                <input type="hidden" name="mailingListId" value={mailingListId} />
                <TextInput
                    label="List Name"
                    name="listName"
                    defaultValue={state.values.listName}
                    error={state.ok ? undefined : state.errors.listName}
                />
                <TextInput
                    label="List Address"
                    name="listAddress"
                    type="email"
                    defaultValue={state.values.listAddress}
                    error={state.ok ? undefined : state.errors.listAddress}
                />
                <TextInput
                    label="IMAP Server"
                    name="imapServer"
                    defaultValue={state.values.imapServer}
                    error={state.ok ? undefined : state.errors.imapServer}
                />
                <TextInput
                    label="IMAP Port"
                    name="imapPort"
                    type="number"
                    defaultValue={state.values.imapPort}
                    error={state.ok ? undefined : state.errors.imapPort}
                />
                <TextInput
                    label="Username"
                    name="username"
                    autoComplete="off"
                    defaultValue={state.values.username}
                    error={state.ok ? undefined : state.errors.username}
                />
                <TextInput
                    label={passwordLabel}
                    name="password"
                    type="password"
                    autoComplete="off"
                    error={state.ok ? undefined : state.errors.password}
                />
                <TableRadioButtons
                    label="Connection Type"
                    name="connectionType"
                    colSpan="full"
                    options={[
                        {
                            name: 'ssl',
                            label: 'SSL',
                            defaultChecked: state.values.connectionType === 'ssl',
                        },
                        {
                            name: 'tls',
                            label: 'TLS',
                            defaultChecked: state.values.connectionType === 'tls',
                        },
                        {
                            name: 'none',
                            label: 'None',
                            defaultChecked: state.values.connectionType === 'none',
                        },
                    ]}
                />
                <SubscribersField initialSubscribers={state.values.subscribers} />
                <FormButtons secondaryLinkHref="/admin/mailing-lists" isPending={isPending} />
            </Form>
        </>
    );
}
