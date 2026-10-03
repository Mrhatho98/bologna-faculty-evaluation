<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'from_status',
        'to_status',
        'user_id',
        'comments',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
