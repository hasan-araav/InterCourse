<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    mostPopular: Array,
    upcomingStats: Array,
    pastStats: Array,
    totalRegistrations: Number,
    totalWaitlist: Number,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString([], {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Workshop Statistics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workshop Statistics</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Total Confirmed</div>
                        <div class="mt-2 text-3xl font-bold text-indigo-600">{{ totalRegistrations }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Total Waitlisted</div>
                        <div class="mt-2 text-3xl font-bold text-yellow-600">{{ totalWaitlist }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Upcoming Workshops</div>
                        <div class="mt-2 text-3xl font-bold text-green-600">{{ upcomingStats.length }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Completed Workshops</div>
                        <div class="mt-2 text-3xl font-bold text-gray-600">{{ pastStats.length }}</div>
                    </div>
                </div>

                <!-- Most Popular Workshops -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Most Popular Workshops</h3>
                        <div class="space-y-4">
                            <div v-for="workshop in mostPopular" :key="workshop.id" class="flex items-center">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ workshop.title }}</div>
                                    <div class="text-xs text-gray-500">{{ formatDate(workshop.starts_at) }}</div>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ workshop.confirmed_count + workshop.waitlist_count }} Interested</div>
                                    <div class="text-xs text-gray-500">{{ workshop.confirmed_count }} confirmed, {{ workshop.waitlist_count }} waitlisted</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Workshop Performance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Upcoming Performance</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workshop</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fill Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waitlist</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="workshop in upcomingStats" :key="workshop.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ workshop.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(workshop.starts_at) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ workshop.capacity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-900 mr-2">{{ workshop.fill_percentage }}%</span>
                                            <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: workshop.fill_percentage + '%' }"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ workshop.waitlist_count }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
