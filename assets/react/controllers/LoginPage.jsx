import React from 'react';
import { LoginForm } from '@webhemi/ui';

export default function LoginPage({ action, csrfToken, csrfFieldName, emailDefault, error }) {
    return (
        <LoginForm
            action={action}
            csrfToken={csrfToken}
            csrfFieldName={csrfFieldName}
            emailDefault={emailDefault || ''}
            error={error || undefined}
        />
    );
}
