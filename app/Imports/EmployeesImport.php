<?php

namespace App\Imports;

use App\Enums\EducationLevel;
use App\Models\Employee\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeesImport implements ToModel, SkipsEmptyRows, SkipsOnError, WithHeadingRow
{

    /**
    * @param array $row
    *
    * @return Employee|null
     */
    public function model(array $row)
    {
        # ToDo: Tomar vergonha na cara e corrigir esse import

        return new Employee([
            'name' => $row['nome_completo'],
            'birth_date' => $this->parseDate($row['data_de_nascimento']),
            'gender' => $row['genero'],
            'civil_state' => $row['estado_civil'],
            'nationality' => $row['nacionalidade'],
            'birthplace' => $row['naturalidade'],
            'cpf' => $row['cpf'],
            'rg' => $row['rg'],
            'cnpj' => $row['cnpj'],
            'issuing_agency' => $row['orgao_emissor_rg'],
            'issue_date' => $this->parseDate($row['data_de_emissao_rg']),
            'education_level' => $this->parseEducationLevel($row['nivel_de_escolaridade']),
            'voter_registration' => $row['titulo_de_eleitor'],
            'military_certificate' => $row['certificado_militar'],
            'mother_name' => $row['nome_da_mae'],
            'father_name' => $row['nome_do_pai'],
            'photo' => $row['foto'],
            'status' => $this->parseStatus($row['status']) ?? 'active',
            'ctps_number' => $row['numero_ctps'],
            'ctps_series' => $row['serie_ctps'],
            'ctps_state' => $row['estado_ctps'],
            'pis_pasep' => $row['pispasep'],
            'nit' => $row['nit'],
            'cnh' => $row['cnh'],
            'cnh_category' => $row['categoria_cnh'],
            'cnh_expiry' => $this->parseDate($row['validade_cnh']),
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
            'pix_key_type' => $this->parsePixType($row['tipo_de_chave_pix']),
            'pix_key' => $row['chave_pix'],
            'contract_type' => $row['tipo_de_contrato'],
            'admission_date' => $this->parseDate($row['data_de_admissao']),
            'termination_date' => $this->parseDate($row['data_de_rescisao']),
            'salary' => $row['salario'],
            'last_exam_date' => $this->parseDate($row['data_ultimo_exame_aso']),
            'next_exam_date' => $this->parseDate($row['data_proximo_exame_aso']),
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

    private function parseDate($excelDate)
    {
        if (is_numeric($excelDate)) {
            return Date::excelToDateTimeObject($excelDate)->format('Y-m-d');
        }

        try {
            return Carbon::parse($excelDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parsePixType($type)
    {
        $types = [
            'cpf' => 'cpf',
            'cnpj' => 'cnpj',
            'e-mail' => 'email',
            'email' => 'email',
            'celular' => 'phone',
            'telefone' => 'phone',
            'phone' => 'phone',
            'aleatória' => 'random',
            'aleatoria' => 'random',
            'aleatorio' => 'random',
            'aleatório' => 'random',
        ];

        return $types[strtolower($type)] ?? null;
    }

    private function parseEducationLevel($level)
    {
        $levels = [
            'ensino fundamental' => EducationLevel::ENSINO_FUNDAMENTAL,
            'fundamental' => EducationLevel::ENSINO_FUNDAMENTAL,
            'ensino fundamental completo' => EducationLevel::ENSINO_FUNDAMENTAL,
            'ensino fundamental incompleto' => EducationLevel::ENSINO_FUNDAMENTAL_INCOMPLETO,
            'fundamental incompleto' => EducationLevel::ENSINO_FUNDAMENTAL_INCOMPLETO,

            'ensino médio' => EducationLevel::ENSINO_MEDIO,
            'ensino medio' => EducationLevel::ENSINO_MEDIO,
            'médio' => EducationLevel::ENSINO_MEDIO,
            'medio' => EducationLevel::ENSINO_MEDIO,

            'ensino técnico' => EducationLevel::ENSINO_TECNICO,
            'ensino tecnico' => EducationLevel::ENSINO_TECNICO,
            'técnico' => EducationLevel::ENSINO_TECNICO,
            'tecnico' => EducationLevel::ENSINO_TECNICO,

            'ensino superior incompleto' => EducationLevel::ENSINO_SUPERIOR_INCOMPLETO,
            'superior incompleto' => EducationLevel::ENSINO_SUPERIOR_INCOMPLETO,

            'ensino superior' => EducationLevel::ENSINO_SUPERIOR_COMPLETO,
            'ensino superior completo' => EducationLevel::ENSINO_SUPERIOR_COMPLETO,
            'superior' => EducationLevel::ENSINO_SUPERIOR_COMPLETO,
            'superior completo' => EducationLevel::ENSINO_SUPERIOR_COMPLETO,

            'pós-graduação' => EducationLevel::POS_GRADUACAO,
            'pos-graduação' => EducationLevel::POS_GRADUACAO,
            'pós graduacao' => EducationLevel::POS_GRADUACAO,
            'pos graduacao' => EducationLevel::POS_GRADUACAO,
            'pos graduação' => EducationLevel::POS_GRADUACAO,

            'mestrado' => EducationLevel::MESTRADO,
            'doutorado' => EducationLevel::DOUTORADO,
        ];

        return $levels[strtolower($level)] ?? null;
    }

    private function parseContractType($type)
    {
        $types = [
            'clt' => 'clt',
            'pj' => 'pj',
            'temporário' => 'temporario',
            'temporario' => 'temporario',
            'estágio' => 'estagio',
            'estagio' => 'estagio',
            'aprendiz' => 'aprendiz',
            'diarista' => 'temporario',
            'diaria' => 'temporario',
            'estagiário' => 'estagio',
            'estagiario' => 'estagio',
            'freelancer' => 'pj',
            'autônomo' => 'pj',
            'autonomo' => 'pj',
            'carteira assinada' => 'clt',
        ];

        return $types[strtolower($type)] ?? null;
    }

    private function parseStatus($status)
    {
        $statuses = [
            'ativo' => 'active',
            'inativo' => 'inactive',
            'de férias' => 'on_vacation',
            'de ferias' => 'on_vacation',
            'licença' => 'on_leave',
            'licenca' => 'on_leave',
            'demitido' => 'terminated',
            'rescindido' => 'terminated',
        ];

        return $statuses[strtolower($status)] ?? 'active';
    }
}
