<?php

namespace Terranet\Administrator\Controllers;

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsManager;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Symfony\Component\HttpFoundation\Response;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Media\File;
use Terranet\Administrator\Middleware\ProtectMedia;
use Terranet\Administrator\Middleware\SanitizePaths;
use Terranet\Administrator\Services\FileStorage;

class MediaController extends AdminController
{
    /**
     * @var BreadcrumbsManager
     */
    protected $breadcrumbs;

    /**
     * @var FileStorage
     */
    protected $storage;

    /**
     * MediaController constructor.
     *
     * @param FileStorage $storage
     * @param Translator $translator
     */
    public function __construct(FileStorage $storage, Translator $translator)
    {
        parent::__construct($translator);

        $this->middleware([
            ProtectMedia::class,
            SanitizePaths::class,
        ]);

        $this->initBreadcrumbs();
        $this->storage = $storage;
    }

    public function popup(Request $request)
    {
        return $this->index($request, true);
    }

    public function index(Request $request, $popup = false)
    {
        $directory = $this->storage->path(
            $path = $request->get('path')
        );

        $files = $this->storage
            ->files($directory)
            ->when($popup, function ($files) {
                return $files->filter->isImage();
            })
            ->merge($this->storage->directories($directory));

        $breadcrumbs = $this->breadcrumbs($directory, $popup);

        return view(
            app('scaffold.template')->media('index'),
            compact('files', 'path', 'breadcrumbs', 'popup')
        );
    }

    public function mkdir(Request $request)
    {
        try {
            $directory = $this->storage->mkdir(
                $name = $request->get('name'),
                $request->get('basedir')
            );

            return response()->json([
                'message' => 'Directory created.',
                'data' => (new File($directory, $this->storage))->toArray(),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function move(Request $request)
    {
        $target = $request->get('target');
        $files = array_filter($request->get('files'), function ($file) use ($target) {
            return $file !== $target;
        });

        $this->storage->move($files, $target, $request->get('basedir'));

        return response()->json([], Response::HTTP_OK);
    }

    public function rename(Request $request)
    {
        try {
            $path = $this->storage->rename($request->get('from'), $request->get('to'));

            return response()->json([
                'message' => 'File renamed.',
                'file' => (new File($path, $this->storage))->toArray(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function upload(Request $request)
    {
        $path = $request->get('path');

        try {
            $path = $this->storage->upload($request->allFiles(), $path);

            return response()->json([
                'file' => $path,
                'location' => $path['url'], // duplicate for TinyMCE
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([], Response::HTTP_FOUND);
        }
    }

    public function removeSelected(Request $request)
    {
        $this->storage->delete($request->get('files'), $request->get('directories'));

        return response()->json([
            'message' => 'Files removed successfully.',
        ], Response::HTTP_NO_CONTENT);
    }

    protected function breadcrumbs($directory, $popup = false)
    {
        $directory = str_replace($this->storage->path(), '', $directory);

        // remove storage path from $directory
        $directory = implode('/', \array_slice(explode('/', $directory), 1));

        $this->breadcrumbs->register('index', function (BreadcrumbsGenerator $generator) use ($popup) {
            $generator->push('Home', route('scaffold.media'.($popup ? '.popup' : '')));
        });

        $dirs = $directory ? explode('/', trim($directory, '/')) : [];
        $parent = $section = 'index';
        $path[] = 'index';

        foreach ($dirs as $index => $dir) {
            $tmpPath = $path;
            $path[] = $dir;
            $this->breadcrumbs->register($section = implode('.', $path), function (BreadcrumbsGenerator $generator) use (&$parent, $dir, $tmpPath, $dirs) {
                $generator->parent($parent = implode('.', $tmpPath));
                $generator->push($dir, route('scaffold.media', ['path' => implode('/', \array_slice($dirs, 0, -1))]));
            });
        }

        return $this->breadcrumbs->view('administrator::partials.breadcrumbs', $section);
    }

    protected function initBreadcrumbs()
    {
        $this->breadcrumbs = app(BreadcrumbsManager::class);
    }
}
