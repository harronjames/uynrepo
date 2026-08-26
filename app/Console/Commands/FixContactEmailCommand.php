<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Console\Command;

class FixContactEmailCommand extends Command
{
    protected $signature = 'content:fix-contact-email
                            {--dry-run : Değişiklikleri göster, kaydetme}';

    protected $description = 'Eski info@umzugland.at adreslerini office@umzugland.at ile değiştir';

    private const OLD_EMAIL = 'info@umzugland.at';

    private const NEW_EMAIL = 'office@umzugland.at';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry-run modu – veritabanına yazılmayacak.');
        }

        $updated = 0;

        Post::query()
            ->where(function ($query): void {
                $query->where('content', 'like', '%' . self::OLD_EMAIL . '%')
                    ->orWhere('meta_description', 'like', '%' . self::OLD_EMAIL . '%')
                    ->orWhere('meta_keywords', 'like', '%' . self::OLD_EMAIL . '%');
            })
            ->orderBy('id')
            ->each(function (Post $post) use ($dryRun, &$updated): void {
                $this->replaceInModel($post, ['content', 'meta_description', 'meta_keywords'], $dryRun, $updated, "Post #{$post->id}");
            });

        Page::query()
            ->where(function ($query): void {
                $query->where('content', 'like', '%' . self::OLD_EMAIL . '%')
                    ->orWhere('meta_description', 'like', '%' . self::OLD_EMAIL . '%')
                    ->orWhere('meta_keywords', 'like', '%' . self::OLD_EMAIL . '%');
            })
            ->orderBy('id')
            ->each(function (Page $page) use ($dryRun, &$updated): void {
                $this->replaceInModel($page, ['content', 'meta_description', 'meta_keywords'], $dryRun, $updated, "Page #{$page->id} ({$page->slug})");
            });

        $this->newLine();
        $this->info($dryRun ? "Güncellenecek kayıt: {$updated}" : "Güncellenen kayıt: {$updated}");

        return self::SUCCESS;
    }

    /**
     * @param  list<string>  $fields
     */
    private function replaceInModel(Post|Page $model, array $fields, bool $dryRun, int &$updated, string $label): void
    {
        $changes = [];

        foreach ($fields as $field) {
            $value = $model->{$field};

            if (is_string($value) && str_contains($value, self::OLD_EMAIL)) {
                $changes[$field] = str_replace(self::OLD_EMAIL, self::NEW_EMAIL, $value);
            }
        }

        if ($changes === []) {
            return;
        }

        $this->line("  {$label}");

        if (! $dryRun) {
            $model->fill($changes);
            $model->save();
        }

        $updated++;
    }
}
