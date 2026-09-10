<script setup>
defineProps({
    icon: {
        type: String,
        default: "📭",
    },
    title: {
        type: String,
        default: "",
    },
    description: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center py-24 text-gray-400">
        <span class="text-5xl mb-4">{{ icon }}</span>
        <p class="text-lg font-medium">{{ title }}</p>
        <p v-if="description" class="text-sm mt-2 text-gray-500">
            {{ description }}
        </p>
    </div>
</template>

