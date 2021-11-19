<?php

    class Grupo extends TRecord
    {
        const TABLENAME = 'grupo';
        const PRIMARYKEY = 'id';
        const IDPOLUCY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('id');
          parent::addAttribute('nome');
        }
    } 