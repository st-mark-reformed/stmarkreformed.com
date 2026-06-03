import React from 'react';
import GetEditMenOfTheMark from './GetEditMenOfTheMark';
import EditMenOfTheMarkPageClientSide from './EditMenOfTheMarkPageClientSide';

export default async function EditMenOfTheMarkPage (
    {
        id,
    }: {
        id: string;
    },
) {
    const item = await GetEditMenOfTheMark(id);

    if (!item) {
        return null;
    }

    return <EditMenOfTheMarkPageClientSide item={item} />;
}
