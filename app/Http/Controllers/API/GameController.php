<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Game;


class GameController extends Controller
{
    public function index()
    {
        $games = Game::all();
        if ($games->isEmpty()) {
            $response['message'] = 'Tidak ada data game';
            $response['success'] = false;
            return response()->json(
                $response, Response::HTTP_NOT_FOUND
            );
        }
        $response['success'] = true;
        $response['message'] = 'Game Ditemukan.';
        $response['data'] = $games;
        return response()->json($response, Response::HTTP_OK);
        // atau
        // return response()->json($response, 200);
    }

    public function show($id)
    {
        $game = Game::find($id);
        if (!$game) {
            $response['message'] = 'Game tidak ditemukan';
            $response['success'] = false;
            return response()->json(
                $response, Response::HTTP_NOT_FOUND
            );
        }
        $response['success'] = true;
        $response['message'] = 'Game Ditemukan.';
        $response['data'] = $game;
        return response()->json($response, Response::HTTP_OK);
        // atau
        // return response()->json($response, 200);
    }
}
