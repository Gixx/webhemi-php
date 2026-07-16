import React from 'react';
import { AdminLayout, Alert, PageHeader } from '@webhemi/ui';

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

export default function AdminDashboard({ userLabel, navItems, siteCount, hostCount, flashes }) {
    return (
        <AdminLayout navItems={navItems || []} userLabel={userLabel} topBarTitle="Dashboard">
            <FlashList flashes={flashes} />
            <PageHeader
                title="Dashboard"
                description="Multi-tenant control panel powered by @webhemi/ui."
            />
            <div style={{ display: 'flex', gap: '1.5rem', flexWrap: 'wrap' }}>
                <Alert tone="info" title="Sites">
                    {siteCount} configured
                </Alert>
                <Alert tone="info" title="Hosts">
                    {hostCount} configured
                </Alert>
            </div>
        </AdminLayout>
    );
}
