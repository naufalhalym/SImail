<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disposition extends Model
{
    use HasFactory, LogsActivity;

    //Function to get activity logs
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            -> logOnly(['text'])
            -> logFillable()
            -> useLogName('Dispositions');
    }

    //Function to track user is doing
    public function getDescriptionForEvent(string $eventName): string
    {
        return $this->name . " dispotition {$eventName} by " . Auth::user()->name;
    }

    protected $fillable = [
        'to',
        'due_date',
        'content',
        'note',
        'letter_status',
        'letter_id',
        'user_id'
    ];

    protected $appends = [
        'formatted_due_date',
    ];

    public function getFormattedDueDateAttribute(): string {
        return Carbon::parse($this->due_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', now()->addDays(-1));
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function($query, $find) {
            return $query
                ->orWhere('content', 'LIKE', '%' . $find . '%')
                ->orWhere('to', 'LIKE', $find . '%');
        });
    }

    public function scopeRender($query, Letter $letter, $search)
    {
        $pageSize = Config::code(\App\Enums\Config::PAGE_SIZE)->first();
        return $query
            ->with(['user', 'status', 'letter'])
            ->search($search)
            ->when($letter, function ($query, $letter) {
                return $query
                    ->where('letter_id', $letter->id);
            })
            ->latest('created_at')
            ->paginate($pageSize->value)
            ->appends([
                'search' => $search,
            ]);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(LetterStatus::class, 'letter_status', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class, 'letter_id', 'id');
    }
}
