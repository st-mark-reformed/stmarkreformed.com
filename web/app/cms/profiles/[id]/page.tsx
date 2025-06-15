import { Metadata } from 'next';
import React from 'react';
import { createPageTitle } from '../../../createPageTitle';
import ApiResponseGate from '../../ApiResponseGate';
import CmsLayout from '../../layout/CmsLayout';
import { RequestFactory } from '../../../api/request/RequestFactory';
import EditProfileForm from '../EditProfile/EditProfileForm';
import { Profile } from '../Profile';
import { getLeadershipPositionKeyByValue } from '../EditProfile/ProfileFormData';

export const dynamic = 'force-dynamic';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Edit Profile',
            'Messages',
            'CMS',
        ]),
    };
}

export default async function Page (
    {
        params,
    }: {
        params: Promise<{
            id: string;
        }>;
    },
) {
    const { id } = await params;

    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: `/cms/profiles/${id}`,
        cacheSeconds: 0,
    });

    const profile = apiResponse.json as unknown as Profile & {
        id: string;
    };

    return (
        <CmsLayout
            breadcrumbs={{
                breadcrumbs: [
                    {
                        value: 'CMS',
                        href: '/cms',
                    },
                    {
                        value: 'Profiles',
                        href: '/cms/profiles',
                    },
                ],
                currentBreadcrumb: { value: 'Edit' },
            }}
        >
            <ApiResponseGate apiResponse={apiResponse}>
                <EditProfileForm
                    id={profile.id}
                    initialFormData={{
                        ...profile,
                        leadershipPosition: getLeadershipPositionKeyByValue(
                            profile.leadershipPosition,
                        ),
                    }}
                />
            </ApiResponseGate>
        </CmsLayout>
    );
}
