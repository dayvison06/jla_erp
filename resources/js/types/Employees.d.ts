import { File } from 'lucide-vue-next';

export interface EmployeeList {
    id: number;
    name: string;
    role: string;
    department: string;
    status: 'ativo' | 'inativo' | 'ferias' | 'afastado' | 'demitido';
    admission_date: string;
}
export interface Employee {
    id: number;
    name: string;
    birth_date: string;
    gender: string;
    civil_state: string;
    nationality: string;
    birthplace: string;
    cnpj: string;
    cpf: string;
    rg: string;
    issuing_agency: string;
    issue_date: string;
    education_level: 'Ensino Fundamental' | 'Ensino Médio' | 'Ensino Superior Incompleto' | 'Ensino Superior Completo' | 'Pós-Graduação (Especialização/MBA)' | 'Mestrado' | 'Doutorado';
    voter_registration: string;
    military_certificate: string;
    mother_name: string;
    father_name: string;
    photo: string | null;
    status: 'ativo' | 'inativo' | 'ferias' | 'afastado' | 'demitido';

    ctps_number: string;
    ctps_series: string;
    ctps_state: string;
    pis_pasep: string;
    nit: string;
    cnh: string;
    cnh_category: string;
    cnh_expiry: string;
    professional_registration: string;

    postal_code: string;
    street: string;
    number: string;
    complement: string;
    district: string;
    city: string;
    state: string;
    phone: string;
    mobile: string;
    email: string;
    emergency_contact: string;
    emergency_phone: string;

    bank: string;
    agency: string;
    account: string;
    account_type: string;
    pix_key: string;
    pix_key_type: string;

    role: string;
    department: string;
    contract_type: string;
    admission_date: string;
    termination_date: string | null;
    salary: string;
    work_schedule: string;
    benefits: string[];

    last_exam_date: string;
    next_exam_date: string;
    aso_result: string;
    allergies: string;
    blood_type: string;
    accident_history: string;
    additional_info: string;

    dependents: Dependent[];
    attachments: Attachment[];
}

interface Dependent {
    name: string;
    relationship: string;
    birth_date: string;
    cpf: string;
    rg: string;
    issuing_agency: string;
    issue_date: string;
    civil_state: string;
    purposes: string[];
}

interface Attachment {
    id: number,
    employee_id: number;
    name: string;
    type: string;
    path: string;
    size: string;
    uploaded_by: string;
    created_at: string;
    file: File | null;
}

interface RoleHistory {
    id: number;
    role: string;
    department: string;
    start_date: string;
    end_date: string | null;
    salary: string;
    reason: string;
}
