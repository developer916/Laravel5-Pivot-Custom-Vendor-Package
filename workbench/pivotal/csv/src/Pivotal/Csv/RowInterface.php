<?php namespace Pivotal\Csv;

interface RowInterface
{

    /**
     * Return a new attribute clean model clone
     * @return RowInterface
     */
    public function getInstance();

    public function setHeaderRow(RowInterface $row);
}