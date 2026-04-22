<script setup>
import { ref } from 'vue';
import SidebarLink from '@/Components/SidebarLink.vue';

const props = defineProps({
    user: Object,
});
</script>

<template>
    <div class="flex">
        <!-- Sidebar for Desktop -->
        <aside class="hidden w-64 bg-white border-r border-gray-100 min-h-screen sm:block">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800">Employee Portal</h2>
            </div>
            <nav class="mt-4 px-4 space-y-2">
                <SidebarLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                >
                    Dashboard
                </SidebarLink>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1">
            <slot />
        </div>
    </div>
</template>
