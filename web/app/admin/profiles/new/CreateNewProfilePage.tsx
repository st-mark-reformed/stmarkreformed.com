import React from 'react';
import Breadcrumbs from '../../Breadcrumbs';
import PageTitle from '../../PageTitle';
import Form from '../../Forms/Form';
import TextInput from '../../Forms/TextInput';
import LeadershipPositionRadioButtons from './LeadershipPositionRadioButtons';
import RichText from '../../Forms/RichText';

export default async function CreateNewProfilePage () {
    return (
        <>
            <Breadcrumbs crumbs={
                [
                    {
                        content: 'Profiles',
                        href: '/admin/profiles',
                    },
                ]
            }
            />
            <PageTitle>
                Create New Profile
            </PageTitle>
            <Form>
                <TextInput
                    label="Title/Honorific"
                    name="title_or_honorific"
                />
                <TextInput
                    label="Email"
                    name="email"
                    type="email"
                />
                <TextInput
                    label="First Name"
                    name="first_name"
                    autoComplete="first-name"
                />
                <TextInput
                    label="Last Name"
                    name="last_name"
                    autoComplete="last-name"
                />
                <LeadershipPositionRadioButtons />
                <RichText
                    label="Bio"
                    name="bio"
                />
            </Form>
        </>
    );
}
