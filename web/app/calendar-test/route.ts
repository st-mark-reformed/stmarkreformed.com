import { NextResponse } from 'next/server';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export async function GET () {
    const date = new Date();

    const year = date.getFullYear();

    const month = (date.getMonth() + 1).toString().padStart(
        2,
        '0',
    );

    return NextResponse.redirect(new URL(
        `/calendar-test/${year}/${month}`,
        getConfigString(ConfigOptions.BASE_URL),
    ));
}
