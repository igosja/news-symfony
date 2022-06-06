<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\LocaleSwitcher;

/**
 * Class AbstractController
 * @package App\Admin\Controller
 */
abstract class AbstractController extends BaseController
{
    protected int $pagesAroundCurrent = 4;
    protected int $itemsPerPage = 1;

    public function __construct(LocaleSwitcher $localeSwitcher)
    {
        $request = Request::createFromGlobals();
        if ($request->get('language')) {
            $localeSwitcher->setLocale($request->get('language'));
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return string[]
     */
    protected function getSortingCriteria(Request $request): array
    {
        $sort = $request->get('sort', 'id');
        $order = 'ASC';
        if ('-' === $sort[0]) {
            $order = 'DESC';
            $sort = substr($sort, 1);
        }

        return ['sort' => $sort, 'order' => $order];
    }

    /**
     * @param string $route
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return string
     */
    protected function getFilterUrl(string $route, Request $request): string
    {
        return $this->generateUrl($route, ['sort' => $request->get('sort')], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $route
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return string
     */
    protected function getPaginationUrl(string $route, Request $request): string
    {
        $params = $request->query->all();
        unset($params['page']);
        return $this->generateUrl($route, $params);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return int
     */
    protected function getOffset(Request $request): int
    {
        $page = $request->get('page', 1);
        return ($page - 1) * $this->itemsPerPage;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $totalCount
     * @return array
     */
    protected function getPaginationPages(Request $request, int $totalCount): array
    {
        $firstPage = $this->getFirstPage($request);
        $lastPage = $this->getLastPage($request, $totalCount);
        return range($firstPage, $lastPage);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return int
     */
    private function getFirstPage(Request $request): int
    {
        $page = $request->get('page', 1);
        $firstPage = $page - $this->pagesAroundCurrent;
        if ($firstPage < 0) {
            $firstPage = 1;
        }
        return $firstPage;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $totalCount
     * @return float|int|mixed
     */
    private function getLastPage(Request $request, int $totalCount)
    {
        $page = $request->get('page', 1);
        $lastPage = $page + $this->pagesAroundCurrent;
        $maxPage = ceil($totalCount / $this->itemsPerPage);
        if ($lastPage > ceil($totalCount / $this->itemsPerPage)) {
            $lastPage = $maxPage;
        }
        return $lastPage;
    }
}