import React, { useActionState, useRef, useState } from 'react';
import { CreateEditHymnOfTheMonthValues } from './CreateEditHymnOfTheMonthValues';
import { CreateEditHymnOfTheMonthSubmitActionState } from './CreateEditHymnOfTheMonthSubmitActionState';
import EditHymnOfTheMonthSubmitFormAction from './edit/[hymnOfTheMonthId]/EditHymnOfTheMonthSubmitFormAction';
import CreateNewHymnOfTheMonthSubmitFormAction from './new/CreateNewHymnOfTheMonthSubmitFormAction';
import HymnPracticeTracksField from './HymnPracticeTracksField';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import Form from '../Forms/Form';
import Alert from '../../Alert';
import TextInput from '../Forms/TextInput';
import Toggle from '../Forms/Toggle';
import FormButtons from '../Forms/FormButtons';
import ChunkedFileUploader from '../Forms/FileUploads/ChunkedFileUploader';

export default function CreateEditHymnOfTheMonthPage (
    {
        pageTitle,
        submitFormAction,
        initialValues = {
            isEnabled: true,
            month: '',
            hymnPsalmName: '',
            musicSheet: '',
            practiceTracks: [],
        },
        hymnOfTheMonthId = '',
    }: {
        pageTitle: string;
        submitFormAction: 'new' | 'edit';
        initialValues?: CreateEditHymnOfTheMonthValues;
        hymnOfTheMonthId?: string;
    },
) {
    const initialState: CreateEditHymnOfTheMonthSubmitActionState = {
        ok: true,
        success: false,
        values: initialValues,
    };

    const [state, formAction, isPending] = useActionState(
        submitFormAction === 'edit'
            ? EditHymnOfTheMonthSubmitFormAction
            : CreateNewHymnOfTheMonthSubmitFormAction,
        initialState,
    );

    const formRef = useRef<HTMLFormElement>(null);

    const [isUploadingSheet, setIsUploadingSheet] = useState(false);
    const [isUploadingTracks, setIsUploadingTracks] = useState(false);

    const isUploading = isUploadingSheet || isUploadingTracks;
    const isBusy = isPending || isUploading;

    const buttons: Button[] = [
        {
            content: 'Cancel',
            href: '/admin/hymns-of-the-month',
            type: 'secondary',
        },
    ];

    if (isBusy) {
        buttons.push({
            content: isUploading ? 'Uploading…' : 'Submitting…',
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
                            content: 'Hymns of the Month',
                            href: '/admin/hymns-of-the-month',
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
                <input type="hidden" name="hymnOfTheMonthId" value={hymnOfTheMonthId} />
                <TextInput
                    label="Month"
                    name="month"
                    type="month"
                    defaultValue={state.values.month}
                    error={state.ok ? undefined : state.errors.month}
                />
                <Toggle
                    label="Is Enabled?"
                    name="isEnabled"
                    defaultValue={state.values.isEnabled}
                    error={state.ok ? undefined : state.errors.isEnabled}
                />
                <TextInput
                    label="Hymn/Psalm Name"
                    name="hymnPsalmName"
                    colSpan="full"
                    defaultValue={state.values.hymnPsalmName}
                    error={state.ok ? undefined : state.errors.hymnPsalmName}
                />
                <ChunkedFileUploader
                    label="Music Sheet"
                    name="musicSheet"
                    fileTypes={['PDF']}
                    acceptType="any"
                    defaultValue={state.values.musicSheet}
                    error={state.ok ? undefined : state.errors.musicSheet}
                    onUploadingChange={setIsUploadingSheet}
                />
                <HymnPracticeTracksField
                    initialTracks={state.values.practiceTracks}
                    onUploadingChange={setIsUploadingTracks}
                />
                <FormButtons
                    secondaryLinkHref="/admin/hymns-of-the-month"
                    isPending={isBusy}
                    submitButtonContentWhenPending={isUploading ? 'Uploading…' : 'Submitting…'}
                />
            </Form>
        </>
    );
}
