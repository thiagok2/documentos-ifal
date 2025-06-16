<?php

namespace App\Searches\Commands;

use App\Searches\Queries\QueryBuilder;
use App\Searches\Models\QueryElastic;
use Elastic\Elasticsearch\ClientBuilder;
use App\Searches\Models\SearchResult;

class SearchCommandA0 implements ISearchCommand
{
    protected $queryBuilder;
    private $index;
    private $root;
    private $clientElastic;
    private $public;

    function __construct($_index, $_root, $_public = true) {
        $this->index = $_index;
        $this->root = $_root;
        $this->public = $_public;

        $this->queryBuilder = new QueryBuilder("ato");

        $hosts = [getenv('ELASTIC_URL')];
        $this->clientElastic = ClientBuilder::create()->setHosts($hosts)->build();
    }

    public function getArrayQuery(){
        return $this->queryBuilder->getQueryArray();
    }

    public function search($query, $filters, $from, $sizePage){
        $this->addExactMatch($query);
        $this->addBoolFilterExpressions($filters);


        $queryElastic = new QueryElastic(
            $this->queryBuilder->getQueryArray(), 
            $this->index,
            $this->root,
            $from,
            $sizePage
        );

        $elasticResult = $this->clientElastic->search($queryElastic->get());
        $aggs = $this->clientElastic->search($queryElastic->agg());

        return new SearchResult($elasticResult, $sizePage, $aggs);
    }

    private function addExactMatch($query){
        $this->queryBuilder
            ->addBoolShouldMatchPhrase($query, "ementa", 2.0, 0)
            ->addBoolShouldMatchPhrase($query, "titulo", 2.0, 0)
            ->addBoolShouldMatchPhrase($query, "tags", 2.0, 0)
            ->addBoolShouldMatchPhraseAttach($query, 2.0, 0);
    }

    private function checkHasFilter($filter, $arrayFilters){
        return array_key_exists($filter, $arrayFilters) 
            && $arrayFilters[$filter] != "all" 
            && isset($arrayFilters[$filter]);
    }

    private function addBoolFilterExpressions($filters){
        if ($this->public) {
            $this->queryBuilder->addBoolFilterTerm(true, "publico");
        } else {
            $this->queryBuilder->addBoolFilterTerm(false, "publico");
        }
        if (!$this->public && $this->checkHasFilter("orgao", $filters)) {
            $this->queryBuilder->addBoolFilterTerm($filters["orgao"], "fonte.orgao.keyword");
        }
        if ($this->checkHasFilter("tipo_doc", $filters))
            $this->queryBuilder->addBoolFilterTerm($filters["tipo_doc"], "tipo_doc");

        if ($this->checkHasFilter("esfera", $filters))
            $this->queryBuilder->addBoolFilterTerm($filters["esfera"], "fonte.esfera");

        if ($this->checkHasFilter("ano", $filters))
            $this->queryBuilder->addBoolFilterTerm($filters["ano"], "ano");

        if ($this->checkHasFilter("fonte", $filters))
            $this->queryBuilder->addBoolFilterTerm($filters["fonte"], "fonte.sigla");

        if ($this->checkHasFilter("periodo", $filters))
            $this->queryBuilder->addBoolFilterGte($filters["periodo"], "ano");
    }
}
