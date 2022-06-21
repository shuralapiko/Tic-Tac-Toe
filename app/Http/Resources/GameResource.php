<?php

namespace App\Http\Resources;


use App\Models\Game;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray($request) {
        return [
            "board" => $this->board,
            "currentTurn" => $this->current_turn,
            "victory" => $this->victory ?? '',
            // todo: move out from here to some other place?
            "score" => [
                "x" => Game::where('victory', 'x')->count(),
                "o" => Game::where('victory', 'o')->count(),
            ],
        ];
    }
}
