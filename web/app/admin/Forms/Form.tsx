import React, { forwardRef, ComponentPropsWithoutRef } from 'react';

type FormProps = {
    children: React.ReactNode;
    action: ComponentPropsWithoutRef<'form'>['action'];
};

const Form = forwardRef<HTMLFormElement, FormProps>((
    {
        children,
        action,
    },
    ref,
) => (
    <form
        ref={ref}
        action={action}
    >
        <div className="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
            {children}
        </div>
    </form>
));

export default Form;
