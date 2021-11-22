<?php

    use Adianti\Database\TRecord;

    class Estado extends TRecord
    {
        const TABLENAME = 'estado';
        const PRIMARYKEY = 'id';
        const IDPOLUCY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('nome');
          parent::addAttribute('uf');
        }
    } 