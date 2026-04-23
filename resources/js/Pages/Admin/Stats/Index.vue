<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    mostPopular: Array,
    upcomingStats: Array,
    pastStats: Array,
    totalRegistrations: Number,
    totalWaitlist: Number,
    capacityAlerts: Number,
});

const upcomingData = ref([...props.upcomingStats]);
const pulseStates = ref({});
const isLive = ref(false);

// Watch for prop updates
watch(() => props.upcomingStats, (newData) => {
    upcomingData.value = [...newData];
}, { deep: true });

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString([], {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

onMounted(() => {
    if (window.Echo) {
        isLive.value = window.Echo.connector.pusher.connection.state === 'connected';
        
        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
            isLive.value = states.current === 'connected';
        });

        upcomingData.value.forEach((workshop) => {
            window.Echo.channel('workshops.' + workshop.id)
                .listen('.registration.updated', (data) => {
                    const index = upcomingData.value.findIndex(w => w.id === data.id);
                    if (index !== -1) {
                        upcomingData.value[index].confirmed_count = data.confirmed_count;
                        upcomingData.value[index].fill_percentage = data.capacity > 0 
                            ? Math.round((data.confirmed_count / data.capacity) * 100 * 100) / 100
                            : 0;
                        
                        pulseStates.value[workshop.id] = true;
                        setTimeout(() => {
                            pulseStates.value[workshop.id] = false;
                        }, 1000);
                    }
                });
        });
    }
});

onUnmounted(() => {
    upcomingData.value.forEach((workshop) => {
        window.Echo.leave('workshops.' + workshop.id);
    });
});

const mostPopularWorkshop = computed(() => {
    return props.mostPopular.length > 0 ? props.mostPopular[0] : null;
});
</script>

<template>
    <Head title="Workshop Statistics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Academy Insights</h2>
                <div class="flex items-center space-x-2">
                    <span 
                        class="h-2.5 w-2.5 rounded-full" 
                        :class="isLive ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-red-500'"
                    ></span>
                    <span class="text-sm font-medium" :class="isLive ? 'text-green-700' : 'text-red-700'">
                        {{ isLive ? 'Live' : 'Offline' }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- At a Glance Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Most Popular</div>
                        <div v-if="mostPopularWorkshop" class="mt-2">
                            <div class="text-xl font-bold text-gray-900 truncate">{{ mostPopularWorkshop.title }}</div>
                            <div class="text-sm text-gray-500">{{ mostPopularWorkshop.confirmed_count + mostPopularWorkshop.waitlist_count }} interested users</div>
                        </div>
                        <div v-else class="mt-2 text-xl font-bold text-gray-400">No data</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Registrations</div>
                        <div class="mt-2 flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900">{{ totalRegistrations }}</div>
                            <div class="ml-2 text-sm text-gray-500">confirmed seats</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4" :class="capacityAlerts > 0 ? 'border-amber-500' : 'border-gray-200'">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Capacity Alerts</div>
                        <div class="mt-2 flex items-baseline">
                            <div class="text-3xl font-bold" :class="capacityAlerts > 0 ? 'text-amber-600' : 'text-gray-900'">{{ capacityAlerts }}</div>
                            <div class="ml-2 text-sm text-gray-500">workshops at >90%</div>
                        </div>
                    </div>
                </div>

                <!-- Performance Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Upcoming Performance</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workshop</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fill Rate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waitlist</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr 
                                        v-for="workshop in upcomingData" 
                                        :key="workshop.id"
                                        :class="{ 'bg-amber-50': workshop.fill_percentage > 90 }"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ workshop.title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(workshop.starts_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span 
                                                    class="text-sm font-bold mr-3 min-w-[3rem]"
                                                    :class="[
                                                        workshop.fill_percentage > 90 ? 'text-amber-600' : 'text-gray-900',
                                                        { 'counter-pulse': pulseStates[workshop.id] }
                                                    ]"
                                                >
                                                    {{ workshop.fill_percentage }}%
                                                </span>
                                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                                    <div 
                                                        class="h-2 rounded-full transition-all duration-500" 
                                                        :class="workshop.fill_percentage > 90 ? 'bg-amber-500' : 'bg-indigo-600'"
                                                        :style="{ width: workshop.fill_percentage + '%' }"
                                                    ></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ workshop.waitlist_count }}
                                        </td>
                                    </tr>
                                    <tr v-if="upcomingData.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                            No upcoming workshops to monitor.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Past Workshops -->
                <div v-if="pastStats.length > 0" class="mt-8 opacity-60">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">Past Workshops</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="workshop in pastStats" :key="workshop.id" class="bg-gray-100 p-4 rounded-lg border border-gray-200">
                            <div class="text-sm font-bold text-gray-700 truncate">{{ workshop.title }}</div>
                            <div class="text-xs text-gray-500 mt-1">Final Fill: {{ workshop.fill_percentage }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.counter-pulse {
    animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
</style>
