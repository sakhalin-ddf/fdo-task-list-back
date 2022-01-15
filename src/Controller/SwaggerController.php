<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Generator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OpenApi\Annotations\Info(title="FDO Todo list", version="1.0.0")
 */
#[Route(
    path: '/swagger'
)]
class SwaggerController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'get-swagger',
        methods: ['GET']
    )]
    public function show(): BinaryFileResponse
    {
        $this->generateSwaggerJson();

        // load the template from the filesystem
        $file = new File(APP_DIR.'/templates/swagger.html');
        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    private function generateSwaggerJson(): void
    {
        $dir = APP_DIR;
        $openApi = Generator::scan(["{$dir}/src"]);

        $path = "{$dir}/public/swagger.json";

        $openApi->saveAs($path, 'json');
    }
}
