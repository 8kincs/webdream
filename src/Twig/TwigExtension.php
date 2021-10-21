<?php

namespace App\Twig;

use App\Entity\Product;
use App\Service\ProductService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function __construct(private ProductService $productService)
    {
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('product_properties', [$this, 'productProperties']),
        ];
    }

    public function productProperties(Product $product): string
    {
        return $this->productService->getProductProperties($product);
    }
}
