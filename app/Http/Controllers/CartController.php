<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with(['game' => function($query) {
                $query->withTrashed(); // Include soft deleted games
            }])
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->game->id,
                    'title' => $item->game->title,
                    'thumbnail' => $item->game->thumbnail,
                    'slug' => $item->game->slug,
                    'platform' => $item->game->platform,
                    'genre' => $item->game->genre,
                    'price' => $item->price_at_time, // Use stored price
                    'stock' => $item->game->stock,
                    'quantity' => $item->quantity,
                    'description' => $item->game->description,
                    'subtotal' => $item->price_at_time * $item->quantity
                ];
            });

        $total = $cartItems->sum('subtotal');

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'total_items' => $cartItems->sum('quantity')
            ]
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $game = Game::findOrFail($request->game_id);
        
       
        if ($game->trashed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game tidak tersedia'
            ], 400);
        }
        

        if ($game->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'game_id' => $request->game_id
            ],
            [
                'quantity' => $request->quantity,
                'price_at_time' => $game->price 
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Game berhasil ditambahkan ke keranjang',
            'data' => [
                'id' => $game->id,
                'title' => $game->title,
                'thumbnail' => $game->thumbnail,
                'platform' => $game->platform,
                'genre' => $game->genre,
                'price' => $cartItem->price_at_time,
                'quantity' => $cartItem->quantity,
                'stock' => $game->stock
            ]
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $game = Game::withTrashed()->findOrFail($request->game_id);
        
    
        if ($game->trashed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game tidak tersedia'
            ], 400);
        }

        if ($game->stock < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('game_id', $request->game_id)
            ->firstOrFail();

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Quantity berhasil diupdate',
            'data' => [
                'quantity' => $cartItem->quantity,
                'subtotal' => $cartItem->price_at_time * $cartItem->quantity
            ]
        ]);
    }

    public function removeFromCart($gameId)
    {
        Cart::where('user_id', Auth::id())
            ->where('game_id', $gameId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil dihapus dari keranjang'
        ]);
    }

    public function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
}
