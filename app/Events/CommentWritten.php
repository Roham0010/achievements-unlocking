<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentWritten
{
    use Dispatchable, SerializesModels;

    const TYPE = 'comment';

    public Comment $comment;
    public User $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->user = $comment->user;
    }
}
