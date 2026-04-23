<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import WorkshopForm from './Partials/WorkshopForm.vue';

const form = useForm({
    title: '',
    description: '',
    starts_at: '',
    duration_minutes: '',
    capacity: '',
});

const submit = () => {
    form.post(route('admin.workshops.store'));
};
</script>

<template>
    <Head title="Create Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Workshop</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <WorkshopForm :form="form" @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
