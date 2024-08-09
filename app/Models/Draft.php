<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Draft extends Model
{

    protected $fillable = ['content', 'poster_id'];
    public $timestamps = true;

    protected $casts = [
        'content' => 'array',
    ];


    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function getFirstDeadlineAttribute(): string
    {
        $content = $this->attributes['content'] ? json_decode($this->attributes['content'], true) : null;

        if ($content && !empty($content['deadlines'])) {
            $deadlines = array_values($content['deadlines']);
            usort($deadlines, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $futureDeadlines = array_filter($deadlines, function ($deadline) {
                return Carbon::parse($deadline['date'])->isAfter(today());
            });

            if (!empty($futureDeadlines)) {
                $firstFutureDeadline = reset($futureDeadlines);

                if ($firstFutureDeadline['continuous'] == 1) {
                    return 'Continu';
                } else {
                    return Carbon::parse($firstFutureDeadline['date'])->format('d/m/Y') . '|' . $firstFutureDeadline['proof'];
                }
            } else {
                $lastDeadline = end($deadlines);
                if ($lastDeadline['continuous'] == 1) {
                    return 'Continu';
                } else {
                    return Carbon::parse($lastDeadline['date'])->format('d/m/Y');
                }
            }
        } else {
            return "No deadline";
        }
    }
}
