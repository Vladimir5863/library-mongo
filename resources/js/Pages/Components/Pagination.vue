<template>
    <div class="flex items-center justify-center mt-6">
        <Link
            v-for="link in data.links"
            :key="link.label"
            v-html="link.label"
            :href="link.url ?? '#'"
            class="px-3 py-1.5 mx-0.5 text-sm rounded-lg transition-colors"
            :class="{
                'text-gray-300 cursor-default pointer-events-none': !link.url,
                'bg-emerald-700 text-white': link.active,
                'bg-white text-gray-600 hover:bg-emerald-50 border border-gray-200':
                    !link.active && link.url,
            }"
        />
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    data: Object,
});
</script>
