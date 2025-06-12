import React from 'react';

export default function TextInput (
    {
        label,
        name,
        value,
        setValue,
        type = 'text',
        min,
        max,
    }: {
        label: string;
        name: string;
        value: string;
        setValue: (key: string, val: string) => void;
        type?: 'text' | 'date' | 'time' | 'datetime-local' | 'number';
        min?: number;
        max?: number;
    },
) {
    if (type === 'time' && value !== '') {
        const parts1 = value.split(' ');
        const timeString = parts1[0];
        const amPm = parts1[1];

        const parts2 = timeString.split(':');
        let hour = parseInt(parts2[0], 10);
        const mins = parseInt(parts2[1], 10);

        if (amPm === 'PM' && hour < 12) {
            hour += 12;
        }

        const hourString = hour.toString().padStart(2, '0');
        const minString = mins.toString().padStart(2, '0');

        value = `${hourString}:${minString}`;
    }

    return (
        <div>
            <label
                htmlFor={name}
                className="block text-sm font-semibold leading-6 text-gray-900"
            >
                {label}
            </label>
            <div className="mt-2">
                <input
                    type={type}
                    min={min}
                    max={max}
                    name={name}
                    id={name}
                    className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-inset focus:ring-gray-400 sm:text-sm sm:leading-6"
                    value={value}
                    onChange={(e) => {
                        let val = e.currentTarget.value;

                        if (type === 'time' && val !== '') {
                            const parts = val.split(':');
                            let hour = parseInt(parts[0], 10);
                            const mins = parseInt(parts[1], 10);
                            let amPm = 'AM';

                            if (hour >= 12) {
                                amPm = 'PM';
                            }

                            if (hour > 12) {
                                hour -= 12;
                            }

                            const hourString = hour.toString().padStart(2, '0');

                            const minString = mins.toString().padStart(2, '0');

                            val = `${hourString}:${minString} ${amPm}`;
                        }

                        setValue(name, val);
                    }}
                />
            </div>
        </div>
    );
}
