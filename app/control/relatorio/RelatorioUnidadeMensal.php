<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TDatabase;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapFormBuilder;

class RRelatorioUnidadeMensal extends TPage
{
    private $form; //form
    protected $data;

    function __construct()
    {
    parent::__construct();
    
    //create the form
    $this->form = new BootstrapFormBuilder('form_RelatorioUnidadeMensal_report');
    $this->setFormTitke('Relatório Mensal de Unidade');

    $data_inicio = new TDate('data_inicio');
    $data_fim = new TDate('data_fim');

    $unidade_id = new TDBCombo('unidade_id', 'db_condominio', 'Unidade', 'id', 'descricao', );
    $situacao = new TBCombo('situacao');

    //create the form fields
    $this->form->addFields([new TLabel('Data Inicio', 'red')], [$data_inicio],
                           [new TLabel('Data Fim', 'red')], [$data_fim]);
    $this->form->addFields([new TLabel('Unidade ',)], [$unidade_id],
                           [new TLabel('Situação',)], [$situacao]);
    
    //set Mask
    $data_inicio->setMask('dd/mm/yyyy');
    $data_fim->setMask('dd/mm/yyyy');

    $output_type = new TRadioGroup('ouytputType');
    $this->form->addFields([new TLabel('Mostrar em:')], [$output_type]);

    //define field proprietário
    $output_type->setUseButton();
    $opitions = ['html' => 'HTML', 'pdf' => 'PDF', 'rtf' => 'RTF', 'xml' => 'XML'];
    $output_type->addItems($opitions);
    $output_type->setValue('pdf');
    $output_type->setLayout('horizontal');
    
    $this->form->addAction('Gerer Relatório', new TAction(array($this, 'onGenerate')), 'fa:download blue');

    //wrap the page content using vertical box
    $vbox = new TVBox;
    $vbox->style = 'width: 100%';
    $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
    $vbox->add($this->form);

    parent::add($vbox);
    }
    function onGenerate()
    {
        try
        {
            //get the form data into an active record Customer
            $this->data = $this->form->getData();
            $this->form->setData($this->data);

            $format = $this->data->$output_type;

            //open a transaction whit database''
            $query = 'SELECT conta_pagar.descricao, conta_pagar.valor, conta_pagar.data_vencimento, conta_pagar.valor_pago, pessoa.nome
                      FROM conta_pagar, pessoa
                      WHERE pessoa.id = conta_pagar.pessoa_id AND
                            conta_pagar.data_vencimento BETWEEN :data_inicio AND :data_fim';
        
            if (!empty($this->data->unidade_id))
            {
                $query .= "and conta_pagar = {$this->data->id}";
            }
            $fiters = [];
            $fiters['data_inicio'] = TDate::date2us($this->$this->data->data_inicio);
            $fiters['data_fim'] = TDate::date2us($this->$this->data->data_fim);

            $rows = TDatabase::getData($source, $query, null, $fiters);

            if ($rows)
            {
                $widths = [100,370,110,130,,140,100];

                switch ($format)
                {
                    case'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }
                if(!empty($table))
                {
                    //create the document style
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B8E57');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#6CC361');
                    $table->addStyle('datap',  'Helvetica', '10',  '', '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10',  '', '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10',  '', '#2B2B2B', '#B5FFb4');

                    $table->setHeaderCallback(function($table){
                        $table->addRow();
                        $table->addCell('Relatorio Divida Unidade', 'center', 'header', 6);
                        //Pega data início e data fim imprimida no relatório
                        $table->addRow();
                        $table->addCell('Data Início: ' .$this->data->datainicio ,' -Data Fim: ' .$this->data->data_fim, 'center', 'title', 6);

                        $table->addRow();
                        $table->addCell('Conta', 'center', 'title');
                        $table->addCell('Valor', 'center', 'title');
                        $table->addCell('Vencimento', 'center', 'title');
                        $table->addCell('Valor Pago', 'center', 'title');
                        $table->addCell('Pessoa', 'center', 'title');
                    });

                    $table->setFooterCallback(function($table){
                        $table->addRow();
                        $table->addCell(date('d/m/Y h:i:s:'), 'center', 'footer', 6);
                    });

                    //controls the background filling
                    $colour= FALSE;
                    //Inicia variável ValorTotal igual a 0
                    $Valor = 0;
                    $ValorPago = 0;

                    //data rows
                    foreach ($rows as $row)
                    {
                        $style = $colours ? 'datap' : 'datai';
                        //para converter data_vencimento no relatório
                        $row ['data_vencimento'] = TDate::date2br($row['data_vencimento']);

                        $table->addRow();
                        $table->addCell($row['conta'], 'left', $style);
                        $table->addCell($row['valor'], 'left', $style);
                        $table->addCell($row['data_vencimento'], 'left', $style);
                        $table->addCell($row['valor_pago'], 'left', $style);
                        $table->addCell($row['nome'], 'left', $style);

                        $Valor +=$row['valor'];
                        $ValorPago +=$row['valor_pago'];

                        $colour = !$colour;
                    }

                    $table->addRow();
                    $table->addCell('Valor Total: ', 'left', 'footer', 1);
                    $table->addCell(number_format($Valor,2,',','.'), 'rigth', 'footer', 3);
                    $table->addCell(number_format($ValorPago,2,',','.'), 'rigth', 'footer', 2);

                    $output = "app/output/tabular.{$format}";

                    //so=tores the file
                    if (!file_exists($output) OR is_writable($output))
                    {
                        $table->save($output);
                        parent::openFile($output);
                    }
                    else
                    {
                        throw new Exception(_t('Permission denued') . ': ' .$output);
                    }
                    //show the succes message
                    new TMessage('info', 'Relatório gerado. Por favor, ative popups no navegador');                    
                }
            }
            else
            {
                new TMessage('error', 'Relatório não encontrado.');
            }
            //clouse the transaction
            TTransaction::close();
        }
        catch (Exception $e)//in case of exceprion
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}