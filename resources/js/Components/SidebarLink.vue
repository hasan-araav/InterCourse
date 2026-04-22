<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full px-4 py-2 text-left text-sm font-medium leading-5 text-indigo-700 bg-indigo-50 rounded-md focus:outline-none focus:bg-indigo-100 transition duration-150 ease-in-out'
        : 'block w-full px-4 py-2 text-left text-sm font-medium leading-5 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
