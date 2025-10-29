<?php

namespace App\Imports;

use App\Models\Employee\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, SkipsEmptyRows, SkipsOnError, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return Employee|null
     */
    public function model(array $row)
    {
        return new Employee([
            'name' => $row['nome_completo'],
            'birth_date' => $row['data_de_nascimento'],
            'gender' => $row['genero'],
            'civil_state' => $row['estado_civil'],
            'nationality' => $row['nacionalidade'],
            'birthplace' => $row['naturalidade'],
            'cpf' => $row['cpf'],
            'rg' => $row['rg'],
            'cnpj' => $row['cnpj'],
            'issuing_agency' => $row['orgao_emissor_rg'],
            'issue_date' => $row['data_de_emissao_rg'],
            'education_level' => $row['nivel_de_escolaridade'],
            'voter_registration' => $row['titulo_de_eleitor'],
            'military_certificate' => $row['certificado_militar'],
            'mother_name' => $row['nome_da_mae'],
            'father_name' => $row['nome_do_pai'],
            'photo' => $row['foto'],
            'status' => $row['status'] ?? 'active',
            'ctps_number' => $row['numero_ctps'],
            'ctps_series' => $row['serie_ctps'],
            'ctps_state' => $row['estado_ctps'],
            'pis_pasep' => $row['pispasep'],
            'nit' => $row['nit'],
            'cnh' => $row['cnh'],
            'cnh_category' => $row['categoria_cnh'],
            'cnh_expiry' => $row['validade_cnh'],
            'professional_registration' => $row['registro_profissional'],
            'postal_code' => $row['cep'],
            'street' => $row['logradouro'],
            'number' => $row['numero'],
            'complement' => $row['complemento'],
            'district' => $row['bairro'],
            'city' => $row['cidade'],
            'state' => $row['estado'],
            'phone' => $row['telefone_fixo'],
            'mobile' => $row['telefone_celular'],
            'email' => $row['e_mail'],
            'emergency_contact' => $row['contato_de_emergencia'],
            'emergency_phone' => $row['telefone_de_emergencia'],
            'bank' => $row['banco'],
            'agency' => $row['agencia'],
            'account' => $row['conta'],
            'account_type' => $row['tipo_de_conta'],
            'pix_key_type' => $row['tipo_de_chave_pix'],
            'pix_key' => $row['chave_pix'],
            'contract_type' => $row['tipo_de_contrato'],
            'admission_date' => $row['data_de_admissao'],
            'termination_date' => $row['data_de_rescisao'],
            'salary' => $row['salario'],
            'last_exam_date' => $row['data_ultimo_exame_aso'],
            'next_exam_date' => $row['data_proximo_exame_aso'],
            'aso_result' => $row['resultado_aso'],
            'allergies' => $row['alergias'],
            'blood_type' => $row['tipo_sanguineo'],
            'accident_history' => $row['historico_de_acidentes'],
            'additional_info' => $row['informacoes_adicionais'],
        ]);
    }

    public function onError(\Throwable $e)
    {
        dd('Erro', $e->getMessage());
    }
}
