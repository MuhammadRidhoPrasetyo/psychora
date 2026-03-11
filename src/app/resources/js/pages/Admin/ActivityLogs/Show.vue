<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import type { BreadcrumbItem } from '@/types';
import type { ActivityLog } from '@/types/models';

const props = defineProps<{ activityLog: ActivityLog }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Activity Logs', href: '/admin/activity-logs' },
    { title: 'Detail', href: '#' },
];
</script>

<template>
    <Head title="Activity Log Detail" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Activity Log</CardTitle>
                    <CardDescription>{{ new Date(activityLog.created_at).toLocaleString('id-ID') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-muted-foreground">User</dt>
                            <dd class="font-medium">{{ activityLog.user?.name ?? 'System' }}</dd>
                            <dd v-if="activityLog.user" class="text-xs text-muted-foreground">{{ activityLog.user.email }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Action</dt>
                            <dd><Badge variant="outline">{{ activityLog.action }}</Badge></dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Subject Type</dt>
                            <dd>{{ activityLog.subject_type ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Subject ID</dt>
                            <dd class="font-mono text-xs">{{ activityLog.subject_id ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Description</dt>
                            <dd>{{ activityLog.description ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">IP Address</dt>
                            <dd class="font-mono">{{ activityLog.ip_address ?? '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-muted-foreground">User Agent</dt>
                            <dd class="text-xs break-all">{{ activityLog.user_agent ?? '-' }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card v-if="activityLog.properties && Object.keys(activityLog.properties).length">
                <CardHeader><CardTitle>Properties</CardTitle></CardHeader>
                <CardContent>
                    <pre class="bg-muted p-4 rounded-lg text-xs overflow-x-auto">{{ JSON.stringify(activityLog.properties, null, 2) }}</pre>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
