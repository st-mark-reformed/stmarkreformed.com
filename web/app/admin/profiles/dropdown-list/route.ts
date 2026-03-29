import GetProfilesDropdownList from './GetProfilesDropdownList';

export async function GET () {
    const options = await GetProfilesDropdownList();

    return Response.json(options);
}
