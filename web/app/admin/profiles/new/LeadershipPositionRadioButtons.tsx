import React from 'react';
import GetLeadershipPositionOptions from './GetLeadershipPositionOptions';
import TableRadioButtons from '../../Forms/TableRadioButtons';

export default async function LeadershipPositionRadioButtons () {
    const positions = await GetLeadershipPositionOptions();

    positions[0].defaultChecked = true;

    return (
        <TableRadioButtons
            label="Leadership Position"
            name="leadership_position"
            options={positions}
        />
    );
}
