<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'module',
        'record_id',
        'old_values_json',
        'new_values_json',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values_json' => 'array',
        'new_values_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction(
        string $action,
        string $module,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'role' => $user?->role ?? 'system',
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values_json' => $oldValues,
            'new_values_json' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
