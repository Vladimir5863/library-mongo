<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-4xl mx-auto flex flex-col gap-8">
            <!-- Nazad -->
            <Link
                :href="route('news.index')"
                class="inline-flex items-center gap-2 bg-emerald-700 text-white text-sm px-4 py-2 rounded-full w-fit hover:bg-emerald-600 transition-colors"
            >
                ← Nazad
            </Link>

            <!-- Naslovna slika -->
            <div class="relative rounded-2xl overflow-hidden">
                <img
                    v-if="item.multimedia"
                    :src="'data:image/png;base64,' + item.multimedia"
                    class="w-full h-72 object-cover"
                />
                <div
                    v-else
                    class="w-full h-72 bg-gray-200 flex items-center justify-center"
                >
                    <span class="text-6xl">📰</span>
                </div>

                <!-- Tip badge -->
                <span
                    class="absolute top-4 right-4 text-xs px-3 py-1.5 rounded-full font-medium"
                    :class="{
                        'bg-blue-100 text-blue-700':
                            item.type === 'announcement',
                        'bg-purple-100 text-purple-700': item.type === 'event',
                        'bg-orange-100 text-orange-700':
                            item.type === 'promotion',
                    }"
                >
                    {{ typeLabels[item.type] }}
                </span>
            </div>

            <!-- Glavni sadržaj -->
            <div class="bg-white rounded-2xl shadow-sm p-8 flex flex-col gap-6">
                <!-- Logo i naslov -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center gap-4"
                >
                    <img
                        v-if="item.logo"
                        :src="'data:image/png;base64,' + item.logo"
                        class="w-16 h-16 rounded-full object-cover border-2 border-gray-100 flex-shrink-0"
                    />
                    <span
                        v-else
                        class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl flex-shrink-0"
                        >📰</span
                    >
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ item.title }}
                        </h1>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ item.startDate }} — {{ item.endDate }}
                        </p>
                    </div>
                </div>

                <hr class="border-gray-100" />

                <!-- Tekst -->
                <div
                    class="text-gray-600 leading-relaxed text-sm whitespace-pre-line"
                >
                    {{ item.text }}
                </div>

                <!-- Brisanje -->
                <div
                    v-if="canDelete"
                    class="flex justify-end pt-4 border-t border-gray-100"
                >
                    <button
                        @click="deleteItem"
                        class="flex items-center gap-2 text-sm text-red-500 hover:text-red-400 transition-colors"
                    >
                        🗑️ Obriši vest
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    item: Object,
});

const page = usePage();

const canDelete =
    page.props.auth.user?.userType === "librarian" ||
    page.props.auth.user?.userType === "admin";

const typeLabels = {
    announcement: "Obaveštenje",
    event: "Događaj",
    promotion: "Promocija",
};

const deleteForm = useForm({});
const deleteItem = () => {
    if (confirm("Da li ste sigurni da želite da obrišete ovu vest?")) {
        deleteForm.delete(route("news.destroy", props.item.newsId), {
            onSuccess: () => {},
        });
    }
};
</script>
