<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\LetterType;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use App\Enums\Config as ConfigEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Letter extends Model
{
    use HasFactory, LogsActivity;

    //Function to get activity logs
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            -> logOnly(['text'])
            -> logFillable()
            -> useLogName('Letters');
    }

    //Function to track user is doing
    public function getDescriptionForEvent(string $eventName): string
    {
        return $this->name . " tetter {$eventName} by " . Auth::user()->name;
    }

    /**
     * @var string[]
     */
    protected $fillable = [
        'reference_number',
        'division',
        'from',
        'to',
        'letter_date',
        'received_date',
        'description',
        'note',
        'type',
        'classification_code',
        'user_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'letter_date' => 'date',
        'received_date' => 'date',
    ];

    protected $appends = [
        'formatted_letter_date',
        'formatted_received_date',
        'formatted_created_at',
        'formatted_updated_at',
    ];

    public function getFormattedLetterDateAttribute(): string {
        return Carbon::parse($this->letter_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function getFormattedReceivedDateAttribute(): string {
        return Carbon::parse($this->received_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function getFormattedCreatedAtAttribute(): string {
        return $this->created_at->isoFormat('dddd, D MMMM YYYY, HH:mm:ss');
    }

    public function getFormattedUpdatedAtAttribute(): string {
        return $this->updated_at->isoFormat('dddd, D MMMM YYYY, HH:mm:ss');
    }

    public function scopeType($query, LetterType $type)
    {
        return $query->where('type', $type->type());
    }

    public function scopeIncoming($query)
    {
        return $this->scopeType($query, LetterType::INCOMING);
    }

    public function scopeOutgoing($query)
    {
        return $this->scopeType($query, LetterType::OUTGOING);
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
                ->where('reference_number', $find)
                ->orWhere('division', $find)
                ->orWhere('from', 'LIKE', $find . '%')
                ->orWhere('to', 'LIKE', $find . '%');
        });
    }

    public function scopeRender($query, $search)
    {
        $divisionId = auth()->user()->division;
        $userRole = auth()->user()->role;
        if ($userRole == 'Admin' || $userRole == 'Ketua P3MP') {
            return $query
                ->with(['attachments', 'classification'])
                ->search($search)
                ->latest('letter_date')
                ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
                ->appends([
                    'search' => $search,
                ]);
        } else {
            return $query
                ->with(['attachments', 'classification'])
                ->search($search)
                ->latest('letter_date')
                ->where('division', $divisionId)
                ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
                ->appends([
                    'search' => $search,
                ]);
        }
        // return $query
        //     ->with(['attachments', 'classification'])
        //     ->search($search)
        //     ->latest('letter_date')
        //     ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
        //     ->appends([
        //         'search' => $search,
        //     ]);
    }

    // public function scopeAgenda($query, $since, $until, $filter)
    // {
    //     return $query
    //         ->when($since && $until && $filter, function ($query, $condition) use ($since, $until, $filter) {
    //             return $query->whereBetween(DB::raw('DATE(' . $filter . ')'), [$since, $until]);
    //         });
    // }

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
    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class, 'classification_code', 'code');
    }

    /**
     * @return HasMany
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'letter_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'letter_id', 'id');
    }
}
