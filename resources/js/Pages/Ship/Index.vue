<script setup>
import Sidebar from '@/Layouts/Sidebar.vue'
import Modal from  '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
// import Pagination from '@/Components/Pagination.vue'
import Card from '@/Components/Card.vue'
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, Link, Head,router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue';
 import { inject } from 'vue';
const toggleDarkMode = inject('isDarkMode');

let showConfirm = ref(false)
let showEdit = ref(false)
let showAdd = ref(false)
let selectedShipForDelete = null
let selectedShip = null
let deleteForm = useForm({});

let form = useForm({
    name: '',
    departure: '',
    arrival: '',
    departure_time: '',
    arrival_time: '',
    ticket_price: '',
    description: '',
    image: '',
})

let props = defineProps({
    ships: Array,
    filters: Object,
    shipCount: Number
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

const shipPic = ref(props.ships);

        // Method to get the image URL
        const getPicUrl = (image) => `/ships/${image}`;

        // On mounted, update the ref if the prop changes
        onMounted(() => {
            shipPic.value = props.ships;
        });


        function extractFilename(storageLink) {
      const parts = storageLink.split('/');
      const filenameWithExtension = parts[parts.length - 1];
      const filenameParts = filenameWithExtension.split('.');
      return filenameParts[0]; // Only get the filename without extension
    }
function add() {
    showAdd.value = true;
    form.name = '';
    form.departure = '';
    form.arrival = '';
    form.departure_time = '';
    form.arrival_time = '';
    form.ticket_price = '';
    form.description = '';
    form.image = '';
}

function edit(ship) {
    selectedShip = ship
    showEdit.value = true;
    form.name = ship.name
    form.departure = ship.departure
    form.departure_time = ship.departure_time
    form.arrival_time = ship.arrival_time
    form.ticket_price = ship.ticket_price
    form.description = ship.description
    form.image = ship.image
}

function closeModal(){
    showConfirm.value = false;
}

function close(){
    showEdit.value = false;
}

function close2(){
    showAdd.value = false;
}

function remove(ship) {
    selectedShipForDelete = ship
    showConfirm.value = true;
}

function deleteShip(){
    deleteForm.delete('/ship/' + selectedShipForDelete.id)
    showConfirm.value = false;
}

const submit = () =>{
    form.post('/ship')
    form.name = "";
    form.departure = "";
    form.arrival = "";
    form.departure_time = "";
    form.arrival_time = "";
    form.ticket_price = "";
    form.description = "";
    form.image = '';
    close2();
}

const update = () =>{
    form.put('/ship/' + selectedShip.id)
    form.name = "";
    form.departure = "";
    form.arrival = "";
    form.departure_time = "";
    form.arrival_time = " ";
    form.ticket_price = " ";
    form.description = "";
     console.log(form.image);
    close();
}

let search = ref(props.filters.search);
watch(search, (value) => {
    router.get(
        "/ship",
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

 const image = ref(null);
    const fileError = ref(null);

    const handleFileChange = (event) => {
      const file = event.target.files[0];
      form.image = file;
      const reader = new FileReader();
      reader.onload = (e) => (image.value = e.target.result);
      reader.readAsDataURL(file);
    }
    const fileHandler = (event) => {
      const file = event.target.files[0];
      image.value = URL.createObjectURL(file);

      // Perform your custom validation here
      const allowedExtensions = ["png", "jpg", "jpeg", "pdf"];
      const maxFileSize = 10 * 1024 * 1024; // 10MB

      if (!allowedExtensions.includes(file.name.split('.').pop().toLowerCase())) {
        fileError.value = "Invalid file format. Please choose a PNG, JPG, or PDF file.";
      } else if (file.size > maxFileSize) {
        fileError.value = "File size exceeds the maximum limit of 10MB.";
      } else {
        fileError.value = null;
      }
    };

onMounted(() => {
// Set a timeout to hide the success flash message after 3 seconds
const successFlashMessage = document.getElementById('flash-success-message');
if (successFlashMessage) {
setTimeout(() => {
    successFlashMessage.style.display = 'none';
}, 3000);
}

// Set a timeout to hide the error flash message after 3 seconds
const errorFlashMessage = document.getElementById('flash-error-message');
if (errorFlashMessage) {
setTimeout(() => {
    errorFlashMessage.style.display = 'none';
}, 3000);
}
});
</script>

<template>
    <Sidebar>
        <Head title="Ship"/>

        <section class="container px-4 py-6 mx-auto">
            <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
                <div class="progress-bar success"></div>
            </div>
            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
                <div class="progress-bar error"></div>
            </div>
            <div class="sm:flex sm:items-center sm:justify-between" :class="themeMode">
                <div>
                    <div class="flex items-center gap-x-3">
                        <h2 class="text-3xl font-bold mb-2">Booking Ticket</h2>
                    </div>
                </div>
            </div>

            <hr>
            <div class="mt-6 md:flex md:items-center md:justify-between md:w-full">
                <div class="inline-flex overflow-hidden">
                    <div class="py-3 px-4">
                        <div class="relative max-w-xs">
                            <label for="hs-table-search" class="sr-only">Search</label>
                            <input type="search" v-model="search"  name="hs-table-search" id="hs-table-search" class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search ship">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center  md:mt-0" >

                    <div class="flex items-center  gap-x-3">
                        <button @click="add()" v-if="$page.props.auth.permissions.includes('manage-ship')" class="flex items-center justify-center w-1/2 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg shrink-0 sm:w-auto gap-x-2 hover:bg-[#E36414] dark:hover:bg-[#FB8B24] dark:bg-[#E36414]">

                            <span class="">Add Ship</span>
                        </button>
                        <Modal :show="showAdd" @close="close2">
                            <div class="p-4 sm:p-10 overflow-y-auto">
                                <div class="flex items-center justify-center ">
                                    <div class="mx-auto w-full max-w-[550px] bg-white">
                                        <form @submit.prevent = "submit">
                                            <div class="mb-5 pt-3">
                                                <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                                    Adding Ship
                                                </label>
                                                <div class="-mx-3 flex flex-wrap">
                                                    <div class="w-full px-3">
                                                        <div class="max-w-md mx-auto p-6 bg-white rounded-md">


                                                            <img v-if="image" id="image" class=" mx-auto w-[100px] h-[100px] " :src="image" />

                                                            <img v-else class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">
                                                        <label for="image" class="block text-sm font-medium text-gray-700 text-center">Choose an image</label>

                                                        <!-- Custom File Input Styling -->
                                                        <div class="relative mt-2 mb-2 text-center">
                                                            <input type="file" @change="handleFileChange" class="absolute inset-0 w-full h-full opacity-0 z-50 cursor-pointer" />
                                                            <input @change="handleFileChange"  name="file-upload" type="file" class="hidden" />

                                                            <label for="image" class="cursor-pointer inline-block px-4 py-2 bg-blue-500 text-white rounded-md">
                                                            Select Image
                                                            </label>
                                                            <span id="file-name" class="ml-2 text-gray-600"></span>
                                                            <p v-if="form.errors.image" class="text-red-500 mt-2">{{ form.errors.image }}</p>
                                                        </div>

                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="name" class="text-left">Ship Name</label>
                                                            <input type="text" v-model="form.name" name="name" id="name" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="departure" class="text-left">Departure</label>
                                                            <input type="text" v-model="form.departure" name="departure" id="departure" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="arrival" class="text-left">Arrival</label>
                                                            <input type="text" v-model="form.arrival" name="arrival" id="arrival" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                        <div class="col-span-6 sm:col-span-2">
                                                            <label for="departure_time" class="text-sm font-medium text-gray-900 block mb-2">Departure time</label>
                                                            <input type="time" name="departure_time" id="departure_time" v-model="form.departure_time"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                                            <div class="text-sm text-red-500 italic" v-if="form.errors.departure_time">{{ form.errors.departure_time }}</div>
                                                        </div>
                                                        <div class="col-span-6 sm:col-span-2">
                                                            <label for="arrival_time" class="text-sm font-medium text-gray-900 block mb-2">Arrival time</label>
                                                            <input type="time" name="arrival_time" id="arrival_time" v-model="form.arrival_time"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                                            <div class="text-sm text-red-500 italic" v-if="form.errors.arrival_time">{{ form.errors.arrival_time }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="ticket_price" class="text-left">Ticket price</label>
                                                            <input type="number" v-model="form.ticket_price" name="ticket_price" id="ticket_price" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>

                                                        <div class="mb-5">
                                                            <label for="description" class="text-left">Description</label>
                                                            <input type="text" v-model="form.description" name="description" id="description" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-6 flex justify-end gap-x-4">
                                                <SecondaryButton @click="close2">Cancel</SecondaryButton>
                                                <PrimaryButton type="submit" @click="submit">Save</PrimaryButton>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>

            <div class="w-full px-2 mt-3" :class="themeMode">
                <div class="h-12">
                    <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                        <hr>
                            <Card>
                            <template #head>
                                <p class="total mb-5 text-2xl">Total ship: ({{ shipCount }})</p>
                                <div class="flex flex-wrap -mx-4">
                            <div class="w-full sm:w-1/2 md:w-1/2 lg:w-1/2 xl:w-1/2 px-1 mb-8 rounded-md" v-for="ship in ships.data" :key="ship.id">

                            <div class="border border-gray-300" :title="(ship.description)">
                               <div class="relative overflow-hidden object-cover h-[300px] bg-blue-gray-500 bg-clip-border text-white shadow-lg shadow-blue-gray-500/40">
                                    <img
                                        :src="getPicUrl(ship.image)"
                                        alt="ship image"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <div class="text-center mt-2">
                                    <h5 class="block font-sans text-2xl font-medium leading-snug tracking-normal  antialiased">
                                    <p>
                                        {{ ship.name }}
                                    </p>
                                </h5>
                                <div class="flex justify-between px-10">
                                <p class="block font-sans text-base font-light leading-relaxed  antialiased">
                                    {{ ship.departure }}
                                </p>
                                <p>to</p>
                                <p class="block font-sans text-base font-light leading-relaxed  antialiased">
                                    {{ ship.arrival }}
                                </p>
                                </div>
                                <div class="flex justify-between px-10">
                                    <p class="block font-sans text-base font-light leading-relaxed  antialiased">
                                    {{ formatTimeToAMPM(ship.departure_time) }}
                                </p>
                                <p>to</p>
                                <p class="block font-sans text-base font-light leading-relaxed  antialiased">
                                    {{ formatTimeToAMPM(ship.arrival_time) }}
                                </p>
                                </div>
                                <p class="block font-sans text-base font-light leading-relaxed  antialiased">
                                    <i class="fas fa-peso-sign"></i> {{ ship.ticket_price }}
                                </p>
                                </div>
                                <div class="group mt-8 flex flex-wrap items-center justify-center gap-3" v-if="isAdmin">

                                <a href="#"  @click="edit(ship)" title="Edit Ship"
                                    data-tooltip-target="wifi"
                                    class="btn cursor-pointer rounded-full border border-[#EADDDD] bg-[#FFD6A7] p-3 text-[#8C5454] transition-colors hover:border-[#5B3929] hover:bg-[#F7CEA1] hover:!opacity-100 group-hover:opacity-70"
                                >
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>

                                </a>
                                <Modal :show="showEdit" @close="close">
                                    <div class="p-4 sm:p-10 overflow-y-auto">
                                        <div class="flex items-center justify-center ">
                                            <div class="mx-auto w-full max-w-[550px] bg-white">
                                                <form @submit.prevent = "update">
                                                    <div class="mb-5 pt-3">
                                                        <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                                        Edit Ship Details
                                                        </label>
                                                        <div class="-mx-3 flex flex-wrap">
                                                            <div class="w-full px-3">
                                                                <div class="mb-5">
                                                                    <label for="name" class="text-left">Name</label>
                                                                    <input type="text" v-model="form.name" name="name" id="name" placeholder=""
                                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                                </div>
                                                                <label for="name" class="text-left">Name</label>
                                                                <div class="w-full relative border-2 mb-2 border-gray-300 border-dashed rounded-lg p-6" id="dropzone">
                                                                    <input type="file" @change="handleFileChange" class="absolute inset-0 w-full h-full opacity-0 z-50" />
                                                                        <div class="text-center">

                                                                            <img v-if="image" id="image" class=" mx-auto w-[100px] h-[100px] " :src="image" />

                                                                            <img v-else class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">
                                                                            <h3  class="mt-2 text-sm font-medium text-gray-900">
                                                                                <label for="file-upload" class="relative cursor-pointer">
                                                                                    <span>Drag and drop</span>
                                                                                    <span class="text-indigo-600"> or browse </span>
                                                                                    <span>to upload</span>
                                                                                    <input id="file-upload" @change="handleFileChange"  name="file-upload" type="file" class="sr-only">
                                                                                </label>
                                                                            </h3>
                                                                            <p  class="mt-1 text-xs text-gray-500">
                                                                                PNG, JPG, PDF up to 10MB
                                                                            </p>
                                                                        </div>
                                                                        <p v-if="form.errors.image" class="text-red-500 mt-2">{{ form.errors.image }}</p>
                                                                </div>
                                                                <div class="mb-5">
                                                                    <label for="description" class="text-left">Description</label>
                                                                    <input type="text" v-model="form.description" name="description" id="description" placeholder=""
                                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-6 flex justify-end gap-x-4">
                                                        <SecondaryButton @click="close">Cancel</SecondaryButton>
                                                        <PrimaryButton type="submit" @click="update">Save</PrimaryButton>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </Modal>

                                <a  href="#" @click="remove(ship)"  title="Delete Ship"
                                    data-tooltip-target="tv"
                                     class="btn cursor-pointer rounded-full border border-[#EADDDD] bg-[#FFD6A7] p-3 text-[#8C5454] transition-colors hover:border-[#5B3929] hover:bg-[#F7CEA1] hover:!opacity-100 group-hover:opacity-70"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>

                                </a>
                                <Modal :show="showConfirm" @close="closeModal">
                                    <div class="p-4 sm:p-10 text-center overflow-y-auto">

                                        <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                            </svg>
                                        </span>


                                        <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                            Delete Ship
                                        </h3>
                                        <p class="text-gray-500">
                                            Are you sure you want like to delete this Ship?
                                        </p>

                                        <div class="mt-6 flex justify-center gap-x-4">
                                            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                            <DangerButton @click="deleteShip()">Delete</DangerButton>
                                        </div>
                                    </div>
                                </Modal>


                                </div>
                            <div class="p-3 pt-3 border">
                                <Link :href="('/ticket/create/' + ship.id)"
                                class="block w-full select-none rounded-lg bg-[#5F0F40] py-2 px-7 text-center align-middle font-sans text-sm font-bold text-white shadow-md shadow-[#FFD6A7] transition-all hover:shadow-lg hover:shadow-[#F7CEA1] focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                                type="button"
                                data-ripple-light="true"
                                >
                                Book a ticket for ({{ ship.name }})
                                </Link>
                            </div>
                            </div>
                            </div>
                            </div>
                            </template>
                        </Card>
                        <p class="text-center" v-if="ships.data == 0">No ships found</p>
                    </div>

                </div>
            </div>

        </section>

    </Sidebar>
</template>

<style scoped>

#flash-success-message {
    animation: fadeOut 6s ease-in-out forwards;
}

.progress-bar {
    height: 5px;
    width: 100%;
    background-color: #4CAF50; /* Green color */
    animation: progressBar 3s linear;
}
#flash-error-message {
    animation: fadeOut 7s ease-in-out forwards;
}

.success .progress-bar {

    animation: progressBar 5s linear;
}
.error .progress-bar {
    background-color: #FF5733; /* Red color */
    animation: progressBar 5s linear;
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

@keyframes progressBar {
    0% {
        width: 100%;
    }
    100% {
        width: 0;
    }
}
</style>
