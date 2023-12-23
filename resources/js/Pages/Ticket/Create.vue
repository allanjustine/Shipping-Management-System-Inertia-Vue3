<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import { ref, reactive, computed, watch, onMounted } from 'vue';
    import axios from 'axios';
    import { Link , Head} from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';





    let props = defineProps({
        ships: Object,
        ticket: Object,
        users:Object,
        isAdmin:Boolean,
        isStandard: Boolean,
        user:Object

    });
let form = useForm({
       'date' : '',
       'time' : '',
       'user_id': '',
       'days_stay': '',
       'person_number': '',
       'ship_id': '',
       'name': props.isStandard ? props.user.name : '',

})



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
                                    <label for="user_id" class="text-sm font-medium text-gray-900 block mb-2">Client</label>
                                    <select v-model="form.user_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                                        <option selected disabled >Select client</option>
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
                                    <input type="time" name="time" id="time" v-model="form.time"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.time">{{ form.errors.time }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="ship_id" class="text-sm font-medium text-gray-900 block mb-2">Ship</label>
                                    <select v-model="form.ship_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                                        <option selected disabled >Select ships</option>
                                        <option v-for="ship in ships" :key="ship.id" :value="ship.id">{{ ship.name }}</option>
                                    </select>
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.ship_id">{{ form.errors.ship_id }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="days_stay" class="text-sm font-medium text-gray-900 block mb-2">Days_stay</label>
                                    <input type="number" name="days_stay" id="days_stay" v-model="form.days_stay"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.days_stay">{{ form.errors.days_stay }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="person_number" class="text-sm font-medium text-gray-900 block mb-2">Person_number</label>
                                    <input type="number" name="person_number" id="person_number" v-model="form.person_number"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.person_number">{{ form.errors.person_number }}</div>
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
