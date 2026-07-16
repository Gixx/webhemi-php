import React from 'react';
import { AdminLayout, Alert, Button, FormField, Input, SiteListView } from '@webhemi/ui';

function FlashList({ flashes }) {
    if (!flashes) {
        return null;
    }
    return Object.entries(flashes).flatMap(([tone, messages]) =>
        messages.map((message, index) => (
            <Alert
                key={`${tone}-${index}`}
                tone={tone === 'success' ? 'success' : 'danger'}
                className="mb-4"
            >
                {message}
            </Alert>
        )),
    );
}

export default function SitesPage({ userLabel, navItems, sites, canEdit, createAction, flashes }) {
    return (
        <AdminLayout navItems={navItems || []} userLabel={userLabel} topBarTitle="Sites">
            <FlashList flashes={flashes} />
            <SiteListView sites={sites || []} createHref="#create-site" />
            {canEdit ? (
                <form id="create-site" action={createAction} method="post" style={{ marginTop: '2rem' }}>
                    <FormField label="Name" htmlFor="name" required>
                        <Input id="name" name="name" required />
                    </FormField>
                    <FormField label="Slug" htmlFor="slug" required hint="Lowercase identifier">
                        <Input id="slug" name="slug" required />
                    </FormField>
                    <Button type="submit">Create site</Button>
                </form>
            ) : null}
        </AdminLayout>
    );
}
