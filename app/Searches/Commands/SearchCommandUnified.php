<?php

namespace App\Searches\Commands;

use App\Searches\Models\QueryElastic;
use App\Searches\Models\SearchResult;
use App\Searches\Queries\QueryBuilder;
use Elastic\Elasticsearch\ClientBuilder;

class SearchCommandUnified implements ISearchCommand
{
    protected $queryBuilder;
    private $index;
    private $root;
    private $clientElastic;
    private $public;

    public function __construct($_index, $_root, $_public = true)
    {
        $this->index = $_index;
        $this->root = $_root;
        $this->public = $_public;

        $username = getenv('ELASTICSEARCH_USERNAME');
        $password = getenv('ELASTICSEARCH_PASSWORD');
        $hosts = [getenv('ELASTIC_URL')];

        $this->clientElastic = ClientBuilder::create()
            ->setHosts($hosts)
            ->setBasicAuthentication($username, $password)
            ->build();
    }

    public function search($query, $filters, $from, $sizePage, $exactPhraseOnly = false, $withAggregations = true)
    {
        $this->queryBuilder = new QueryBuilder('ato');

        if ($exactPhraseOnly) {
            $this->addExactMatch($query);
        } else {
            $this->addExactMatch($query);
            $this->addFuzzyMatch($query);
        }

        $this->addBoolFilterExpressions($filters);

        $queryElastic = new QueryElastic(
            $this->queryBuilder->getQueryArray(),
            $this->index,
            $this->root,
            $from,
            $sizePage
        );

        $elasticResult = $this->clientElastic->search($queryElastic->get());

        $aggs = ['aggregations' => []];
        if ($withAggregations) {
            $aggs = $this->clientElastic->search($queryElastic->agg());
        }

        return new SearchResult($elasticResult, $sizePage, $aggs);
    }

    private function addExactMatch($query)
    {
        $this->queryBuilder
            ->addBoolShouldMatchPhrase($query, 'ementa', 2.0, 0)
            ->addBoolShouldMatchPhrase($query, 'titulo', 2.0, 0)
            ->addBoolShouldMatchPhrase($query, 'tags', 2.0, 0)
            ->addBoolShouldMatchPhraseAttach($query, 2.0, 0);
    }

    private function addFuzzyMatch($query)
    {
        $this->queryBuilder
            ->addBoolShouldMatchPhrase($query, 'ementa', 1.5, 5)
            ->addBoolShouldMatchFuzziness($query, 'ementa', 1.5, 1, 3)
            ->addBoolShouldMatchPhrase($query, 'titulo', 1.5, 2)
            ->addBoolShouldMatchPhrase($query, 'tags', 1.5, 2)
            ->addBoolShouldMatchFuzziness($query, 'tags', 1.5, 1, 3)
            ->addBoolShouldMatchPhraseAttach($query, 1.25, 5)
            ->addBoolShouldMatchFuzzinessAttach($query, 1, '1', 3);
    }

    private function checkHasFilter($filter, $arrayFilters)
    {
        return array_key_exists($filter, $arrayFilters)
            && $arrayFilters[$filter] != 'all'
            && isset($arrayFilters[$filter]);
    }

    private function addBoolFilterExpressions($filters)
    {
        if ($this->public) {
            $this->queryBuilder->addBoolFilterTerm(true, 'publico');
        } else {
            $this->queryBuilder->addBoolFilterTerm(false, 'publico');
        }

        if (!$this->public && $this->checkHasFilter('orgao', $filters)) {
            $this->queryBuilder->addBoolFilterTerm($filters['orgao'], 'fonte.orgao.keyword');
        }

        if ($this->checkHasFilter('tipo_doc', $filters)) {
            $this->queryBuilder->addBoolFilterTerm($filters['tipo_doc'], 'tipo_doc.keyword');
        }

        if ($this->checkHasFilter('esfera', $filters)) {
            $this->queryBuilder->addBoolFilterTerm($filters['esfera'], 'fonte.esfera.keyword');
        }

        if ($this->checkHasFilter('ano', $filters)) {
            $this->queryBuilder->addBoolFilterTerm($filters['ano'], 'ano.keyword');
        }

        if ($this->checkHasFilter('fonte', $filters)) {
            $this->queryBuilder->addBoolFilterTerm($filters['fonte'], 'fonte.sigla.keyword');
        }

        if ($this->checkHasFilter('periodo', $filters)) {
            $this->queryBuilder->addBoolFilterGte($filters['periodo'], 'ano');
        }
    }
}
