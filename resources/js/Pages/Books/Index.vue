<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8">
            <!-- Filteri sa strane -->
            <aside class="w-full md:w-64 md:flex-shrink-0">
                <div
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-6 md:sticky top-6"
                >
                    <h2 class="text-lg font-semibold text-gray-800">Filteri</h2>

                    <!-- Pretraga -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700"
                            >Pretraga</label
                        >
                        <div
                            class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2"
                        >
                            <span class="text-gray-400">🔍</span>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Naziv, autor..."
                                class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                            />
                        </div>
                    </div>

                    <!-- Žanr -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700"
                            >Žanr</label
                        >
                        <div class="flex flex-col gap-1">
                            <label
                                v-for="genre in genres"
                                :key="genre"
                                class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-emerald-700"
                            >
                                <input
                                    type="checkbox"
                                    :value="genre"
                                    v-model="filters.genres"
                                    class="accent-emerald-700"
                                />
                                {{ genre }}
                            </label>
                        </div>
                    </div>

                    <!-- Dostupnost -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700"
                            >Dostupnost</label
                        >
                        <div class="flex flex-col gap-1">
                            <label
                                class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-emerald-700"
                            >
                                <input
                                    type="checkbox"
                                    v-model="filters.availableForLoan"
                                    class="accent-emerald-700"
                                />
                                Dostupno za iznajmljivanje
                            </label>
                            <label
                                class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-emerald-700"
                            >
                                <input
                                    type="checkbox"
                                    v-model="filters.availableForSell"
                                    class="accent-emerald-700"
                                />
                                Dostupno za kupovinu
                            </label>
                        </div>
                    </div>

                    <!-- Jezik -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700"
                            >Jezik</label
                        >
                        <select
                            v-model="filters.language"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none"
                        >
                            <option value="">Svi jezici</option>
                            <option
                                v-for="lang in languages"
                                :key="lang"
                                :value="lang"
                            >
                                {{ lang }}
                            </option>
                        </select>
                    </div>

                    <!-- Reset -->
                    <button
                        @click="resetFilters"
                        class="w-full text-sm text-gray-500 hover:text-red-500 transition-colors underline"
                    >
                        Resetuj filtere
                    </button>
                </div>
            </aside>

            <!-- Knjige -->
            <div class="flex-1">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-serif font-normal text-gray-800">
                        Prikaz {{ books.from }} do {{ books.to }} knjiga
                    </h1>
                    <select
                        v-model="sortBy"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none bg-white"
                    >
                        <option value="title">Po nazivu</option>
                        <option value="author">Po autoru</option>
                        <option value="newest">Najnovije</option>
                    </select>
                </div>

                <!-- Grid -->
                <div
                    v-if="filteredBooks.length > 0"
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                    <Book
                        v-for="book in filteredBooks"
                        :key="book.bookId"
                        :book="book"
                    />
                </div>

                <!-- Prazno -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center py-24 text-gray-400"
                >
                    <span class="text-5xl mb-4">📭</span>
                    <p class="text-lg font-medium">
                        Nema knjiga za zadane filtere
                    </p>
                    <button
                        @click="resetFilters"
                        class="mt-3 text-sm text-emerald-700 hover:underline"
                    >
                        Resetuj filtere
                    </button>
                </div>
            </div>
        </div>
        <Pagination :data="books" />
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import Book from "@/Pages/Components/Book.vue";
import Pagination from "@/Pages/Components/Pagination.vue";

const props = defineProps({
    books: Object,
});

const filters = ref({
    search: "",
    genres: [],
    availableForLoan: false,
    availableForSell: false,
    language: "",
});

const sortBy = ref("title");

const genres = computed(() => [
    ...new Set(props.books.data.map((b) => b.genre)),
]);
const languages = computed(() => [
    ...new Set(props.books.data.map((b) => b.language)),
]);

const filteredBooks = computed(() => {
    let result = [...props.books.data];

    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(
            (b) =>
                b.title.toLowerCase().includes(q) ||
                b.author.toLowerCase().includes(q),
        );
    }

    if (filters.value.genres.length > 0) {
        result = result.filter((b) => filters.value.genres.includes(b.genre));
    }

    if (filters.value.availableForLoan) {
        result = result.filter((b) => b.remainingForLoan > 0);
    }

    if (filters.value.availableForSell) {
        result = result.filter((b) => b.remainingForSell > 0);
    }

    if (filters.value.language) {
        result = result.filter((b) => b.language === filters.value.language);
    }

    if (sortBy.value === "title") {
        result.sort((a, b) => a.title.localeCompare(b.title));
    } else if (sortBy.value === "author") {
        result.sort((a, b) => a.author.localeCompare(b.author));
    } else if (sortBy.value === "newest") {
        result.sort(
            (a, b) => new Date(b.publicationDate) - new Date(a.publicationDate),
        );
    }

    return result;
});

const resetFilters = () => {
    filters.value = {
        search: "",
        genres: [],
        availableForLoan: false,
        availableForSell: false,
        language: "",
    };
};
</script>
