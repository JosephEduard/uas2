<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function initiateCheckout(Request $request)
    {
        try {
            
            $cartItems = Cart::with('game')
                ->where('user_id', Auth::id())
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 400);
            }

            
            foreach ($cartItems as $item) {
                if ($item->quantity > $item->game->stock) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Not enough stock for {$item->game->title}"
                    ], 400);
                }
            }

            
            $total = $cartItems->sum(function ($item) {
                return $item->game->price * $item->quantity;
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $cartItems,
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate checkout'
            ], 500);
        }
    }

    public function confirmCheckout(Request $request)
    {
        try {
            DB::beginTransaction();

            
            $cartItems = Cart::with('game')
                ->where('user_id', Auth::id())
                ->get();

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 400);
            }

            
            foreach ($cartItems as $item) {
                if ($item->quantity > $item->game->stock) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Not enough stock for {$item->game->title}"
                    ], 400);
                }
            }


            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $cartItems->sum(function ($item) {
                    return $item->game->price * $item->quantity;
                }),
                'status' => 'completed'
            ]);

            
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'game_id' => $item->game_id,
                    'quantity' => $item->quantity,
                    'price' => $item->game->price
                ]);

                
                $item->game->decrement('stock', $item->quantity);
            }

            
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to complete checkout'
            ], 500);
        }
    }
}