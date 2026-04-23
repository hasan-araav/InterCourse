<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    workshops: Object,
});

const deleteWorkshop = (id) => {
    if (confirm('Are you sure you want to delete this workshop?')) {
        router.delete(route('admin.workshops.destroy', id));
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};
</script>

<template>
    <Head title="Manage Workshops" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workshops</h2>
                <Link :href="route('admin.workshops.create')">
                    <PrimaryButton>Create New Workshop</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="workshop in workshops.data" :key="workshop.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ workshop.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(workshop.starts_at) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ workshop.duration_minutes }} mins</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ workshop.capacity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('admin.workshops.edit', workshop.id)">
                                            <SecondaryButton size="sm">Edit</SecondaryButton>
                                        </Link>
                                        <DangerButton size="sm" @click="deleteWorkshop(workshop.id)">
                                            Delete
                                        </DangerButton>
                                    </td>
                                </tr>
                                <tr v-if="workshops.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No workshops found.</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div v-if="workshops.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-1">
                            <template v-for="(link, key) in workshops.links" :key="key">
                                <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border rounded" v-html="link.label" />
                                <Link
                                    v-else
                                    :href="link.url"
                                    class="px-4 py-2 text-sm border rounded hover:bg-gray-100"
                                    :class="{ 'bg-indigo-600 text-white hover:bg-indigo-700 border-indigo-600': link.active }"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
