<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    form: Object,
    isEdit: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="mt-6 space-y-6 max-w-xl">
        <div>
            <InputLabel for="title" value="Title" />
            <TextInput
                id="title"
                type="text"
                class="mt-1 block w-full"
                v-model="form.title"
                required
                autofocus
            />
            <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div>
            <InputLabel for="description" value="Description" />
            <TextArea
                id="description"
                class="mt-1 block w-full"
                v-model="form.description"
                rows="4"
            />
            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel for="starts_at" value="Starts At" />
                <input
                    id="starts_at"
                    type="datetime-local"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.starts_at"
                    required
                />
                <InputError class="mt-2" :message="form.errors.starts_at" />
            </div>

            <div>
                <InputLabel for="duration_minutes" value="Duration (minutes)" />
                <TextInput
                    id="duration_minutes"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.duration_minutes"
                    required
                />
                <InputError class="mt-2" :message="form.errors.duration_minutes" />
            </div>
        </div>

        <div>
            <InputLabel for="capacity" value="Capacity" />
            <TextInput
                id="capacity"
                type="number"
                class="mt-1 block w-full"
                v-model="form.capacity"
                required
            />
            <InputError class="mt-2" :message="form.errors.capacity" />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">
                {{ isEdit ? 'Update Workshop' : 'Create Workshop' }}
            </PrimaryButton>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
            </Transition>
        </div>
    </form>
</template>
