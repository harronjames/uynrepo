<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Support\GermanExcerpt;
use App\Support\SchemaMarkup;
use Illuminate\Console\Command;

class MigrateLegacyPostsCommand extends Command
{
    protected $signature = 'migrate_legacy_posts {--dry-run : Änderungen nur anzeigen, nicht speichern}';

    protected $description = 'Leere meta_description und schema_json Felder bestehender Posts automatisch füllen';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updatedMeta = 0;
        $updatedSchema = 0;
        $skipped = 0;

        $this->info($dryRun ? 'Dry-run: es wird nichts gespeichert.' : 'Legacy-Posts werden aktualisiert …');

        Post::query()->orderBy('id')->chunkById(50, function ($posts) use ($dryRun, &$updatedMeta, &$updatedSchema, &$skipped): void {
            foreach ($posts as $post) {
                $dirty = false;

                if (trim((string) $post->meta_description) === '') {
                    $excerpt = GermanExcerpt::fromHtml((string) $post->content, 150);

                    if ($excerpt === '') {
                        $excerpt = GermanExcerpt::fromHtml((string) $post->title, 150);
                    }

                    if ($excerpt !== '') {
                        $post->meta_description = mb_substr($excerpt, 0, 160);
                        $dirty = true;
                        $updatedMeta++;
                        $this->line('  meta_description ← #' . $post->id . ' ' . $post->title);
                    }
                }

                if (trim((string) $post->schema_json) === '') {
                    $schema = SchemaMarkup::blogPostingFor($post);
                    $post->schema_json = json_encode(
                        $schema,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                    );
                    $dirty = true;
                    $updatedSchema++;
                    $this->line('  schema_json ← #' . $post->id . ' ' . $post->title);
                }

                if (! $dirty) {
                    $skipped++;
                    continue;
                }

                if (! $dryRun) {
                    $post->save();
                }
            }
        });

        $this->newLine();
        $this->info('meta_description gesetzt: ' . $updatedMeta);
        $this->info('schema_json gesetzt: ' . $updatedSchema);
        $this->info('unverändert: ' . $skipped);

        return self::SUCCESS;
    }
}
