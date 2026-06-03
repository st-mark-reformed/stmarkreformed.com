import React, { useActionState, useRef } from 'react';
import { CreateEditPastorsPageValues } from './CreateEditPastorsPageValues';
import { CreateEditPastorsPageSubmitActionState } from './CreateEditPastorsPageSubmitActionState';
import EditPastorsPageSubmitFormAction from './edit/[pastorsPageId]/EditPastorsPageSubmitFormAction';
import CreateNewPastorsPageSubmitFormAction from './new/CreateNewPastorsPageSubmitFormAction';
import PastorsPageTitleSlugFields from './PastorsPageTitleSlugFields';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import Toggle from '../Forms/Toggle';
import RichText from '../Forms/RichText';
import FormButtons from '../Forms/FormButtons';

export default function CreateEditPastorsPagePage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            isEnabled: true,
            date: '',
            title: '',
            slug: '',
            heading: '',
            subheading: '',
            body: '',
        },
        pastorsPageId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditPastorsPageValues;
        pastorsPageId?: string;
    },
) {
    const initialState: CreateEditPastorsPageSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditPastorsPageSubmitFormAction
            : CreateNewPastorsPageSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/pastors-page',
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
                            content: 'Pastor’s Page',
                            href: '/admin/pastors-page',
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
                <input type="hidden" name="pastorsPageId" value={pastorsPageId} />
                <PastorsPageTitleSlugFields
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
                <TextInput
                    label="Heading"
                    name="heading"
                    colSpan="full"
                    defaultValue={state.values.heading}
                    error={state.ok ? undefined : state.errors.heading}
                />
                <TextInput
                    label="Subheading"
                    name="subheading"
                    colSpan="full"
                    defaultValue={state.values.subheading}
                    error={state.ok ? undefined : state.errors.subheading}
                />
                <RichText
                    label="Body"
                    name="body"
                    colSpan="full"
                    defaultValue={state.values.body}
                    error={state.ok ? undefined : state.errors.body}
                />
                <FormButtons secondaryLinkHref="/admin/pastors-page" isPending={isPending} />
            </Form>
        </>
    );
}
