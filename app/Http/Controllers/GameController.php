<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class GameController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $games = Game::where('status', Game::Status['Active'])->get();
        return  Inertia::render('TicTacToe/Board', compact('games'));
    }

    public function show(Request $request, $id): \Inertia\Response
    {
        $game = Game::where('id', $id)->with('users')->first();
        return  Inertia::render('TicTacToe/Index', compact('game'));
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'boardTypeLabel' => 'required'
        ]);


        if ($validator->fails()){
            return response()->json([
                'status' => ResponseAlias::HTTP_NOT_ACCEPTABLE,
                'error' => $validator->getMessageBag()->getMessages()
            ]);
        }

        try {
            $game = Game::create([
                'type' => Game::GameType[$request->get('boardTypeLabel')],
                'status' => Game::Status['Active']
            ]);

            if (empty($game)){
                throw new Exception('Could not create game');
            }

            return response()->json([
                'status' => ResponseAlias::HTTP_OK,
                'data' => $game->load('users')
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => ResponseAlias::HTTP_BAD_REQUEST,
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function current_game(Request $request, $id): JsonResponse
    {
        $currentGame = Game::where('id', $id)->with('users')->first();
        return response()->json([
            'status' => ResponseAlias::HTTP_OK,
            'data' => $currentGame
        ]);
    }

    public function set_house(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'houseNo' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => ResponseAlias::HTTP_NOT_ACCEPTABLE,
                'error' => $validator->getMessageBag()->getMessages()
            ]);
        }

        try {
            $game = Game::where('id', $id)->first();
            if (empty($game)){
                throw new Exception('Game not found');
            }

            $gameU = DB::table('game_users')->insert([
                'user_id' => $request->user()->id,
                'game_id' => $id,
                'house_no' => $request->get('houseNo'),
                'recent_move' => $request->user()->id,
            ]);

            if (empty($gameU)){
                throw new Exception('Could not update game');
            }

            $game = Game::where('id', $id)->with('users')->first();;

            return response()->json([
                'status' => ResponseAlias::HTTP_OK,
                'data' => $game
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => ResponseAlias::HTTP_BAD_REQUEST,
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function status_update($id){
        try {
            $game = Game::where('id', $id)->with('users')->first();
            if (empty($game)){
                throw new Exception('Not found');
            }

            $gameU = $game->update([
                'status' => Game::Status['finished']
            ]);

            if (empty($gameU)){
                throw new Exception('Could not update status');
            }

            $game = $game->fresh();
            return response()->json([
                'status' => ResponseAlias::HTTP_OK,
                'data' => $game
            ]);
        } catch (Exception $exception){
            return response()->json([
                'status' => ResponseAlias::HTTP_BAD_REQUEST,
                'error' => $exception->getMessage()
            ]);
        }
    }
}
