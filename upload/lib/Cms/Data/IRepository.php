<?php


namespace Cms\Data;


interface IRepository
{
    public function exists($id);
    public function getById($id);
}