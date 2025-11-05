<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/notifications/{user_id}",
     *   tags={"Notifications"},
     *   summary="Lister les notifications d'un utilisateur",
     *   @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function indexByUser(string $user_id)
    {
        return response()->json(
            Notification::where('user_id', $user_id)->orderByDesc('id')->paginate(50)
        );
    }

    /**
     * @OA\Put(
     *   path="/api/notifications/{id}/read",
     *   tags={"Notifications"},
     *   summary="Marquer une notification comme lue",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function markAsRead(string $id)
    {
        $n = Notification::findOrFail($id);
        $n->update(['lu' => true]);
        return response()->json(['message' => 'Notification marked as read']);
    }
}
