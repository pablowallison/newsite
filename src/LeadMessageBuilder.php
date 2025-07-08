<?php

namespace App;

class LeadMessageBuilder implements MessageBuilderInterface
{
    public function build(array $d): string
    {
        $periodo = $d['employed_over_3_years'] ? 'Sim' : 'Não';
        $depend  = $d['has_minor_child']       ? 'Sim' : 'Não';

        return <<<TXT
    Olá, você gostaria de simular as condições de financiamento com os dados abaixo:
    Nome completo: {$d['full_name']},
    Telefone: {$d['phone_number']},
    Renda: {$d['gross_income']}.
    CPF: {$d['cpf']},
    Data de nascimento: {$d['birth_date']},
    Estado civil: {$d['marital_status']},
    Mais de 3 anos de carteira assinada: {$periodo},
    Filho menor ou dependente: {$depend}
    TXT;
        }
}