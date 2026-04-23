<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import {
    Users,
    UserCheck,
    Clock,
    AlertCircle,
    TrendingUp,
    Calendar,
    ChevronRight,
    Activity
} from 'lucide-vue-next';
import { Line, Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    CategoryScale,
    ArcElement
} from 'chart.js';

ChartJS.register(
    Title, Tooltip, Legend, LineElement, LinearScale, PointElement, CategoryScale, ArcElement
);

const props = defineProps({
    mostPopular: Array,
    upcomingStats: Array,
    pastStats: Array,
    totalRegistrations: Number,
    totalWaitlist: Number,
    capacityAlerts: Number,
    registrationTrends: Array,
    statusDistribution: Object,
});

const upcomingData = ref([...props.upcomingStats]);
const pulseStates = ref({});
const pulseGlobal = ref(false);
const isLive = ref(false);

watch(() => props.upcomingStats, (newStats) => {
    upcomingData.value = [...newStats];
}, { deep: true });

const trendChartData = computed(() => ({
    labels: props.registrationTrends.map(t => t.date),
    datasets: [{
        label: 'Daily Registrations',
        backgroundColor: 'rgba(99, 102, 241, 0.2)',
        borderColor: '#6366f1',
        data: props.registrationTrends.map(t => t.count),
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#6366f1',
        pointBorderWidth: 2,
    }]
}));

const statusChartData = computed(() => ({
    labels: ['Confirmed', 'Waitlisted'],
    datasets: [{
        backgroundColor: ['#6366f1', '#fbbf24'],
        data: [props.statusDistribution.confirmed, props.statusDistribution.waitlisted],
        borderWidth: 0,
        hoverOffset: 4
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: { beginAtZero: true, grid: { display: false } },
        x: { grid: { display: false } }
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString([], { month: 'short', day: 'numeric' });
};

onMounted(() => {
    if (window.Echo) {
        isLive.value = window.Echo.connector.pusher.connection.state === 'connected';
        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
            isLive.value = states.current === 'connected';
        });

        // Listen to global stats channel for dashboard-wide updates
        window.Echo.private('admin.stats')
            .listen('.registration.updated', (data) => {
                // 1. Update the local upcoming data for immediate feedback
                const index = upcomingData.value.findIndex(w => w.id === data.id);
                if (index !== -1) {
                    upcomingData.value[index].confirmed_count = data.confirmed_count;
                    upcomingData.value[index].waitlist_count = data.waitlist_count;
                    upcomingData.value[index].fill_percentage = data.capacity > 0
                        ? Math.round((data.confirmed_count / data.capacity) * 100)
                        : 0;

                    // Trigger pulse animation
                    pulseStates.value[data.id] = true;
                    setTimeout(() => pulseStates.value[data.id] = false, 1000);
                }

                // Global pulse for stats cards
                pulseGlobal.value = true;
                setTimeout(() => pulseGlobal.value = false, 1000);

                // 2. Refresh the global stats from the server to ensure accuracy
                router.reload({
                    only: [
                        'totalRegistrations',
                        'totalWaitlist',
                        'capacityAlerts',
                        'mostPopular',
                        'registrationTrends',
                        'statusDistribution',
                        'upcomingStats',
                        'pastStats'
                    ],
                    preserveScroll: true,
                    preserveState: true
                });
            });
    }
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave('admin.stats');
    }
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Header Section -->
            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Executive Dashboard</h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Real-time overview of the InterCourse Academy performance.</p>
                </div>
                <div class="flex items-center space-x-3 px-4 py-2 bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="isLive ? 'bg-green-400' : 'bg-red-400'"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" :class="isLive ? 'bg-green-500' : 'bg-red-500'"></span>
                    </div>
                    <span class="text-sm font-semibold tracking-wide" :class="isLive ? 'text-green-700' : 'text-red-700'">
                        {{ isLive ? 'LIVE SYSTEM' : 'OFFLINE' }}
                    </span>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group"
                    :class="{ 'ring-2 ring-indigo-500 scale-[1.02] shadow-indigo-100': pulseGlobal }"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-indigo-50 rounded-2xl group-hover:bg-indigo-100 transition-colors">
                            <UserCheck class="h-6 w-6 text-indigo-600" />
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">+12%</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Confirmations</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1 transition-all" :class="{ 'scale-110 text-indigo-600': pulseGlobal }">{{ totalRegistrations }}</h3>
                </div>

                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group"
                    :class="{ 'ring-2 ring-amber-500 scale-[1.02] shadow-amber-100': pulseGlobal }"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-50 rounded-2xl group-hover:bg-amber-100 transition-colors">
                            <Clock class="h-6 w-6 text-amber-600" />
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Active</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Waitlist Demand</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1 transition-all" :class="{ 'scale-110 text-amber-600': pulseGlobal }">{{ totalWaitlist }}</h3>
                </div>

                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group"
                    :class="{ 'ring-2 ring-red-500 scale-[1.02] shadow-red-100': pulseGlobal }"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-50 rounded-2xl group-hover:bg-red-100 transition-colors">
                            <AlertCircle class="h-6 w-6 text-red-600" />
                        </div>
                        <span v-if="capacityAlerts > 0" class="text-xs font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">Action Needed</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Capacity Alerts</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1 transition-all" :class="{ 'scale-110 text-red-600': pulseGlobal }">{{ capacityAlerts }}</h3>
                </div>

                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group"
                    :class="{ 'ring-2 ring-emerald-500 scale-[1.02] shadow-emerald-100': pulseGlobal }"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-50 rounded-2xl group-hover:bg-emerald-100 transition-colors">
                            <TrendingUp class="h-6 w-6 text-emerald-600" />
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Top</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Popular Workshop</p>
                    <h3 class="text-lg font-bold text-gray-900 mt-1 truncate transition-all" :class="{ 'scale-110 text-emerald-600': pulseGlobal }" :title="mostPopular[0]?.title">{{ mostPopular[0]?.title || 'N/A' }}</h3>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Registration Trends</h3>
                            <p class="text-sm text-gray-500 mt-1 font-medium">Weekly engagement overview</p>
                        </div>
                        <div class="flex items-center space-x-2 text-indigo-600 font-bold text-sm bg-indigo-50 px-3 py-1.5 rounded-xl">
                            <Activity class="h-4 w-4" />
                            <span>Last 7 Days</span>
                        </div>
                    </div>
                    <div class="h-64">
                        <Line :data="trendChartData" :options="chartOptions" />
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-8">Status Distribution</h3>
                    <div class="h-48 relative">
                        <Doughnut :data="statusChartData" :options="{ ...chartOptions, cutout: '75%' }" />
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-3xl font-black text-gray-900">{{ totalRegistrations + totalWaitlist }}</span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total</span>
                        </div>
                    </div>
                    <div class="mt-8 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                            <div class="flex items-center space-x-3">
                                <div class="h-3 w-3 rounded-full bg-indigo-600"></div>
                                <span class="text-sm font-bold text-gray-700">Confirmed</span>
                            </div>
                            <span class="text-sm font-black text-gray-900">{{ totalRegistrations }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                            <div class="flex items-center space-x-3">
                                <div class="h-3 w-3 rounded-full bg-amber-400"></div>
                                <span class="text-sm font-bold text-gray-700">Waitlisted</span>
                            </div>
                            <span class="text-sm font-black text-gray-900">{{ totalWaitlist }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Workshops Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Live Workshop Monitoring</h3>
                        <p class="text-sm text-gray-500 mt-1 font-medium">Real-time capacity tracking for upcoming events</p>
                    </div>
                    <Link :href="route('admin.workshops.index')" class="flex items-center space-x-1 text-sm font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                        <span>Manage All</span>
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                <th class="px-8 py-5">Workshop</th>
                                <th class="px-8 py-5">Schedule</th>
                                <th class="px-8 py-5">Engagement</th>
                                <th class="px-8 py-5">Fill Rate</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="workshop in upcomingData" :key="workshop.id" class="group hover:bg-gray-50/80 transition-all duration-200">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ workshop.title }}</div>
                                    <div class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-wider">ID: #{{ workshop.id }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center space-x-2 text-sm text-gray-600 font-medium">
                                        <Calendar class="h-4 w-4 text-gray-400" />
                                        <span>{{ formatDate(workshop.starts_at) }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-black text-gray-900" :class="{ 'animate-pulse text-indigo-600': pulseStates[workshop.id] }">
                                                {{ workshop.confirmed_count }}
                                            </div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Confirmed</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm font-black text-gray-600">{{ workshop.waitlist_count }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Waitlist</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="w-32">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[10px] font-black" :class="workshop.fill_percentage > 90 ? 'text-red-600' : 'text-indigo-600'">{{ workshop.fill_percentage }}%</span>
                                        </div>
                                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div
                                                class="h-full rounded-full transition-all duration-1000 ease-out"
                                                :class="workshop.fill_percentage > 90 ? 'bg-red-500' : 'bg-indigo-500'"
                                                :style="{ width: workshop.fill_percentage + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <Link :href="route('admin.workshops.show', workshop.id)" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                        <ChevronRight class="h-5 w-5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
