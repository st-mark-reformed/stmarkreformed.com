'use client';

import React, {
    ChangeEvent, ReactNode, useActionState, useState,
} from 'react';
import { useRouter } from 'next/navigation';
import Alert from '../Alert';
import { FormValues } from './FormValues';
import PostLoginForm from './PostLoginForm';

export default function LoginPageClientSide () {
    const router = useRouter();

    const [error, submitAction, isPending] = useActionState(
        async (previousState: ReactNode | null, formData: FormData) => {
            const email = formData.get('email') as string;
            const password = formData.get('password') as string;

            const result = await PostLoginForm({
                email,
                password,
            });

            if (result.isValid) {
                router.refresh();

                return null;
            }

            return (
                <div className="pb-2">
                    <Alert
                        type="error"
                        headline="We were not able to log you in with that email and password"
                    />
                </div>
            );
        },
        null,
    );

    const [formValues, setFormValues] = useState<FormValues>({
        email: '',
        password: '',
    });

    const setFormValue = (
        event: ChangeEvent<HTMLInputElement>,
        key: 'email' | 'password',
    ) => {
        const newFormValues = {
            ...formValues,
        };

        newFormValues[key] = event.target.value;

        setFormValues(newFormValues);
    };

    return (
        <form
            action={submitAction}
            className="space-y-6"
            noValidate
        >
            {error}
            <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                    Email address
                </label>
                <div className="mt-1">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autoComplete="email"
                        className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-crimson focus:border-crimson sm:text-sm"
                        value={formValues.email}
                        onChange={(e) => setFormValue(e, 'email')}
                        required
                    />
                </div>
            </div>
            <div>
                <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <div className="mt-1">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autoComplete="current-password"
                        className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-crimson focus:border-crimson sm:text-sm"
                        value={formValues.password}
                        onChange={(e) => setFormValue(e, 'password')}
                        required
                    />
                </div>
            </div>
            <div>
                <button
                    type="submit"
                    className={(() => {
                        const classes = [
                            'w-full',
                            'flex',
                            'justify-center',
                            'py-2',
                            'px-4',
                            'border',
                            'border-transparent',
                            'rounded-md',
                            'shadow-sm',
                            'text-sm',
                            'font-medium',
                            'text-white',
                            'cursor-pointer',
                        ];

                        if (isPending) {
                            classes.push('bg-gray-400');
                        } else {
                            classes.push(
                                'bg-crimson',
                                'hover:bg-crimson-dark',
                                'focus:outline-none',
                                'focus:ring-2',
                                'focus:ring-offset-2',
                                'focus:ring-crimson-dark',
                            );
                        }

                        return classes.join(' ');
                    })()}
                    disabled={isPending}
                >
                    <div>
                        {(() => {
                            if (!isPending) {
                                return null;
                            }

                            return (
                                <div
                                    className="loader border-t-gray-500 ease-linear rounded-full border-4 border-t-4 border-gray-200 h-4 w-4 inline-block align-middle mr-1"
                                />
                            );
                        })()}
                        <span className="inline-block align-middle">
                            Log in
                        </span>
                    </div>
                </button>
            </div>
        </form>
    );
}
