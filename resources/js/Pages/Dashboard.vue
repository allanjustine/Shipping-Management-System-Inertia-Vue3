<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import moment from 'moment';
import { watch } from 'vue';
import { ref, onMounted, computed } from 'vue';
import { Calendar, DatePicker } from 'v-calendar';
import 'v-calendar/style.css';


let props = defineProps({
    tickets: Array,
    user: Object,
    filters:Object,
    totalTickets:Number,
    acceptedTicket:Number,
    percentageChange:Number,
    cancelledTicketsByMonth: Number,
    upcomingTicketsByMonth: Number,
    totalUsers: Number,
})

    function formattedDate(date){
        return moment(date).format('MMMM   D, YYYY');
    }

    const formatTimeToAMPM = (time) => {
        const [hours, minutes] = time.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = hours % 12 || 12;
        const formattedMinutes = minutes.toString().padStart(2, '0');
        return `${formattedHours}:${formattedMinutes} ${period}`;
    };

    const currentTime = ref('');

    onMounted(() => {
    // Get the current time
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    currentTime.value = `${hours}:${minutes < 10 ? '0' : ''}${minutes}`;
    });

    const greeting = computed(() => {
    const hour = currentTime.value ? parseInt(currentTime.value.split(':')[0]) : 0;

    if (hour >= 5 && hour < 12) {
        return 'Good Morning';
    } else if (hour >= 12 && hour < 17) {
        return 'Good Afternoon';
    } else {
        return 'Good Evening';
    }
    });

    let search = ref(props.filters.search);
    watch(search, (value) => {
        router.get(
            "/dashboard",
            { search: value },
            {
                preserveState: true,
                replace: true,
            }
        );
    });

const attrs = ref([
  ...props.tickets.map(ticket => {
    const ticketDate = new Date(ticket.date);
    console.log('Raw ticket time:', ticket.time);

    const ticketTime = ticket.time.split(':');
    const formattedTime = new Date(ticketDate.getFullYear(), ticketDate.getMonth(), ticketDate.getDate(), parseInt(ticketTime[0], 10), parseInt(ticketTime[1], 10)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    console.log('Popover label:', ticket.popoverLabel);

    let highlightColor = '';
    switch (ticket.status) {
      case 'Pending':
        highlightColor = 'blue';
        break;
      case 'Accepted':
        highlightColor = 'green';
        break;
      case 'Cancelled':
        highlightColor = 'red';
        break;
      default:
        highlightColor = 'gray'; // You can set a default color for other statuses
    }


    return {
    key: `ticket-${ticket.id}`,
      highlight: highlightColor,
      style: 'background-color: red; color: white;',
      dates: ticketDate,
      popover: {
        // label: `${ticket.patient.firstname} ${ticket.patient.lastname}\n${formattedTime}`, // Display patient's name and formatted time in the popover
        label: ticket.popoverLabel || '',
        visibility: 'hover', // Show popover on hover
      },
    };
  }),
]);



    const formattedTickets = computed(() => {
      return props.tickets.map(ticket => {
        return {
          start: (ticket.date, ticket.time),
          title: `${ticket.patient.firstname} - ${ticket.doctor}`,
          // Add other properties as needed based on your ticket structure
        };
      });
    });

</script>

<template>
    <Head title="Dashboard" />

    <Sidebar>
        <div class="flex flex-col flex-1 w-full overflow-y-auto">

          <main class="relative z-0 flex-1 pb-8 px-6 ">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-5">

            <div class="flex items-center justify-between p-6 bg-white p-6 rounded-md shadow-md">
                <div class="text-left">
                    <p class="text-lg font-semibold">Total Tickets</p>
                    <p class="text-3xl font-bold">{{ totalTickets }}</p>
                </div>
                <div class="text-right">
                    <h1><i class="w-8 h-8 fas fa-ticket text-2xl"></i></h1>
                </div>
            </div>
            <div class="flex items-center justify-between p-6 bg-white p-6 rounded-md shadow-md">
                <div class="text-left">
                    <p class="text-lg font-semibold">Total Users</p>
                    <p class="text-3xl font-bold">{{ totalUsers }}</p>
                </div>
                <div class="text-right">
                    <h1><i class="w-8 h-8 fas fa-users text-2xl"></i></h1>
                </div>
            </div>
            <div class="flex items-center justify-between p-6 bg-white p-6 rounded-md shadow-md">
                <div class="text-left">
                    <p class="text-lg font-semibold">Pending Tickets</p>
                    <p class="text-3xl font-bold">{{ upcomingTicketsByMonth }}</p>
                </div>
                <div class="text-right">
                    <h1><i class="w-8 h-8 fas fa-clock text-2xl"></i></h1>
                </div>
            </div>
            <div class="flex items-center justify-between p-6 bg-white p-6 rounded-md shadow-md">
                <div class="text-left">
                    <p class="text-lg font-semibold">Accepted Tickets</p>
                    <p class="text-3xl font-bold">{{ acceptedTicket }}</p>
                </div>
                <div class="text-right">
                    <h1><i class="w-8 h-8 fas fa-square-check text-2xl"></i></h1>
                </div>
            </div>
            <div class="flex items-center justify-between p-6 bg-white p-6 rounded-md shadow-md">
                <div class="text-left">
                    <p class="text-lg font-semibold">Cancelled Tickets</p>
                    <p class="text-3xl font-bold">{{cancelledTicketsByMonth}}</p>
                </div>
                <div class="text-right">
                    <h1><i class="w-8 h-8 fas fa-power-off text-2xl"></i></h1>
                </div>
            </div>

        </div>
              <div class="pt-8 px-2" >
                <div class="w-full">
                   <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 xl:col-span-2 2xl:col-span-2">
                        <div class="flex justify-between ">
                            <h3 class="flex items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark">
                                <span class="mr-3 text-dark font-bold">Recent Ticket Bookings</span>
                            </h3>
                            <Link :href="route('ticket.index')" class=" inline-block text-[.925rem] font-medium leading-normal text-right align-right cursor-pointer rounded-2xl transition-colors duration-150 ease-in-out text-[#5F0F40] bg-light-dark border-light shadow-none border-0 py-2 px-5 sm:self-center">
                                Load More
                            </Link>
                        </div>
                        <div class="flex flex-col">
                            <div class="-m-1.5 overflow-x-auto">
                                <div class="p-1.5 min-w-full inline-block align-middle">
                                <div class=" rounded-lg divide-y divide-gray-200 ">
                                    <!-- <div class="py-3 px-4">
                                        <div class="relative max-w-xs">
                                            <label for="hs-table-search" class="sr-only">Search</label>
                                            <input type="search"  v-model="search" name="hs-table-search" id="hs-table-search" class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search for ticket">
                                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 ">
                                        <thead class="bg-gray-50 ">
                                            <tr>

                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Customer</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Ship Avail</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Claim at</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                            <tbody class="divide-y divide-gray-200">

                                                <tr v-for="app in tickets" :key="app.id">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 ">{{ app.user.name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">{{ app.ship.name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">{{ formattedDate(app.date) }} at {{ formatTimeToAMPM(app.time) }}</td>
                                                    <td class=" text-xs whitespace-nowrap  uppercase px-6 py-4  text-center font-bold " :class="{
                                                                        'text-blue-600': app.status == 'Pending',
                                                                        'text-red-600':app.status == 'Cancelled',
                                                                        'text-green-600': app.status =='Accepted',
                                                                    }">{{ app.status}}</td>
                                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                    <button type="button" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">Delete</button>
                                                    </td> -->
                                                </tr>

                                                <tr v-if="tickets == 0">
                                                <td colspan="4" class="text-center">
                                                    No records found
                                                </td>
                                                </tr>
                                             </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    </div>
                    <hr>
                    <h2 class="text-2xl text-center mt-10 mb-2">Calendar Stamp</h2>
                    <div class="flex items-center justify-center">
                        <div class="my-custom-calendar">
                            <Calendar :attributes="attrs" expanded />
                        </div>
                    </div>


            </div>

          </main>

        </div>


    </Sidebar>
</template>
<style scoped>
.my-custom-calendar {
    width: 70%;

}

/* #app {
  background: #fff;
  border-radius: 4px;
  padding: 20px;
  transition: all 0.2s;
  text-align: center;
} */
</style>
