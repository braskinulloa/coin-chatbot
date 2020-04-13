<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';

    protected $fillable = [
        'message', 'user', 'from_bot'
    ];

    public $timestamps = true;

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
