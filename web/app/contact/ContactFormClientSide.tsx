'use client';

import React, {
    ChangeEvent,
    ReactNode,
    useActionState,
    useState,
} from 'react';
import { useRouter } from 'next/navigation';
import PostContactForm from './PostContactForm';
import Alert from '../Alert';
import { FormValues } from './FormValues';

export default function ContactFormClientSide () {
    const router = useRouter();

    const [error, submitAction, isPending] = useActionState(
        async (previousState: ReactNode | null, formData: FormData) => {
            const aPassword = formData.get('aPassword') as string;
            const yourCompany = formData.get('yourCompany') as string;
            const name = formData.get('name') as string;
            const emailAddress = formData.get('emailAddress') as string;
            const message = formData.get('message') as string;

            const result = await PostContactForm({
                aPassword,
                yourCompany,
                name,
                emailAddress,
                message,
            });

            if (result.success) {
                router.push('/contact/thanks');

                return null;
            }

            const errors = result.errors || [];

            return (
                <div className="pb-6">
                    <Alert
                        type="error"
                        headline={result.message}
                        contentList={errors}
                    />
                </div>
            );
        },
        null,
    );

    const [formValues, setFormValues] = useState<FormValues>({
        aPassword: '',
        yourCompany: '',
        name: '',
        emailAddress: '',
        message: '',
    });

    const setFormValue = (
        event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
        key: 'aPassword' | 'yourCompany' | 'name' | 'emailAddress' | 'message',
    ) => {
        const newFormValues = {
            ...formValues,
        };

        newFormValues[key] = event.target.value;

        setFormValues(newFormValues);
    };

    return (
        <>
            {error}
            <form
                action={submitAction}
                className="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8"
            >
                <input
                    type="text"
                    name="aPassword"
                    className="sr-only"
                    tabIndex={-1}
                    autoComplete="nope"
                    value={formValues.aPassword}
                    onChange={(e) => setFormValue(e, 'aPassword')}
                />
                <input
                    type="text"
                    name="yourCompany"
                    className="sr-only"
                    tabIndex={-1}
                    autoComplete="nope"
                    value={formValues.yourCompany}
                    onChange={(e) => setFormValue(e, 'yourCompany')}
                />
                <div>
                    <label htmlFor="your_name" className="block text-sm font-medium text-gray-900">Your name</label>
                    <div className="mt-1">
                        <input
                            type="text"
                            name="name"
                            id="name"
                            className="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-crimson focus:border-crimson border-gray-300 rounded-md"
                            value={formValues.name}
                            onChange={(e) => setFormValue(e, 'name')}
                        />
                    </div>
                </div>
                <div>
                    <label htmlFor="your_email" className="block text-sm font-medium text-gray-900">Your Email Address</label>
                    <div className="mt-1">
                        <input
                            type="text"
                            name="emailAddress"
                            id="emailAddress"
                            className="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-crimson focus:border-crimson border-gray-300 rounded-md"
                            value={formValues.emailAddress}
                            onChange={(e) => setFormValue(e, 'emailAddress')}
                        />
                    </div>
                </div>
                <div className="sm:col-span-2">
                    <label htmlFor="message" className="block text-sm font-medium text-gray-900">Message</label>
                    <div className="mt-1">
                        <textarea
                            id="message"
                            name="message"
                            rows={4}
                            className="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-crimson focus:border-crimson border border-gray-300 rounded-md"
                            value={formValues.message}
                            onChange={(e) => setFormValue(e, 'message')}
                        />
                    </div>
                </div>
                <div className="sm:col-span-2 sm:flex sm:justify-end">
                    <button
                        type="submit"
                        className={(() => {
                            const classes = [
                                'mt-2',
                                'w-full',
                                'inline-flex',
                                'items-center',
                                'justify-center',
                                'px-6',
                                'py-3',
                                'border',
                                'border-transparent',
                                'rounded-md',
                                'shadow-sm',
                                'text-base',
                                'font-medium',
                                'text-white',
                                'focus:outline-none',
                                'focus:ring-2',
                                'focus:ring-offset-2',
                                'sm:w-auto',
                                'cursor-pointer',
                            ];

                            if (isPending) {
                                classes.push('bg-gray-400');
                            } else {
                                classes.push(
                                    'bg-crimson',
                                    'hover:bg-crimson-dark',
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
                                Submit
                            </span>
                        </div>
                    </button>
                </div>
            </form>
        </>
    );
}
