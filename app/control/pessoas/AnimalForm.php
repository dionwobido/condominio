<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class AnimalForm extends TPage
{
    protected $form;

    use \Adianti\Base\AdiantiStandardFormTrait;

    function __construct()
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['AnimalList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('db_condominio');
        $this->setActiveRecord('Animal');

        $this->form = new BootstrapFormBuilder('Animal');
        $this->form->setFormTitle('Animal');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8']);

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $observacao = new TEntry('observacao');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'db_condominio', 'Pessoa', 'id', 'nome');
        //$estado_id->setMinLength(0);
        //$estado_id->setMask('{nome} ({pessoa})');//confirmar o pessoa

        $this->form->addFields([ new TLabel('Id')], [$id]);
        $this->form->addFields([ new TLabel('Nome')], [$nome]);
        $this->form->addFields([ new TLabel('Observação')], [$observacao]);
        $this->form->addFields([ new TLabel('Pessoa')], [$pessoa_id]);

        $nome->addValidation('Nome', new TRequiredValidator);
        //$observacao->addValidation('Observação', new TRequiredValidator);
        $pessoa_id->addValidation('Pessoa', new TRequiredValidator);

        $id->setSize('100%');
        $nome->setSize('100%');
        $observacao->setSize('100%');
        $pessoa_id->setSize('100%');

        $id->setEditable(FALSE);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save' );
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction([$this, 'onEdit']), 'fa:eraser red');

        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);

    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}