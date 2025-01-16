<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class WishlistController extends Controller
{
    /**
     * ambil wishlist user
     * 
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWishlist($userId)
    {
        try {
          
            if (Auth::id() != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

      
            $wishlistItems = Wishlist::where('user_id', $userId)
                ->with(['game' => function ($query) {
                    $query->select('id', 'title', 'thumbnail', 'slug', 'platform', 'genre', 'price');
                }])
                ->get()
                ->map(function ($item) {
                    return $item->game;
                });

            return response()->json([
                'success' => true,
                'data' => $wishlistItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching wishlist'
            ], 500);
        }
    }

    /**
     * tambah game ke wishlist
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToWishlist(Request $request)
    {
        try {
            $request->validate([
                'game_id' => 'required|exists:games,id'
            ]);

            $userId = Auth::id();
            
            
            $exists = Wishlist::where('user_id', $userId)
                ->where('game_id', $request->game_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Game already in wishlist'
                ], 400);
            }

            
            Wishlist::create([
                'user_id' => $userId,
                'game_id' => $request->game_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Game added to wishlist'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding to wishlist'
            ], 500);
        }
    }

    /**
     * hapus dari wishlist
     * 
     * @param int $userId
     * @param int $gameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromWishlist($userId, $gameId)
    {
        try {
           
            if (Auth::id() != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

         
            $deleted = Wishlist::where('user_id', $userId)
                ->where('game_id', $gameId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Game not found in wishlist'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Game removed from wishlist'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing from wishlist'
            ], 500);
        }
    }

    /**
     * pengecekan game sudah di wishlist
     * 
     * @param int $userId
     * @param int $gameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkWishlist($userId, $gameId)
    {
        try {
            $exists = Wishlist::where('user_id', $userId)
                ->where('game_id', $gameId)
                ->exists();

            return response()->json([
                'success' => true,
                'inWishlist' => $exists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking wishlist'
            ], 500);
        }
    }
}