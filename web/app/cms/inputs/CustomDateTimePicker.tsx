import React from 'react';
import Datepicker from 'react-tailwindcss-datepicker';

export default function CustomDateTimePicker (
    {
        label,
        name,
        value,
        setValue,
    }: {
        label: string;
        name: string;
        value: Date | null;
        setValue: (val: Date | null) => void;
    },
) {
    let timeValue = '';

    if (value !== null) {
        const hours = value.getHours().toString().padStart(2, '0');
        const minutes = value.getMinutes().toString().padStart(2, '0');

        timeValue = `${hours}:${minutes}`;
    }

    return (
        <div>
            <label
                htmlFor={name}
                className="block text-sm font-semibold leading-6 text-gray-900"
            >
                {label}
                {' '}
                <span className="font-light text-xs text-gray-600">(Central Time)</span>
            </label>
            <div className="align-top grid gap-4 md:grid-cols-2">
                <Datepicker
                    asSingle
                    showShortcuts
                    primaryColor="cyan"
                    popoverDirection="down"
                    useRange={false}
                    value={{
                        startDate: value,
                        endDate: value,
                    }}
                    onChange={(val) => {
                        const theDate = val?.startDate;

                        if (theDate !== null && value !== null) {
                            theDate?.setHours(value.getHours());
                            theDate?.setMinutes(value.getMinutes());
                        }

                        setValue(theDate || null);
                    }}
                />
                <input
                    type="time"
                    className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-inset focus:ring-gray-400 sm:text-sm sm:leading-6"
                    value={timeValue}
                    onChange={(e) => {
                        const timeParams = e.currentTarget.value.split(':');
                        const hours = parseInt(timeParams[0], 10);
                        const minutes = parseInt(timeParams[1], 10);

                        const newDate = value || new Date();

                        newDate.setHours(hours);

                        newDate.setMinutes(minutes);

                        setValue(newDate);
                    }}
                />
            </div>
        </div>
    );
}
