<template>
    <div
        :class="
            variant === 'compact'
                ? 'bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-3 hover:shadow-md transition-shadow duration-200'
                : 'bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-200'
        "
    >
        <template v-if="variant === 'compact'">
            <!-- Slika -->
            <div>
                <img
                    v-if="item.multimedia"
                    :src="'data:image/png;base64,' + item.multimedia"
                    class="w-full h-40 object-cover rounded-xl"
                />
                <div
                    v-else
                    class="w-full h-40 bg-gray-100 rounded-xl flex items-center justify-center"
                >
                    <span class="text-4xl">📰</span>
                </div>
            </div>

            <span class="text-xs text-gray-400">{{ item.startDate }}</span>

            <h3 class="font-semibold text-gray-800 line-clamp-2">
                <Link
                    :href="route('news.show', item.newsId)"
                    class="hover:text-emerald-700 transition-colors"
                >
                    {{ item.title }}
                </Link>
            </h3>

            <p class="text-sm text-gray-500 line-clamp-2 flex-1">
                {{ item.text }}
            </p>
        </template>

        <template v-else>
            <!-- Naslovna slika -->
            <div class="relative">
                <img
                    v-if="item.multimedia"
                    :src="'data:image/png;base64,' + item.multimedia"
                    class="w-full h-40 object-cover"
                />
                <div
                    v-else
                    class="w-full h-40 bg-gray-100 flex items-center justify-center"
                >
                    <span class="text-4xl">📰</span>
                </div>

                <!-- Tip badge -->
                <span
                    class="absolute top-2 right-2 text-xs px-2 py-1 rounded-full font-medium"
                    :class="{
                        'bg-blue-100 text-blue-700': item.type === 'announcement',
                        'bg-purple-100 text-purple-700': item.type === 'event',
                        'bg-orange-100 text-orange-700': item.type === 'promotion',
                    }"
                >
                    {{ typeLabels[item.type] }}
                </span>
            </div>

            <!-- Info -->
            <div class="p-4 flex flex-col gap-2 flex-1">
                <!-- Logo i naslov -->
                <div class="flex items-center gap-3">
                    <img
                        v-if="item.logo"
                        :src="'data:image/png;base64,' + item.logo"
                        class="w-10 h-10 rounded-full object-cover flex-shrink-0 border-2 border-gray-100"
                    />
                    <span
                        v-else
                        class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xl flex-shrink-0"
                        >📰</span
                    >
                    <h3 class="text-sm font-bold text-gray-800 line-clamp-2">
                        {{ item.title }}
                    </h3>
                </div>

                <p class="text-xs text-gray-400">
                    {{ item.startDate }} — {{ item.endDate }}
                </p>
                <p class="text-xs text-gray-500 line-clamp-3 flex-1">
                    {{ item.text }}
                </p>
            </div>

            <!-- Footer -->
            <div
                class="px-4 pb-4 pt-4 flex items-center justify-between border-t border-gray-100"
            >
                <Link
                    :href="route('news.show', item.newsId)"
                    class="text-xs text-emerald-700 hover:underline font-medium"
                >
                    Čitaj više →
                </Link>
                <button
                    v-if="canDelete && showDelete"
                    @click="$emit('delete', item.newsId)"
                    class="text-red-400 hover:text-red-600 text-xs transition-colors"
                >
                    🗑️ Obriši
                </button>
            </div>
        </template>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";

defineProps({
    item: Object,
    variant: {
        type: String,
        default: "default",
    },
    showDelete: {
        type: Boolean,
        default: true,
    },
});

defineEmits(["delete"]);

const page = usePage();

const canDelete =
    page.props.auth.user?.userType === "librarian" ||
    page.props.auth.user?.userType === "admin";

const typeLabels = {
    announcement: "Obaveštenje",
    event: "Događaj",
    promotion: "Promocija",
};
</script>
