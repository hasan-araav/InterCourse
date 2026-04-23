<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    registrations: Array,
});

const cancel = (workshopId) => {
    if (confirm('Are you sure you want to cancel your registration? This action cannot be undone.')) {
        router.delete(route('workshops.cancel', workshopId), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString([], {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="My Schedule" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Schedule</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.message" class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="registrations.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workshop</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="reg in registrations" :key="reg.id">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ reg.title }}</div>
                                            <div class="text-xs text-gray-500">{{ reg.duration_minutes }} mins</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(reg.starts_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                :class="{
                                                    'bg-green-100 text-green-800': reg.status === 'confirmed',
                                                    'bg-yellow-100 text-yellow-800': reg.status === 'waitlisted'
                                                }"
                                                class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{ reg.status === 'confirmed' ? 'Confirmed' : 'Waitlisted (#' + reg.position + ')' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <DangerButton @click="cancel(reg.id)">
                                                Cancel
                                            </DangerButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 mb-4">You haven't registered for any workshops yet.</p>
                            <Link :href="route('dashboard')" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Browse Workshops →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
