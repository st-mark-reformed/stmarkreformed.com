import GetLeadershipPositionOptions from './GetLeadershipPositionOptions';

export async function GET () {
    const options = await GetLeadershipPositionOptions();

    return Response.json(options);
}
