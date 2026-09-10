<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
            <!-- Filteri -->
            <aside class="w-full md:w-56 md:flex-shrink-0">
                <div
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-4 md:sticky top-6"
                >
                    <h2 class="text-lg font-semibold text-gray-800">Filteri</h2>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Pretraga</label
                        >
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Naziv..."
                            class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none"
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Tip</label
                        >
                        <div class="flex flex-col gap-1">
                            <label
                                v-for="type in types"
                                :key="type.value"
                                class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :value="type.value"
                                    v-model="filters.types"
                                    class="accent-emerald-700"
                                />
                                {{ type.label }}
                            </label>
                        </div>
                    </div>

                    <button
                        @click="resetFilters"
                        class="text-sm text-gray-400 hover:text-red-500 underline transition-colors"
                    >
                        Resetuj
                    </button>
                </div>
            </aside>

            <!-- Vesti -->
            <div class="flex-1">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-serif font-semibold text-gray-800">
                        Vesti
                        <span class="text-base font-normal text-gray-400"
                            >({{ filteredNews.length }})</span
                        >
                    </h1>
                    <Link
                        v-if="
                            page.props.auth.user?.userType === 'librarian' ||
                            page.props.auth.user?.userType === 'admin'
                        "
                        :href="route('news.create')"
                        class="bg-emerald-700 hover:bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                    >
                        + Nova vest
                    </Link>
                </div>

                <div
                    v-if="filteredNews.length === 0"
                    class="flex flex-col items-center justify-center py-24 text-gray-400"
                >
                    <span class="text-5xl mb-4">📭</span>
                    <p>Nema vesti</p>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <NewsCard
                        v-for="item in filteredNews"
                        :key="item.newsId"
                        :item="item"
                        @delete="deleteNews"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import NewsCard from "@/Pages/Components/News.vue";

const page = usePage();

const props = defineProps({
    news: {
        type: Array,
        default: () => [],
    },
});

const types = [
    { value: "announcement", label: "Obaveštenje" },
    { value: "event", label: "Događaj" },
    { value: "promotion", label: "Promocija" },
];

const filters = ref({
    search: "",
    types: [],
});

const filteredNews = computed(() => {
    let result = [...props.news];

    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter((n) => n.title.toLowerCase().includes(q));
    }

    if (filters.value.types.length > 0) {
        result = result.filter((n) => filters.value.types.includes(n.type));
    }

    // Sortiranje po datumu
    result.sort((a, b) => new Date(b.startDate) - new Date(a.startDate));

    return result;
});

const resetFilters = () => {
    filters.value = { search: "", types: [] };
};

const deleteForm = useForm({});
const deleteNews = (id) => {
    if (confirm("Da li ste sigurni?")) {
        deleteForm.delete(route("news.destroy", id));
    }
};
</script>
