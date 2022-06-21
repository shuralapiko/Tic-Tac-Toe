<?php

namespace App\Http\Controllers;

use App\Http\Requests\Game\GamePlacePieceRequest;
use App\Http\Resources\GameResource;
use App\Http\ResponseCodes;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class GameController extends BaseController
{
    private static $start_turn = 'x';

    public function latestOrCreate() {
        $game = Game::orderBy('id', 'desc')
            ->firstOrCreate([], ['start_turn' => self::$start_turn]);

        return new GameResource($game);
    }

    public function placePiece(GamePlacePieceRequest $request, $piece) {
        $game = Game::orderBy('id', 'desc')->first();

        if ($game->victory !== '') {
            return response()->json(['message' => 'Status code 423 Locked if a game already finished.'], ResponseCodes::$HTTP_LOCKED);
        }

        if ($game->currentTurn != $piece) {
            return response()->json(['message' => 'Status code 406 Not acceptable if a piece is being placed out of turn.'], ResponseCodes::$HTTP_NOT_ACCEPTABLE);
        }

        $is_piece_placed = $game->placePiece($request->x, $request->y, $piece);
        if (!$is_piece_placed) {
            return response()->json(['message' => 'Status code 409 Conflict if a piece is being placed where a piece already is.'], ResponseCodes::$HTTP_CONFLICT);
        }

        $is_victory = $game->checkForVictory($piece);
        if ($is_victory) {
            $game->victory = $piece;
            $game->save();
        }

        return new GameResource($game);

    }

    public function restart() {
        $start_turn = self::$start_turn;
        $latest_game = Game::orderBy('id', 'desc')->first();
        if ($latest_game) {
            $start_turn = ['x' => 'o', 'o' => 'x'][$latest_game->start_turn];
        }

        $game = Game::create(['start_turn' => $start_turn]);

        return new GameResource($game);
    }
    public function destroy() {
        Game::truncate();

        // todo: some resource here?
        return response()->json(['data' => ['currentTurn' => 'x']]);
    }

}
