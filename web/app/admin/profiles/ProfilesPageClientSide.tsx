'use client';

import React, { useRef, useState } from 'react';
import { ChevronRightIcon } from '@heroicons/react/20/solid';
import { Profile } from './Profile';
import Breadcrumbs from '../Breadcrumbs';
import PageTitle, { Button } from '../PageTitle';
import CardList from '../CardList/CardList';

export default function ProfilesPageClientSide (
    {
        profiles,
    }: {
        profiles: Profile[];
    },
) {
    const formRef = useRef<HTMLFormElement>(null);

    const [hasChecked, setHasChecked] = useState(false);

    const buttons: Button[] = (() => {
        if (hasChecked) {
            // TODO: if isPending
            return [
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
            <PageTitle buttons={buttons}>
                Profiles
            </PageTitle>
            <CardList
                ref={formRef}
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
