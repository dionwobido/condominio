<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\Form\TCriteria;
use Adianti\Widget\Form\TEntry;
use Adianti\Database\Form\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
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

        //Adicionar os campos
        $this->form->addFields([new TLabel('Id')], [$id]);
        $this->form->addFields([new TLabel('Tipo')], [$tipo], [new TLabel('CPF/CNPJ')], [$codigo_nacional]);
        $this->form->addFields([new TLabel('Nome')], [$nome]);
        $this->form->addFields([new TLabel('Nome Fantasia')], [$nome_fantasia]);
        $this->form->addFields([new TLabel('Papel')], [$papel_id], [new TLabel('Grupo')], [$grupo_id]);
        $this->form->addFields([new TLabel('I. E.')], [$codigo_estadual], [new TLabel('I. M.')], [$codigo_municipal]);
        $this->form->addFields([new TLabel('Fone')], [$fone], [new TLabel('Email')], [$email]);
        $this->form->addFields([new TLabel('Papel')], [$papel_id], [new TLabel('Grupo')], [$grupo_id]);
        $this->form->addFields([new TLabel('Observação')], [$observacao]);

        $this->form->addFields([new TForm('Endereço')]);
        $this->form->addFields([new TLabel('CEP')], [$cep])->layout = ['col-sm-2 control-label', 'col-sm-4'];
        $this->form->addFields([new TLabel('Logradouro')], [$logradouro], [new TLabel('Número')], [$numero]);
        $this->form->addFields([new TLabel('Complemento')], [$complemento], [new TLabel('Bairro')], [$bairro]);
        $this->form->addFields([new TLabel('Estado')], [$estado_id], [new TLabel('Cidade')], [$cidade_id]);

        //setMask
        $fone->setMask('(99)99999-9999');
        $cep->setMask('99.999-999');

        //setSize
        $id->setsize('100%');
        $nome->setsize('100%');
        $nome_fantasia->setsize('100%');
        $tipo->setsize('100%');
        $codigo_nacional->setsize('100%');
        $codigo_estadual->setsize('100%');
        $codigo_municipal->setsize('100%');
        $fone->setsize('100%');
        $email->setsize('100%');
        $observacao->setsize('100%');
        $cep->setsize('100%');
        $logradouro->setsize('100%');
        $numero->setsize('100%');
        $complemento->setsize('100%');
        $bairro->setsize('100%');
        $cidade_id->setsize('100%');
        $grupo_id->setsize('100%');

        $id-> setEditable(FALSE);
        $nome->addValidation('Nome', new TRequiredValidator);
        $nome_fantasia->addValidation('Nome Fantasia', new TRequiredValidator);
        $tipo->addValidation('Tipo', new TRequiredValidator);
        $codigo_nacional->addValidation('Codigo Nacional', new TRequiredValidator);
        $grupo_id->addValidation('Grupo', new TRequiredValidator);
        $fone->addValidation('Fone', new TRequiredValidator);
        $email->addValidation('Email', new TRequiredValidator);
        $email->addValidation('Email', new TEmailValidator);
        $cidade_id->addValidation('Cidade', new TRequiredValidator);
        $cep->addValidation('Cep', new TRequiredValidator);
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $numero->addValidation('Numero', new TRequiredValidator);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save' );
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction([$this, 'onEdit']), 'fa:eraser red');

        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open('db_condominio');

            $this->form->validate();
            $data = $this->form->validate();

            $object = new Pessoa;
            $object->fromArray((array)$data);
            $object->store();

            PessoaPapel::where('pessoa_id', '=', $object->id)->delete();

            if ($data->$papel_id)
            {
                foreach ($data->$papel_id as $papel_id);
                {
                    $pp = new PessoaPapel();
                    $pp->pessoa_id = $object->id;
                    $pp->store();
                }
                
            }   
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();

            new TMessage('Info', AdiantiCoreTranslator::translate('Record Save'));
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
}