<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import WorkshopForm from './Partials/WorkshopForm.vue';

const props = defineProps({
    workshop: Object,
});

// Format date for datetime-local input (YYYY-MM-DDThh:mm)
const formatForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const form = useForm({
    title: props.workshop.title,
    description: props.workshop.description,
    starts_at: formatForInput(props.workshop.starts_at),
    duration_minutes: props.workshop.duration_minutes,
    capacity: props.workshop.capacity,
});

const submit = () => {
    form.put(route('admin.workshops.update', props.workshop.id));
};
</script>

<template>
    <Head title="Edit Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Workshop</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <WorkshopForm :form="form" :is-edit="true" @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
