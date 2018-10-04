<?php

namespace App\Controller;

use RecipePuppyRecipesProviderFactory;
use rhp\services\searchRecipesService\SearchRecipesService;
use rhp\services\searchRecipesService\SearchRecipesServiceRequestFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchRecipesController extends Controller
{
    private $serviceRequestFactory;
    private $serviceRequest;
    private $service;

    public function __construct()
    {
        $this->serviceRequestFactory = new SearchRecipesServiceRequestFactory();
        $this->service = new SearchRecipesService();
    }

    public function index(Request $request)
    {
        $this->buildServiceRequest($request);
        $this->executeService();
        $recipes = $this->getRecipes();

        return $this->json($recipes);
    }

    private function buildServiceRequest(Request $request): void
    {
        $provider = $this->loadProvider($request);
        $query = $this->loadQuery($request);
        $page = $this->loadPage($request);
        $this->serviceRequest = $this->serviceRequestFactory->build($query, $page, $provider);
    }

    private function loadProvider(Request $request): string
    {
        $provider = $request->query->get('provider');
        if (empty($provider)) {
            $provider = '';
        }

        return $provider;
    }

    private function loadQuery(Request $request): string
    {
        $query = $request->query->get('query');
        if (empty($query)) {
            $query = '';
        }

        return $query;
    }

    private function loadPage(Request $request): int
    {
        $page = $request->query->get('page');
        if (empty($page)) {
            $page = 1;
        }

        return (int)$page;
    }

    private function executeService(): void
    {
        $this->service->execute($this->serviceRequest);
    }

    private function getRecipes(): array
    {
        $serviceResponse = $this->service->getServiceResponse();
        $recipes = $serviceResponse->getRecipes();

        return $recipes;
    }

}