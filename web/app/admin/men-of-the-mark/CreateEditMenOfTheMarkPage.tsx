import React, { useActionState, useRef } from 'react';
import { CreateEditMenOfTheMarkValues } from './CreateEditMenOfTheMarkValues';
import { CreateEditMenOfTheMarkSubmitActionState } from './CreateEditMenOfTheMarkSubmitActionState';
import EditMenOfTheMarkSubmitFormAction from './edit/[id]/EditMenOfTheMarkSubmitFormAction';
import CreateNewMenOfTheMarkSubmitFormAction from './new/CreateNewMenOfTheMarkSubmitFormAction';
import MenOfTheMarkTitleSlugFields from './MenOfTheMarkTitleSlugFields';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import Toggle from '../Forms/Toggle';
import RichText from '../Forms/RichText';
import FormButtons from '../Forms/FormButtons';

export default function CreateEditMenOfTheMarkPage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            isEnabled: true,
            date: '',
            title: '',
            slug: '',
            body: '',
        },
        itemId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditMenOfTheMarkValues;
        itemId?: string;
    },
) {
    const initialState: CreateEditMenOfTheMarkSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditMenOfTheMarkSubmitFormAction
            : CreateNewMenOfTheMarkSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/men-of-the-mark',
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
                            content: 'Men of the Mark',
                            href: '/admin/men-of-the-mark',
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
                <input type="hidden" name="id" value={itemId} />
                <MenOfTheMarkTitleSlugFields
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
                <FormButtons secondaryLinkHref="/admin/men-of-the-mark" isPending={isPending} />
            </Form>
        </>
    );
}
