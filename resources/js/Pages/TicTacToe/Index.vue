<template>
    <AppLayout title="Dashboard">
        <template #header="TicTacToe">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tic Tac Toe
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded"
            :class="winMessage==='Winner Chicken Dinner' ? 'bg-green-500' : 'bg-red-500'">
                <div v-if="winMessage !== ''" class="p-2 col-span-1 text-center text-white rounded cursor-pointer mb-2">{{ winMessage }}</div>
            </div>
        </div>

        <div v-if="gameId && boardTypeLabel==='ThreeIsToThree'" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <div class="grid grid-cols-3 px-72 gap-3 justify-center">
                        <button :disabled="recentMove && recentMove === me && gameStatus !== 1" v-for="(gameType, index) in gameValues"
                             @click.prevent="setValue(gameType, index)"
                             class="col-span-1 p-10 text-center rounded cursor-pointer"
                             :class="currentGames.find(item => item.pivot.house_no === gameType) && currentGames.find(item => item.pivot.house_no === gameType).pivot.user_id === me ? 'bg-green-500 ' : currentGames.find(item => item.pivot.house_no === gameType) && currentGames.find(item => item.pivot.house_no === gameType).pivot.user_id !== me ? 'bg-red-500' : 'bg-amber-400'">
                            {{ gameType }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="gameId && boardTypeLabel==='TenIsToTen'" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <div class="grid grid-cols-10 px-36 gap-3 justify-center">
                        <button :disabled="recentMove && recentMove === me && gameStatus !== 1" v-for="(gameType, index) in gameValues"
                             @click.prevent="setValue(gameType, index)"
                             class="col-span-1 p-10 text-center rounded cursor-pointer"
                             :class="currentGames.find(item => item.pivot.house_no === gameType) && currentGames.find(item => item.pivot.house_no === gameType).pivot.user_id === me ? 'bg-green-500 ' : currentGames.find(item => item.pivot.house_no === gameType) && currentGames.find(item => item.pivot.house_no === gameType).pivot.user_id !== me ? 'bg-red-500' : 'bg-amber-400'">
                            {{ gameType }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

</template>

<script>
import AppLayout from "../../Layouts/AppLayout.vue";
import {chunk} from "lodash";

export default {
    name: "Index",
    components: {AppLayout},

    props: {
        game: Object
    },

    data() {
        return {
            gameValues: [],
            gameType: '',
            boardTypeLabel: '',
            gameId: '',
            gameStatus: '',
            currentGames: [],
            recentMove: null,
            me: this.$page.props.user.id,
            wins: [],
            myArray: [],
            herArray: [],
            winMessage: ''
        }
    },

    mounted() {
        if (this.game) {
            this.gameId = this.game.id;
            this.gameStatus = this.game.status;
            this.boardTypeLabel = this.game.type === 1 ? 'ThreeIsToThree' : 'TenIsToTen';

            if (this.boardTypeLabel === 'ThreeIsToThree'){
                for (let i = 1; i<=3*3; i++){
                    this.gameValues.push(i);
                }
            }

            if (this.boardTypeLabel === 'TenIsToTen'){
                for (let i = 1; i<=10*10; i++){
                    this.gameValues.push(i);
                }
            }

            this.getCurrentGame(this.gameId).then(() => {
                this.divideResultArray();
            });
        }
    },

    methods: {
        async setValue(gameType, index) {
            if (this.me !== this.recentMove){
                return await axios.post(route('game.house', this.gameId), {"houseNo": gameType})
                    .then((response) => {
                        if (response.data.status === 200) {
                            this.currentGames = response.data.data.users;
                            this.recentMove = response.data.data.users[response.data.data.users.length - 1].pivot.recent_move;
                        }
                    });
            }
        },

        async getCurrentGame(id) {
            return await axios.get(route('game.current', id))
                .then((response) => {
                    if (response.data.status === 200) {
                        if (response.data.data.users.length > 0){
                            this.currentGames = response.data.data.users;
                            this.recentMove = response.data.data.users[response.data.data.users.length - 1].pivot.recent_move;
                        }
                    }
                })
        },

        divideResultArray() {
            this.wins = chunk(this.gameValues, 3);
            if (this.boardTypeLabel === 'ThreeIsToThree'){
                let colArray = [];
                for (let i=0; i<3; i++){
                    for (let j=0; j<3; j++){
                        colArray.push(this.wins[j][i]);
                    }
                    this.wins.push(colArray);
                    colArray = [];
                }

                for (let i=0; i<3; i++){
                    colArray.push(this.wins[i][i]);
                }
                this.wins.push(colArray);
                colArray = [];

                for (let i=0; i<3; i++){
                    let n=2;
                    colArray.push(this.wins[i][n-i]);
                }
                this.wins.push(colArray);
                colArray = [];
            }
        },

        async changeStatus(){
            return await axios(route('game.status.update', this.gameId))
                .then((response) => {
                    if (response.data.status === 200){
                        this.gameStatus = response.data.data.status;
                    }
                })
        }
    },

    computed: {
        checkCurrentGames() {
            return JSON.parse(JSON.stringify(this.currentGames));
        },

        checkGameId() {
            return JSON.parse(JSON.stringify(this.gameId));
        }
    },

    watch: {
        checkCurrentGames: {
            handler: function hanler(newValue, oldValue) {
                if (newValue !== oldValue) {
                    for (let i = 0; i < this.currentGames.length; i++) {
                        if (this.currentGames[i].id === this.me){
                            this.myArray.push(this.currentGames[i].pivot.house_no);
                        } else {
                            this.herArray.push(this.currentGames[i].pivot.house_no);
                        }
                    }
                    if (this.myArray.length > 3){
                        for (let i = 0; i < this.wins.length; i++){
                            if (this.wins[i].every(item => this.myArray.includes(item))){
                                this.winMessage = 'Winner Chicken Dinner';
                                this.changeStatus();
                            }
                        }
                    }
                    if (this.herArray.length > 3){
                        for (let i = 0; i < this.wins.length; i++){
                            if (this.wins[i].every(item => this.herArray.includes(item))){
                                this.winMessage = 'Ops! You lose';
                                this.changeStatus();
                            }
                        }
                    }
                }
            }
        },
    }
}
</script>

<style scoped>

</style>
