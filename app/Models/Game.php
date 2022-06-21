<?php

namespace App\Models;

use App\Utilities\GameUtilities;
use Illuminate\Database\Eloquent\Model;


class Game extends Model
{
    protected $guarded = [];

    // Totally supporting different board sizes in code, but need to change  database little bit: x_bitmap and o_bitmap - 9 bits long columns.
    // It allows save info about board 3x3; to save info about 5x5 board - need 25 bits columns, for 6x6 - 36 bits columns and so on.
    public static $board_size = 3;

    public function getBoardAttribute() {
        return GameUtilities::generateAndFillBoard(self::$board_size, $this->x_bitmap, $this->o_bitmap);
    }

    public function getCurrentTurnAttribute() {
        return GameUtilities::defineCurrentTurnByBitmaps($this->x_bitmap, $this->o_bitmap, $this->start_turn);
    }

    // todo: mv to service
    public function placePiece($x, $y, $piece) {
        $target_bit = GameUtilities::defineIfPieceCanBePlaced($x, $y, $piece, $this->x_bitmap, $this->o_bitmap, self::$board_size);

        if (!$target_bit) {
            return false;
        }
        $target_map_name = $piece . '_bitmap';

        $this->$target_map_name = $this->$target_map_name | $target_bit;
        $this->save();

        return true;
    }

    // todo: mv to service
    public function checkForVictory($piece) {
        $target_map_name = $piece . '_bitmap';
        $target_map = $this->$target_map_name;

        return GameUtilities::checkForVictory($target_map, self::$board_size);
    }


}
