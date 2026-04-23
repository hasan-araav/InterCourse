<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { usePolling } from '@/Composables/usePolling';

const props = defineProps({
    workshops: Array,
});

const workshopsData = ref([...props.workshops]);
const pulseStates = ref({});
let echoChannels = [];

// Watch for prop changes and update local state
import { watch } from 'vue';
watch(() => props.workshops, (newWorkshops) => {
    workshopsData.value = [...newWorkshops];
}, { deep: true });

// Start polling as a fallback if WebSockets are not active
usePolling(10000);

onMounted(() => {
    if (window.Echo) {
        console.log('Setting up Echo listeners...');
        workshopsData.value.forEach((workshop) => {
            window.Echo.channel('workshops.' + workshop.id)
                .listen('.registration.updated', (data) => {
                    console.log('Received update for workshop:', data.id);
                    const index = workshopsData.value.findIndex(w => w.id === data.id);
                    if (index !== -1) {
                        workshopsData.value[index].remaining_seats = data.capacity - data.confirmed_count;
                        workshopsData.value[index].confirmed_count = data.confirmed_count;
                        pulseStates.value[workshop.id] = true;
                        setTimeout(() => {
                            pulseStates.value[workshop.id] = false;
                        }, 1000);
                    }
                });

            echoChannels.push('workshops.' + workshop.id);
        });
    } else {
        console.warn('Echo not found. Realtime updates disabled.');
    }
});

onUnmounted(() => {
    echoChannels.forEach((channelName) => {
        window.Echo.leave(channelName);
    });
    echoChannels = [];
});

const register = (workshopId) => {
    router.post(route('workshops.register', workshopId), {}, {
        preserveScroll: true,
    });
};

const cancel = (workshopId) => {
    if (confirm('Are you sure you want to cancel your registration?')) {
        router.delete(route('workshops.cancel', workshopId), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString([], {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div v-if="$page.props.flash?.message" class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                {{ $page.props.flash.message }}
            </div>

            <div v-if="Object.keys($page.props.errors).length > 0" class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm">
                <ul>
                    <li v-for="error in $page.props.errors" :key="error">{{ error }}</li>
                </ul>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="workshop in workshopsData" :key="workshop.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ workshop.title }}</h3>
                            <span
                                :class="{
                                    'bg-green-100 text-green-800': workshop.user_registration?.status === 'confirmed',
                                    'bg-yellow-100 text-yellow-800': workshop.user_registration?.status === 'waitlisted',
                                    'bg-gray-100 text-gray-800': !workshop.user_registration
                                }"
                                class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                            >
                                {{ workshop.user_registration ? (workshop.user_registration.status === 'confirmed' ? 'Registered' : 'Waitlisted (#' + workshop.user_registration.position + ')') : 'Not Joined' }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                            {{ workshop.description }}
                        </p>

                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ formatDate(workshop.starts_at) }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ workshop.duration_minutes }} minutes
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span
                                    :class="{ 'counter-pulse': pulseStates[workshop.id] }"
                                    class="transition-all duration-300"
                                >
                                    {{ workshop.remaining_seats }} / {{ workshop.capacity }} seats remaining
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100 mt-auto">
                        <div v-if="workshop.user_registration">
                            <DangerButton class="w-full justify-center" @click="cancel(workshop.id)">
                                Cancel Registration
                            </DangerButton>
                        </div>
                        <div v-else>
                            <PrimaryButton v-if="workshop.remaining_seats > 0" class="w-full justify-center" @click="register(workshop.id)">
                                Register
                            </PrimaryButton>
                            <SecondaryButton v-else class="w-full justify-center" @click="register(workshop.id)">
                                Join Waitlist
                            </SecondaryButton>
                        </div>
                    </div>
                </div>

                <div v-if="workshopsData.length === 0" class="col-span-full bg-white p-12 text-center rounded-lg shadow-sm">
                    <p class="text-gray-500">No upcoming workshops found.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.counter-pulse {
    animation: pulse 0.6s ease-in-out;
    color: #4f46e5;
    font-weight: bold;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}
</style>
