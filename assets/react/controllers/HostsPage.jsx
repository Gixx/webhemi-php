import React from 'react';
import { AdminLayout, Alert, Button, FormField, Input, Select, SiteHostListView } from '@webhemi/ui';

function FlashList({ flashes }) {
    if (!flashes) {
        return null;
    }
    return Object.entries(flashes).flatMap(([tone, messages]) =>
        messages.map((message, index) => (
            <Alert
                key={`${tone}-${index}`}
                tone={tone === 'success' ? 'success' : tone === 'warning' ? 'warning' : 'danger'}
                className="mb-4"
            >
                {message}
            </Alert>
        )),
    );
}

export default function HostsPage({ userLabel, navItems, hosts, sites, canEdit, createAction, flashes }) {
    return (
        <AdminLayout navItems={navItems || []} userLabel={userLabel} topBarTitle="Hosts">
            <FlashList flashes={flashes} />
            <SiteHostListView hosts={hosts || []} createHref="#create-host" />
            {canEdit ? (
                <form id="create-host" action={createAction} method="post" style={{ marginTop: '2rem' }}>
                    <FormField label="Hostname" htmlFor="host" required>
                        <Input id="host" name="host" placeholder="www.example.com" required />
                    </FormField>
                    <FormField label="Site" htmlFor="site_id" required>
                        <Select id="site_id" name="site_id" required>
                            {(sites || []).map((site) => (
                                <option key={site.id} value={site.id}>
                                    {site.name}
                                </option>
                            ))}
                        </Select>
                    </FormField>
                    <FormField label="Surface" htmlFor="surface">
                        <Select id="surface" name="surface" defaultValue="site">
                            <option value="admin">admin</option>
                            <option value="site">site</option>
                            <option value="api">api</option>
                        </Select>
                    </FormField>
                    <Button type="submit">Add host</Button>
                </form>
            ) : null}
        </AdminLayout>
    );
}
