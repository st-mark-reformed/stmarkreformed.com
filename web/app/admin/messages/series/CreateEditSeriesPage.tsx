import React, { useActionState, useRef } from 'react';
import { CreateEditSeriesSubmitActionState } from './CreateEditSeriesSubmitActionState';
import CreateNewSeriesSubmitFormAction from './new/CreateNewSeriesSubmitFormAction';
import PageTitle, { Button } from '../../PageTitle';
import Breadcrumbs from '../../Breadcrumbs';
import Form from '../../Forms/Form';
import Alert from '../../../Alert';
import TextInput from '../../Forms/TextInput';
import FormButtons from '../../Forms/FormButtons';
import EditSeriesSubmitFormAction from './edit/[seriesId]/EditSeriesSubmitFormAction';

export default function CreateEditSeriesPage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            title: '',
            slug: '',
        },
        seriesId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: {
            title: string;
            slug: string;
        };
        seriesId?: string;
    },
) {
    const initialState: CreateEditSeriesSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditSeriesSubmitFormAction
            : CreateNewSeriesSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/messages/series',
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
                        {
                            content: 'Series',
                            href: '/admin/messages/series',
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
                <input type="hidden" name="seriesId" value={seriesId} />
                <TextInput
                    label="Title"
                    name="title"
                    defaultValue={state.values.title}
                    error={state.ok ? undefined : state.errors.title}
                />
                <TextInput
                    label="Slug"
                    name="slug"
                    defaultValue={state.values.slug}
                    error={state.ok ? undefined : state.errors.slug}
                />
                <FormButtons secondaryLinkHref="/admin/messages/series" />
            </Form>
        </>
    );
}
