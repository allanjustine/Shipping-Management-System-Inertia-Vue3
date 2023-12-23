<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <div
        style="background-image: url('/images/bg.png'); background-size: cover"
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter selection:bg-red-500 selection:text-white"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="route('register')"
                    class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>

        <div class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Content Container -->
        <div
            class="relative z-10 flex items-center justify-between p-8"
        >
            <!-- Left Section: Heading -->
            <div class="max-w-md">
                <h1 class="text-4xl font-bold text-[#FB8B24]">
                    Discover Your Next Adventure
                </h1>
                <p class="mt-4 text-[#E36414]">
                    Book your tickets now and embark on an unforgettable
                    journey!
                </p>
            </div>

            <!-- Right Section: Book a Ticket Button -->
            <div>
                <Link
                    :href="route('login')"
                    as="button"
                    class="px-6 py-3 text-white bg-[#5F0F40] rounded-md hover:bg-[#5F0F40] focus:outline-none focus:shadow-outline-blue active:bg-[#5F0F40]"
                    >Book a Ticket</Link
                >
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
