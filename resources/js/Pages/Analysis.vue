<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {reactive, onMounted} from 'vue';
import {getToday} from '@/common.js';
import Chart from '@/Components/Chart.vue'
import ResultTable from '@/Components/ResultTable.vue'

onMounted(()=>{
    form.startDate = getToday();
    form.endDate = getToday();
})

const form = reactive({
    startDate: null,
    endDate: null,
    type:"perDay",
    rfmPrms: [14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000,30000 ],
});

const data = reactive({
    // data: []

})

const getData = async () => { 
    try{
        await axios.get('/api/analysis/', { params: {
            startDate: form.startDate, endDate: form.endDate, type: form.type, rfmPrms: form.rfmPrms
        } })
    .then( res => {
        data.data = res.data.data
        if(res.data.labels){data.labels = res.data.labels;}
        if(res.data.eachCount){data.eachCount = res.data.eachCount;}
        data.totals = res.data.totals
        // console.log(res.data)
        data.type = res.data.type

        // console.log(data)
    })
    } catch (e){
        console.log(e.message) 
    }
}


</script>

<template>
    <Head title="データ分析" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">データ分析</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="getData">
                            From: <input type="date" name="startDate" v-model="form.startDate">
                            To: <input type="date" name="endDate" v-model="form.endDate">
                            分析方法<br>
                            <input type="radio" name="type" value="perDay" v-model="form.type" checked class="mr-4">日毎
                            <input type="radio" name="type" value="perMonth" v-model="form.type" class="mr-4">月毎
                            <input type="radio" name="type" value="perYear" v-model="form.type" class="mr-4">年毎
                            <input type="radio" name="type" value="decile" v-model="form.type" class="mr-4">デシル分析
                            <input type="radio" name="type" value="rfm" v-model="form.type" class="mr-4">RFM分析
                            <div v-if="form.type === 'rfm'" class="my-8">
                                <table class="mx-auto">
                                    <thead>
                                        <tr>
                                        <th>ランク</th> <th>R (○日以内)</th> <th>F (○回以上)</th> <th>M (○円以上)</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>5</td>
                                            <td><input type="number" v-model="form.rfmPrms[0]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[4]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[8]"></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td><input type="number" v-model="form.rfmPrms[1]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[5]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[9]"></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td><input type="number" v-model="form.rfmPrms[2]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[6]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[10]"></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td><input type="number" v-model="form.rfmPrms[3]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[7]"></td>
                                            <td><input type="number" v-model="form.rfmPrms[11]"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button class="flex mx-auto mt-4 text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Search</button>
                        </form>
                        <div v-if="form.type != 'rfm'">
                            <Chart :data="data"/>
                        </div>
                        <ResultTable :data="data"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
