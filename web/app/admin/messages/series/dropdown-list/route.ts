import GetSeriesDropdownList from './GetSeriesDropdownList';

export async function GET () {
    const options = await GetSeriesDropdownList();

    return Response.json(options);
}
