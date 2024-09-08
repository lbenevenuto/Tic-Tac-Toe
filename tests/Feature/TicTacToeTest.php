<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicTacToeTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    protected array  $initialBoard   = [['', '', ''], ['', '', ''], ['', '', '']];
    protected array  $initialScore   = ['x' => 0, 'o' => 0];
    protected string $initialTurn    = 'x';
    protected string $initialVictory = '';
    protected array  $initialSession = [
        'board'       => [['', '', ''], ['', '', ''], ['', '', '']],
        'score'       => ['x' => 0, 'o' => 0],
        'currentTurn' => 'x',
        'victory'     => ''
    ];

    public function test_getInitialState(): void
    {
        $response = $this->get('/api');

        $response->assertStatus(200)->assertJson($this->initialSession);
    }

    public function test_getSessionState()
    {
        $session  = [
            'board'       => [['x', '', ''], ['', '', ''], ['', '', '']],
            'score'       => ['x' => 1, 'o' => 0],
            'currentTurn' => 'o',
            'victory'     => ''
        ];
        $response = $this->withSession($session)->get('/api');
        $response->assertStatus(200)->assertJson($session);
    }

    public function test_placePiece()
    {
        $response = $this->postJson('/api/x', ['x' => 0, 'y' => 0]);
        $response->assertStatus(200)->assertJson(
            [
                'board'       => [['x', '', ''], ['', '', ''], ['', '', '']],
                'score'       => ['x' => 0, 'o' => 0],
                'currentTurn' => 'o',
                'victory'     => ''
            ]
        );

        $response = $this->postJson('/api/x', ['x' => 0, 'y' => 0]);
        $response->assertStatus(406)->assertJson(['error' => 'Not your turn']);

        $response = $this->postJson('/api/o', ['x' => 0, 'y' => 0]);
        $response->assertStatus(409)->assertJson(['error' => 'Invalid move']);

        $response = $this->postJson('/api/o', ['x' => 1, 'y' => 0]);
        $response->assertStatus(200);
    }

    public function test_restartGame()
    {
        $session                = [
            'board'       => [['x', '', ''], ['', '', ''], ['', '', '']],
            'score'       => ['x' => 1, 'o' => 0],
            'currentTurn' => 'o',
            'victory'     => ''
        ];
        $response               = $this->withSession($session)->post('/api/restart');
        $session['board']       = $this->initialBoard;
        $session['currentTurn'] = $this->initialTurn;
        $response->assertStatus(200)->assertJson($session);
    }

    public function test_resetGame()
    {
        $session  = [
            'board'       => [['x', '', ''], ['', '', ''], ['', '', '']],
            'score'       => ['x' => 1, 'o' => 0],
            'currentTurn' => 'o',
            'victory'     => ''
        ];
        $response = $this->withSession($session)->delete('/api');
        $response->assertStatus(200)->assertJson($this->initialSession);
    }
}
