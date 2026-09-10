<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
// Uklonjen Pagination ako ga ne koristiš za ovu listu, jer je spajana kolekcija
// ali ga možeš zadržati ako si implementirao custom logiku.

defineProps({
    alerts: Array, // Prima niz [{ book: {...}, type: '...' }]
});

const stockModal = ref(false);
const selectedBook = ref(null);
const alertType = ref(""); // Čuvamo tip da znamo šta ažuriramo

const stockForm = useForm({
    addForLoan: 0,
    addForSell: 0,
    remainingForLoan: 0,
    remainingForSell: 0,
});

const openStockModal = (item) => {
    // 'item' je ceo objekat koji sadrži 'book' i 'type'
    selectedBook.value = item.book;
    alertType.value = item.type;

    // Dodavanje novih jedinica, uvek počinje od 0
    stockForm.addForLoan = 0;
    stockForm.addForSell = 0;
    stockModal.value = true;
};

const submitStock = () => {
    const bookId = selectedBook.value.id || selectedBook.value.bookId;

    const currentLoan = Number(selectedBook.value.remainingForLoan ?? 0);
    const currentSell = Number(selectedBook.value.remainingForSell ?? 0);
    const addLoan = Number(stockForm.addForLoan ?? 0);
    const addSell = Number(stockForm.addForSell ?? 0);

    const updatedLoan = Math.max(0, currentLoan + addLoan);
    const updatedSell = Math.max(0, currentSell + addSell);

    stockForm.remainingForLoan = updatedLoan;
    stockForm.remainingForSell = updatedSell;

    console.log("updateStock", bookId, updatedLoan, updatedSell);

    stockForm.patch(route("admin.books.stock", bookId), {
        data: {
            remainingForLoan: updatedLoan,
            remainingForSell: updatedSell,
        },
        onSuccess: () => {
            stockModal.value = false;
            stockForm.reset();
        },
        onError: (errors) => {
            console.error("updateStock error", errors);
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="p-6 bg-emerald-600">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">
                Upozorenja o zalihama
            </h2>
            <Link
                :href="route('admin.index')"
                class="bg-white text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-100 text-sm"
            >
                Nazad
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4 border-b">Knjiga</th>
                        <th class="p-4 border-b">Tip problema</th>
                        <th class="p-4 border-b">Zaliha za iznajmljivanje</th>
                        <th class="p-4 border-b">Zaliha za prodaju</th>
                        <th class="p-4 border-b">Akcija</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, index) in alerts"
                        :key="index"
                        class="hover:bg-gray-50"
                    >
                        <td class="p-4 border-b font-medium">
                            {{ item.book.title }}
                        </td>
                        <td class="p-4 border-b">
                            <span
                                :class="
                                    item.type === 'sell'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-blue-100 text-blue-700'
                                "
                                class="px-2 py-1 rounded text-xs uppercase font-bold"
                            >
                                {{ item.type }}
                            </span>
                        </td>
                        <td class="p-4 border-b text-sm text-gray-600">
                            {{ item.book.remainingForLoan ?? 0 }}
                        </td>
                        <td class="p-4 border-b text-sm text-gray-600">
                            {{ item.book.remainingForSell ?? 0 }}
                        </td>
                        <td class="p-4 border-b">
                            <button
                                @click="openStockModal(item)"
                                class="bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-emerald-500"
                            >
                                Ažuriraj zalihe
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="stockModal"
            class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
        >
            <div
                class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md flex flex-col gap-4"
            >
                <h3 class="text-lg font-semibold text-gray-800">
                    Ažuriraj zalihe — {{ selectedBook?.title }}
                </h3>

                <p class="text-xs text-gray-500 -mt-2">
                    Kritično u kategoriji:
                    <span class="font-bold uppercase">{{ alertType }}</span>
                </p>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700"
                        >Za iznajmljivanje</label
                    >
                    <div class="flex items-center gap-2">
                        <button
                            @click="
                                stockForm.addForLoan = Math.max(
                                    0,
                                    Number(stockForm.addForLoan || 0) - 1,
                                )
                            "
                            :disabled="stockForm.addForLoan <= 0"
                            class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                        >
                            -
                        </button>
                        <input
                            v-model.number="stockForm.addForLoan"
                            type="number"
                            min="0"
                            class="flex-1 border border-gray-200 rounded-lg px-4 py-2 text-sm text-center"
                        />
                        <button
                            @click="stockForm.addForLoan++"
                            class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                        >
                            +
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700"
                        >Za kupovinu</label
                    >
                    <div class="flex items-center gap-2">
                        <button
                            @click="
                                stockForm.addForSell = Math.max(
                                    0,
                                    Number(stockForm.addForSell || 0) - 1,
                                )
                            "
                            :disabled="stockForm.addForSell <= 0"
                            class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                        >
                            -
                        </button>
                        <input
                            v-model.number="stockForm.addForSell"
                            type="number"
                            min="0"
                            class="flex-1 border border-gray-200 rounded-lg px-4 py-2 text-sm text-center"
                        />
                        <button
                            @click="stockForm.addForSell++"
                            class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50"
                        >
                            +
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 mt-2">
                    <button
                        @click="stockModal = false"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50"
                    >
                        Otkaži
                    </button>
                    <button
                        @click="submitStock"
                        class="flex-1 bg-emerald-700 text-white py-2 rounded-lg text-sm hover:bg-emerald-600"
                    >
                        Sačuvaj
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
