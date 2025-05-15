import React from 'react';

interface State {
    full: string;
    abbr: string;
}

interface Address {
    line1: string;
    city: string;
    state: State;
    zip: string;
}

const state: State = {
    full: 'Tennessee',
    abbr: 'TN',
};

export const WorshipAddress: Address = {
    line1: '1301 Franklin Rd.',
    city: 'Brentwood',
    state,
    zip: '37027',
};

export const MailingAddress: Address = {
    line1: 'PO Box 1543',
    city: 'Franklin',
    state,
    zip: '37065',
};

export function WorshipAddressDisplay () {
    return (
        <>
            {WorshipAddress.line1}
            <br />
            {WorshipAddress.city}, {WorshipAddress.state.abbr} {WorshipAddress.zip}
        </>
    );
}

export function MailingAddressDisplay () {
    return (
        <>
            {MailingAddress.line1}
            <br />
            {MailingAddress.city}, {MailingAddress.state.abbr} {MailingAddress.zip}
        </>
    );
}
