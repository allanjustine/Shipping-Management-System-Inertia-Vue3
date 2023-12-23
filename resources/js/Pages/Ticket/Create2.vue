<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import { ref, reactive, computed, watch, onMounted } from 'vue';
    import axios from 'axios';
    import { Link , Head} from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';


    let props = defineProps({
        ship: Array,
        ticket: Object,
        users:Object,
        isAdmin:Boolean,
        isStandard: Boolean,
        user:Object

    });
let form = useForm({
       'date' : '',
       'time' : props.ship ? props.ship[0]?.id : null,
       'user_id': '',
       'ticket_quantity': '',
        'ship_id': props.ship ? props.ship[0]?.id : null,
       'name': props.isStandard ? props.user.name : '',

})

 onMounted(() => {
   console.log('Selected Ship:', props.ship);
   console.log('Ship ID:', props.ship ? props.ship[0]?.id : null);
  });


const submit = () =>{
    form.post('/ticket')
}



</script>

<template>
    <Head title="Create Ticket"/>
    <Sidebar>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Ticket</h2>
        </template>
        <div>
            <div class="w-full mt-10 mx-auto px-4 ">



                  <div class="w-full mt-10 mx-auto px-4 ">
                  <div class="bg-white border border-4 rounded-lg shadow relative m-10">

                    <div class="flex items-start justify-between p-5 border-b rounded-t">
                        <h3 class="text-xl font-semibold">
                        Ticket Details
                        </h3>
                    </div>

                    <div class="p-6 ">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-4 gap-4">

                                <div class="col-span-6 sm:col-span-2" v-if="isAdmin">
                                    <label for="user_id" class="text-sm font-medium text-gray-900 block mb-2">Customer</label>
                                    <select v-model="form.user_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                                        <option selected disabled >Select customer</option>
                                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                    </select>
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.user_id">{{ form.errors.user_id }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2" v-if="isStandard">
                                    <label for="user_id" class="text-sm font-medium text-gray-900 block mb-2">Name</label>
                                    <input
                                        type="text"
                                        name="user_id"
                                        id="user_id"
                                       :value="form.name"
                                        readonly
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5"
                                        required
                                    />
                                 </div>

                                <div class="col-span-6 sm:col-span-2">
                                    <label for="date" class="text-sm font-medium text-gray-900 block mb-2">Date</label>
                                    <input type="date" name="date" v-model="form.date" id="date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.date">{{ form.errors.date }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="time" class="text-sm font-medium text-gray-900 block mb-2">Time</label>
                                    <input type="time" name="time" id="time" readonly :value="props.ship ? props.ship[0]?.departure_time : ''"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.time">{{ form.errors.time }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="ship_id" class="text-sm font-medium text-gray-900 block mb-2">Ship</label>
                                     <input
                                        type="text"
                                        name="ship_id"
                                        id="ship_id"
                                        :value="props.ship ? props.ship[0]?.name : ''"
                                        readonly
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5"
                                        required
                                    />
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.ship_id">{{ form.errors.ship_id }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="ticket_quantity" class="text-sm font-medium text-gray-900 block mb-2">Ticket Quantity</label>
                                    <input type="number" name="ticket_quantity" id="ticket_quantity" v-model="form.ticket_quantity"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.ticket_quantity">{{ form.errors.ticket_quantity }}</div>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200 rounded-b flex justify-end">
                                <button class="text-white bg-[#E36414] hover:bg-[#FB8B24] focus:ring-4 focus:ring-indigo-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </Sidebar>

</template>
