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
class SwaggerController extends AbstractController
{
    #[Route(
        path: '/swagger',
        name: 'get-swagger',
        methods: ['GET']
    )]
    public function __invoke(): BinaryFileResponse
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
        $openApi = Generator::scan([SRC_DIR]);

        $path = WWW_DIR.'/swagger.json';

        $openApi->saveAs($path, 'json');
    }
}
