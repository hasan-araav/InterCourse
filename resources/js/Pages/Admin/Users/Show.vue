<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Mail,
    Calendar,
    Clock,
    UserCheck,
    AlertCircle,
    TrendingUp,
    Activity,
    CheckCircle2,
    XCircle
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    registrations: Array,
    metrics: Object,
});

const getStatusClass = (status) => {
    switch (status) {
        case 'confirmed': return 'bg-emerald-50 text-emerald-700 border-emerald-100';
        case 'waitlisted': return 'bg-amber-50 text-amber-700 border-amber-100';
        case 'cancelled': return 'bg-gray-50 text-gray-700 border-gray-100';
        default: return 'bg-gray-50 text-gray-700 border-gray-100';
    }
};
</script>

<template>
    <Head :title="`${user.name}'s Profile`" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Header & Back -->
            <div class="flex items-center justify-between">
                <Link :href="route('admin.users.index')" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors group">
                    <ChevronLeft class="h-4 w-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                    Back to Directory
                </Link>
                <div class="text-xs font-black text-gray-400 uppercase tracking-widest">
                    Joined: {{ user.created_at }}
                </div>
            </div>

            <!-- Profile Summary Card -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    <img :src="user.photo" class="h-32 w-32 rounded-3xl shadow-lg ring-4 ring-gray-50 object-cover" />
                    <div class="flex-1 space-y-4 text-center md:text-left">
                        <div class="space-y-1">
                            <h1 class="text-4xl font-black text-gray-900 tracking-tight">{{ user.name }}</h1>
                            <div class="flex flex-wrap justify-center md:justify-start gap-4 items-center mt-2">
                                <div class="flex items-center text-gray-500 font-bold">
                                    <Mail class="h-4 w-4 mr-2" />
                                    {{ user.email }}
                                </div>
                                <span
                                    :class="[
                                        user.role === 'admin' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border'
                                    ]"
                                >
                                    {{ user.role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-indigo-50 rounded-2xl">
                            <Activity class="h-6 w-6 text-indigo-600" />
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Registrations</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1">{{ metrics.total_registrations }}</h3>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-50 rounded-2xl">
                            <CheckCircle2 class="h-6 w-6 text-emerald-600" />
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Confirmed Spots</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1 text-emerald-600">{{ metrics.confirmed_workshops }}</h3>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-50 rounded-2xl">
                            <Clock class="h-6 w-6 text-amber-600" />
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Waitlisted</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1 text-amber-600">{{ metrics.waitlisted_workshops }}</h3>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-rose-50 rounded-2xl">
                            <TrendingUp class="h-6 w-6 text-rose-600" />
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Attendance Rate</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-1">{{ metrics.attendance_rate }}%</h3>
                </div>
            </div>

            <!-- Workshop History Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-xl font-black text-gray-900">Workshop History</h3>
                    <p class="text-sm text-gray-500 font-medium mt-1">Detailed record of registrations and attendance status.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                <th class="px-8 py-5">Workshop</th>
                                <th class="px-8 py-5">Schedule</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5">Position</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="reg in registrations" :key="reg.id" class="group hover:bg-gray-50/80 transition-all">
                                <td class="px-8 py-6">
                                    <Link :href="route('admin.workshops.show', reg.id)" class="font-black text-gray-900 hover:text-indigo-600 transition-colors">
                                        {{ reg.title }}
                                    </Link>
                                    <div v-if="reg.is_past" class="inline-flex ml-2 px-2 py-0.5 bg-gray-100 text-gray-400 text-[10px] font-black uppercase rounded">Past</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center text-sm font-bold text-gray-600">
                                        <Calendar class="h-4 w-4 mr-2 text-gray-300" />
                                        {{ reg.starts_at }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        :class="[
                                            getStatusClass(reg.status),
                                            'px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border'
                                        ]"
                                    >
                                        {{ reg.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div v-if="reg.status === 'waitlisted'" class="text-sm font-black text-amber-600">
                                        #{{ reg.position }}
                                    </div>
                                    <div v-else class="text-sm text-gray-300">N/A</div>
                                </td>
                            </tr>
                            <tr v-if="registrations.length === 0">
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <Activity class="h-12 w-12 text-gray-200 mx-auto mb-4" />
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No registration history found</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
