'use client';

import React, {
    useActionState, useEffect, useRef, useState,
} from 'react';
import { useRouter } from 'next/navigation';
import { Profile } from './Profile';
import Breadcrumbs from '../Breadcrumbs';
import PageTitle, { Button } from '../PageTitle';
import CardList, { CardListHandle } from '../CardList/CardList';
import SubmitDeleteProfilesFormAction from './SubmitDeleteProfilesFormAction';
import Alert from '../../Alert';

export default function ProfilesPageClientSide (
    {
        profiles,
    }: {
        profiles: Profile[];
    },
) {
    const router = useRouter();

    const formRef = useRef<CardListHandle>(null);

    const [hasChecked, setHasChecked] = useState(false);

    const [state, formAction, isPending] = useActionState(
        /**
         * I can't figure out why the types aren't matching. As far as I can
         * tell, they're exactly right.
         */
        // @ts-expect-error TS2769
        SubmitDeleteProfilesFormAction,
        {
            status: 'unsubmitted',
            iteration: 0,
            message: '',
        },
    );

    useEffect(() => {
        formRef.current?.clearCheckedItems();
    }, [state]);

    useEffect(() => {
        if (state.status !== 'success') {
            return;
        }

        router.refresh();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [state]);

    const buttons: Button[] = (() => {
        if (isPending) {
            return [
                {
                    type: 'pending',
                    content: 'Deleting…',
                    glyph: 'trash',
                    href: 'delete-button',
                    onClick: () => {},
                },
            ];
        }

        if (hasChecked) {
            return [
                {
                    type: 'secondary',
                    content: 'Deselect All',
                    glyph: 'x-mark',
                    href: 'deselect-button',
                    onClick: () => {
                        formRef.current?.clearCheckedItems();
                    },
                },
                {
                    type: 'primary',
                    content: 'Delete Selected',
                    glyph: 'trash',
                    href: 'delete-button',
                    onClick: () => {
                        formRef.current?.requestSubmit();
                    },
                },
            ];
        }

        return [
            {
                type: 'primary',
                content: 'New Profile',
                glyph: 'plus',
                href: '/admin/profiles/new',
            },
        ];
    })();

    return (
        <>
            <Breadcrumbs />
            <PageTitle buttons={buttons}>Profiles</PageTitle>
            {(() => {
                if (state.status !== 'failure' || isPending) {
                    return null;
                }

                return (
                    <div className="mb-4">
                        <Alert
                            headline={state.message}
                            type="error"
                        />
                    </div>
                );
            })()}
            <CardList
                ref={formRef}
                formAction={formAction}
                noItemsFoundMessage="No profiles found."
                items={profiles.map((profile) => ({
                    id: profile.id,
                    columns: [
                        {
                            id: `title-${profile.id}`,
                            line1: profile.fullNameWithHonorific,
                            line2: profile.leadershipPositionHumanReadable,
                        },
                        [
                            {
                                content: 'Edit',
                                href: `/admin/profiles/edit/${profile.id}`,
                                type: 'primary',
                                rightGlyph: 'pencil',
                            },
                        ],
                    ],
                }))}
                onCheckedChange={(hasCheckedItems) => {
                    setHasChecked(hasCheckedItems);
                }}
                showCheckBoxes
            />
        </>
    );
}
