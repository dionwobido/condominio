<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class EleicaoList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;

    use \Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('db_condominio');
        $this->setActiveRecord('Eleicao');
        $this->setDefaultOrder('id', 'asc');
        $this->setOrderCommand('pessoa->nome', '(SELECT nome FROM pessoa WHERE id=eleicao.pessoa_id)');
        $this->setLimit(10);

        $this->addFilterField('id', '=','id');
        $this->addFilterField('pessoa_id', '=','pessoa_id');
        $this->addFilterField('papel_id', '=','papel_id');
        //$this->addFilterField('data_inicio', '=','data_inicio');
        //$this->addFilterField('data_fim', '=','data_fim');
        //$this->addFilterField('observacao', 'like','observacao');

        
        $this->form = new BootstrapFormBuilder('form_search_Eleicao');
        $this->form->setFormTitle('Eleição');

        $id = new TEntry('id');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'db_condominio', 'Pessoa', 'id', 'pessoa->nome');
        $pessoa_id->setMinLength(0);
        $papel_id = new TDBUniqueSearch('papel_id', 'db_condominio', 'Papel', 'id', 'papel->nome');
        $data_inicio = new TEntry('data_inicio');//TDate
        $data_fim = new TEntry('data_fim');//TDate
        $observacao = new TEntry('observacao');
        //$estado_id->setMinLength(0);
        //$estado_id->setMask('{nome} ({uf})');

        $this->form->addFields([new TLabel('Id')], [$id]);
        $this->form->addFields([new TLabel('Pessoa')], [$pessoa_id]);
        $this->form->addFields([new TLabel('Papel')], [$papel_id]);
        $this->form->addFields([new TLabel('Data Início')], [$data_inicio]);
        $this->form->addFields([new TLabel('Data Fim')], [$data_fim]);
        $this->form->addFields([new TLabel('Observação')], [$observacao]);

        $this->form->setData(TSession::getValue(__CLASS__.'_filter_data_'));

        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['EleicaoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');

        //Cria datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width 100%';

        //cria as colunas
        $column_id = new TDataGridColumn('id', 'Id', 'center', '10%');
        $column_pessoa_id = new TDataGridColumn('{pessoa->nome}', 'Pessoa', 'left');
        $column_papel_id = new TDataGridColumn('{papel->nome}', 'Papel', 'left');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Data Inicio', 'left',);
        $column_data_fim = new TDataGridColumn('data_fim', 'Data Fim', 'left',);
        $column_observacao = new TDataGridColumn('observacao', 'Observação', 'left');
        
        $column_pessoa_id->enableAutoHide(500);
        $column_papel_id->enableAutoHide(500);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_pessoa_id);
        $this->datagrid->addColumn($column_papel_id);
        $this->datagrid->addColumn($column_data_inicio);
        $this->datagrid->addColumn($column_data_fim);
        $this->datagrid->addColumn($column_observacao);

        $column_papel_id->setTransformer(function($value, $object, $row) { 
            $lbl = new TLabel(''); 
            if ($value == 'Proprietário(a)') { 
                $lbl->setValue('Proprietário(a)'); 
                $lbl->class = 'label label-primary'; 
            } 
            elseif ($value == 'Inquilino(a)') { 
                $lbl->setValue('Inquilino(a)'); 
                $lbl->class = 'label label-secondary'; 
            }  
            elseif ($value == 'Sindico(a)') { 
                $lbl->setValue('Sindico(a)'); 
                $lbl->class = 'label label-success'; 
            }
            elseif ($value == 'Conselho Fiscal') { 
                $lbl->setValue('Conselho Fiscal'); 
                $lbl->class = 'label label-danger'; 
            }
            elseif ($value == 'Brigada de Incêncio') { 
                $lbl->setValue('Brigada de Incêncio'); 
                $lbl->class = 'label label-warning'; 
            }
            elseif ($value == 'Fornecedor') { 
                $lbl->setValue('Fornecedor'); 
                $lbl->class = 'label label-info'; 
            }
            elseif ($value == 'Funcionário(a) Particular') { 
                $lbl->setValue('Funcionário(a) Particular'); 
                $lbl->class = 'label label-light'; 
            }
            elseif ($value == 'Funcionário(a) Tercerizado') { 
                $lbl->setValue('Funcionário(a) Tercerizado'); 
                $lbl->class = 'label label-dark'; 
            }
            elseif ($value == 'Empresa Tercerizado') { 
                $lbl->setValue('Empresa Tercerizado'); 
                $lbl->class = 'label label-dark'; 
            }
            return $lbl; 
            });

        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $column_pessoa_id->setAction(new TAction([$this, 'onReload']), ['order' => 'pessoa->nome']);
        $column_papel_id->setAction(new TAction([$this, 'onReload']), ['order' => 'papel->nome']);

        $action1 = new TDataGridAction(['EleicaoForm', 'onEdit'], ['id' =>'{id}', 'register_start' =>'false']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id' =>'{id}']);

        //Converte data inicio no datagrid
        $column_data_inicio->setTransformer(function($value){
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $column_data_fim->setTransformer(function($value){
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });


        $this->datagrid->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->datagrid->addAction($action2, _t('Delete'), 'far:trash-alt red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']) );

        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static' => '1']), 'fa:file-pdf red');
        $panel->addHeaderWidget($dropdown);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

        }
}