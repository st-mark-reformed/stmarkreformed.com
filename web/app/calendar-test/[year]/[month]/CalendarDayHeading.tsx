import React from 'react';

export default function CalendarDayHeading () {
    return (
        <div className="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-400 text-center text-xs font-semibold leading-6 text-gray-100 lg:flex-none">
            <div className="bg-crimson-dark py-2">Sun</div>
            <div className="bg-crimson-dark py-2">Mon</div>
            <div className="bg-crimson-dark py-2">Tue</div>
            <div className="bg-crimson-dark py-2">Wed</div>
            <div className="bg-crimson-dark py-2">Thu</div>
            <div className="bg-crimson-dark py-2">Fri</div>
            <div className="bg-crimson-dark py-2">Sat</div>
        </div>
    );
}
