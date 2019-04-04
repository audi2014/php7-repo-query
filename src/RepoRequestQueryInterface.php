<?php
/**
 * Created by PhpStorm.
 * User: arturmich
 * Date: 2/27/19
 * Time: 11:24 AM
 */

namespace Audi2014\RepoRequestQuery;

use Audi2014\RequestQuery\RequestQueryInterface;
use Audi2014\RequestQuery\RequestQueryPageInterface;
use Audi2014\Repo\RepoInterface;

interface RepoRequestQueryInterface extends RepoInterface {

    public function fetchQueryPageItems(RequestQueryPageInterface $query): array;

    public function fetchQueryItems(RequestQueryInterface $query): array;

    public function deleteQueryItems(RequestQueryInterface $query): int;

    public function fetchQueryItem(RequestQueryInterface $query);

    /**
     * @param RequestQueryInterface $query
     * @return int
     * @throws \Exception
     */
    public function fetchQueryCount(RequestQueryInterface $query): int;

}