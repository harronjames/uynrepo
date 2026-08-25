<?php

namespace App\Models;

use App\Models\Concerns\HasSeoMeta;
use App\Models\Concerns\HasUniqueSlug;
use App\Support\HtmlSanitizer;
use Carbon\Carbon;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int         $id
 * @property string      $title
 * @property string      $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $schema_json
 * @property string      $content
 * @property string|null $preview_image
 * @property string|null $main_image
 * @property string      $status
 * @property Carbon|null $published_at
 * @property int|null    $queue_position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read string|null $card_image
 * @property-read string $human_read_time
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, User> $likedUsers
 * @property-read int|null $liked_users_count
 * @property-read Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static PostFactory  factory($count = null, $state = [])
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereContent($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereDeletedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereMainImage($value)
 * @method static Builder|Post whereMetaDescription($value)
 * @method static Builder|Post whereMetaKeywords($value)
 * @method static Builder|Post whereMetaTitle($value)
 * @method static Builder|Post wherePreviewImage($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post onlyTrashed()
 * @method static Builder|Post withTrashed()
 * @method static Builder|Post withoutTrashed()
 */
class Post extends Model
{
    use HasFactory;
    use HasSeoMeta;
    use HasUniqueSlug;
    use SoftDeletes;

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_DRAFT = 'draft';

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'schema_json',
        'preview_image',
        'main_image',
        'status',
        'published_at',
        'queue_position',
    ];

    protected $withCount = ['likedUsers'];

    protected $casts = [
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
        'published_at'   => 'datetime',
        'queue_position' => 'integer',
    ];

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePublishedStatus(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now());
    }

    public function displayDate(): Carbon
    {
        return $this->published_at ?? $this->created_at ?? now();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'Veröffentlicht',
            self::STATUS_SCHEDULED => 'Geplant',
            self::STATUS_DRAFT     => 'Entwurf',
            default                => (string) $this->status,
        };
    }

    public function publishCountdown(): ?string
    {
        if ($this->status !== self::STATUS_SCHEDULED || $this->published_at === null) {
            return null;
        }

        if ($this->published_at->isPast()) {
            return 'Veröffentlichungszeit erreicht (Cron ausstehend)';
        }

        $when = $this->published_at->copy()->timezone(config('publish_queue.timezone', config('app.timezone')));

        return $when->locale('de')->translatedFormat('d. M Y, H:i') . ' (' . $when->locale('de')->diffForHumans() . ')';
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id');
    }

    public function likedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function shortBody(int $words = 20): string
    {
        return Str::words(strip_tags((string) $this->content), $words);
    }

    public function safeContent(): string
    {
        return HtmlSanitizer::clean((string) $this->content);
    }

    /**
     * Small list/card image: dedicated preview if set, otherwise the detail cover.
     */
    public function cardImage(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->preview_image ?: $this->main_image);
    }

    public function getFormattedDate(): string
    {
        return $this->displayDate()->format('F jS Y');
    }

    public function humanReadTime(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes): string {
                $words   = Str::wordCount(strip_tags((string) $attributes['content']));
                $minutes = ceil($words / 200);

                return $minutes . ' ' . str('min')->plural($minutes) . ', '
                    . $words . ' ' . str('word')->plural($words);
            }
        );
    }
}
