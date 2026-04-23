<script setup>
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Calendar, 
    Clock, 
    ChevronRight, 
    CheckCircle2, 
    AlertCircle,
    ArrowRight,
    Activity,
    Trash2,
    CalendarCheck
} from 'lucide-vue-next';

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
    return new Date(dateString).toLocaleDateString([], {
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

    <EmployeeLayout>
        <template #header>
            <div class="py-4">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">My Schedule</h2>
                <p class="text-gray-500 font-medium mt-1">Track your upcoming professional development sessions.</p>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Flash Message -->
            <div v-if="$page.props.flash?.message" class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center text-emerald-700 font-bold text-sm animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="bg-emerald-100 p-1 rounded-lg mr-3">
                    <CheckCircle2 class="h-4 w-4" />
                </div>
                {{ $page.props.flash.message }}
            </div>

            <!-- Schedule Table -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 bg-gray-50/50">
                                <th class="px-8 py-5">Workshop</th>
                                <th class="px-8 py-5">Schedule</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="reg in registrations" :key="reg.id" class="group hover:bg-gray-50/80 transition-all duration-200">
                                <td class="px-8 py-6">
                                    <div class="font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ reg.title }}</div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Duration: {{ reg.duration_minutes }} Mins</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center text-sm font-bold text-gray-600">
                                        <Calendar class="h-4 w-4 mr-2 text-gray-300" />
                                        {{ formatDate(reg.starts_at) }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span 
                                        :class="[
                                            reg.status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100',
                                            'px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border shadow-sm'
                                        ]"
                                    >
                                        {{ reg.status === 'confirmed' ? 'Confirmed' : 'Waitlisted #' + reg.position }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <Link :href="route('workshops.show', reg.id)" class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">
                                            Details
                                        </Link>
                                        <button 
                                            @click="cancel(reg.id)"
                                            class="p-2 text-gray-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all"
                                            title="Cancel Registration"
                                        >
                                            <Trash2 class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="registrations.length === 0">
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <div class="bg-gray-50 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <CalendarCheck class="h-10 w-10 text-gray-300" />
                                        </div>
                                        <h3 class="text-xl font-black text-gray-900">Your schedule is empty</h3>
                                        <p class="text-gray-400 font-medium mt-2 mb-8">You haven't registered for any workshops yet.</p>
                                        <Link 
                                            :href="route('dashboard')"
                                            class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"
                                        >
                                            Browse Workshops
                                            <ArrowRight class="ml-2 h-5 w-5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>
