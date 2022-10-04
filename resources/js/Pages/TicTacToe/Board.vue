<template>
    <AppLayout title="Dashboard">
        <template #header="TicTacToe">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tic Tac Toe
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg px-72 grid grid-cols-2 gap-3">
                <div class="p-2 col-span-1 text-center text-red-50 rounded cursor-pointer mb-2"
                     @click="gameType = 'new game'"
                     :class="gameType === 'new game' ? 'bg-green-500' : 'bg-amber-400'">New Game</div>

                <div class="p-2 col-span-1 text-center text-red-50 rounded cursor-pointer mb-2"
                     @click="gameType = 'old game'"
                     :class="gameType === 'old game' ? 'bg-green-500' : 'bg-amber-400'">Old Game</div>
            </div>
        </div>

        <div v-if="gameType === 'new game'" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg px-72">
                <select id="board"
                        v-model="boardTypeLabel"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" selected disabled>Select Board</option>
                    <option value="ThreeIsToThree">3:3</option>
                    <option value="TenIsToTen">10:10</option>
                </select>
                <div class="p-4 bg-blue-500 text-white rounded mt-3 text-center cursor-pointer"
                     @click="submitBoardType">Start Game
                </div>
            </div>
        </div>

        <div v-if="gameType === 'old game'" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg px-72">
                <select id="board"
                        v-model="gameId"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" selected disabled>Select Game</option>
                    <option v-for="(game, index) in games" :key="'game'+index" :value="game.id">Game-{{ game.id }}</option>
                </select>
                <Link :href="route('game.show', gameId)" class="p-4 bg-blue-500 text-white rounded mt-3 text-center cursor-pointer inline-block w-full">
                    Start Game
                </Link>
            </div>
        </div>
    </AppLayout>

</template>

<script>
import AppLayout from "../../Layouts/AppLayout.vue";
import {Link} from "@inertiajs/inertia-vue3";
import {chunk} from "lodash";

export default {
    name: "Index",
    components: {AppLayout, Link},
    props: {
        games: Array
    },

    data() {
        return {
            boardTypeLabel: '',
            gameId: '',
            gameType: '',

        }
    },

    mounted() {

    },

    methods: {
        async submitBoardType() {
            if (this.boardTypeLabel === '') {
                return false;
            } else {
                console.log('asdfasdfasdf')
                return await axios.post(route('game.create'), {"boardTypeLabel": this.boardTypeLabel})
                    .then((response) => {
                        if (response.data.status === 200) {
                            this.boardTypeLabel = response.data.data.type;
                            this.gameId = response.data.data.id;
                            this.$inertia.get(route('game.show', response.data.data.id));
                        }
                    });
            }
        },
    }
}
</script>

<style scoped>

</style>
