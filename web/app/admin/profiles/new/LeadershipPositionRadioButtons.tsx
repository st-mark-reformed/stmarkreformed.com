import React, { useEffect, useState } from 'react';
import TableRadioButtons from '../../Forms/TableRadioButtons';
import PartialPageLoading from '../../../PartialPageLoading';
import { LeadershipPositionOption } from '../leadership-position-options/GetLeadershipPositionOptions';

export default function LeadershipPositionRadioButtons (
    {
        defaultValue = undefined,
    }: {
        defaultValue?: string | undefined;
    },
) {
    const [
        positions,
        setPositions,
    ] = useState<LeadershipPositionOption[] | null>(null);

    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        let cancelled = false;

        async function loadPositions () {
            try {
                const response = await fetch(
                    '/admin/profiles/leadership-position-options',
                    {
                        cache: 'no-store',
                    },
                );

                if (!response.ok) {
                    throw new Error('Failed to load leadership positions');
                }

                const data = await response.json() as LeadershipPositionOption[];

                if (cancelled) {
                    return;
                }

                setPositions(data);
            } catch (error_) {
                if (cancelled) {
                    return;
                }
                setError(
                    error_ instanceof Error
                        ? error_.message
                        : 'Something went wrong',
                );
            }
        }

        loadPositions();

        return () => {
            cancelled = true;
        };
    }, []);

    if (error) {
        return <p className="text-sm text-red-600">{error}</p>;
    }

    if (!positions) {
        return <PartialPageLoading />;
    }

    return (
        <TableRadioButtons
            label="Leadership Position"
            name="leadershipPosition"
            options={positions.map((position, index) => {
                let defaultChecked;

                if (defaultValue) {
                    defaultChecked = defaultValue === position.name;
                } else {
                    defaultChecked = index === 0;
                }

                return ({
                    ...position,
                    defaultChecked,
                });
            })}
        />
    );
}
