<template>
<div class="page">
    <div class="game-wrapper">

        <div class="tboard" v-if="game.board">
            <template v-for="(row, y) in game.board" >
                <div class="trow">
                    <template v-for="(cell, x) in row" class="trow">
                        <div class="tcolumn" >
                            <div v-if="cell == 'x'" class="cell-x"><div class="x"></div></div>
                            <div v-else-if="cell == 'o'" class="cell-o"><div class="o"></div></div>
                            <div v-else class="cell-empty" @click="loading ? '' : placePeace(x, y)">
                                <div :class="[game.currentTurn]"></div>
                            </div>
                        </div>

                    </template>
                </div>
            </template>
        </div>

        <div style="flex: 1;
    width: 100%;
    text-align: center;">
            <h3>
                <template v-if="(game.victory !== '') || !game.board">
                    Next game first turn:
                </template>
                <template v-else>
                    Current turn:
                </template>
                [{{game.currentTurn}}]
            </h3>

            <hr>
            <template v-if="game.score">
                <h4>
                    Score
                    <br>
                    X: {{game.score.x}}
                    <br>
                    O: {{game.score.o}}
                </h4>
            </template>

            <hr>

            <div style="display: flex; justify-content: space-between;">
            <button class="button" @click="reset">
                <template v-if="game.victory">
                    Start again
                </template>
                <template v-else>
                    Reset
                </template>
            </button>
            <button class="button" @click="destroy">Delete</button>
            </div>
        </div>

    </div>
</div>
</template>

<script>
import axios from 'axios';

export default {
    name: "test",
    data() {
        return {
            game: {
                board: [
                    ['', '', ''],
                    ['', '', ''],
                    ['', '', '']
                ],
                score: {
                    "x": 0,
                    "o": 0
                },
                currentTurn: "x",
                victory: '',
            },
            loading: false,


        };
    },
    created() {
        this.fetch();
    },
    methods: {
        fetch() {
            this.loading = true;

            axios.get('/api/')
                .then((res) => {
                    this.game = res.data.data;
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        placePeace(x, y) {
            this.loading = true;
            axios.post('/api/' + this.game.currentTurn, {x: x, y: y})
                .then((res) => {
                   this.game = res.data.data;
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        changeCurrentTurn() {
            this.currentTurn = this.currentTurn == 'x' ? 'o' : 'x';
        },
        reset() {
            this.loading = true;
            axios.post('/api/restart')
                .then((res) => {
                    this.game = res.data.data;
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        destroy() {
            this.loading = true;
            axios.delete('/api/')
                .then((res) => {
                    this.game = res.data.data;
                })
                .catch((err) => {
                    console.log(err);
                })
                .finally(() => {
                    this.loading = false;
                })
        }
    }
}
</script>

<style scoped lang="scss">

$size: 4;
$width: 9px;
$step: 7px;

$test: "";
@for $i from 0 through $size {
    $coma: if($i == $size, '', ',');
    $pos: $step * $i;
    $test: $test
        + " #{$pos} #{$pos} 0 0 currentColor,"
        + " #{-$pos} #{$pos} 0 0 currentColor,"
        + " #{$pos} #{-$pos} 0 0 currentColor,"
        + " #{-$pos} #{-$pos} 0 0 currentColor"
        + $coma;
}

.x {
    $color: #5dc9cf;
    width: $width;
    height: $width;
    background: $color;
    color: $color;
    box-shadow: #{-$width} #{-$width} 0 0 currentColor;
    box-shadow: #{$test};
}
.o {
    $size: 45px;
    background: transparent;
    width: $size;
    height: $size;
    border-radius: 100px;
    border: 11px solid #a90303;
    box-sizing: content-box;
}

$page-color: #b18f2d;
.page {
    background: $page-color;
    width: 100%;
    height: 100%;

    display: flex;
    justify-content: center;
    align-items: center;

}

.game-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border: 10px solid #8c6f1e;
    @media (max-width: 650px) {
        flex-direction: column;
    }
    @media (min-width: 650px) {
        min-width: 600px;
    }
}

.tboard {
    $size: 75px;
    $gap: 10px;

    display: flex;
    flex-direction: column;
    gap: $gap;
    margin: auto;
    width: fit-content;

    background: black;
    .trow {
        display: flex;
        gap: $gap;
        .tcolumn {
            width: $size;
            height: $size;
            background: $page-color;

            display: flex;
            justify-content: center;
            align-items: center;

            .cell-empty {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;

                position: relative;

                > * {
                    opacity: 0;
                }

                &:hover > * {
                    opacity: 0.5;
                }
            }
        }
    }
}

.button {
    padding: 10px 15px;
    font-size: 18px;
    background: #dee881;
    &:hover {
        background: darken(#dee881, 20%);
    }
    &:active {
        background: darken(#dee881, 40%);
    }
}
</style>
