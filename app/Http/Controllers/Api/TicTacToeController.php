<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TicTacToeController extends Controller
{
    // Initialize the game state if no session exists
    private function initializeGame(): void
    {
        logger(__METHOD__);

        if (!Session::has('board')) {
            Session::put('board', [['', '', ''], ['', '', ''], ['', '', '']]);
            Session::put('score', ['x' => 0, 'o' => 0]);
            Session::put('currentTurn', 'x');
            Session::put('victory', '');
        }
    }

    // Get the current game state
    public function getState(): JsonResponse
    {
        logger(__METHOD__);

        $this->initializeGame();
        return response()->json([
            'board'       => Session::get('board'),
            'score'       => Session::get('score'),
            'currentTurn' => Session::get('currentTurn'),
            'victory'     => Session::get('victory')
        ]);
    }

    // Place a piece on the board
    public function placePiece(Request $request, $piece): JsonResponse
    {
        logger(__METHOD__);

        $this->initializeGame();
        $board       = Session::get('board');
        $currentTurn = Session::get('currentTurn');
        $victory     = Session::get('victory');

        if ($victory !== '') {
            return response()->json(['error' => 'Game over, player [ ' . $victory . ' ] won.'], 409);
        }

        if ($piece !== $currentTurn) {
            return response()->json(['error' => 'Not your turn'], 406);
        }

        $x = $request->input('x');
        $y = $request->input('y');

        // Ensure the piece is placed in a valid position
        if ($x < 0 || $x > 2 || $y < 0 || $y > 2 || $board[$x][$y] !== '') {
            return response()->json(['error' => 'Invalid move'], 409);
        }

        // Place the piece
        $board[$x][$y] = $piece;
        Session::put('board', $board);

        // Check for victory
        if ($this->checkVictory($board, $piece)) {
            Session::put('victory', $piece);
            Session::put('score.' . $piece, Session::get('score.' . $piece) + 1);
        }

        // Switch turn
        Session::put('currentTurn', $currentTurn === 'x' ? 'o' : 'x');

        return $this->getState();
    }

    // Restart the game but maintain scores
    public function restartGame(): JsonResponse
    {
        logger(__METHOD__);

        $this->initializeGame();
        Session::put('board', [['', '', ''], ['', '', ''], ['', '', '']]);
        Session::put('currentTurn', 'x');
        Session::put('victory', '');

        return $this->getState();
    }

    // Reset the game and score
    public function resetGame(): JsonResponse
    {
        logger(__METHOD__);

        Session::flush();
        return $this->getState();
    }

    // Helper function to check for victory
    private function checkVictory($board, $piece): bool
    {
        logger(__METHOD__);

        // Check rows and columns
        for ($i = 0; $i < 3; $i++) {
            if ($board[$i][0] === $piece && $board[$i][1] === $piece && $board[$i][2] === $piece) {
                return true;
            }
            if ($board[0][$i] === $piece && $board[1][$i] === $piece && $board[2][$i] === $piece) {
                return true;
            }
        }

        // Check diagonals
        if ($board[0][0] === $piece && $board[1][1] === $piece && $board[2][2] === $piece) {
            return true;
        }
        if ($board[0][2] === $piece && $board[1][1] === $piece && $board[2][0] === $piece) {
            return true;
        }

        return false;
    }
}
