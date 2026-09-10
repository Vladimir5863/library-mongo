<script setup>
const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    error: {
        type: [String, Object],
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="flex flex-col gap-1">
        <label
            v-if="label"
            class="text-sm font-medium text-gray-700"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <slot />

        <p v-if="error" class="text-xs text-red-500">
            {{ typeof error === "string" ? error : error?.message }}
        </p>
    </div>
</template>

