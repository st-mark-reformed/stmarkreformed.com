import React, { useActionState, useRef } from 'react';
import { CreateEditResourceValues } from './CreateEditResourceValues';
import { CreateEditResourceSubmitActionState } from './CreateEditResourceSubmitActionState';
import EditResourceSubmitFormAction from './edit/[resourceId]/EditResourceSubmitFormAction';
import CreateNewResourceSubmitFormAction from './new/CreateNewResourceSubmitFormAction';
import ResourceTitleSlugFields from './ResourceTitleSlugFields';
import ResourceDownloadsField from './ResourceDownloadsField';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import Toggle from '../Forms/Toggle';
import RichText from '../Forms/RichText';
import FormButtons from '../Forms/FormButtons';

export default function CreateEditResourcePage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            isEnabled: true,
            date: '',
            title: '',
            slug: '',
            body: '',
            downloads: [],
        },
        resourceId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditResourceValues;
        resourceId?: string;
    },
) {
    const initialState: CreateEditResourceSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditResourceSubmitFormAction
            : CreateNewResourceSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/resources',
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
                            content: 'Resources',
                            href: '/admin/resources',
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
                <input type="hidden" name="resourceId" value={resourceId} />
                <ResourceTitleSlugFields
                    initialTitle={state.values.title}
                    initialSlug={state.values.slug}
                    titleError={state.ok ? undefined : state.errors.title}
                    slugError={state.ok ? undefined : state.errors.slug}
                />
                <TextInput
                    label="Date"
                    name="date"
                    type="datetime-local"
                    defaultValue={state.values.date}
                    error={state.ok ? undefined : state.errors.date}
                />
                <Toggle
                    label="Is Enabled?"
                    name="isEnabled"
                    defaultValue={state.values.isEnabled}
                    error={state.ok ? undefined : state.errors.isEnabled}
                />
                <RichText
                    label="Body"
                    name="body"
                    colSpan="full"
                    defaultValue={state.values.body}
                    error={state.ok ? undefined : state.errors.body}
                />
                <ResourceDownloadsField initialDownloads={state.values.downloads} />
                <FormButtons secondaryLinkHref="/admin/resources" isPending={isPending} />
            </Form>
        </>
    );
}
