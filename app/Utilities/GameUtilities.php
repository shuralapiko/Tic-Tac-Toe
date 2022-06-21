<?php

namespace App\Utilities;


class GameUtilities
{
    public static function generateEmptyBoard($board_size) {
        $board = [];

        for ($i = 0; $i < $board_size; $i++) {
            for ($j = 0; $j < $board_size; $j++) {
                if (!isset($board[$i])) {
                    $board[$i] = [];
                }
                $board[$i][] = '';
            }
        }

        return $board;
    }

    public static function generateAndFillBoard($board_size, $x_bitmap, $o_bitmap) {
        $board = self::generateEmptyBoard($board_size);

        $board = self::fillBoardWithPieces($board, $x_bitmap, 'x', $board_size);
        $board = self::fillBoardWithPieces($board, $o_bitmap, 'o', $board_size);

        return $board;
    }

    public static function defineCurrentTurnByBitmaps($x_bitmap, $o_bitmap, $start_turn) {
        $piece_count_x = self::countBits($x_bitmap);
        $piece_count_o = self::countBits($o_bitmap);

        if ($piece_count_x == $piece_count_o) {
            return $start_turn;
        }

        return $piece_count_x > $piece_count_o ? 'o' : 'x';
    }

    // $board_size can be removed since we can simply  count($board),
    private static function fillBoardWithPieces($board, $piece_map, $piece, $board_size = 3) {
        for ($i = 0, $i_max = $board_size * $board_size; $i < $i_max; $i++) {
            $target_bit = 1 << $i;
            $should_place_piece = ($piece_map & $target_bit) == $target_bit;

            if ($should_place_piece) {
                $board[$i / $board_size][$i % $board_size] = $piece;
            }
        }

        return $board;
    }

    // return false or $target_bit
    public static function defineIfPieceCanBePlaced($x, $y, $piece, $x_bitmap, $o_bitmap, $board_size = 3) {
        $target_bit = 1 << ($x + $y * $board_size);

        $is_x_busy = ($x_bitmap & $target_bit) == $target_bit;
        $is_o_busy = ($o_bitmap & $target_bit) == $target_bit;
        if ($is_x_busy || $is_o_busy) {
            return false;
        }

        return $target_bit;
    }


        // todo: not GameUtilities related function. Move to common helpers;
    // Returns the number of active set bits in positive integer
    private static function countBits($num) {
        $count = 0;
        while ($num > 0)
        {
            $count++;
            $num = $num & ($num - 1);
        }
        // Returning the value of calculate result
        return $count;
    }

    public static function checkForVictory($target_map, $board_size = 3) {
        return self::checkMapRowsForVictory($target_map, $board_size)
        || self::checkMapColumnsForVictory($target_map, $board_size)
        || self::checkMapLeftDiagonalsForVictory($target_map, $board_size)
        || self::checkMapRightDiagonalsForVictory($target_map, $board_size);
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
