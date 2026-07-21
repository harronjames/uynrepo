<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\UpdateImpressumRequest;
use App\Models\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateImpressumController extends Controller
{
    public function __invoke(UpdateImpressumRequest $request)
    {
        $page = Page::query()->where('slug', 'impressum')->firstOrFail();
        $data = $request->validated();

        $removeImage = (bool) ($data['remove_image'] ?? false);
        unset($data['remove_image']);

        if ($removeImage) {
            $this->deleteStoredFile($page->image);
            $data['image'] = null;
        } elseif (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->deleteStoredFile($page->image);
            $path = $data['image']->store('impressum', 'public');
            $data['image'] = '/storage/' . ltrim($path, '/');
        } else {
            unset($data['image']);
        }

        $page->update($data);

        return redirect()
            ->route('admin.page.impressum.edit')
            ->with('success', 'Impressum saved.');
    }

    private function deleteStoredFile(?string $url): void
    {
        if (! $url) {
            return;
        }

        $prefix = '/storage/';
        $path = str_starts_with($url, $prefix)
            ? substr($url, strlen($prefix))
            : ltrim(parse_url($url, PHP_URL_PATH) ?: '', '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
