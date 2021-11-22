<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\Form\TCriteria;
use Adianti\Widget\Form\TEntry;
use Adianti\Database\Form\TFilter;
use Adianti\Widget\Wrapper\TCombo;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TText;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class PessoaForm extends TWindow
{
    protected $form;

    public function __construct($param)
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::removePadding();
        parent::removeTitleBar();

        //criar form
        $this->form = new BootstrapFormBuilder('form_Pessoa');
        $this->form->setFormTitle('Pessoa');
        $this->form->setProperty('style', 'margin:0; border:0');
        $this->form->setClientValidation(true);

        //criar campos
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $nome_fantasia = new TEntry('nome_fantasia');
        $tipo = new TCombo('tipo');
        $codigo_nacional = new TEntry('codigo_nacional');
        $codigo_estadual = new TEntry('codigo_estadual');
        $codigo_municipal = new TEntry('codigo_municipal');
        $fone = new TEntry('fone');
        $email = new TEntry('email');
        $observacao = new TText('observacao');
        $cep = new TEntry('cep');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');

        $filter = new TCriteria;
        $filter->add(new TFilter('id', '<', '0'));
        $cidade_id = new TDBCombo('cidade_id', 'db_condominio', 'Cidade', 'id', 'nome','nome', $filter);
        $grupo_id = new TDBUniqueSearch('grupo_id', 'db_condominio', 'Grupo', 'id', 'nome');
        $papel_id = new TDBUniqueSearch('grupo_id', 'db_condominio', 'Papel', 'id', 'nome');
        $estado_id = new TDBCombo('estado_id', 'db_condominio', 'Estado', 'id', '{nome} {uf}');

        $estado_id->setChangeAction(new TAction($this, 'onChangeEstado'));
        $cep->setExitAction(new TAction($this, 'onExitCep'));
        $codigo_nacional->setExitAction(new TAction($this, 'onExitCNPJ'));

        $cidade_id->enableSearch();
        $estado_id->enableSearch();
        $grupo_id->setMiniLength(0);
        $papel_id->setMiniLength(0);
        $papel_id->setsize('100%', 60);
        $observacao->setsize('100%', 60);
        $tipo->addItems(['F' => 'Fisica', 'J' => 'Juridica']);



    }
}