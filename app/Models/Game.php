<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Game extends Model
{
    protected $guarded = [];

    public function getBoardAttribute() {
        $board_size = 3;

        $board = [];
        for ($i = 0; $i < $board_size; $i++) {
            for ($j = 0; $j < $board_size; $j++) {
                if (!isset($board[$i])) {
                    $board[$i] = [];
                }
                $board[$i][] = '';
            }
        }

        $board = $this->fillBoardWithPieces($board, $this->player_x, 'x', $board_size);
        $board = $this->fillBoardWithPieces($board, $this->player_o, 'o', $board_size);

        return $board;
    }

    public function getCurrentTurnAttribute() {
        $piece_count_x = $this->countBits($this->player_x);
        $piece_count_o = $this->countBits($this->player_o);

        if ($piece_count_x == $piece_count_o) {
            return $this->start_turn;
        }

        return $piece_count_x > $piece_count_o ? 'o' : 'x';
    }

    private function fillBoardWithPieces($board, $piece_map, $piece, $board_size = 3) {
        for ($i = 0, $i_max = $board_size * $board_size; $i < $i_max; $i++) {
            $target_bit = 1 << $i;
            $should_place_piece = ($piece_map & $target_bit) == $target_bit;

            if ($should_place_piece) {
                $board[$i / $board_size][$i % $board_size] = $piece;
            }
        }

        return $board;
    }



    // todo: move to service / helpers;
    // Returns the number of active set bits in positive integer
    private function countBits($num) {
		$count = 0;
		while ($num > 0)
		{
			$count++;
			$num = $num & ($num - 1);
		}
		// Returning the value of calculate result
		return $count;
	}

    public function placePiece($x, $y, $piece, $board_size = 3) {
        $target_bit = 1 << ($x + $y * $board_size);

        $is_x_busy = ($this->player_x & $target_bit) == $target_bit;
        $is_o_busy = ($this->player_o & $target_bit) == $target_bit;
        if ($is_x_busy || $is_o_busy) {
            return false;
        }

        $target_map_name = 'player_' . $piece;

        $this->$target_map_name = $this->$target_map_name | $target_bit;
        $this->save();

        return true;
    }

    public function checkForVictory($piece, $board_size = 3) {
        $target_map_name = 'player_' . $piece;
        $target_map = $this->$target_map_name;

        return $this->checkMapRowsForVictory($target_map, $board_size)
            || $this->checkMapColumnsForVictory($target_map, $board_size)
            || $this->checkMapLeftDiagonalsForVictory($target_map, $board_size)
            || $this->checkMapRightDiagonalsForVictory($target_map, $board_size);
    }

    private function checkMapRowsForVictory($map, $board_size = 3) {
        $start_bit = 0;
        // for $board_size = 3 => $start_bit = 0b111
        for ($i = 0; $i < $board_size; $i++) {
            $start_bit = ($start_bit << 1) + 1;
        }

        for ($i = 0; $i < $board_size; $i++) {
            $target_bits = $start_bit << ($i * $board_size);

            if (($map & $target_bits) == $target_bits) {
                return true;
            }
        }

        return false;
    }

    private function checkMapColumnsForVictory($map, $board_size = 3) {
        $start_bit = 0;
        // for $board_size = 3 => $start_bit = 0b001 001 001
        for ($i = 0; $i < $board_size; $i++) {
            $start_bit = ($start_bit << $board_size) + 1;
        }

        for ($i = 0; $i < $board_size; $i++) {
            $target_bits = $start_bit << $i;

            if (($map & $target_bits) == $target_bits) {
                return true;
            }
        }

        return false;
    }

    private function checkMapLeftDiagonalsForVictory($map, $board_size = 3) {
        $target_bits = 0;

        // for $board_size = 3 => $start_bit = 0b001 010 100

        for ($i = 0; $i < $board_size; $i++) {
            $target_bit = 1 << (($board_size + 1) * $i);
            $target_bits = $target_bits | $target_bit;
        }

        return ($map & $target_bits) == $target_bits;
    }

    private function checkMapRightDiagonalsForVictory($map, $board_size = 3) {
        $target_bits = 0;
        $start_bit = 1 << ($board_size - 1);

        // for $board_size = 3 => $start_bit = 0b100 010 100
        for ($i = 0; $i < $board_size; $i++) {
            $target_bit = $start_bit << (($board_size - 1) * $i);
            $target_bits = $target_bits | $target_bit;
        }

        return ($map & $target_bits) == $target_bits;
    }
}
