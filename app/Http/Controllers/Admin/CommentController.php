<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Комментарий успешно удалён.');
    }

    public function toggle(Comment $comment): RedirectResponse
    {
        $comment->update([
            'is_approved' => !$comment->is_approved,
        ]);

        $status = $comment->is_approved ? 'опубликован' : 'скрыт';

        return back()->with('success', "Комментарий {$status}.");
    }
}
